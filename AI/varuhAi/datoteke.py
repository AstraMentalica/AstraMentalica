"""
orodja/datoteke.py
Orodja za dostop do datotek projekta.
DeepSeek pokliče te funkcije prek function calling.
"""
import os
import json
import glob
import shutil
from pathlib import Path
from datetime import datetime


def _varno_pot(projekt_pot: str, relativna: str) -> Path | None:
    """Preveri da pot ne izstopa iz projekta (path traversal zaščita)."""
    osnova = Path(projekt_pot).resolve()
    cilj   = (osnova / relativna).resolve()
    if not str(cilj).startswith(str(osnova)):
        return None
    return cilj


# ── Branje ───────────────────────────────────────────────────────────────────

def preberi_datoteko(projekt_pot: str, pot: str) -> dict:
    """Preberi vsebino datoteke."""
    cilj = _varno_pot(projekt_pot, pot)
    if not cilj:
        return {"napaka": "Neveljavna pot."}
    if not cilj.exists():
        return {"napaka": f"Datoteka ne obstaja: {pot}"}
    if not cilj.is_file():
        return {"napaka": f"Ni datoteka: {pot}"}

    try:
        vsebina = cilj.read_text(encoding='utf-8')
        return {
            "pot": pot,
            "vsebina": vsebina,
            "velikost": len(vsebina),
            "vrstice": vsebina.count('\n') + 1
        }
    except Exception as e:
        return {"napaka": str(e)}


def seznam_datotek(projekt_pot: str, mapa: str = "", vzorec: str = "*") -> dict:
    """Izpiši datoteke v mapi."""
    cilj = _varno_pot(projekt_pot, mapa) if mapa else Path(projekt_pot).resolve()
    if not cilj:
        return {"napaka": "Neveljavna pot."}
    if not cilj.exists():
        return {"napaka": f"Mapa ne obstaja: {mapa}"}

    rezultati = []
    for pot in sorted(cilj.rglob(vzorec)):
        if pot.is_file() and not any(
            del_ in pot.parts for del_ in ['.git', '__pycache__', 'node_modules', '.env']
        ):
            rel = pot.relative_to(Path(projekt_pot).resolve())
            rezultati.append({
                "pot":      str(rel).replace('\\', '/'),
                "velikost": pot.stat().st_size,
                "spremenjen": datetime.fromtimestamp(pot.stat().st_mtime).strftime('%d.%m.%Y %H:%M')
            })

    return {"mapa": mapa or "/", "datoteke": rezultati, "skupaj": len(rezultati)}


def poisci_v_datotekah(projekt_pot: str, iskanje: str, koncnica: str = ".php") -> dict:
    """Poišči besedilo v datotekah projekta."""
    osnova   = Path(projekt_pot).resolve()
    rezultati = []

    for pot in osnova.rglob(f"*{koncnica}"):
        if any(del_ in pot.parts for del_ in ['.git', '__pycache__', 'node_modules']):
            continue
        try:
            vsebina = pot.read_text(encoding='utf-8', errors='ignore')
            if iskanje.lower() in vsebina.lower():
                vrstice = []
                for i, vrstica in enumerate(vsebina.splitlines(), 1):
                    if iskanje.lower() in vrstica.lower():
                        vrstice.append({"st": i, "vsebina": vrstica.strip()})
                rel = pot.relative_to(osnova)
                rezultati.append({
                    "pot":    str(rel).replace('\\', '/'),
                    "vrstice": vrstice[:5]  # Max 5 vrstic na datoteko
                })
        except Exception:
            continue

    return {"iskanje": iskanje, "najdeno": rezultati, "skupaj": len(rezultati)}


# ── Pisanje ───────────────────────────────────────────────────────────────────

def zapisi_datoteko(projekt_pot: str, pot: str, vsebina: str, varnostna_kopija: bool = True) -> dict:
    """Zapiši vsebino v datoteko."""
    cilj = _varno_pot(projekt_pot, pot)
    if not cilj:
        return {"napaka": "Neveljavna pot."}

    # Ustvari mape če ne obstajajo
    cilj.parent.mkdir(parents=True, exist_ok=True)

    # Varnostna kopija
    if varnostna_kopija and cilj.exists():
        kopija = cilj.with_suffix(cilj.suffix + '.bak')
        shutil.copy2(cilj, kopija)

    try:
        cilj.write_text(vsebina, encoding='utf-8')
        return {
            "uspeh": True,
            "pot": pot,
            "velikost": len(vsebina),
            "varnostna_kopija": varnostna_kopija and cilj.exists()
        }
    except Exception as e:
        return {"napaka": str(e)}


def ustvari_datoteko(projekt_pot: str, pot: str, vsebina: str) -> dict:
    """Ustvari novo datoteko (ne prepiše obstoječe)."""
    cilj = _varno_pot(projekt_pot, pot)
    if not cilj:
        return {"napaka": "Neveljavna pot."}
    if cilj.exists():
        return {"napaka": f"Datoteka že obstaja: {pot}. Uporabi zapisi_datoteko za prepis."}

    return zapisi_datoteko(projekt_pot, pot, vsebina, varnostna_kopija=False)


def pobrisi_datoteko(projekt_pot: str, pot: str) -> dict:
    """Pobriši datoteko (premakne v _kos/)."""
    cilj = _varno_pot(projekt_pot, pot)
    if not cilj:
        return {"napaka": "Neveljavna pot."}
    if not cilj.exists():
        return {"napaka": f"Datoteka ne obstaja: {pot}"}

    # Premakni v _kos namesto brisanja
    kos = Path(projekt_pot) / '_kos' / datetime.now().strftime('%Y%m%d_%H%M%S')
    kos.mkdir(parents=True, exist_ok=True)
    shutil.move(str(cilj), str(kos / cilj.name))

    return {"uspeh": True, "pot": pot, "premaknjeno_v": str(kos / cilj.name)}


def preimenuj_datoteko(projekt_pot: str, stara_pot: str, nova_pot: str) -> dict:
    """Preimenuj ali premakni datoteko."""
    stara = _varno_pot(projekt_pot, stara_pot)
    nova  = _varno_pot(projekt_pot, nova_pot)
    if not stara or not nova:
        return {"napaka": "Neveljavna pot."}
    if not stara.exists():
        return {"napaka": f"Datoteka ne obstaja: {stara_pot}"}
    if nova.exists():
        return {"napaka": f"Ciljna datoteka že obstaja: {nova_pot}"}

    nova.parent.mkdir(parents=True, exist_ok=True)
    shutil.move(str(stara), str(nova))
    return {"uspeh": True, "iz": stara_pot, "v": nova_pot}


# ── Definicije orodij za DeepSeek function calling ───────────────────────────

ORODJA_DEFINICIJE = [
    {
        "type": "function",
        "function": {
            "name": "preberi_datoteko",
            "description": "Preberi vsebino datoteke iz projekta.",
            "parameters": {
                "type": "object",
                "properties": {
                    "pot": {"type": "string", "description": "Relativna pot od korena projekta, npr. SISTEM/api.php"}
                },
                "required": ["pot"]
            }
        }
    },
    {
        "type": "function",
        "function": {
            "name": "seznam_datotek",
            "description": "Izpiši seznam datotek v mapi projekta.",
            "parameters": {
                "type": "object",
                "properties": {
                    "mapa":   {"type": "string", "description": "Relativna pot do mape, prazno = koren"},
                    "vzorec": {"type": "string", "description": "Glob vzorec npr. *.php, privzeto *"}
                },
                "required": []
            }
        }
    },
    {
        "type": "function",
        "function": {
            "name": "poisci_v_datotekah",
            "description": "Poišči besedilo v datotekah projekta.",
            "parameters": {
                "type": "object",
                "properties": {
                    "iskanje":  {"type": "string", "description": "Besedilo za iskanje"},
                    "koncnica": {"type": "string", "description": "Končnica datotek npr. .php, .js, privzeto .php"}
                },
                "required": ["iskanje"]
            }
        }
    },
    {
        "type": "function",
        "function": {
            "name": "zapisi_datoteko",
            "description": "Zapiši ali posodobi vsebino datoteke. Ustvari varnostno kopijo.",
            "parameters": {
                "type": "object",
                "properties": {
                    "pot":     {"type": "string", "description": "Relativna pot datoteke"},
                    "vsebina": {"type": "string", "description": "Celotna vsebina datoteke"}
                },
                "required": ["pot", "vsebina"]
            }
        }
    },
    {
        "type": "function",
        "function": {
            "name": "ustvari_datoteko",
            "description": "Ustvari novo datoteko (ne prepiše obstoječe).",
            "parameters": {
                "type": "object",
                "properties": {
                    "pot":     {"type": "string", "description": "Relativna pot nove datoteke"},
                    "vsebina": {"type": "string", "description": "Vsebina datoteke"}
                },
                "required": ["pot", "vsebina"]
            }
        }
    },
    {
        "type": "function",
        "function": {
            "name": "pobrisi_datoteko",
            "description": "Pobriši datoteko (varno — premakne v _kos/).",
            "parameters": {
                "type": "object",
                "properties": {
                    "pot": {"type": "string", "description": "Relativna pot datoteke za brisanje"}
                },
                "required": ["pot"]
            }
        }
    },
    {
        "type": "function",
        "function": {
            "name": "preimenuj_datoteko",
            "description": "Preimenuj ali premakni datoteko.",
            "parameters": {
                "type": "object",
                "properties": {
                    "stara_pot": {"type": "string", "description": "Trenutna pot datoteke"},
                    "nova_pot":  {"type": "string", "description": "Nova pot datoteke"}
                },
                "required": ["stara_pot", "nova_pot"]
            }
        }
    }
]


def izvedi_orodje(ime: str, argumenti: dict, projekt_pot: str) -> dict:
    """Izvedi orodje glede na ime."""
    orodja = {
        "preberi_datoteko":   lambda a: preberi_datoteko(projekt_pot, a["pot"]),
        "seznam_datotek":     lambda a: seznam_datotek(projekt_pot, a.get("mapa", ""), a.get("vzorec", "*")),
        "poisci_v_datotekah": lambda a: poisci_v_datotekah(projekt_pot, a["iskanje"], a.get("koncnica", ".php")),
        "zapisi_datoteko":    lambda a: zapisi_datoteko(projekt_pot, a["pot"], a["vsebina"]),
        "ustvari_datoteko":   lambda a: ustvari_datoteko(projekt_pot, a["pot"], a["vsebina"]),
        "pobrisi_datoteko":   lambda a: pobrisi_datoteko(projekt_pot, a["pot"]),
        "preimenuj_datoteko": lambda a: preimenuj_datoteko(projekt_pot, a["stara_pot"], a["nova_pot"]),
    }

    fn = orodja.get(ime)
    if not fn:
        return {"napaka": f"Neznano orodje: {ime}"}

    try:
        return fn(argumenti)
    except Exception as e:
        return {"napaka": f"Napaka pri izvajanju {ime}: {str(e)}"}