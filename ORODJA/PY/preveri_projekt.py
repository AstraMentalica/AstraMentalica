#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
==========================================================
AstraMentalica - Preverjanje strukture projekta
==========================================================
Namen: Analiza strukture projekta za Copilot optimizacijo
Avtor: AstraMentalica Mojster
Verzija: 1.0.0
==========================================================
"""

import os
import sys
from pathlib import Path
from typing import Dict, List, Tuple
from datetime import datetime

# Barve za izpis (ANSI)
class BARVE:
    """ANSI barve za terminal."""
    RESET = '\033[0m'
    BOLD = '\033[1m'
    CYAN = '\033[36m'
    GREEN = '\033[32m'
    YELLOW = '\033[33m'
    RED = '\033[31m'
    WHITE = '\033[37m'
    GRAY = '\033[90m'


def print_naslov(besedilo: str) -> None:
    """Izpiše naslov."""
    print(f"\n{BARVE.CYAN}{'=' * 70}{BARVE.RESET}")
    print(f"{BARVE.CYAN}{BARVE.BOLD}{besedilo}{BARVE.RESET}")
    print(f"{BARVE.CYAN}{'=' * 70}{BARVE.RESET}")


def print_podnaslov(besedilo: str) -> None:
    """Izpiše podnaslov."""
    print(f"\n{BARVE.GRAY}{'-' * 50}{BARVE.RESET}")
    print(f"{BARVE.WHITE}{besedilo}{BARVE.RESET}")


def print_sporocilo(besedilo: str, tip: str = "info") -> None:
    """Izpiše sporočilo glede na tip."""
    oznake = {
        "opravilo": f"{BARVE.GREEN}[V]{BARVE.RESET}",
        "opozorilo": f"{BARVE.YELLOW}[!]{BARVE.RESET}",
        "napaka": f"{BARVE.RED}[X]{BARVE.RESET}",
        "info": f"{BARVE.GRAY}[i]{BARVE.RESET}"
    }
    print(f"{oznake.get(tip, oznake['info'])} {besedilo}")


def analiziraj_glavne_mape(pot_projekta: Path) -> Tuple[int, int]:
    """Analizira glavne mape projekta."""
    print_podnaslov("1. Glavne mape projekta")
    
    glavne_mape = [
        "ADAPTER", "AI", "ASTRA", "GLOBALNO", "MODULI",
        "PODATKI", "SISTEM", "UPORABNIKI", "VSEBINA"
    ]
    
    skupaj_datotek = 0
    skupaj_podmap = 0
    
    for mapa in glavne_mape:
        polna_pot = pot_projekta / mapa
        if polna_pot.exists():
            datoteke = list(polna_pot.rglob("*"))
            stevilo_datotek = len([d for d in datoteke if d.is_file()])
            stevilo_podmap = len([d for d in datoteke if d.is_dir()])
            
            print_sporocilo(f"{mapa}: {stevilo_datotek} datotek, {stevilo_podmap} podmap", "opravilo")
            skupaj_datotek += stevilo_datotek
            skupaj_podmap += stevilo_podmap
        else:
            print_sporocilo(f"{mapa}: MANJKA", "opozorilo")
    
    return skupaj_datotek, skupaj_podmap


def analiziraj_kljucne_datoteke(pot_projekta: Path) -> None:
    """Analizira ključne datoteke."""
    print_podnaslov("2. Ključne datoteke")
    
    kljucne_datoteke = [
        ("index.php", "Vstopna točka"),
        ("pot.php", "Absolutno sidro"),
        ("ai_proxy.php", "AI proxy"),
        ("AstraMentalica.md", "Glavni README"),
    ]
    
    for ime, opis in kljucne_datoteke:
        polna_pot = pot_projekta / ime
        if polna_pot.exists():
            velikost = polna_pot.stat().st_size
            print_sporocilo(f"{ime} ({opis}): {velikost / 1024:.2f} KB", "opravilo")
        else:
            print_sporocilo(f"{ime} ({opis}): MANJKA", "opozorilo")


def analiziraj_php_datoteke(pot_projekta: Path) -> Tuple[int, int, int]:
    """Analizira PHP datoteke."""
    print_podnaslov("3. PHP datoteke")
    
    php_datoteke = list(pot_projekta.rglob("*.php"))
    php_datoteke = [d for d in php_datoteke if not any(
        x in str(d) for x in [".venv", "node_modules", "vendor"]
    )]
    
    # Štej po mapah
    po_mapah: Dict[str, int] = {}
    skupaj_vrstic = 0
    skupaj_velikost = 0
    
    for datoteka in php_datoteke:
        mapa = datoteka.parent.name
        po_mapah[mapa] = po_mapah.get(mapa, 0) + 1
        
        try:
            with open(datoteka, 'r', encoding='utf-8', errors='ignore') as f:
                vsebina = f.read()
                skupaj_vrstic += len(vsebina.splitlines())
            skupaj_velikost += datoteka.stat().st_size
        except Exception:
            pass
    
    # Izpiši top 10
    for mapa, stevilo in sorted(po_mapah.items(), key=lambda x: x[1], reverse=True)[:10]:
        print(f"  {mapa}: {stevilo} PHP datotek")
    
    return len(php_datoteke), skupaj_vrstic, skupaj_velikost


def analiziraj_json_datoteke(pot_projekta: Path) -> int:
    """Analizira JSON datoteke."""
    print_podnaslov("4. JSON konfiguracijske datoteke")
    
    json_datoteke = list(pot_projekta.rglob("*.json"))
    json_datoteke = [d for d in json_datoteke if "node_modules" not in str(d)]
    
    for json_file in json_datoteke[:20]:
        relativna_pot = json_file.relative_to(pot_projekta)
        print_sporocilo(str(relativna_pot), "opravilo")
    
    if len(json_datoteke) > 20:
        print(f"  ... in {len(json_datoteke) - 20} več")
    
    return len(json_datoteke)


def analiziraj_module(pot_projekta: Path) -> None:
    """Analizira module."""
    print_podnaslov("5. Seznam modulov")
    
    pot_modulov = pot_projekta / "MODULI" / "Univerzalno"
    if pot_modulov.exists():
        moduli = [d for d in pot_modulov.iterdir() if d.is_dir()]
        for modul in sorted(moduli):
            php_datoteke = list(modul.glob("*.php"))
            print(f"  {modul.name}: {len(php_datoteke)} datotek")
    else:
        print_sporocilo("Mapa MODULI/Univerzalno ne obstaja", "opozorilo")


def analiziraj_varnost(pot_projekta: Path) -> None:
    """Analizira varnost."""
    print_podnaslov("6. Varnostni pregled")
    
    # .gitignore
    if (pot_projekta / ".gitignore").exists():
        print_sporocilo(".gitignore: obstaja", "opravilo")
    else:
        print_sporocilo(".gitignore: MANJKA", "opozorilo")
    
    # Varnostne datoteke
    varnostne = list(pot_projekta.rglob("varnost*.php"))
    print_sporocilo(f"Varnostne datoteke: {len(varnostne)}", "info")


def glavna(pot_projekta: str = None) -> None:
    """Glavna funkcija."""
    if pot_projekta is None:
        pot_projekta = Path(__file__).parent.parent
    
    pot_projekta = Path(pot_projekta)
    
    print_naslov("ASTRA MENTALICA - PREVERJANJE PROJEKTA")
    print(f"Pot projekta: {pot_projekta}")
    print(f"Čas: {datetime.now().strftime('%d.%m.%Y %H:%M:%S')}")
    
    if not pot_projekta.exists():
        print_sporocilo(f"Pot ne obstaja: {pot_projekta}", "napaka")
        sys.exit(1)
    
    # Analize
    skupaj_datotek, skupaj_podmap = analiziraj_glavne_mape(pot_projekta)
    analiziraj_kljucne_datoteke(pot_projekta)
    php_stevilo, php_vrstice, php_velikost = analiziraj_php_datoteke(pot_projekta)
    json_stevilo = analiziraj_json_datoteke(pot_projekta)
    analiziraj_module(pot_projekta)
    analiziraj_varnost(pot_projekta)
    
    # Povzetek
    print_naslov("POVZETEK")
    print(f"Skupaj datotek: {skupaj_datotek}")
    print(f"Skupaj podmap: {skupaj_podmap}")
    print(f"PHP datotek: {php_stevilo}")
    print(f"JSON datotek: {json_stevilo}")
    print(f"Skupaj vrstic kode: {php_vrstice}")
    print(f"Skupaj velikost kode: {php_velikost / 1024 / 1024:.2f} MB")
    
    print("\nPreverjanje končano!")


if __name__ == "__main__":
    pot = sys.argv[1] if len(sys.argv) > 1 else None
    glavna(pot)
