#!/usr/bin/env python3
"""
ASTRA/pregled_knjig.py
======================
Pregleda VSEBINA/gradiva/Knjige/ in izpiše vsako mapo kot knjigo ali zbirko.

Logika:
  - Vsaka mapa = knjiga
  - Če mapa vsebuje podmape = zbirka knjig
  - Datoteke v korenu mape so "prosti dokumenti" (ne zbirka)

Poganjaj iz root mape projekta:
  python3 ASTRA/pregled_knjig.py [pot_do_mape]
"""

import os
import sys
import json

# ── BARVE ──────────────────────────────────────────────────────────────────
RESET  = "\033[0m"
BOLD   = "\033[1m"
DIM    = "\033[2m"
CYAN   = "\033[96m"
YELLOW = "\033[93m"
GREEN  = "\033[92m"
BLUE   = "\033[94m"
MAGENTA= "\033[95m"
RED    = "\033[91m"
GRAY   = "\033[90m"

# ── PRIPOMOČKI ─────────────────────────────────────────────────────────────
PRIPONKE_VSEBINA = {'.md', '.txt', '.html', '.htm', '.pdf', '.odt', '.doc', '.docx', '.json'}
PRIPONKE_SLIKE   = {'.png', '.jpg', '.jpeg', '.gif', '.webp', '.svg'}
PRIPONKE_AVDIO   = {'.mp3', '.ogg', '.wav', '.m4a'}

def tip_datoteke(ime):
    p = os.path.splitext(ime)[1].lower()
    if p in PRIPONKE_VSEBINA: return 'vsebina'
    if p in PRIPONKE_SLIKE:   return 'slika'
    if p in PRIPONKE_AVDIO:   return 'avdio'
    return 'drugo'

def stej_vsebino(pot):
    """Preštej datoteke po tipu v mapi (rekurzivno)."""
    stevci = {'vsebina': 0, 'slika': 0, 'avdio': 0, 'drugo': 0}
    for root, dirs, files in os.walk(pot):
        # preskoči skrite mape
        dirs[:] = [d for d in dirs if not d.startswith('.')]
        for f in files:
            stevci[tip_datoteke(f)] += 1
    return stevci

def preberi_naslov_iz_json(pot):
    """Poskusi prebrati naslov iz knjiga.json ali kazalo.json."""
    for ime in ('knjiga.json', 'kazalo.json', 'manifest.json'):
        j = os.path.join(pot, ime)
        if os.path.isfile(j):
            try:
                with open(j, encoding='utf-8') as f:
                    data = json.load(f)
                # knjiga.json standard
                if 'identifikacija' in data:
                    n = data['identifikacija'].get('naslov', '')
                    if n: return n, ime
                # kakšen drug format
                for kljuc in ('naslov', 'name', 'title'):
                    n = data.get(kljuc, '')
                    if n: return n, ime
            except Exception:
                pass
    return None, None

def ima_podmape(pot):
    try:
        return any(
            os.path.isdir(os.path.join(pot, e))
            for e in os.listdir(pot)
            if not e.startswith('.')
        )
    except PermissionError:
        return False

def liste_mape(pot):
    try:
        return sorted([
            e for e in os.listdir(pot)
            if os.path.isdir(os.path.join(pot, e)) and not e.startswith('.')
        ])
    except PermissionError:
        return []

def liste_datoteke(pot):
    try:
        return sorted([
            e for e in os.listdir(pot)
            if os.path.isfile(os.path.join(pot, e)) and not e.startswith('.')
        ])
    except PermissionError:
        return []

# ── IZPIS ──────────────────────────────────────────────────────────────────
def izpisi_datoteke(datoteke, zamik='  '):
    """Izpiše datoteke grupirane po tipu."""
    po_tipu = {}
    for f in datoteke:
        t = tip_datoteke(f)
        po_tipu.setdefault(t, []).append(f)

    for tip, seznam in po_tipu.items():
        ikona = {'vsebina': '📄', 'slika': '🖼️ ', 'avdio': '🔊', 'drugo': '📎'}.get(tip, '•')
        print(f"{zamik}{GRAY}{ikona} {', '.join(seznam)}{RESET}")

def analiziraj_knjigo(pot, ime, nivo=0, je_podknjiga=False):
    """Analizira eno mapo kot knjigo ali zbirko."""
    zamik = '  ' * nivo
    podmape = liste_mape(pot)
    datoteke = liste_datoteke(pot)
    je_zbirka = bool(podmape)

    # naslov iz json ali ime mape
    naslov_json, vir_json = preberi_naslov_iz_json(pot)
    naslov = naslov_json or ime

    stevci = stej_vsebino(pot)
    vsebina_st = stevci['vsebina']
    slike_st   = stevci['slika']

    # ── GLAVA ──
    if je_zbirka:
        ikona = '📚'
        tip_label = f"{YELLOW}ZBIRKA{RESET}"
    else:
        ikona = '📖'
        tip_label = f"{GREEN}KNJIGA{RESET}"

    if je_podknjiga:
        print(f"{zamik}{GRAY}├─{RESET} {ikona} {BOLD}{naslov}{RESET}  {DIM}[{tip_label}]{RESET}")
    else:
        print(f"\n{zamik}{CYAN}{'─'*60}{RESET}")
        print(f"{zamik}{ikona}  {BOLD}{CYAN}{naslov}{RESET}  {DIM}[{tip_label}]{RESET}")
        if ime != naslov:
            print(f"{zamik}   {GRAY}📁 {ime}{RESET}")

    # ── METAPODATKI ──
    if naslov_json:
        print(f"{zamik}   {GRAY}📋 manifest: {vir_json}{RESET}")

    # ── VSEBINA STATISTIKA ──
    stati = []
    if vsebina_st: stati.append(f"{vsebina_st} dok.")
    if slike_st:   stati.append(f"{slike_st} slik")
    if stevci['avdio']: stati.append(f"{stevci['avdio']} avdio")
    if stati:
        print(f"{zamik}   {GRAY}📊 {', '.join(stati)}{RESET}")

    # ── DATOTEKE V KORENU (samo pri knjigi) ──
    if not je_zbirka and datoteke:
        izpisi_datoteke(datoteke, zamik + '   ')

    # ── PODMAPE ──
    if je_zbirka:
        for podmap in podmape:
            analiziraj_knjigo(
                os.path.join(pot, podmap),
                podmap,
                nivo=nivo+1,
                je_podknjiga=True
            )

        # datoteke v korenu zbirke (prosti dokumenti)
        datoteke_zbirke = [f for f in datoteke if not f.startswith('.')]
        if datoteke_zbirke:
            print(f"{zamik}   {GRAY}📎 prosti dokumenti v korenu:{RESET}")
            izpisi_datoteke(datoteke_zbirke, zamik + '      ')

# ── GLAVNA FUNKCIJA ────────────────────────────────────────────────────────
def main():
    # Pot: argument ali privzeta
    if len(sys.argv) > 1:
        koren = sys.argv[1]
    else:
        # Poišči relativno od lokacije skripte
        script_dir = os.path.dirname(os.path.abspath(__file__))
        projekt = os.path.dirname(script_dir)  # root projekta
        koren = os.path.join(projekt, 'VSEBINA', 'gradiva', 'Knjige')

    if not os.path.isdir(koren):
        print(f"{RED}Mapa ne obstaja: {koren}{RESET}")
        sys.exit(1)

    print(f"\n{BOLD}{MAGENTA}═══════════════════════════════════════════════════════════════{RESET}")
    print(f"{BOLD}{MAGENTA}  📚  PREGLED KNJIG — AstraMentalica{RESET}")
    print(f"{MAGENTA}  📁  {koren}{RESET}")
    print(f"{BOLD}{MAGENTA}═══════════════════════════════════════════════════════════════{RESET}")

    # ── DATOTEKE V KORENU (ne v mapi) ──
    koren_datoteke = liste_datoteke(koren)
    if koren_datoteke:
        print(f"\n{YELLOW}📎  PROSTI DOKUMENTI (v korenu, ne v mapi):{RESET}")
        izpisi_datoteke(koren_datoteke, '  ')

    # ── MAPE = KNJIGE / ZBIRKE ──
    mape = liste_mape(koren)
    if not mape:
        print(f"\n{RED}Ni map v: {koren}{RESET}")
        sys.exit(0)

    knjige = 0
    zbirke = 0
    for ime in mape:
        pot = os.path.join(koren, ime)
        if ima_podmape(pot):
            zbirke += 1
        else:
            knjige += 1
        analiziraj_knjigo(pot, ime, nivo=0)

    # ── POVZETEK ──
    skupaj_podmape = sum(
        len(liste_mape(os.path.join(koren, m)))
        for m in mape
        if ima_podmape(os.path.join(koren, m))
    )
    print(f"\n{CYAN}{'─'*60}{RESET}")
    print(f"{BOLD}POVZETEK:{RESET}")
    print(f"  {GREEN}📖 Knjig:{RESET}  {knjige}")
    print(f"  {YELLOW}📚 Zbirk:{RESET}  {zbirke}  {GRAY}(skupaj {skupaj_podmape} podknjig){RESET}")
    print(f"  {CYAN}skupaj map:{RESET} {len(mape)}")
    print()

if __name__ == '__main__':
    main()
