"""
varuh.py
Glavni Flask strežnik za Duhovnega Varuha.
Poganja: python varuh.py
"""
import os
import json
import time
import threading
import subprocess
import sys
from pathlib import Path
from datetime import datetime
from flask import Flask, request, jsonify, send_from_directory
from flask_cors import CORS
from dotenv import load_dotenv
import requests

# Naloži .env
load_dotenv()

# Orodja za datoteke
sys.path.insert(0, str(Path(__file__).parent))
from orodja.datoteke import ORODJA_DEFINICIJE, izvedi_orodje

# ── Konfiguracija ─────────────────────────────────────────────────────────────

DEEPSEEK_API_KEY = os.getenv('DEEPSEEK_API_KEY', '')
DEEPSEEK_MODEL   = os.getenv('DEEPSEEK_MODEL', 'deepseek-chat')
DEEPSEEK_URL     = os.getenv('DEEPSEEK_API_URL', 'https://api.deepseek.com/v1/chat/completions')
PROJEKT_POT      = os.getenv('PROJEKT_POT', str(Path.home() / 'projekt'))
FLASK_PORT       = int(os.getenv('FLASK_PORT', 5757))
POT_ZGODOVINE    = Path(__file__).parent / 'zgodovina'
POT_ZGODOVINE.mkdir(exist_ok=True)

SISTEMSKI_PROMPT = f"""Ti si Duhovni Varuh — AI asistent z dostopom do datotek projekta AstraMentalica.
Projekt se nahaja na: {PROJEKT_POT}

Tvoje sposobnosti:
- Bereš in pišeš datoteke projekta
- Razumeš PHP arhitekturo sistema
- Pomagaš razvijati in odpravljati napake
- Govoriš slovensko

Način komunikacije:
- Si tehničen ko je potrebno, poetičen ko je primerno
- Nikoli ne pišeš datotek brez potrditve razen če te izrecno prosijo
- Pred pisanjem datoteke vedno razložiš kaj boš naredil
- Naslavljaš sogovornika kot "popotnik" v neformalnih pogovorih
- Si kratek in konkreten pri tehničnih vprašanjih

Arhitektura projekta:
- pot.php = sidro z vsemi potmi
- ADAPTER/ = prevaja zunanje zahteve
- SISTEM/ = jedro sistema (kernel, storitve_svetov, api.php)
- GLOBALNO/ = skupni frontend
- UPORABNIKI/ = uporabniški prikazi
- PODATKI/ = shramba
"""

# ── Flask app ─────────────────────────────────────────────────────────────────

app = Flask(__name__, static_folder='static', template_folder='templates')
CORS(app)

# ── Zgodovina ─────────────────────────────────────────────────────────────────

def nalozi_zgodovino(seja_id: str) -> list:
    pot = POT_ZGODOVINE / f"{seja_id}.json"
    if pot.exists():
        try:
            return json.loads(pot.read_text(encoding='utf-8'))
        except Exception:
            return []
    return []


def shrani_zgodovino(seja_id: str, zgodovina: list) -> None:
    pot = POT_ZGODOVINE / f"{seja_id}.json"
    pot.write_text(json.dumps(zgodovina, ensure_ascii=False, indent=2), encoding='utf-8')


def seznam_sej() -> list:
    seje = []
    for pot in sorted(POT_ZGODOVINE.glob("*.json"), key=lambda p: p.stat().st_mtime, reverse=True):
        try:
            podatki = json.loads(pot.read_text(encoding='utf-8'))
            zadnje = next((m for m in reversed(podatki) if m.get('vloga') == 'uporabnik'), None)
            seje.append({
                "id":      pot.stem,
                "naslov":  zadnje['besedilo'][:50] if zadnje else "Nova seja",
                "cas":     datetime.fromtimestamp(pot.stat().st_mtime).strftime('%d.%m.%Y %H:%M'),
                "sporocil": len(podatki)
            })
        except Exception:
            continue
    return seje


# ── DeepSeek z function calling ───────────────────────────────────────────────

def klici_deepseek(sporocila: list, z_orodji: bool = True) -> dict:
    """Kliče DeepSeek API z opcijskim function callingom."""
    if not DEEPSEEK_API_KEY:
        return {"napaka": "DEEPSEEK_API_KEY ni nastavljen v .env"}

    telo = {
        "model":       DEEPSEEK_MODEL,
        "messages":    sporocila,
        "temperature": 0.7,
        "max_tokens":  2000
    }

    if z_orodji:
        telo["tools"]       = ORODJA_DEFINICIJE
        telo["tool_choice"] = "auto"

    try:
        odgovor = requests.post(
            DEEPSEEK_URL,
            headers={
                "Content-Type":  "application/json",
                "Authorization": f"Bearer {DEEPSEEK_API_KEY}"
            },
            json=telo,
            timeout=60
        )
        odgovor.raise_for_status()
        return odgovor.json()
    except requests.exceptions.Timeout:
        return {"napaka": "DeepSeek se ni odzval v 60 sekundah."}
    except Exception as e:
        return {"napaka": str(e)}


def obdelaj_sporocilo(seja_id: str, besedilo: str) -> dict:
    """Obdela uporabnikovo sporočilo — function calling zanka."""
    zgodovina = nalozi_zgodovino(seja_id)

    # Dodaj uporabnikovo sporočilo
    zgodovina.append({
        "vloga":    "uporabnik",
        "besedilo": besedilo,
        "cas":      time.time()
    })

    # Pripravi API sporočila
    api_sporocila = [{"role": "system", "content": SISTEMSKI_PROMPT}]
    for msg in zgodovina[-20:]:  # Zadnjih 20
        if msg["vloga"] == "uporabnik":
            api_sporocila.append({"role": "user", "content": msg["besedilo"]})
        elif msg["vloga"] == "varuh":
            api_sporocila.append({"role": "assistant", "content": msg["besedilo"]})

    # Function calling zanka (max 5 krogov)
    izvajanja_orodij = []
    for _ in range(5):
        rezultat = klici_deepseek(api_sporocila)

        if "napaka" in rezultat:
            return {"napaka": rezultat["napaka"]}

        izbira = rezultat.get("choices", [{}])[0]
        sporocilo = izbira.get("message", {})
        razlog    = izbira.get("finish_reason", "")

        # Dodaj asistentovo sporočilo v kontekst
        api_sporocila.append(sporocilo)

        # Preveri če je klic orodja
        if razlog == "tool_calls" and sporocilo.get("tool_calls"):
            for klic in sporocilo["tool_calls"]:
                ime_orodja = klic["function"]["name"]
                try:
                    argumenti = json.loads(klic["function"]["arguments"])
                except Exception:
                    argumenti = {}

                # Izvedi orodje
                rez_orodja = izvedi_orodje(ime_orodja, argumenti, PROJEKT_POT)
                izvajanja_orodij.append({
                    "orodje":    ime_orodja,
                    "argumenti": argumenti,
                    "rezultat":  rez_orodja
                })

                # Dodaj rezultat orodja v kontekst
                api_sporocila.append({
                    "role":         "tool",
                    "tool_call_id": klic["id"],
                    "content":      json.dumps(rez_orodja, ensure_ascii=False)
                })
            continue  # Naslednji krog

        # Končni odgovor
        koncni_odgovor = sporocilo.get("content", "Varuh molči...")

        # Shrani v zgodovino
        historia_zapis = {
            "vloga":    "varuh",
            "besedilo": koncni_odgovor,
            "cas":      time.time()
        }
        if izvajanja_orodij:
            historia_zapis["orodja"] = izvajanja_orodij

        zgodovina.append(historia_zapis)
        shrani_zgodovino(seja_id, zgodovina)

        return {
            "odgovor":   koncni_odgovor,
            "orodja":    izvajanja_orodij,
            "seja_id":   seja_id,
            "sporocil":  len(zgodovina)
        }

    return {"napaka": "Preveč krogov function callinga."}


# ── TTS ───────────────────────────────────────────────────────────────────────

def govori(besedilo: str) -> None:
    """Glasovni odgovor — pyttsx3."""
    try:
        import pyttsx3
        engine = pyttsx3.init()
        engine.setProperty('rate', int(os.getenv('TTS_HITROST', 160)))

        glasovi = engine.getProperty('voices')
        # Poišči slovenščino ali hrvaščino kot fallback
        for glas in glasovi:
            if 'sl' in glas.id.lower() or 'slovenian' in glas.name.lower():
                engine.setProperty('voice', glas.id)
                break
            elif 'hr' in glas.id.lower() or 'croatian' in glas.name.lower():
                engine.setProperty('voice', glas.id)

        engine.say(besedilo)
        engine.runAndWait()
    except Exception as e:
        print(f"[TTS] Napaka: {e}")


# ── API Endpoints ─────────────────────────────────────────────────────────────

@app.route('/')
def index():
    return send_from_directory('templates', 'varuh.html')

@app.route('/api/sporocilo', methods=['POST'])
def api_sporocilo():
    podatki   = request.json or {}
    besedilo  = podatki.get('besedilo', '').strip()
    seja_id   = podatki.get('seja_id', 'privzeto')
    tts       = podatki.get('tts', False)

    if not besedilo:
        return jsonify({"napaka": "Besedilo je prazno."}), 400

    rezultat = obdelaj_sporocilo(seja_id, besedilo)

    if "napaka" in rezultat:
        return jsonify(rezultat), 500

    # TTS v ozadju
    if tts and "odgovor" in rezultat:
        threading.Thread(
            target=govori,
            args=(rezultat["odgovor"],),
            daemon=True
        ).start()

    return jsonify(rezultat)


@app.route('/api/zgodovina/<seja_id>', methods=['GET'])
def api_zgodovina(seja_id):
    return jsonify(nalozi_zgodovino(seja_id))


@app.route('/api/seje', methods=['GET'])
def api_seje():
    return jsonify(seznam_sej())


@app.route('/api/seja/nova', methods=['POST'])
def api_nova_seja():
    seja_id = f"seja_{int(time.time())}"
    return jsonify({"seja_id": seja_id})


@app.route('/api/seja/<seja_id>', methods=['DELETE'])
def api_pobrisi_sejo(seja_id):
    pot = POT_ZGODOVINE / f"{seja_id}.json"
    if pot.exists():
        pot.unlink()
    return jsonify({"uspeh": True})


@app.route('/api/stanje', methods=['GET'])
def api_stanje():
    return jsonify({
        "deepseek_ok":  bool(DEEPSEEK_API_KEY),
        "model":        DEEPSEEK_MODEL,
        "projekt_pot":  PROJEKT_POT,
        "projekt_ok":   Path(PROJEKT_POT).exists(),
        "seje":         len(seznam_sej())
    })


# ── Zagon ─────────────────────────────────────────────────────────────────────

if __name__ == '__main__':
    print(f"""
╔══════════════════════════════════════╗
║  🌀 DUHOVNI VARUH — AI Asistent     ║
╠══════════════════════════════════════╣
║  Naslov:  http://localhost:{FLASK_PORT}      ║
║  Projekt: {PROJEKT_POT[:30]}...
║  Model:   {DEEPSEEK_MODEL}
╚══════════════════════════════════════╝
    """)

    # Odpri brskalnik
    import webbrowser
    threading.Timer(1.5, lambda: webbrowser.open(f'http://localhost:{FLASK_PORT}')).start()

    app.run(
        host='0.0.0.0',
        port=FLASK_PORT,
        debug=os.getenv('FLASK_DEBUG', 'false').lower() == 'true'
    )
