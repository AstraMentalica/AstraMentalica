#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
==========================================================
AstraMentalica - Preverjanje PHP sintakse
==========================================================
Namen: Validacija PHP datotek za Copilot optimizacijo
Avtor: AstraMentalica Mojster
Verzija: 1.0.0
==========================================================
"""

import os
import sys
import subprocess
import re
from pathlib import Path
from typing import List, Tuple, Optional
from datetime import datetime

try:
    from colorama import init, Fore, Style
    init(autoreset=True)
    BARVE = type('obj', (object,), {
        'CYAN': Fore.CYAN, 'GREEN': Fore.GREEN, 'YELLOW': Fore.YELLOW,
        'RED': Fore.RED, 'WHITE': Fore.WHITE, 'GRAY': Fore.LIGHTBLACK_EX,
        'RESET': Style.RESET_ALL, 'BOLD': Style.BRIGHT
    })()
except ImportError:
    BARVE = type('obj', (object,), {
        'CYAN': '', 'GREEN': '', 'YELLOW': '',
        'RED': '', 'WHITE': '', 'GRAY': '',
        'RESET': '', 'BOLD': ''
    })()


class PorociloPHP:
    """Poročilo PHP validacije."""
    
    def __init__(self):
        self.skupaj_preverjenih: int = 0
        self.skupaj_napak: int = 0
        self.sporocila: List[Tuple[str, str]] = []
        self.napake: List[dict] = []
    
    def dodaj_uspeh(self, datoteka: str, pot: str) -> None:
        self.skupaj_preverjenih += 1
        self.sporocila.append((datoteka, pot, "uspeh"))
    
    def dodaj_napako(self, datoteka: str, pot: str, sporocilo: str) -> None:
        self.skupaj_preverjenih += 1
        self.skupaj_napak += 1
        self.sporocila.append((datoteka, pot, "napaka", sporocilo))
        self.napake.append({"datoteka": pot, "napaka": sporocilo})
    
    def dodaj_opozorilo(self, datoteka: str, pot: str, sporocilo: str) -> None:
        self.sporocila.append((datoteka, pot, "opozorilo", sporocilo))


def je_php_name(name: str) -> bool:
    """Preveri ali je datoteka PHP."""
    return name.lower().endswith(('.php', '.phtml'))


def preveri_php_sintakso(datoteka: Path, php_pot: Optional[str] = None) -> Tuple[bool, str]:
    """
    Preveri PHP sintakso datoteke.
    
    Returns:
        Tuple[bool, str]: (je_veljavna, sporocilo_napake)
    """
    if php_pot is None:
        php_pot = "php"
    
    try:
        result = subprocess.run(
            [php_pot, "-l", str(datoteka)],
            capture_output=True,
            text=True,
            timeout=30
        )
        
        if result.returncode == 0:
            return True, ""
        else:
            # Odstrani PHP prefix iz sporocila
            sporocilo = result.stdout + result.stderr
            sporocilo = re.sub(r'PHP (Notice|Warning|Error):?\s*', '', sporocilo)
            return False, sporocilo.strip()
    
    except subprocess.TimeoutExpired:
        return False, "Časovna omejitev"
    except FileNotFoundError:
        return False, "PHP interpreter ni najden"
    except Exception as e:
        return False, str(e)


def najdi_php_datoteke(pot_projekta: Path) -> List[Path]:
    """Najde vse PHP datoteke v projektu."""
    izkljucene_mape = {".venv", "node_modules", ".git", "vendor", "__pycache__"}
    
    php_datoteke = []
    for php_file in pot_projekta.rglob("*.php"):
        # Preveri ali je v izključeni mapi
        if any(izklucena in php_file.parts for izklucena in izkljucene_mape):
            continue
        php_datoteke.append(php_file)
    
    return sorted(php_datoteke)


def izpisi_naslov(besedilo: str) -> None:
    """Izpiše naslov."""
    print(f"\n{BARVE.CYAN}{BARVE.BOLD}{'=' * 70}{BARVE.RESET}")
    print(f"{BARVE.CYAN}{BARVE.BOLD}{besedilo}{BARVE.RESET}")
    print(f"{BARVE.CYAN}{BARVE.BOLD}{'=' * 70}{BARVE.RESET}")


def izpisi_podnaslov(besedilo: str) -> None:
    """Izpiše podnaslov."""
    print(f"\n{BARVE.GRAY}{'-' * 50}{BARVE.RESET}")
    print(f"{BARVE.WHITE}{besedilo}{BARVE.RESET}")


def izpisi_sporocilo(tip: str, besedilo: str, pot: str = "", dodatno: str = "") -> None:
    """Izpiše sporočilo glede na tip."""
    oznake = {
        "uspeh": f"{BARVE.GREEN}[✓]{BARVE.RESET}",
        "napaka": f"{BARVE.RED}[✗]{BARVE.RESET}",
        "opozorilo": f"{BARVE.YELLOW}[⚠]{BARVE.RESET}",
        "info": f"{BARVE.GRAY}[i]{BARVE.RESET}"
    }
    
    print(f"{oznake.get(tip, oznake['info'])} {besedilo}")
    if pot:
        print(f"{BARVE.GRAY}         {pot}{BARVE.RESET}")
    if dodatno:
        print(f"{BARVE.GRAY}    → {dodatno}{BARVE.RESET}")


def preveri_php_v_projektu(pot_projekta: str = None, strict: bool = False) -> PorociloPHP:
    """
    Preveri PHP sintakso v celotnem projektu.
    
    Args:
        pot_projekta: Pot do projekta
        strict: Ali naj se prikažejo opozorila
    
    Returns:
        PorociloPHP: Poročilo rezultatov
    """
    if pot_projekta is None:
        pot_projekta = Path(__file__).parent.parent
    else:
        pot_projekta = Path(pot_projekta)
    
    porocilo = PorociloPHP()
    
    # Preveri ali PHP obstaja
    php_najden = False
    php_poti = ["php", "php.exe", "C:\\xampp\\php\\php.exe", "C:\\wamp\\bin\\php\\php*\\php.exe"]
    
    for php_test in ["php", "php.exe"]:
        try:
            subprocess.run([php_test, "-v"], capture_output=True, timeout=5, check=True)
            php_najden = True
            php_pot = php_test
            break
        except (subprocess.CalledProcessError, FileNotFoundError, subprocess.TimeoutExpired):
            continue
    
    izpisi_naslov("ASTRA MENTALICA - PREVERJANJE PHP SINTAKSE")
    print(f"Pot projekta: {pot_projekta}")
    print(f"Čas: {datetime.now().strftime('%d.%m.%Y %H:%M:%S')}")
    
    if not php_najden:
        print(f"\n{BARVE.RED}⚠ PHP interpreter ni najden v PATH!{BARVE.RESET}")
        print(f"{BARVE.YELLOW}Namestite PHP in dodajte pot v sistemsko spremenljivko PATH.{BARVE.RESET}")
        print(f"\nRočno preverjanje: php -l datoteka.php")
        return porocilo
    
    # Pridobi verzijo PHP
    try:
        verzija = subprocess.run(["php", "-v"], capture_output=True, text=True, timeout=5)
        print(f"PHP verzija: {verzija.stdout.splitlines()[0]}")
    except Exception:
        pass
    
    if not pot_projekta.exists():
        print(f"{BARVE.RED}[✗] Pot ne obstaja: {pot_projekta}{BARVE.RESET}")
        return porocilo
    
    # Najdi PHP datoteke
    print("\nIskanje PHP datotek...")
    php_datoteke = najdi_php_datoteke(pot_projekta)
    print(f"Najdenih {len(php_datoteke)} PHP datotek")
    
    # Preveri vsako datoteko
    print("\nPreverjanje sintakse...")
    print("-" * 70)
    
    for i, datoteka in enumerate(php_datoteke, 1):
        relativna_pot = datoteka.relative_to(pot_projekta)
        je_veljavna, napaka = preveri_php_sintakso(datoteka)
        
        if je_veljavna:
            porocilo.dodaj_uspeh(datoteka.name, str(relativna_pot))
            izpisi_sporocilo("uspeh", "OK", str(relativna_pot))
        else:
            porocilo.dodaj_napako(datoteka.name, str(relativna_pot), napaka)
            izpisi_sporocilo("napaka", f"NAPAKA: {napaka[:80]}", str(relativna_pot))
        
        # Progress indicator
        if i % 50 == 0:
            print(f"  [{i}/{len(php_datoteke)}] preverjenih...")
    
    # Posebna preverjanja
    if strict:
        print("\nPreverjanje dobrih praks...")
        
        for datoteka in php_datoteke:
            try:
                with open(datoteka, 'r', encoding='utf-8', errors='ignore') as f:
                    vsebina = f.read()
                
                relativna_pot = datoteka.relative_to(pot_projekta)
                
                # Preveri declare(strict_types=1)
                if "declare(strict_types=1)" not in vsebina:
                    porocilo.dodaj_opozorilo(
                        datoteka.name,
                        str(relativna_pot),
                        "Brez declare(strict_types=1)"
                    )
                    izpisi_sporocilo("opozorilo", "Brez strict types", str(relativna_pot))
                
            except Exception:
                pass
    
    return porocilo


def izpisi_povzetek(porocilo: PorociloPHP) -> None:
    """Izpiše povzetek rezultatov."""
    print(f"\n{BARVE.CYAN}{'=' * 70}{BARVE.RESET}")
    print(f"{BARVE.CYAN}{BARVE.BOLD}POVZETEK PREVERJANJA{BARVE.RESET}")
    print(f"{BARVE.CYAN}{'=' * 70}{BARVE.RESET}")
    print()
    print(f"Skupaj preverjenih:  {porocilo.skupaj_preverjenih}")
    print(f"Brez napak:         {porocilo.skupaj_preverjenih - porocilo.skupaj_napak}")
    print(f"S napakami:         {porocilo.skupaj_napak}")
    
    if porocilo.napake:
        print()
        print(f"{BARVE.RED}DATOTEKE Z NAPAKAMI:{BARVE.RESET}")
        print("-" * 50)
        for napaka in porocilo.napake[:20]:
            print(f"  {BARVE.RED}{napaka['datoteka']}{BARVE.RESET}")
            print(f"    {BARVE.GRAY}{napaka['napaka'][:100]}{BARVE.RESET}")
    
    print()
    if porocilo.skupaj_napak == 0:
        print(f"{BARVE.GREEN}✓ Vse PHP datoteke so sintaktično pravilne!{BARVE.RESET}")
    else:
        print(f"{BARVE.RED}✗ Najdenih {porocilo.skupaj_napak} datotek z napakami!{BARVE.RESET}")


def glavna() -> None:
    """Glavna funkcija."""
    import argparse
    
    parser = argparse.ArgumentParser(description="Preverjanje PHP sintakse v AstraMentalica projektu")
    parser.add_argument("pot", nargs="?", default=None, help="Pot do projekta")
    parser.add_argument("--strict", action="store_true", help="Striktno preverjanje")
    
    args = parser.parse_args()
    
    pot_projekta = args.pot if args.pot else str(Path(__file__).parent.parent)
    porocilo = preveri_php_v_projektu(pot_projekta, args.strict)
    izpisi_povzetek(porocilo)


if __name__ == "__main__":
    glavna()
