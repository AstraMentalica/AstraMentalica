#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
==========================================================
AstraMentalica - Preverjanje poti v PHP kodi
==========================================================
Namen: Validacija poti za Copilot optimizacijo
Avtor: AstraMentalica Mojster
Verzija: 1.0.0
==========================================================
"""

import re
import sys
from pathlib import Path
from typing import List, Dict, Tuple, Set
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


class PotValidator:
    """Validator poti v PHP kodi."""
    
    def __init__(self, pot_projekta: Path):
        self.pot_projekta = pot_projekta
        self.najdene_poti: List[Dict] = []
        self.neveljavne: List[Dict] = []
        self.veljavne: List[Dict] = []
        self.opozorila: List[Dict] = []
    
    def poisci_poti_v_datoteki(self, datoteka: Path) -> List[str]:
        """Poišče poti v PHP datoteki."""
        poti = []
        
        try:
            with open(datoteka, 'r', encoding='utf-8', errors='ignore') as f:
                vsebina = f.read()
            
            # require/include
            vzorec_require = r'(?:require|include|require_once|include_once)\s*\(\s*[\'"]([^\'"]+)[\'"]'
            for match in re.finditer(vzorec_require, vsebina):
                poti.append(match.group(1))
            
            # file_exists
            vzorec_file_exists = r'file_exists\s*\(\s*[\'"]([^\'"]+)[\'"]'
            for match in re.finditer(vzorec_file_exists, vsebina):
                poti.append(match.group(1))
            
            # dirname(__DIR__
            vzorec_dir = r'dirname\s*\(\s*__DIR__\s*\)\s*\.\s*[\'"]([^\'"]+)[\'"]'
            for match in re.finditer(vzorec_dir, vsebina):
                poti.append(match.group(1))
            
        except Exception:
            pass
        
        return poti
    
    def poisci_pot_konstante(self, datoteka: Path) -> Dict[str, str]:
        """Poišče POT_ konstante v datoteki."""
        konstante = {}
        
        try:
            with open(datoteka, 'r', encoding='utf-8', errors='ignore') as f:
                vsebina = f.read()
            
            # Poišči define()
            vzorec_define = r"define\s*\(\s*['\"](POT_[A-Z_]+)['\"]\s*,\s*['\"]([^'\"]+)['\"]"
            for match in re.finditer(vzorec_define, vsebina):
                konstante[match.group(1)] = match.group(2)
            
        except Exception:
            pass
        
        return konstante
    
    def analiziraj(self) -> None:
        """Analizira vse poti v projektu."""
        php_datoteke = list(self.pot_projekta.rglob("*.php"))
        php_datoteke = [d for d in php_datoteke if not any(x in str(d) for x in [".venv", "node_modules", "vendor"])]
        
        for php_file in php_datoteke:
            poti = self.poisci_poti_v_datoteki(php_file)
            
            for pot in poti:
                relativna = php_file.relative_to(self.pot_projekta)
                
                # Preveri ali je relativna pot
                je_relativna = pot.startswith('.') or not pot.startswith('/')
                
                # Preveri ali pot obstaja
                if je_relativna:
                    polna_pot = php_file.parent / pot
                    obstaja = polna_pot.exists()
                else:
                    polna_pot = Path(pot)
                    obstaja = polna_pot.exists()
                
                podatek = {
                    "datoteka": str(relativna),
                    "pot": pot,
                    "obstaja": obstaja
                }
                
                self.najdene_poti.append(podatek)
                
                if obstaja:
                    self.veljavne.append(podatek)
                else:
                    self.neveljavne.append(podatek)
    
    def analiziraj_pot_php(self) -> Dict[str, bool]:
        """Analizira pot.php datoteko."""
        pot_php = self.pot_projekta / "pot.php"
        
        if not pot_php.exists():
            return {"obstaja": False}
        
        konstante = self.poisci_pot_konstante(pot_php)
        
        rezultat = {
            "obstaja": True,
            "stevilo_konstant": len(konstante),
            "konstante": []
        }
        
        for ime, vrednost in konstante.items():
            if vrednost.startswith("POT_"):
                pot = self.pot_projekta / vrednost.replace("POT_", "").lower().replace("_", "/")
                obstaja = pot.exists()
            else:
                pot = self.pot_projekta / vrednost
                obstaja = pot.exists()
            
            rezultat["konstante"].append({
                "ime": ime,
                "vrednost": vrednost,
                "pot": str(pot.relative_to(self.pot_projekta)),
                "obstaja": obstaja
            })
        
        return rezultat
    
    def analiziraj_modul_bridge(self) -> Dict[str, bool]:
        """Analizira Modul_Bridge."""
        bridge_pot = self.pot_projekta / "MODULI" / "Modul_Bridge" / "modul_bridge.php"
        
        if not bridge_pot.exists():
            return {"obstaja": False}
        
        rezultat = {
            "obstaja": True,
            "iskane_poti": []
        }
        
        try:
            with open(bridge_pot, 'r', encoding='utf-8', errors='ignore') as f:
                vsebina = f.read()
            
            # Poišči iskane poti
            vzorec = r'__DIR__\s*\.\s*[\'"]([^\'"]+)[\'"]'
            for match in re.finditer(vzorec, vsebina):
                pot = bridge_pot.parent / match.group(1).lstrip("/")
                rezultat["iskane_poti"].append({
                    "pot": str(match.group(1)),
                    "obstaja": pot.exists()
                })
        
        except Exception:
            pass
        
        return rezultat
    
    @property
    def stevilo_nepravilnih(self) -> int:
        return len(self.neveljavne)
    
    @property
    def stevilo_opozoril(self) -> int:
        return len(self.opozorila)


def izpisi_naslov(besedilo: str) -> None:
    print(f"\n{Barve.CYAN}{Barve.BOLD}{'=' * 70}{Barve.RESET}")
    print(f"{Barve.CYAN}{Barve.BOLD}{besedilo}{Barve.RESET}")
    print(f"{Barve.CYAN}{Barve.BOLD}{'=' * 70}{Barve.RESET}")


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


def glavna(pot_projekta: str = None) -> None:
    """Glavna funkcija."""
    if pot_projekta is None:
        pot_projekta = Path(__file__).parent.parent
    else:
        pot_projekta = Path(pot_projekta)
    
    validator = PotValidator(pot_projekta)
    
    izpisi_naslov("ASTRA MENTALICA - PREVERJANJE POTI V KODI")
    print(f"Pot projekta: {pot_projekta}")
    print(f"Čas: {datetime.now().strftime('%d.%m.%Y %H:%M:%S')}")
    
    if not pot_projekta.exists():
        print(f"{Barve.RED}[✗] Pot ne obstaja: {pot_projekta}{Barve.RESET}")
        return
    
    # Analiziraj poti
    print("\nAnaliziranje poti v PHP datotekah...")
    validator.analiziraj()
    
    # Rezultati
    print("\nNajdene poti:")
    print("-" * 50)
    
    if validator.neveljavne:
        print(f"\n{Barve.RED}NEVELJAVNE POTI:{Barve.RESET}")
        for podatek in validator.neveljavne[:20]:
            izpisi_vrstico("napaka", podatek["datoteka"], podatek["pot"])
    else:
        print(f"\n{Barve.GREEN}Ni neveljavnih poti!{Barve.RESET}")
    
    # Analiziraj pot.php
    print("\nAnaliza pot.php:")
    print("-" * 50)
    pot_php_rezultat = validator.analiziraj_pot_php()
    
    if pot_php_rezultat.get("obstaja"):
        print(f"{Barve.GREEN}[✓] pot.php{Barve.RESET}")
        print(f"   {pot_php_rezultat['stevilo_konstant']} konstant")
    else:
        print(f"{Barve.RED}[✗] pot.php ne obstaja{Barve.RESET}")
    
    # Analiziraj Modul_Bridge
    print("\nAnaliza Modul_Bridge:")
    print("-" * 50)
    bridge_rezultat = validator.analiziraj_modul_bridge()
    
    if bridge_rezultat.get("obstaja"):
        print(f"{Barve.GREEN}[✓] modul_bridge.php{Barve.RESET}")
        for iskana in bridge_rezultat.get("iskane_poti", []):
            status = "uspeh" if iskana["obstaja"] else "napaka"
            izpisi_vrstico(status, iskana["pot"])
    else:
        print(f"{Barve.RED}[✗] modul_bridge.php ne obstaja{Barve.RESET}")
    
    # Povzetek
    print(f"\n{Barve.CYAN}{'=' * 70}{Barve.RESET}")
    print(f"{Barve.CYAN}{Barve.BOLD}POVZETEK{BARVE.RESET}")
    print(f"{Barve.CYAN}{'=' * 70}{Barve.RESET}")
    print(f"\nSkupaj najdenih poti: {len(validator.najdene_poti)}")
    print(f"Veljavnih: {Barve.GREEN}{len(validator.veljavne)}{Barve.RESET}")
    print(f"Neveljavnih: {Barve.RED}{len(validator.neveljavne)}{Barve.RESET}")
    
    print()
    if validator.stevilo_nepravilnih == 0:
        print(f"{Barve.GREEN}✓ Vse poti so veljavne!{Barve.RESET}")
    else:
        print(f"{Barve.RED}✗ Najdenih {validator.stevilo_nepravilnih} neveljavnih poti!{Barve.RESET}")


if __name__ == "__main__":
    import argparse
    
    parser = argparse.ArgumentParser(description="Preverjanje poti v PHP kodi")
    parser.add_argument("pot", nargs="?", default=None, help="Pot do projekta")
    
    args = parser.parse_args()
    glavna(args.pot)
