# 🌀 Duhovni Varuh — AI Asistent

Lokalni AI asistent z dostopom do datotek projekta AstraMentalica, prek DeepSeek API.

## Namestitev

1. **Python 3.10+** mora biti nameščen.

2. Odpri terminal v tej mapi in namesti odvisnosti:
   ```bash
   pip install -r requirements.txt
   ```

   Opomba: `pyaudio` lahko na Windows potrebuje:
   ```bash
   pip install pipwin
   pipwin install pyaudio
   ```
   Če `openai-whisper` ali `pyaudio` povzročata težave in jih ne potrebuješ
   (uporabljaš samo brskalniški mikrofon), ju lahko odstraniš iz
   `requirements.txt`.

3. Kopiraj `.env.primer` v `.env`:
   ```bash
   copy .env.primer .env
   ```
   (na Linux/Mac: `cp .env.primer .env`)

4. Uredi `.env`:
   - `DEEPSEEK_API_KEY` — tvoj DeepSeek API ključ
   - `PROJEKT_POT` — pot do korena tvojega AstraMentalica projekta
     (npr. `D:\streznik\xampp\htdocs\10.6`)

## Zagon

```bash
python varuh.py
```

Brskalnik se samodejno odpre na `http://localhost:5757`.

## Uporaba

- **Tipkaj ali govori** — Varuh razume slovenščino.
- **Vprašaj o kodi**: "Preberi mi SISTEM/api.php" ali "Poišči funkcijo avatar_dodaj_tocke"
- **Prosi za spremembe**: "Dodaj komentar v vrstico X" ali "Napiši novo funkcijo za..."
- **Nove seje**: gumb "+ Nova seja" v stranski vrstici, vse pogovore ohrani.

## Varnost

- Vse spremembe datotek ustvarijo `.bak` varnostno kopijo.
- Brisanje datotek jih premakne v `_kos/` mapo (ne izbriše trajno).
- Dostop je omejen na `PROJEKT_POT` — Varuh ne more dostopati zunaj te mape.

## Function Calling — orodja Varuha

| Orodje | Opis |
|---|---|
| `preberi_datoteko` | Prebere vsebino datoteke |
| `seznam_datotek` | Izpiše datoteke v mapi |
| `poisci_v_datotekah` | Iskanje besedila po projektu |
| `zapisi_datoteko` | Posodobi obstoječo datoteko (z `.bak`) |
| `ustvari_datoteko` | Ustvari novo datoteko |
| `pobrisi_datoteko` | Varno "briše" (premakne v `_kos/`) |
| `preimenuj_datoteko` | Preimenuje/premakne datoteko |

## Struktura

```
varuh_ai/
├── varuh.py              ← Flask strežnik
├── requirements.txt
├── .env.primer           ← kopiraj v .env
├── orodja/
│   └── datoteke.py        ← function calling orodja
├── templates/
│   └── varuh.html          ← spletni vmesnik
├── static/
└── zgodovina/             ← shranjeni pogovori (JSON)
```
