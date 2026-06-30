#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
==========================================================
AstraMentalica - Preverjanje JSON datotek
==========================================================
Namen: Validacija JSON konfiguracij
Avtor: AstraMentalica Mojster
Verzija: 1.0.0
==========================================================
"""

import json
import sys
from pathlib import Path
from typing import List, Dict, Any, Tuple
from datetime import datetime


# Barve (enostavna implementacija brez colorama)
class Barve:
    CYAN = '\033[36m' if hasattr(sys.stdout, 'isatty') and sys.stdout.isatty() else ''
    GREEN = '\033[32m' if hasattr(sys.stdout, 'isatty') and sys.stdout.isatty() else ''
    YELLOW = '\033[33m' if hasattr(sys.stdout, 'isatty') and sys.stdout.isatty() else ''
    RED = '\033[31m' if hasattr(sys.stdout, 'isatty') and sys.stdout.isatty() else ''
    WHITE = '\033[37m' if hasattr(sys.stdout, 'isatty' and sys.stdout.isatty() else ''
    GRAY = '\033[90m' if hasattr(sys.stdout, 'isatty') and sys.stdout.isatty() else ''
    RESET = '\033[0m' if hasattr(sys.stdout, 'isatty') and sys.stdout.isatty() else ''
    BOLD = '\033[1m' if hasattr(sys.stdout, 'isatty') and sys.stdout.isatty() else ''


class JsonValidator:
    """Validator za JSON datoteke."""
    
    def __init__(self):
        self.rezultati: List[Dict[str, Any]] = []
    
    def dodaj_uspeh(self, pot: str, sporocilo: str = "") -> None:
        self.rezultati.append({
            "pot": pot,
            "status": "uspeh",
            "sporocilo": sporocilo
        })
    
    def dodaj_napako(self, pot: str, sporocilo: str) -> None:
        self.rezultati.append({
            "pot": pot,
            "status": "napaka",
            "sporocilo": sporocilo
        })
    
    def dodaj_opozorilo(self, pot: str, sporocilo: str) -> None:
        self.rezultati.append({
            "pot": pot,
            "status": "opozorilo",
            "sporocilo": sporocilo
        })
    
    @property
    def stevilo_napak(self) -> int:
        return sum(1 for r in self.rezultati if r["status"] == "napaka")
    
    @property
    def stevilo_opozoril(self) -> int:
        return sum(1 for r in self.rezultati if r["status"] == "opozorilo")
    
    @property
    def stevilo_uspehov(self) -> int:
        return sum(1 for r in self.rezultati if r["status"] == "uspeh")


def je_json_veljaven(pot: Path) -> Tuple[bool, str]:
    """
    Preveri ali je JSON datoteka veljavna.
    
    Returns:
        Tuple[bool, str]: (je_veljavna, sporocilo_napake)
    """
    try:
        with open(pot, 'r', encoding='utf-8') as f:
            json.load(f)
        return True, ""
    except json.JSONDecodeError as e:
        return False, f"Napaka JSON: {e.msg} (vrstica {e.lineno}, stolpec {e.colno})"
    except Exception as e:
        return False, str(e)


def najdi_json_datoteke(pot_projekta: Path) -> List[Path]:
    """Najde vse JSON datoteke v projektu."""
    izkljucene_mape = {"node_modules", ".venv", "vendor"}
    
    json_datoteke = []
    for json_file in pot_projekta.rglob("*.json"):
        if not any(izklucena in json_file.parts for izklucena in izkljucene_mape):
            json_datoteke.append(json_file)
    
    return sorted(json_datoteke)


def preveri_json_v_datoteki(pot: Path) -> Dict[str, Any]:
    """Preveri posamezno JSON datoteko."""
    rezultat = {
        "pot": pot,
        "veljavnost": True,
        "velikost": 0,
        "tip": "objekt",
        "stevilo_kljucev": 0,
        "opozorila": []
    }
    
    try:
        # Preveri velikost
        rezultat["velikost"] = pot.stat().st_size
        
        # Beri vsebino
        with open(pot, 'r', encoding='utf-8') as f:
            vsebina = f.read()
        
        # Preveri prazno datoteko
        if not vsebina.strip():
            rezultat["opozorila"].append("Datoteka je prazna")
            return rezultat
        
        # Preveri zadnjo vejico
        import re
        if re.search(r',(\s*[\]\}])', vsebina):
            rezultat["opozorila"].append("Zadnja vejica pred zaklepajem")
        
        # Parsiraj JSON
        data = json.loads(vsebina)
        
        # Določi tip
        if isinstance(data, list):
            rezultat["tip"] = "polje"
            rezultat["stevilo_kljucev"] = len(data)
        elif isinstance(data, dict):
            rezultat["tip"] = "objekt"
            rezultat["stevilo_kljucev"] = len(data.keys())
        
        # Dodatna preverjanja glede na ime datoteke
        if "modul" in pot.name.lower():
            if isinstance(data, dict):
                obvezna_polja = ["aktiviran", "nivo", "tip"]
                manjkajocha = [p for p in obvezna_polja if p not in data]
                if manjkajocha:
                    rezultat["opozorila"].append(f"Manjkajoča polja: {', '.join(manjkajocha)}")
        
    except json.JSONDecodeError as e:
        rezultat["veljavnost"] = False
        rezultat["opozorila"].append(f"Napaka: {e.msg}")
    except Exception as e:
        rezultat["veljavnost"] = False
        rezultat["opozorila"].append(f"Napaka: {str(e)}")
    
    return rezultat


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


def preveri_sistemske_json(pot_projekta: Path, validator: JsonValidator) -> None:
    """Preveri specifične sistemske JSON datoteke."""
    izpisi_podnaslov("Specifične sistemske JSON datoteke")
    
    sistemske_datoteke = [
        ("PODATKI/registri/moduli_register.json", "Register modulov"),
        ("PODATKI/data_bus.json", "Data bus"),
        ("PODATKI/frekvence.json", "Frekvence"),
        ("PODATKI/kanonični_varuhi.json", "Kanonični varuhi"),
    ]
    
    for pot_str, opis in sistemske_datoteke:
        polna_pot = pot_projekta / pot_str
        
        if polna_pot.exists():
            je_veljavna, sporocilo = je_json_veljaven(polna_pot)
            
            if je_veljavna:
                try:
                    with open(polna_pot, 'r', encoding='utf-8') as f:
                        data = json.load(f)
                    
                    if isinstance(data, dict):
                        stevilo_kljucev = len(data.keys())
                        izpisi_vrstico("uspeh", f"{opis}", f"{stevilo_kljucev} vnosov")
                    elif isinstance(data, list):
                        izpisi_vrstico("uspeh", f"{opis}", f"{len(data)} elementov")
                    else:
                        izpisi_vrstico("uspeh", f"{opis}")
                    
                    validator.dodaj_uspeh(pot_str, "")
                
                except Exception as e:
                    izpisi_vrstico("napaka", f"{opis}", str(e))
                    validator.dodaj_napako(pot_str, str(e))
            else:
                izpisi_vrstico("napaka", f"{opis}", sporocilo)
                validator.dodaj_napako(pot_str, sporocilo)
        else:
            izpisi_vrstico("opozorilo", f"{opis}", "NE NAJDEN")
            validator.dodaj_opozorilo(pot_str, "Ne obstaja")


def preveri_modul_json(pot_projekta: Path, validator: JsonValidator) -> None:
    """Preveri modul.json datoteke."""
    izpisi_podnaslov("modul.json datoteke v modulih")
    
    modul_json_datoteke = list((pot_projekta / "MODULI").rglob("modul.json"))
    
    for modul_json in modul_json_datoteke:
        ime_modula = modul_json.parent.name
        je_veljavna, sporocilo = je_json_veljaven(modul_json)
        
        if je_veljavna:
            try:
                with open(modul_json, 'r', encoding='utf-8') as f:
                    data = json.load(f)
                
                obvezna_polja = ["aktiviran", "nivo", "tip"]
                manjkajocha = [p for p in obvezna_polja if p not in data]
                
                if not manjkajocha:
                    izpisi_vrstico("uspeh", ime_modula)
                else:
                    izpisi_vrstico("opozorilo", ime_modula, f"manjkajoča: {', '.join(manjkajocha)}")
                    validator.dodaj_opozorilo(str(modul_json.relative_to(pot_projekta)), f"Manjkajoča polja: {', '.join(manjkajocha)}")
            
            except Exception as e:
                izpisi_vrstico("napaka", ime_modula, str(e))
                validator.dodaj_napako(str(modul_json.relative_to(pot_projekta)), str(e))
        else:
            izpisi_vrstico("napaka", ime_modula, sporocilo)
            validator.dodaj_napako(str(modul_json.relative_to(pot_projekta)), sporocilo)


def glavna(pot_projekta: str = None) -> None:
    """Glavna funkcija."""
    if pot_projekta is None:
        pot_projekta = Path(__file__).parent.parent
    else:
        pot_projekta = Path(pot_projekta)
    
    validator = JsonValidator()
    
    izpisi_naslov("ASTRA MENTALICA - PREVERJANJE JSON DATOTEK")
    print(f"Pot projekta: {pot_projekta}")
    print(f"Čas: {datetime.now().strftime('%d.%m.%Y %H:%M:%S')}")
    
    if not pot_projekta.exists():
        print(f"{Barve.RED}[✗] Pot ne obstaja: {pot_projekta}{Barve.RESET}")
        return
    
    # Najdi JSON datoteke
    print("\nIskanje JSON datotek...")
    json_datoteke = najdi_json_datoteke(pot_projekta)
    print(f"Najdenih {len(json_datoteke)} JSON datotek")
    
    # Preveri vsako
    print("\nPreverjanje JSON veljavnosti...")
    print("-" * 70)
    
    for json_file in json_datoteke:
        relativna_pot = json_file.relative_to(pot_projekta)
        rezultat = preveri_json_v_datoteki(json_file)
        
        if rezultat["veljavnost"]:
            if not rezultat["opozorila"]:
                izpisi_vrstico("uspeh", str(relativna_pot))
                validator.dodaj_uspeh(str(relativna_pot))
            else:
                izpisi_vrstico("opozorilo", str(relativna_pot), "; ".join(rezultat["opozorila"][:2]))
                validator.dodaj_opozorilo(str(relativna_pot), "; ".join(rezultat["opozorila"]))
        else:
            izpisi_vrstico("napaka", str(relativna_pot), "; ".join(rezultat["opozorila"]))
            validator.dodaj_napako(str(relativna_pot), "; ".join(rezultat["opozorila"]))
    
    # Sistemske datoteke
    preveri_sistemske_json(pot_projekta, validator)
    
    # Modul json
    preveri_modul_json(pot_projekta, validator)
    
    # Povzetek
    print(f"\n{Barve.CYAN}{'=' * 70}{Barve.RESET}")
    print(f"{Barve.CYAN}{Barve.BOLD}POVZETEK JSON PREVERJANJA{BARVE.RESET}")
    print(f"{Barve.CYAN}{'=' * 70}{Barve.RESET}")
    print()
    print(f"Skupaj preverjenih: {validator.rezultati.__len__()}")
    print(f"Veljavnih:         {validator.stevilo_uspehov}")
    print(f"Z napakami:        {validator.stevilo_napak}")
    print(f"Opozoril:          {validator.stevilo_opozoril}")
    
    print()
    if validator.stevilo_napak == 0:
        print(f"{Barve.GREEN}✓ Vse JSON datoteke so veljavne!{Barve.RESET}")
    else:
        print(f"{Barve.RED}✗ Najdenih {validator.stevilo_napak} neveljavnih JSON datotek!{Barve.RESET}")


if __name__ == "__main__":
    import argparse
    
    parser = argparse.ArgumentParser(description="Preverjanje JSON datotek v AstraMentalica projektu")
    parser.add_argument("pot", nargs="?", default=None, help="Pot do projekta")
    
    args = parser.parse_args()
    glavna(args.pot)
