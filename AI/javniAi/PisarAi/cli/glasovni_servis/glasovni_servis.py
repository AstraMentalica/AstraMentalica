"""
============================================================
POT: glasovni-servis/glasovni_servis.py
VERZIJA: v114 (9.6.2026 18:00)
============================================================

NAMEN:
    Lokalni glasovni mikroservis – TTS (Coqui XTTS-v2, voice
    cloning) in STT (faster-whisper). Teče ločeno od PHP
    sistema na lastnem portu (privzeto 8088).

    AstraMentalica PHP backend (tts_storitev.php,
    stt_storitev.php) kliče ta servis prek HTTP namesto
    ElevenLabs/Azure/OpenAI – BREZPLAČNO in LOKALNO.

ENDPOINTI:
    POST /tts      – besedilo → zvok (wav)
    POST /stt      – zvok → besedilo
    GET  /glasovi  – seznam razpoložljivih kloniranih glasov
    POST /klonriaj – naloži vzorec glasu (6-30s), shrani kot nov glas
    GET  /zdravje  – health check

NAMESTITEV:
    pip install fastapi uvicorn TTS faster-whisper python-multipart

    # GPU (priporočeno, ~10x hitreje):
    pip install torch --index-url https://download.pytorch.org/whl/cu121

    Zagon:
    uvicorn glasovni_servis:app --host 0.0.0.0 --port 8088

OPOMBA O KAKOVOSTI ZA SLOVENŠČINO:
    XTTS-v2 ni uradno treniran na slovenščini, vendar:
    - Multilingual checkpoint razume fonetiko podobnih
      slovanskih jezikov (hr, sl so fonetsko blizu)
    - Voice cloning (govorimo "language": "sl" ni podprt
      direktno – uporabimo "hr" kot najboljši približek
      ALI fine-tuniramo na lastnih posnetkih, glej docs/)
    - Za PRAVO slovenščino priporočamo fine-tuning:
      glej docs/FINETUNE_SL.md

STATUS: Stabilno
AVTOR:  AstraMentalica Mojster
JEZIK:  komentarji v slovenščini, koda standardna (Python convention)
OZNAKE: glasovni-servis, tts, stt, xtts, whisper, lokalno
============================================================
"""

import os
import io
import uuid
import json
import tempfile
from pathlib import Path
from typing import Optional

from fastapi import FastAPI, UploadFile, File, Form, HTTPException
from fastapi.responses import StreamingResponse, JSONResponse
from fastapi.middleware.cors import CORSMiddleware

# ============================================================
# KONFIGURACIJA
# ============================================================

MAPA_GLASOVI   = Path(os.environ.get("GLASOVI_MAPA", "./glasovi"))
MAPA_GLASOVI.mkdir(parents=True, exist_ok=True)

WHISPER_MODEL  = os.environ.get("WHISPER_MODEL", "large-v3")  # ali "medium" za hitrejše/manj RAM
WHISPER_DEVICE = os.environ.get("WHISPER_DEVICE", "cpu")        # "cuda" če imaš GPU
WHISPER_COMPUTE = os.environ.get("WHISPER_COMPUTE", "int8")     # "float16" na GPU

XTTS_DEVICE    = os.environ.get("XTTS_DEVICE", "cpu")            # "cuda" priporočeno

# Lazy-load modelov – ne nalagaj ob zagonu, šele ob prvi uporabi
_xtts_model    = None
_whisper_model = None


def _naloži_xtts():
    """Naloži Coqui XTTS-v2 model (lazy)."""
    global _xtts_model
    if _xtts_model is None:
        from TTS.api import TTS
        print("[glasovni-servis] Nalagam XTTS-v2 ... (prvič lahko traja 1-2 min)")
        _xtts_model = TTS("tts_models/multilingual/multi-dataset/xtts_v2").to(XTTS_DEVICE)
        print("[glasovni-servis] XTTS-v2 naložen.")
    return _xtts_model


def _naloži_whisper():
    """Naloži faster-whisper model (lazy)."""
    global _whisper_model
    if _whisper_model is None:
        from faster_whisper import WhisperModel
        print(f"[glasovni-servis] Nalagam faster-whisper ({WHISPER_MODEL}) ...")
        _whisper_model = WhisperModel(
            WHISPER_MODEL,
            device=WHISPER_DEVICE,
            compute_type=WHISPER_COMPUTE,
        )
        print("[glasovni-servis] Whisper naložen.")
    return _whisper_model


# ============================================================
# JEZIKOVNO MAPIRANJE (XTTS nima 'sl')
# ============================================================

# XTTS-v2 podprti jeziki: en, es, fr, de, it, pt, pl, tr, ru, nl,
# cs, ar, zh-cn, ja, hu, ko, hi
#
# Slovenščina NI med njimi. Najboljši fonetski približek je
# 'cs' (češčina) ali 'hr' preko custom fine-tune.
#
# Za PRAVO kakovost: fine-tune lasten model (glej docs/FINETUNE_SL.md)
# Za HITER začetek: uporabi 'cs' kot približek – razumljivo, a z
# rahlim "tujim" naglasom.

XTTS_JEZIK_MAPA = {
    "sl": "cs",   # najboljši fonetski približek brez fine-tuninga
    "sl_finetune": "sl",  # če imaš fine-tuned model, podpira pravi 'sl'
}


# ============================================================
# FASTAPI APP
# ============================================================

app = FastAPI(title="AstraMentalica – Glasovni servis", version="v114")

app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],  # V produkciji omeji na svojo domeno
    allow_methods=["*"],
    allow_headers=["*"],
)


@app.get("/zdravje")
def zdravje():
    """Health check – preveri ali so modeli naloženi."""
    return {
        "status": "ok",
        "xtts_naložen": _xtts_model is not None,
        "whisper_naložen": _whisper_model is not None,
        "glasovi": [p.stem for p in MAPA_GLASOVI.glob("*.wav")],
    }


# ============================================================
# TTS – BESEDILO V GOVOR
# ============================================================

@app.post("/tts")
def tts(
    besedilo: str = Form(...),
    glas: str = Form("privzeti"),          # ime .wav datoteke v glasovi/ (brez .wav)
    jezik: str = Form("sl"),
    uporabi_finetune: bool = Form(False),  # True = uporabi fine-tuned SL model
):
    """
    Pretvori besedilo v govor z kloniranim glasom.

    `glas` – ime referenčnega vzorca v mapi glasovi/ (npr. "moj_glas.wav")
             Naloži ga prek /klonriaj endpointa.

    Vrne: audio/wav stream
    """
    if not besedilo.strip():
        raise HTTPException(400, "Besedilo je prazno.")

    glas_pot = MAPA_GLASOVI / f"{glas}.wav"
    if not glas_pot.exists():
        # Fallback na privzeti glas
        glas_pot = MAPA_GLASOVI / "privzeti.wav"
        if not glas_pot.exists():
            raise HTTPException(
                404,
                f"Glas '{glas}' ne obstaja in privzeti.wav manjka. "
                f"Naloži referenčni vzorec prek /klonriaj.",
            )

    model = _naloži_xtts()

    # Določi jezikovno kodo za XTTS
    if uporabi_finetune:
        jezik_xtts = "sl"  # fine-tuned model razume 'sl' direktno
    else:
        jezik_xtts = XTTS_JEZIK_MAPA.get(jezik, jezik)

    # Generiraj v začasno datoteko (XTTS API zahteva pot)
    with tempfile.NamedTemporaryFile(suffix=".wav", delete=False) as tmp:
        izhodna_pot = tmp.name

    try:
        model.tts_to_file(
            text=besedilo,
            speaker_wav=str(glas_pot),
            language=jezik_xtts,
            file_path=izhodna_pot,
        )

        with open(izhodna_pot, "rb") as f:
            audio_bytes = f.read()

    finally:
        os.unlink(izhodna_pot)

    return StreamingResponse(
        io.BytesIO(audio_bytes),
        media_type="audio/wav",
        headers={"Content-Disposition": "inline; filename=govor.wav"},
    )


# ============================================================
# STT – GOVOR V BESEDILO
# ============================================================

@app.post("/stt")
async def stt(
    avdio: UploadFile = File(...),
    jezik: str = Form("sl"),
    prompt: Optional[str] = Form(None),
):
    """
    Prepiše avdio posnetek v besedilo (faster-whisper).

    Vrne: {"besedilo": "...", "zaupanje": 0.95, "jezik": "sl", "segmenti": [...]}
    """
    model = _naloži_whisper()

    # Shrani naloženo datoteko začasno
    suffix = Path(avdio.filename or "posnetek.webm").suffix or ".webm"
    with tempfile.NamedTemporaryFile(suffix=suffix, delete=False) as tmp:
        vsebina = await avdio.read()
        tmp.write(vsebina)
        tmp_pot = tmp.name

    try:
        segmenti_iter, info = model.transcribe(
            tmp_pot,
            language=jezik,
            initial_prompt=prompt,
            vad_filter=True,  # Odstrani tišino – hitrejše in natančnejše
            beam_size=5,
        )

        segmenti = []
        besedilo_deli = []
        verjetnosti = []

        for seg in segmenti_iter:
            segmenti.append({
                "začetek": round(seg.start, 2),
                "konec": round(seg.end, 2),
                "besedilo": seg.text.strip(),
            })
            besedilo_deli.append(seg.text.strip())
            verjetnosti.append(seg.avg_logprob)

        besedilo = " ".join(besedilo_deli).strip()
        zaupanje = round(
            min(1.0, max(0.0, (sum(verjetnosti) / len(verjetnosti) if verjetnosti else -1) + 1)),
            2,
        )

    finally:
        os.unlink(tmp_pot)

    return {
        "status": "success",
        "besedilo": besedilo,
        "zaupanje": zaupanje,
        "jezik": info.language,
        "jezik_verjetnost": round(info.language_probability, 2),
        "segmenti": segmenti,
    }


# ============================================================
# UPRAVLJANJE GLASOV (VOICE CLONING)
# ============================================================

@app.get("/glasovi")
def seznam_glasov():
    """Vrne seznam vseh kloniranih glasov."""
    glasovi = []
    for wav_pot in MAPA_GLASOVI.glob("*.wav"):
        meta_pot = wav_pot.with_suffix(".json")
        meta = {}
        if meta_pot.exists():
            meta = json.loads(meta_pot.read_text())
        glasovi.append({
            "id": wav_pot.stem,
            "naziv": meta.get("naziv", wav_pot.stem),
            "opis": meta.get("opis", ""),
            "jezik": meta.get("jezik", "sl"),
        })
    return {"glasovi": glasovi}


@app.post("/klonriaj")
async def klonriaj_glas(
    avdio: UploadFile = File(...),
    id: str = Form(...),
    naziv: str = Form(...),
    opis: str = Form(""),
):
    """
    Naloži referenčni vzorec glasu (6-30 sekund, čisto WAV/MP3
    brez glasbe v ozadju) za uporabo z XTTS voice cloning.

    Priporočila za vzorec:
    - 10-20 sekund govora, naravna intonacija
    - Brez šuma v ozadju, brez glasbe
    - Format: WAV 22050Hz ali 24000Hz, mono
    - Govori v slovenščini (XTTS posnema TON, ne razume vsebine)
    """
    if not id.replace("_", "").replace("-", "").isalnum():
        raise HTTPException(400, "ID lahko vsebuje samo črke, številke, _ in -")

    izhodna_pot = MAPA_GLASOVI / f"{id}.wav"
    meta_pot    = MAPA_GLASOVI / f"{id}.json"

    # Shrani avdio (pretvorba v WAV če potrebno – uporabi ffmpeg)
    vsebina = await avdio.read()
    suffix  = Path(avdio.filename or "vzorec.wav").suffix or ".wav"

    with tempfile.NamedTemporaryFile(suffix=suffix, delete=False) as tmp:
        tmp.write(vsebina)
        tmp_pot = tmp.name

    try:
        if suffix.lower() != ".wav":
            # Pretvori z ffmpeg v WAV 24kHz mono
            import subprocess
            subprocess.run(
                ["ffmpeg", "-y", "-i", tmp_pot, "-ar", "24000", "-ac", "1", str(izhodna_pot)],
                check=True, capture_output=True,
            )
        else:
            import shutil
            shutil.copy(tmp_pot, izhodna_pot)
    finally:
        os.unlink(tmp_pot)

    # Shrani metapodatke
    meta_pot.write_text(json.dumps({
        "naziv": naziv,
        "opis": opis,
        "jezik": "sl",
    }, ensure_ascii=False, indent=2))

    return {
        "status": "success",
        "sporocilo": f"Glas '{id}' shranjen. Uporabi ga z 'glas={id}' v /tts klicu.",
        "id": id,
    }


@app.delete("/glasovi/{id}")
def izbrisi_glas(id: str):
    """Izbriše kloniran glas."""
    wav_pot  = MAPA_GLASOVI / f"{id}.wav"
    meta_pot = MAPA_GLASOVI / f"{id}.json"

    if not wav_pot.exists():
        raise HTTPException(404, "Glas ne obstaja.")

    wav_pot.unlink()
    if meta_pot.exists():
        meta_pot.unlink()

    return {"status": "success", "sporocilo": f"Glas '{id}' izbrisan."}


# ============================================================
# ZAGON: uvicorn glasovni_servis:app --host 0.0.0.0 --port 8088
# ============================================================

if __name__ == "__main__":
    import uvicorn
    uvicorn.run(app, host="0.0.0.0", port=8088)
