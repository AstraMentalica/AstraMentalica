#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
==========================================================
AstraMentalica - Preverjanje podvojenih datotek
==========================================================
Namen: Iskanje podvojenih datotek za refactoring
Avtor: AstraMentalica Mojster
Verzija: 1.0.0
==========================================================
"""

import hashlib
import sys
from pathlib import Path
from typing import Dict, List, Set, Tuple
from collections import defaultdict
from datetime import datetime


class Barve:
    CYAN = '\033[36m' if sys.stdout.isatty() else ''
    GREEN = '\033[32m' if sys.stdout.isatty() else ''
    YELLOW = '\033[33m' if sys.stdout.isatty() else ''
    RED = '\033[31m' if sys.stdout.isatty() else ''
    WHITE = '\033[37m' if sys.stdout.isatty() else ''
    GRAY = '\033[90m' if sys.stdout.isatty() else ''
    RESET = '\033[0m' if sys.stdout.isatty() else ''
    BOLD = '\033[1m' if sys.stdout.isatty() else ''


class DuplicateFinder:
    """Iskalec podvojenih datotek."""
    
    def __init__(self):
        self.po_imenu: Dict[str, List[Path]] = defaultdict(list)
        self.po_vsebini: Dict[str, List[Path]] = defaultdict(list)
        self.podvojitve_2: List[Tuple[Path, Path]] = []
        self.skupaj_datotek = 0
    
    def dodaj_datoteko(self, pot: Path) -> None:
        """Doda datoteko za preverjanje."""
        self.skupaj_datotek += 1
        
        # Po imenu
        self.po_imenu[pot.name].append(pot)
        
        # Po vsebini (hash)
        try:
            hash_md5 = self.izracunaj_md5(pot)
            if hash_md5:
                self.po_vsebini[hash_md5].append(pot)
        except Exception:
            pass
    
    def izracunaj_md5(self, pot: Path) -> str:
        """Izračuna MD5 hash datoteke."""
        hash_md5 = hashlib.md5()
        with open(pot, 'rb') as f:
            for chunk in iter(lambda: f.read(4096), b""):
                hash_md5.update(chunk)
        return hash_md5.hexdigest()
    
    def poisci_podvojitve_2(self) -> List[Tuple[Path, Path]]:
        """Poišče datoteke z '(2)' v imenu."""
        import re
        
        podvojitve = []
        for pot in self.po_imenu:
            if re.search(r'\s*\(2\)\.[^.]+$', pot):
                original = re.sub(r'\s*\(2\)', '', pot)
                originalna_pot = self.po_imenu[pot][0].parent / original
                
                for podvojena in self.po_imenu[pot]:
                    if originalna_pot.exists():
                        podvojitve.append((podvojena, originalna_pot))
        
        return podvojitve
    
    @property
    def podvojene_po_imenu(self) -> List[Tuple[str, List[Path]]]:
        """Vrne podvojene datoteke po imenu."""
        return [(ime, poti) for ime, poti in self.po_imenu.items() if len(poti) > 1]
    
    @property
    def podvojene_po_vsebini(self) -> List[Tuple[str, List[Path]]]:
        """Vrne podvojene datoteke po vsebini."""
        return [(hash, poti) for hash, poti in self.po_vsebini.items() if len(poti) > 1]


def najdi_vse_datoteke(pot_projekta: Path, min_velikost_kb: int = 1) -> List[Path]:
    """Najde vse datoteke v projektu."""
    izkljucene_mape = {".git", ".venv", "vendor", "node_modules", ".vscode", "__pycache__"}
    
    datoteke = []
    for pot in pot_projekta.rglob("*"):
        if pot.is_file():
            # Preveri ali je v izključeni mapi
            if not any(izklucena in pot.parts for izklucena in izkljucene_mape):
                # Preveri velikost
                if pot.stat().st_size >= min_velikost_kb * 1024:
                    datoteke.append(pot)
    
    return datoteke


def izpisi_naslov(besedilo: str) -> None:
    print(f"\n{Barve.CYAN}{Barve.BOLD}{'=' * 70}{Barve.RESET}")
    print(f"{Barve.CYAN}{Barve.BOLD}{besedilo}{Barve.RESET}")
    print(f"{Barve.CYAN}{Barve.BOLD}{'=' * 70}{Barve.RESET}")


def izpisi_podnaslov(besedilo: str) -> None:
    print(f"\n{Barve.GRAY}{'-' * 50}{Barve.RESET}")
    print(f"{Barve.WHITE}{besedilo}{Barve.RESET}")


def izpisi_vrstico(tip: str, besedilo: str, dodatno: str = "") -> None:
    oznake = {
        "uspeh": f"{Barve.GREEN}[✓]{Barve.RESET}",
        "napaka": f"{Barve.RED}[✗]{Barve.RESET}",
        "opozorilo": f"{Barve.YELLOW}[⚠]{Barve.RESET}",
        "info": f"{Barve.GRAY}[i]{Barve.RESET}"
    }
    
    print(f"{oznake.get(tip, oznake['info'])} {besedilo}")
    if dodatno:
        print(f"{Barve.GRAY}    → {dodatno}{Barve.RESET}")


def poisci_specivicne_podvojitve(pot_projekta: Path) -> Dict[str, List[Path]]:
    """Poišče specifične podvojitve v projektu."""
    rezultati = {
        "deepseek": [],
        "modul_bridge": [],
        "(2)": []
    }
    
    # DeepSeek podvojitve
    deepseek_pot = pot_projekta / "AI" / "zasebniAi" / "arhitekturniAi"
    if deepseek_pot.exists():
        deepseek_datoteke = list(deepseek_pot.glob("*.php"))
        
        po_imenu = defaultdict(list)
        for d in deepseek_datoteke:
            po_imenu[d.stem].append(d)
        
        for ime, poti in po_imenu.items():
            if len(poti) > 1:
                rezultati["deepseek"].append((ime, poti))
    
    # (2) podvojitve
    for pot in pot_projekta.rglob("*"):
        if pot.is_file() and " (2)." in pot.name:
            original = Path(str(pot).replace(" (2)", ""))
            rezultati["(2)"].append((pot, original if original.exists() else None))
    
    return rezultati


def glavna(pot_projekta: str = None) -> None:
    """Glavna funkcija."""
    if pot_projekta is None:
        pot_projekta = Path(__file__).parent.parent
    else:
        pot_projekta = Path(pot_projekta)
    
    finder = DuplicateFinder()
    
    izpisi_naslov("ASTRA MENTALICA - PREVERJANJE PODVOJENIH DATOTEK")
    print(f"Pot projekta: {pot_projekta}")
    print(f"Čas: {datetime.now().strftime('%d.%m.%Y %H:%M:%S')}")
    
    if not pot_projekta.exists():
        print(f"{Barve.RED}[✗] Pot ne obstaja: {pot_projekta}{Barve.RESET}")
        return
    
    # Najdi datoteke
    print("\nIskanje datotek...")
    datoteke = najdi_vse_datoteke(pot_projekta, min_velikost_kb=1)
    print(f"Najdenih {len(datoteke)} datotek")
    
    # Dodaj v finder
    print("\nAnaliziranje...")
    for i, pot in enumerate(datoteke, 1):
        finder.dodaj_datoteko(pot)
        if i % 100 == 0:
            print(f"  [{i}/{len(datoteke)}] obdelanih...")
    
    # Podvojitve po imenu
    izpisi_podnaslov("Podvojitve po imenu")
    podvojene_po_imenu = finder.podvojene_po_imenu
    
    if podvojene_po_imenu:
        for ime, poti in podvojene_po_imenu[:10]:
            izpisi_vrstico("opozorilo", ime, f"{len(poti)} primerkov")
            for p in poti:
                print(f"{Barve.GRAY}    → {p.relative_to(pot_projekta)}{Barve.RESET}")
    else:
        print(f"{Barve.GREEN}Ni podvojenih datotek po imenu{Barve.RESET}")
    
    # (2) podvojitve
    izpisi_podnaslov("(2) podvojitve")
    podvojitve_2 = finder.poisci_podvojitve_2()
    
    if podvojitve_2:
        for originalna, podvojena in podvojitve_2:
            izpisi_vrstico("opozorilo", podvojena.name)
            print(f"{Barve.GRAY}    → {podvojena.relative_to(pot_projekta)}{Barve.RESET}")
            if originalna.exists():
                print(f"{Barve.GRAY}    = {originalna.relative_to(pot_projekta)}{Barve.RESET}")
            else:
                print(f"{Barve.RED}    ? ORIGINAL NE obstaja!{Barve.RESET}")
    else:
        print(f"{Barve.GREEN}Ni (2) podvojitev{Barve.RESET}")
    
    # Specifične podvojitve
    izpisi_podnaslov("Specifične podvojitve projekta")
    
    spec_podvojitve = poisci_specivicne_podvojitve(pot_projekta)
    
    # DeepSeek
    if spec_podvojitve["deepseek"]:
        print("\nDeepSeek arhitekt podvojitve:")
        for ime, poti in spec_podvojitve["deepseek"]:
            print(f"  {Barve.YELLOW}{ime}: {len(poti)} verzij{Barve.RESET}")
            for p in poti:
                print(f"{Barve.GRAY}    → {p.name}{Barve.RESET}")
    
    # Povzetek
    print(f"\n{Barve.CYAN}{'=' * 70}{Barve.RESET}")
    print(f"{Barve.CYAN}{Barve.BOLD}POVZETEK{BARVE.RESET}")
    print(f"{Barve.CYAN}{'=' * 70}{Barve.RESET}")
    print()
    print(f"Skupaj analiziranih: {finder.skupaj_datotek}")
    print(f"Podvojenih po imenu: {len(podvojene_po_imenu)}")
    print(f"(2) podvojitev: {len(podvojitve_2)}")
    
    print()
    if len(podvojene_po_imenu) == 0 and len(podvojitve_2) == 0:
        print(f"{Barve.GREEN}✓ Ni podvojenih datotek!{Barve.RESET}")
    else:
        print(f"{Barve.YELLOW}⚠ Najdene podvojitve - glej zgoraj{Barve.RESET}")
        print("\nPriporočila za refactoring:")
        print(f"  {Barve.GRAY}1. Analiziraj (2) datoteke - izbriši ali združi{Barve.RESET}")
        print(f"  {Barve.GRAY}2. DeepSeek nadzorniki - izberi najboljšo verzijo{Barve.RESET}")
        print(f"  {Barve.GRAY}3. Modul boilerplate - premakni v skupno datoteko{Barve.RESET}")


if __name__ == "__main__":
    import argparse
    
    parser = argparse.ArgumentParser(description="Preverjanje podvojenih datotek")
    parser.add_argument("pot", nargs="?", default=None, help="Pot do projekta")
    
    args = parser.parse_args()
    glavna(args.pot)
