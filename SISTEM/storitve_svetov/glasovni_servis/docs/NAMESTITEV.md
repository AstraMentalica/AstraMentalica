# Lokalni glasovni servis — Namestitev

> Brezplačen, lokalen TTS (XTTS-v2 voice cloning) in STT (faster-whisper) za AstraMentalica.
> Nič ne gre na ElevenLabs/Azure/OpenAI strežnike — vse teče na tvojem strežniku/računalniku.

---

## 1. ZAHTEVE

```
Python 3.10–3.11  (3.12 lahko dela težave s TTS paketom)
ffmpeg            (za pretvorbo zvočnih formatov)
~6 GB prostora    (modeli)
RAM: min 8GB (CPU), priporočeno 16GB
GPU (opcijsko):   NVIDIA z 6GB+ VRAM = 10x hitreje
```

Preveri Python verzijo:
```bash
python3 --version
```

Namesti ffmpeg (Ubuntu/Debian):
```bash
sudo apt install ffmpeg
```

---

## 2. NAMESTITEV PAKETOV

```bash
cd glasovni-servis/
python3 -m venv venv
source venv/bin/activate          # Linux/Mac
# venv\Scripts\activate            # Windows

pip install -r requirements.txt
```

### Če imaš NVIDIA GPU (priporočeno za XTTS):
```bash
pip install torch --index-url https://download.pytorch.org/whl/cu121 --upgrade
```

Preveri da GPU dela:
```bash
python3 -c "import torch; print(torch.cuda.is_available())"
# Mora izpisati: True
```

---

## 3. PRVI ZAGON

```bash
uvicorn glasovni_servis:app --host 0.0.0.0 --port 8088
```

Prvi zagon traja dlje — model XTTS-v2 (~2GB) se prenese samodejno
ob prvi uporabi `/tts` endpointa. faster-whisper model se prenese
ob prvi uporabi `/stt`.

Preveri da servis dela:
```bash
curl http://127.0.0.1:8088/zdravje
```

---

## 4. KONFIGURACIJA (okoljske spremenljivke)

```bash
# Privzeto: 127.0.0.1:8088 — če teče na drugem strežniku, spremeni:
export GLASOVNI_SERVIS_URL="http://127.0.0.1:8088"

# Whisper model — "large-v3" (najboljša točnost) ali "medium" (hitrejši, manj RAM)
export WHISPER_MODEL="large-v3"

# Naprava: "cpu" ali "cuda" (če imaš GPU)
export WHISPER_DEVICE="cpu"
export XTTS_DEVICE="cpu"

# Compute type za whisper: "int8" (CPU) ali "float16" (GPU)
export WHISPER_COMPUTE="int8"
```

Te spremenljivke dodaj v `PODATKI/sistem/env/kljuci.json` ali v systemd
service datoteko (glej spodaj).

---

## 5. NALOŽI SVOJ GLAS (voice cloning)

To je **najpomembnejši korak** za dober slovenski naglas.

### Posnemi vzorec (10-20 sekund)
- Mirno okolje, brez šuma in glasbe
- Naravna intonacija, kot da pripoveduješ zgodbo
- Format: WAV ali MP3, lahko tudi telefon

### Naloži prek API:
```bash
curl -X POST http://127.0.0.1:8088/klonriaj \
  -F "avdio=@moj_glas.wav" \
  -F "id=privzeti" \
  -F "naziv=Moj glas" \
  -F "opis=Glavni pripovedovalec za AstraMentalica"
```

Ali naredi to prek admin vmesnika (Astra) — glej `astra_glasovi.php`.

### Preveri seznam glasov:
```bash
curl http://127.0.0.1:8088/glasovi
```

---

## 6. TEST SINTEZE

```bash
curl -X POST http://127.0.0.1:8088/tts \
  -F "besedilo=Pozdravljen na poti samospoznavanja." \
  -F "glas=privzeti" \
  -F "jezik=sl" \
  --output test.wav

# Predvajaj
ffplay test.wav   # ali odpri v predvajalniku
```

> **Opomba o kakovosti**: XTTS-v2 brez fine-tuninga uporablja češčino (`cs`)
> kot fonetski približek za slovenščino — razumljivo, vendar zazna se
> tujost naglasa. Za PRAVO slovenščino glej `FINETUNE_SL.md`.

---

## 7. PHP INTEGRACIJA

V `PODATKI/sistem/env/kljuci.json` ni treba ničesar — sistem
samodejno preveri `http://127.0.0.1:8088/zdravje` in če servis teče,
ga uporabi kot prvo prioriteto (brezplačno).

Fallback veriga (`_tts_izberi_ponudnika()`):
```
1. lokalni     (XTTS-v2 — če servis teče)
2. elevenlabs  (če je ELEVENLABS_API_KEY nastavljen)
3. azure       (če je AZURE_TTS_KEY nastavljen)
4. openai      (če je OPENAI_API_KEY nastavljen)
5. browser     (Web Speech API — zadnja možnost)
```

---

## 8. PRODUKCIJA — systemd servis

Ustvari `/etc/systemd/system/astra-glasovni.service`:

```ini
[Unit]
Description=AstraMentalica Glasovni Servis (XTTS + Whisper)
After=network.target

[Service]
Type=simple
User=www-data
WorkingDirectory=/pot/do/glasovni-servis
Environment="WHISPER_MODEL=large-v3"
Environment="WHISPER_DEVICE=cpu"
Environment="WHISPER_COMPUTE=int8"
Environment="XTTS_DEVICE=cpu"
ExecStart=/pot/do/glasovni-servis/venv/bin/uvicorn glasovni_servis:app --host 127.0.0.1 --port 8088
Restart=on-failure
RestartSec=5

[Install]
WantedBy=multi-user.target
```

```bash
sudo systemctl daemon-reload
sudo systemctl enable astra-glasovni
sudo systemctl start astra-glasovni
sudo systemctl status astra-glasovni
```

---

## 9. RESOURCE OPOMBE

| Konfiguracija | RAM | Hitrost TTS | Hitrost STT |
|---|---|---|---|
| CPU, whisper medium | ~6GB | ~3-5s/stavek | ~realtime |
| CPU, whisper large-v3 | ~10GB | ~3-5s/stavek | ~1.5x realtime |
| GPU 8GB, large-v3 | ~6GB VRAM | ~0.5-1s/stavek | ~5x hitreje |

Za majhne VPS-e (2-4GB RAM): uporabi `WHISPER_MODEL=medium` ali `small`.

---

## 10. TEŽAVE

**"ModuleNotFoundError: No module named 'TTS'"**
→ `pip install TTS` znova, preveri Python verzijo (3.10-3.11 priporočeno)

**XTTS prvi klic zelo počasen (1-2 min)**
→ Normalno — model se nalaga v RAM. Naslednji klici so hitri.

**"CUDA out of memory"**
→ Zmanjšaj `WHISPER_MODEL` na "medium" ali nastavi `XTTS_DEVICE=cpu`

**Lokalni servis ni dosegljiv iz PHP**
→ Preveri `curl http://127.0.0.1:8088/zdravje` direktno na strežniku
→ Preveri firewall (servis mora biti dosegljiv iz PHP procesa)
