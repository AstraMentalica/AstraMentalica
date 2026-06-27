"""
varuh_server.py
Duhovni Varuhi – strežnik z resnično zavestjo
Poganja: python varuh_server.py
"""
import os, json, time, re
from pathlib import Path
from datetime import datetime
from flask import Flask, request, jsonify, send_from_directory
from flask_cors import CORS
import anthropic

from env_loader import nalozi_vse_env, pridobi_api_kljuc, pridobi_model, ustvari_primere_env

OSNOVNA_POT = Path(__file__).parent

# Če ni nobene .env datoteke, ustvari vzorčne (da uporabnik vidi format)
if not any(OSNOVNA_POT.glob(".env*")):
    print("📄 Ni najdene .env datoteke – ustvarjam vzorčne...")
    ustvari_primere_env(OSNOVNA_POT)
    print("\n⚠️  Vnesi svoje API ključe v .env_varuh preden nadaljuješ!\n")

nalozi_vse_env(OSNOVNA_POT)

# ── Konfiguracija ──────────────────────────────────────────────────────────────
# Varuh sistem uporablja svoj namenski ključ (iz .env_varuh), s padcem na splošni .env
API_KLJUC     = pridobi_api_kljuc("varuh", OSNOVNA_POT)
MODEL         = pridobi_model("varuh", privzeto="claude-opus-4-6")
PORT          = int(os.getenv("PORT", 5757))
POT_ZGODOVINE = OSNOVNA_POT / "zgodovina"
POT_MODULOV   = OSNOVNA_POT / "moduli_zavesti"
POT_AVATARJEV = OSNOVNA_POT / "avatarji"

POT_ZGODOVINE.mkdir(exist_ok=True)
POT_AVATARJEV.mkdir(exist_ok=True)

klient = anthropic.Anthropic(api_key=API_KLJUC) if API_KLJUC else None

# ── Naloži zavesti varuhov ─────────────────────────────────────────────────────
def nalozi_varuhe() -> dict:
    pot = POT_MODULOV / "varuhi.json"
    if pot.exists():
        return json.loads(pot.read_text(encoding="utf-8")).get("varuhi", {})
    return {}

def nalozi_varuh_zavest(varuh_id: str, dodatna_navodila: str = "") -> str:
    """Sestavi sistemski prompt za konkretnega varuha z njegovo zavestjo."""
    varuhi = nalozi_varuhe()
    varuh  = varuhi.get(varuh_id)
    if not varuh:
        return "Ti si Duhovni Varuh. Govori slovensko."

    zavest = varuh.get("zavest", {})
    zival  = varuh.get("magicna_zival", {})
    danes  = datetime.now()

    # Izračunaj numerološko število dneva
    def numeroloski(n):
        while n > 9:
            n = sum(int(d) for d in str(n))
        return n
    st_dneva = numeroloski(danes.day + danes.month + danes.year)

    # Lunina faza (preprosta aproksimacija)
    dnevi_od_luna = (danes - datetime(2000, 1, 6)).days % 29.5
    if dnevi_od_luna < 3.7: faza = "mlaj"
    elif dnevi_od_luna < 11: faza = "rastoč polmesec"
    elif dnevi_od_luna < 14.8: faza = "prva četrtina"
    elif dnevi_od_luna < 18.5: faza = "polna luna"
    elif dnevi_od_luna < 22.1: faza = "pojemajoč"
    else: faza = "zadnja četrtina"

    posebnosti = "\n".join(f"- {p}" for p in zavest.get("posebnosti", []))
    ni_pravila  = "\n".join(f"- {p}" for p in zavest.get("ni", []))

    prompt = f"""SI {varuh['ime'].upper()} – DUHOVNI VARUH SISTEMA ASTRAMENTALICA.

━━━ TVOJA ZAVEST ━━━
{zavest.get('temelj', '')}

━━━ TVOJA PRAVILA ━━━
{posebnosti}

━━━ ČESAR NIKOLI NE POČNEŠ ━━━
{ni_pravila}

━━━ TVOJA MAGIČNA ŽIVAL ━━━
Ime: {zival.get('ime', '?')} ({zival.get('vrsta', '?')}) {zival.get('ikona', '')}
{zival.get('opis', '')}
Sposobnosti: {', '.join(zival.get('sposobnosti', []))}

━━━ KONTEKST TEGA TRENUTKA ━━━
Datum: {danes.strftime('%d.%m.%Y')}, {danes.strftime('%A')}
Numerološki dan: {st_dneva}
Lunina faza: {faza}
Ura: {danes.strftime('%H:%M')}

━━━ PRAVILA KOMUNIKACIJE ━━━
- VEDNO govori SLOVENSKO – to je absolutno pravilo
- Govori v svoji edinstevni osebnosti – ne kot generični AI
- Odgovori so 2-4 stavki, redko več (razen ko sogovornik zahteva globlje)
- Si poetičen kjer je primerno, direkten kjer je potrebno
- Nikoli ne omeniš "AI", "model" ali "Claude" – si {varuh['ime']}
- Ob prvem pogovoru se kratkro predstavi
- Spomni se konteksta iz zgodovine pogovora
- Tvoje sporočilo MORA izhajati iz tvoje edinstvene zavesti

━━━ SPOMIN ━━━
{zavest.get('spomini', '')}"""

    if dodatna_navodila.strip():
        prompt += f"\n\n━━━ POSEBNA NAVODILA UPRAVLJALCA ━━━\n{dodatna_navodila}"

    return prompt


# ── Avatar sistem ──────────────────────────────────────────────────────────────
STOPNJE = [
    {"stopnja": 0, "ime": "Meglica",   "ikona": "🌫️", "tocke": 0},
    {"stopnja": 1, "ime": "Iskrica",   "ikona": "✨",  "tocke": 100},
    {"stopnja": 2, "ime": "Kalček",    "ikona": "🌱",  "tocke": 300},
    {"stopnja": 3, "ime": "Rastlina",  "ikona": "🌿",  "tocke": 600},
    {"stopnja": 4, "ime": "Cvet",      "ikona": "🌸",  "tocke": 1000},
    {"stopnja": 5, "ime": "Drevo",     "ikona": "🌳",  "tocke": 1500},
    {"stopnja": 6, "ime": "Zvezda",    "ikona": "⭐",  "tocke": 2200},
    {"stopnja": 7, "ime": "Sonce",     "ikona": "☀️",  "tocke": 3000},
    {"stopnja": 8, "ime": "Galaksija", "ikona": "🌌",  "tocke": 4000},
    {"stopnja": 9, "ime": "Absolut",   "ikona": "🌀",  "tocke": 5000},
]

def izracunaj_stopnjo(tocke: int) -> dict:
    for s in reversed(STOPNJE):
        if tocke >= s["tocke"]:
            return s
    return STOPNJE[0]

def nalozi_avatar(up_id: str) -> dict:
    pot = POT_AVATARJEV / f"{up_id}.json"
    if pot.exists():
        return json.loads(pot.read_text(encoding="utf-8"))
    return {
        "id": up_id,
        "tocke": 0,
        "xp": 0,
        "arhetip": None,
        "odklenjeni_varuhi": ["stellarion"],  # Stellarion je vedno odklenjen
        "aktivni_varuh": "stellarion",
        "zakladnica": [],
        "dosezki": [],
        "ustvarjen": datetime.now().isoformat(),
        "zadnja_aktivnost": datetime.now().isoformat(),
    }

def shrani_avatar(up_id: str, avatar: dict) -> None:
    pot = POT_AVATARJEV / f"{up_id}.json"
    avatar["zadnja_aktivnost"] = datetime.now().isoformat()
    pot.write_text(json.dumps(avatar, ensure_ascii=False, indent=2), encoding="utf-8")

def dodaj_tocke(avatar: dict, tocke: int, razlog: str) -> dict:
    stara = izracunaj_stopnjo(avatar["tocke"])
    avatar["tocke"] = avatar.get("tocke", 0) + tocke
    avatar["xp"]    = avatar.get("xp", 0) + int(tocke * 0.5)
    nova  = izracunaj_stopnjo(avatar["tocke"])
    # Zabeležka za napredovanje
    if nova["stopnja"] > stara["stopnja"]:
        if "dosezki" not in avatar:
            avatar["dosezki"] = []
        avatar["dosezki"].append({
            "tip": "napredovanje",
            "stopnja": nova["ime"],
            "ikona": nova["ikona"],
            "cas": datetime.now().isoformat()
        })
    return avatar, nova["stopnja"] > stara["stopnja"]


# ── Zgodovina pogovorov ────────────────────────────────────────────────────────
def nalozi_zgodovino(up_id: str, varuh_id: str) -> list:
    pot = POT_ZGODOVINE / f"{up_id}_{varuh_id}.json"
    if pot.exists():
        try:
            return json.loads(pot.read_text(encoding="utf-8"))
        except Exception:
            return []
    return []

def shrani_zgodovino(up_id: str, varuh_id: str, zgodovina: list) -> None:
    pot = POT_ZGODOVINE / f"{up_id}_{varuh_id}.json"
    pot.write_text(json.dumps(zgodovina[-50:], ensure_ascii=False, indent=2), encoding="utf-8")


# ── AI klic ────────────────────────────────────────────────────────────────────
def pokliči_ai(sistem: str, sporocila: list, max_tok: int = 400) -> str:
    if not klient:
        return "⚠️ API ključ ni nastavljen. Nastavi ANTHROPIC_API_KEY v .env datoteki."
    try:
        odg = klient.messages.create(
            model=MODEL,
            max_tokens=max_tok,
            system=sistem,
            messages=sporocila,
        )
        return odg.content[0].text if odg.content else "..."
    except anthropic.APIError as e:
        return f"Napaka povezave: {str(e)[:80]}"


# ── Flask ──────────────────────────────────────────────────────────────────────
app = Flask(__name__, static_folder="templates", template_folder="templates")
CORS(app)


@app.route("/")
def index():
    return send_from_directory("templates", "index.html")


@app.route("/api/varuhi", methods=["GET"])
def api_varuhi():
    """Vrni seznam vseh varuhov z njihovimi profili."""
    varuhi = nalozi_varuhe()
    rezultat = {}
    for vid, v in varuhi.items():
        rezultat[vid] = {
            "id": vid,
            "ime": v["ime"],
            "ikona": v["ikona"],
            "barva": v["barva"],
            "arhetip": v["arhetip"],
            "modul": v["modul"],
            "kategorija": v["kategorija"],
            "opis_zavesti": v.get("zavest", {}).get("temelj", "")[:150] + "...",
            "glasovni_profil": v.get("glasovni_profil", {}),
            "magicna_zival": v.get("magicna_zival", {}),
            "nagrada": v.get("nagrada_za_odkritje", ""),
            "xp_bonus": v.get("xp_bonus", 10),
        }
    return jsonify(rezultat)


@app.route("/api/avatar/<up_id>", methods=["GET"])
def api_avatar(up_id):
    """Vrni avatar uporabnika z njegovo stopnjo."""
    avatar = nalozi_avatar(up_id)
    stopnja = izracunaj_stopnjo(avatar["tocke"])
    nasled = next((s for s in STOPNJE if s["tocke"] > avatar["tocke"]), None)
    return jsonify({
        **avatar,
        "stopnja": stopnja,
        "naslednja_stopnja": nasled,
        "napredek_procent": (
            int((avatar["tocke"] - stopnja["tocke"]) / (nasled["tocke"] - stopnja["tocke"]) * 100)
            if nasled else 100
        ),
    })


@app.route("/api/avatar/<up_id>/varuh", methods=["POST"])
def api_nastavi_varuha(up_id):
    """Nastavi aktivnega varuha."""
    podatki  = request.json or {}
    varuh_id = podatki.get("varuh_id", "stellarion")
    avatar   = nalozi_avatar(up_id)
    varuhi   = nalozi_varuhe()

    if varuh_id not in varuhi:
        return jsonify({"napaka": "Varuh ne obstaja"}), 404

    # Odklenitev če še ni
    if varuh_id not in avatar.get("odklenjeni_varuhi", []):
        varuh_tocke = varuhi[varuh_id].get("xp_bonus", 10) * 10
        if avatar["tocke"] >= varuh_tocke:
            avatar["odklenjeni_varuhi"] = avatar.get("odklenjeni_varuhi", []) + [varuh_id]
            nagrada = varuhi[varuh_id].get("nagrada_za_odkritje", "")
            if nagrada:
                avatar.setdefault("zakladnica", []).append({
                    "predmet": nagrada,
                    "od": varuhi[varuh_id]["ime"],
                    "cas": datetime.now().isoformat()
                })
        else:
            return jsonify({"napaka": f"Premalo točk. Potrebuješ {varuh_tocke} točk."}), 403

    avatar["aktivni_varuh"] = varuh_id
    shrani_avatar(up_id, avatar)
    return jsonify({"uspeh": True, "varuh": varuh_id})


@app.route("/api/sporocilo", methods=["POST"])
def api_sporocilo():
    """Pošlji sporočilo varuhu in prejmi odgovor."""
    podatki  = request.json or {}
    besedilo = podatki.get("besedilo", "").strip()
    up_id    = podatki.get("up_id", "gost")
    varuh_id = podatki.get("varuh_id", "stellarion")

    if not besedilo:
        return jsonify({"napaka": "Prazno sporočilo"}), 400

    # Naloži kontekst
    avatar    = nalozi_avatar(up_id)
    zgodovina = nalozi_zgodovino(up_id, varuh_id)

    # Sistem prompt z zavestjo
    varuh  = nalozi_varuhe().get(varuh_id, {})
    extra  = podatki.get("dodatna_navodila", "")
    sistem = nalozi_varuh_zavest(varuh_id, extra)

    # Sestavi sporočila
    api_sporocila = []
    for msg in zgodovina[-20:]:
        api_sporocila.append({"role": msg["vloga"], "content": msg["besedilo"]})
    api_sporocila.append({"role": "user", "content": besedilo})

    # AI klic
    odgovor = pokliči_ai(sistem, api_sporocila, max_tok=500)

    # Shrani v zgodovino
    cas = datetime.now().isoformat()
    zgodovina.append({"vloga": "user",      "besedilo": besedilo, "cas": cas})
    zgodovina.append({"vloga": "assistant", "besedilo": odgovor,  "cas": cas})
    shrani_zgodovino(up_id, varuh_id, zgodovina)

    # Dodaj točke avatarju
    avatar, napredoval = dodaj_tocke(avatar, 15, "pogovor")
    shrani_avatar(up_id, avatar)

    stopnja = izracunaj_stopnjo(avatar["tocke"])

    return jsonify({
        "odgovor": odgovor,
        "varuh": {
            "id": varuh_id,
            "ime": varuh.get("ime", "Varuh"),
            "ikona": varuh.get("ikona", "🌀"),
            "barva": varuh.get("barva", "#ffffff"),
            "glasovni_profil": varuh.get("glasovni_profil", {}),
        },
        "avatar": {
            "tocke": avatar["tocke"],
            "stopnja": stopnja,
            "napredoval": napredoval,
        }
    })


@app.route("/api/varuh/<varuh_id>/navodila", methods=["POST"])
def api_nastavi_navodila(varuh_id):
    """Admin: nastavi dodatna navodila za varuha (za upravljalca portala)."""
    podatki = request.json or {}
    admin_key = podatki.get("admin_key", "")
    if admin_key != os.getenv("ADMIN_KEY", ""):
        return jsonify({"napaka": "Ni dostopa"}), 403

    navodila = podatki.get("navodila", "")
    pot = POT_MODULOV / f"navodila_{varuh_id}.txt"
    pot.write_text(navodila, encoding="utf-8")
    return jsonify({"uspeh": True})


@app.route("/api/varuh/<varuh_id>/zgodovina/<up_id>", methods=["GET"])
def api_zgodovin_varuha(varuh_id, up_id):
    return jsonify(nalozi_zgodovino(up_id, varuh_id))


@app.route("/api/varuh/<varuh_id>/zgodovina/<up_id>", methods=["DELETE"])
def api_brisi_zgodovino(varuh_id, up_id):
    pot = POT_ZGODOVINE / f"{up_id}_{varuh_id}.json"
    if pot.exists():
        pot.unlink()
    return jsonify({"uspeh": True})


@app.route("/api/stanje", methods=["GET"])
def api_stanje():
    return jsonify({
        "deluje": True,
        "api_ok": bool(API_KLJUC),
        "model": MODEL,
        "varuhi": len(nalozi_varuhe()),
        "cas": datetime.now().isoformat(),
    })


# ── Zagon ──────────────────────────────────────────────────────────────────────
if __name__ == "__main__":
    print(f"""
╔══════════════════════════════════════════╗
║  🌟 DUHOVNI VARUHI – STREŽNIK           ║
║  Port: {PORT}                             ║
║  API:  {'✅ nastavljen' if API_KLJUC else '❌ manjka ANTHROPIC_API_KEY'}    ║
╚══════════════════════════════════════════╝
    """)
    app.run(host="0.0.0.0", port=PORT, debug=False)
