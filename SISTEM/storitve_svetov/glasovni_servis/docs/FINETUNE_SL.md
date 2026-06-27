# Fine-tuning za PRAVO slovenščino

> XTTS-v2 nima slovenščine med 17 uradno podprtimi jeziki.
> Privzeto uporablja češčino (`cs`) kot fonetski približek —
> deluje, vendar zazna se tujost. Ta vodič opiše, kako narediti
> **prav slovenski model** s svojimi posnetki.

---

## ZAKAJ JE TO VREDNO

- Lasten glas / domač govorec → naravna intonacija slovenščine
- Pravilen naglas na besedah (XTTS brez fine-tuninga pogosto
  napačno naglasi dvozložne in trizložne besede)
- Pravilna izgovarjava šumnikov (č, š, ž) — XTTS jih včasih
  "anglizira"

## KOLIKO PODATKOV POTREBUJEŠ

| Količina posnetkov | Rezultat |
|---|---|
| 10-30 min | Osnovni fine-tune, opazna izboljšava naglasa |
| 1-3 ure | Dober rezultat, naraven govor |
| 5+ ur | Profesionalna kakovost, blizu pravega govorca |

**Vir podatkov**: lahko posnameš sam (mikrofon + besedilo za branje),
ali uporabiš obstoječe slovenske posnetke z transkripcijo
(npr. javno dostopni avdio-knjige, podcasti — preveri licenco!).

---

## KORAK 1: PRIPRAVI PODATKOVNI SET

Struktura mape:
```
podatki_sl/
├── wavs/
│   ├── posnetek_0001.wav   (22050Hz ali 24000Hz, mono, 16-bit)
│   ├── posnetek_0002.wav
│   └── ...
└── metadata.csv
```

`metadata.csv` format (LJSpeech style, ločeno z `|`):
```
posnetek_0001|Pozdravljen na poti samospoznavanja.
posnetek_0002|Vsaka pot se začne v tišini.
posnetek_0003|Zvezde so ogledalo tvoje duše.
```

### Pretvorba obstoječih posnetkov v pravi format:
```bash
# Pretvori v 22050Hz mono WAV
for f in *.mp3; do
    ffmpeg -i "$f" -ar 22050 -ac 1 "wavs/${f%.mp3}.wav"
done
```

### Avtomatska transkripcija (če nimaš besedila):
Uporabi naš `/stt` endpoint (faster-whisper) za samodejni izpis:
```bash
for f in wavs/*.wav; do
    ime=$(basename "$f" .wav)
    besedilo=$(curl -s -X POST http://127.0.0.1:8088/stt \
        -F "avdio=@$f" -F "jezik=sl" | python3 -c "import sys,json; print(json.load(sys.stdin)['besedilo'])")
    echo "${ime}|${besedilo}" >> metadata.csv
done
```
**Vedno preveri in popravi avtomatsko transkripcijo ročno** — napake
v transkripciji se prenesejo v model.

---

## KORAK 2: FINE-TUNE Z COQUI TTS TRAINER

```bash
pip install TTS[all]

# Prenesi recept za XTTS fine-tuning
git clone https://github.com/coqui-ai/TTS
cd TTS/recipes/ljspeech/xtts_v2/
```

Uredi `train_gpt_xtts.py`:
```python
# Spremeni poti na svoj podatkovni set
DATASET_CONFIG = BaseDatasetConfig(
    formatter="ljspeech",
    dataset_name="slovenscina",
    path="/pot/do/podatki_sl/",
    meta_file_train="metadata.csv",
    language="sl",  # Dodamo novo jezikovno kodo
)

# Število epoh - odvisno od količine podatkov
# 10-30min podatkov: 50-100 epoh
# 1-3h podatkov: 20-50 epoh
NUM_EPOCHS = 50
BATCH_SIZE = 4  # Zmanjšaj če CUDA out of memory
```

### Pomembno: Dodaj 'sl' v jezikovni config

V `TTS/tts/layers/xtts/tokenizer.py` poišči `LANGUAGES` slovar in dodaj:
```python
LANGUAGES = {
    # ... obstoječi jeziki ...
    "sl": "Slovenian",
}
```

In v `TTS/tts/layers/xtts/zh_num2words.py` ali podoben modul za
normalizacijo besedil — XTTS uporablja jezikovno-specifične
normalizatorje za številke/datume. Za slovenščino lahko uporabiš
poljski (`pl`) normalizator kot osnovo (slovnično najbližji za
številke) in ga prilagodiš slovenskim končnicam.

### Zaženi treniranje:
```bash
CUDA_VISIBLE_DEVICES=0 python3 train_gpt_xtts.py
```

Treniranje na 1h podatkov, RTX 3090: ~4-8 ur za 50 epoh.
Na CPU: NI priporočeno (dnevi).

---

## KORAK 3: UPORABI FINE-TUNED MODEL

Po treniranju imaš checkpoint v `output/run/best_model.pth`.

V `glasovni_servis.py` spremeni `_naloži_xtts()`:

```python
def _naloži_xtts():
    global _xtts_model
    if _xtts_model is None:
        from TTS.tts.configs.xtts_config import XttsConfig
        from TTS.tts.models.xtts import Xtts

        config = XttsConfig()
        config.load_json("/pot/do/output/run/config.json")

        _xtts_model = Xtts.init_from_config(config)
        _xtts_model.load_checkpoint(
            config,
            checkpoint_path="/pot/do/output/run/best_model.pth",
            vocab_path="/pot/do/output/run/vocab.json",
        )
        _xtts_model.to(XTTS_DEVICE)
    return _xtts_model
```

In v `/tts` endpointu, ko je `uporabi_finetune=true`:
```python
jezik_xtts = "sl"  # Tvoj fine-tuned model razume 'sl' direktno
```

---

## ALTERNATIVA: PIPER TTS (lažji vstop)

Če se XTTS fine-tuning zdi prezahteven, **Piper TTS** je enostavnejši:

```bash
pip install piper-tts

# Obstaja osnovni sl_SI model na HuggingFace:
# rhasspy/piper-voices → sl/sl_SI/

# Fine-tuning Piper-ja je preprostejši — VITS arhitektura,
# trenira se hitreje (manj parametrov), a kakovost je nekoliko
# nižja od XTTS voice cloning.
```

Vodič: https://github.com/rhasspy/piper/blob/master/TRAINING.md

**Priporočilo**: začni z XTTS voice cloning brez fine-tuninga
(10s vzorca, `jezik=cs` približek) — če je rezultat dovolj dober
za tvoje potrebe, fine-tuning morda ni nujen. Fine-tuning rezerviraj
za primer, ko ti naglas v `cs` približku moti.

---

## PRIMERJAVA REZULTATOV

| Pristop | Čas priprave | Kakovost SL naglasa | Strošek |
|---|---|---|---|
| XTTS voice cloning (cs approx) | 5 min | 6/10 — razumljivo, tuj naglas | 0€ |
| XTTS voice cloning + fine-tune (30min podatkov) | 1-2 dni | 8/10 — naraven | 0€ (lasten GPU) ali ~5€ (cloud GPU za par ur) |
| XTTS fine-tune (3h+ podatkov) | 1 teden | 9/10 — profesionalno | 0€ ali ~20€ cloud GPU |
| Azure Petra/Rok Neural | 0 (takoj) | 7/10 — dobro, a generičen "radijski" glas | 16$/1M znakov |
