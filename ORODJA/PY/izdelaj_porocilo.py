#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
==========================================================
AstraMentalica - Izdelava celotnega poročila
==========================================================
Namen: Generiranje celotnega poročila o projektu
Avtor: AstraMentalica Mojster
Verzija: 1.0.0
==========================================================
"""

import json
import sys
from pathlib import Path
from typing import Dict, List, Any
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


class PorociloGenerator:
    """Generator poročil."""
    
    def __init__(self, pot_projekta: Path):
        self.pot_projekta = pot_projekta
        self.podatki: Dict[str, Any] = {}
    
    def analiziraj(self) -> Dict[str, Any]:
        """Izvede celotno analizo projekta."""
        self.podatki = {
            "datum": datetime.now().isoformat(),
            "pot": str(self.pot_projekta),
            "struktura": self._analiziraj_strukturo(),
            "moduli": self._analiziraj_module(),
            "knjiznice": self._analiziraj_knjiznice(),
            "json_baze": self._analiziraj_json(),
            "php_napake": self._analiziraj_php(),
            "varnost": self._analiziraj_varnost(),
            "dokumentacija": self._analiziraj_dokumentacijo(),
        }
        return self.podatki
    
    def _analiziraj_strukturo(self) -> Dict[str, Any]:
        """Analizira strukturo projekta."""
        glavne_mape = ["ADAPTER", "AI", "ASTRA", "GLOBALNO", "MODULI", "PODATKI", "SISTEM", "UPORABNIKI", "VSEBINA"]
        
        rezultat = {"mape": {}, "skupaj": {"datotek": 0, "podmap": 0}}
        
        for mapa in glavne_mape:
            pot = self.pot_projekta / mapa
            if pot.exists():
                datoteke = list(pot.rglob("*"))
                stevilo_datotek = len([d for d in datoteke if d.is_file()])
                stevilo_podmap = len([d for d in datoteke if d.is_dir()])
                
                rezultat["mape"][mapa] = {
                    "datotek": stevilo_datotek,
                    "podmap": stevilo_podmap
                }
                rezultat["skupaj"]["datotek"] += stevilo_datotek
                rezultat["skupaj"]["podmap"] += stevilo_podmap
        
        # PHP datoteke
        php_datoteke = list(self.pot_projekta.rglob("*.php"))
        php_datoteke = [d for d in php_datoteke if not any(x in str(d) for x in [".venv", "node_modules", "vendor"])]
        rezultat["skupaj"]["php_datotek"] = len(php_datoteke)
        
        # JSON datoteke
        json_datoteke = list(self.pot_projekta.rglob("*.json"))
        json_datoteke = [d for d in json_datoteke if "node_modules" not in str(d)]
        rezultat["skupaj"]["json_datotek"] = len(json_datoteke)
        
        return rezultat
    
    def _analiziraj_module(self) -> Dict[str, Any]:
        """Analizira module."""
        moduli_pot = self.pot_projekta / "MODULI" / "Univerzalno"
        
        if not moduli_pot.exists():
            return {"skupaj": 0, "moduli": []}
        
        moduli = []
        for modul in moduli_pot.iterdir():
            if modul.is_dir():
                php_datoteke = list(modul.glob("*.php"))
                moduli.append({
                    "ime": modul.name,
                    "datotek": len(php_datoteke)
                })
        
        return {"skupaj": len(moduli), "moduli": moduli}
    
    def _analiziraj_knjiznice(self) -> Dict[str, Any]:
        """Analizira knjižnice."""
        knjiznice_pot = self.pot_projekta / "MODULI" / "Knjiznice"
        
        if not knjiznice_pot.exists():
            return {"skupaj": 0, "knjiznice": []}
        
        knjiznice = []
        for knjiznica in knjiznice_pot.iterdir():
            if knjiznica.is_dir():
                php_datoteke = list(knjiznica.glob("*.php"))
                ima_podatke = (knjiznica / "podatki").exists()
                ima_readme = (knjiznica / "README.md").exists()
                
                knjiznice.append({
                    "ime": knjiznica.name,
                    "datotek": len(php_datoteke),
                    "ima_podatke": ima_podatke,
                    "ima_readme": ima_readme
                })
        
        return {"skupaj": len(knjiznice), "knjiznice": knjiznice}
    
    def _analiziraj_json(self) -> Dict[str, Any]:
        """Analizira JSON baze."""
        baze = {
            "moduli_register": "PODATKI/registri/moduli_register.json",
            "data_bus": "PODATKI/data_bus.json",
            "frekvence": "PODATKI/frekvence.json",
            "kanonicni_varuhi": "PODATKI/kanonični_varuhi.json"
        }
        
        rezultat = {}
        for ime, pot in baze.items():
            polna_pot = self.pot_projekta / pot
            rezultat[ime] = {
                "obstaja": polna_pot.exists(),
                "pot": pot,
                "velikost": polna_pot.stat().st_size if polna_pot.exists() else 0
            }
            
            # Preveri veljavnost JSON
            if polna_pot.exists():
                try:
                    with open(polna_pot, 'r', encoding='utf-8') as f:
                        json.load(f)
                    rezultat[ime]["veljaven"] = True
                except Exception:
                    rezultat[ime]["veljaven"] = False
            else:
                rezultat[ime]["veljaven"] = False
        
        return rezultat
    
    def _analiziraj_php(self) -> Dict[str, Any]:
        """Analizira PHP datoteke."""
        php_datoteke = list(self.pot_projekta.rglob("*.php"))
        php_datoteke = [d for d in php_datoteke if not any(x in str(d) for x in [".venv", "node_modules", "vendor"])]
        
        # Štej napake (preprosto preverjanje)
        napake = 0
        for php in php_datoteke:
            try:
                with open(php, 'r', encoding='utf-8', errors='ignore') as f:
                    vsebina = f.read()
                
                # Preveri osnovne napake
                if vsebina.count('{') != vsebina.count('}'):
                    napake += 1
                elif vsebina.count('(') != vsebina.count(')'):
                    napake += 1
            except Exception:
                pass
        
        return {
            "skupaj": len(php_datoteke),
            "ocenjene_napake": napake
        }
    
    def _analiziraj_varnost(self) -> Dict[str, Any]:
        """Analizira varnost."""
        return {
            "gitignore": (self.pot_projekta / ".gitignore").exists(),
            "htaccess": (self.pot_projekta / ".htaccess").exists(),
            "sistem_varnost": "SISTEM_VARNOST" in (self.pot_projekta / "pot.php").read_text() if (self.pot_projekta / "pot.php").exists() else False
        }
    
    def _analiziraj_dokumentacijo(self) -> Dict[str, bool]:
        """Analizira dokumentacijo."""
        doc_datoteke = [
            "README.md",
            "AstraMentalica.md",
            "PROJEKT_PREGLED.md",
            "MODULI.md",
            "AGENTI.md",
            "KNJIGE.md",
            "ARHITEKTURA.md",
            "TODO_REFAKTOR.md"
        ]
        
        return {ime: (self.pot_projekta / ime).exists() for ime in doc_datoteke}
    
    def izdelaj_markdown_porocilo(self) -> str:
        """Izdela poročilo v Markdown formatu."""
        d = self.podatki
        
        md = f"""# ASTRA MENTALICA - Celotno poročilo projekta

## 1. Osnovni podatki

- **Datum**: {datetime.now().strftime('%d.%m.%Y %H:%M:%S')}
- **Pot**: `{d['pot']}`

## 2. Struktura projekta

### Datoteke

| Tip | Število |
|-----|---------|
| PHP datotek | {d['struktura']['skupaj']['php_datotek']} |
| JSON datotek | {d['struktura']['skupaj']['json_datotek']} |
| Skupaj datotek | {d['struktura']['skupaj']['datotek']} |

### Glavne mape

| Mapa | Datotek | Podmap |
|------|---------|--------|
"""
        
        for mapa, podatki in d['struktura']['mape'].items():
            md += f"| {mapa} | {podatki['datotek']} | {podatki['podmap']} |\n"
        
        md += f"""
## 3. moduli

**Skupaj modulov**: {d['moduli']['skupaj']}

| Modul | Datotek |
|-------|---------|
"""
        
        for modul in d['moduli']['moduli']:
            md += f"| {modul['ime']} | {modul['datotek']} |\n"
        
        md += f"""
## 4. Knjižnice

**Skupaj knjižnic**: {d['knjiznice']['skupaj']}

| Knjižnica | Datotek | Podatki | README |
|-----------|---------|---------|--------|
"""
        
        for knj in d['knjiznice']['knjiznice']:
            podatki = "✓" if knj['ima_podatke'] else "✗"
            readme = "✓" if knj['ima_readme'] else "✗"
            md += f"| {knj['ime']} | {knj['datotek']} | {podatki} | {readme} |\n"
        
        md += """
## 5. JSON baze

| Baza | Obstaja | Veljavna | Velikost |
|------|---------|----------|----------|
"""
        
        for ime, podatki in d['json_baze'].items():
            obstaja = "✓" if podatki['obstaja'] else "✗"
            veljaven = "✓" if podatki.get('veljaven', False) else "✗"
            velikost = f"{podatki['velikost'] / 1024:.1f} KB" if podatki['velikost'] > 0 else "-"
            md += f"| {ime} | {obstaja} | {veljaven} | {velikost} |\n"
        
        md += f"""
## 6. PHP

| Metrika | Vrednost |
|---------|----------|
| Skupaj PHP datotek | {d['php']['skupaj']} |
| Ocenjene napake | {d['php']['ocenjene_napake']} |

## 7. Varnost

| Preverjanje | Status |
|-------------|--------|
| .gitignore | {'✓' if d['varnost']['gitignore'] else '✗'} |
| .htaccess | {'✓' if d['varnost']['htaccess'] else '✗'} |
| SISTEM_VARNOST | {'✓' if d['varnost']['sistem_varnost'] else '✗'} |

## 8. Dokumentacija

| Datoteka | Obstaja |
|----------|---------|
"""
        
        for ime, obstaja in d['dokumentacija'].items():
            md += f"| {ime} | {'✓' if obstaja else '✗'} |\n"
        
        md += f"""
## 9. Orodja

| Tip | Število |
|-----|---------|
| PowerShell skripte | {len(list((self.pot_projekta / 'ORODJA' / 'PS').glob('*.ps1')))} |
| Python skripte | {len(list((self.pot_projekta / 'ORODJA' / 'PY').glob('*.py')))} |

---

*Poročilo generirano: {datetime.now().strftime('%d.%m.%Y %H:%M:%S')}*
*AstraMentalica - Copilot optimizacijska orodja*
"""
        
        return md
    
    def shrani_porocilo(self, izhodna_pot: Path = None) -> Path:
        """Shrani poročilo."""
        if izhodna_pot is None:
            izhodna_pot = Path(__file__).parent.parent / "POROCILA"
        
        izhodna_pot.mkdir(parents=True, exist_ok=True)
        
        ime_datoteke = f"AstraMentalica_porocilo_{datetime.now().strftime('%Y%m%d_%H%M%S')}.md"
        pot_datoteke = izhodna_pot / ime_datoteke
        
        # Shrani Markdown
        with open(pot_datoteke, 'w', encoding='utf-8') as f:
            f.write(self.izdelaj_markdown_porocilo())
        
        # Shrani JSON
        json_pot = izhodna_pot / ime_datoteke.replace(".md", ".json")
        with open(json_pot, 'w', encoding='utf-8') as f:
            json.dump(self.podatki, f, indent=2, ensure_ascii=False)
        
        return pot_datoteke


def izpisi_naslov(besedilo: str) -> None:
    print(f"\n{Barve.CYAN}{Barve.BOLD}{'=' * 70}{Barve.RESET}")
    print(f"{Barve.CYAN}{Barve.BOLD}{besedilo}{Barve.RESET}")
    print(f"{Barve.CYAN}{Barve.BOLD}{'=' * 70}{Barve.RESET}")


def glavna(pot_projekta: str = None, izhodna_pot: str = None) -> None:
    """Glavna funkcija."""
    if pot_projekta is None:
        pot_projekta = Path(__file__).parent.parent
    else:
        pot_projekta = Path(pot_projekta)
    
    if izhodna_pot:
        izhodna_pot = Path(izhodna_pot)
    else:
        izhodna_pot = pot_projekta / "POROCILA"
    
    izpisi_naslov("ASTRA MENTALICA - IZDELAVA POROCILA")
    print(f"Pot projekta: {pot_projekta}")
    print(f"Izhodna pot: {izhodna_pot}")
    
    generator = PorociloGenerator(pot_projekta)
    
    print("\nAnaliziranje projekta...")
    generator.analiziraj()
    
    print("\nShranjevanje poročila...")
    pot_porocila = generator.shrani_porocilo(izhodna_pot)
    
    print(f"\n{Barve.GREEN}✓ POROCILO IZDELANO!{Barve.RESET}")
    print(f"\nPoročilo shranjeno: {pot_porocila}")
    
    # Odpri poročilo (samo na Windows)
    try:
        import os
        if sys.platform == 'win32':
            os.startfile(pot_porocila)
    except Exception:
        pass


if __name__ == "__main__":
    import argparse
    
    parser = argparse.ArgumentParser(description="Izdelava celotnega poročila projekta")
    parser.add_argument("pot", nargs="?", default=None, help="Pot do projekta")
    parser.add_argument("-o", "--izhod", default=None, help="Izhodna pot za poročilo")
    
    args = parser.parse_args()
    glavna(args.pot, args.izhod)
