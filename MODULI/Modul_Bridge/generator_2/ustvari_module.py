#!/usr/bin/env python3
"""
ustvari_module.py
==================
Generira vseh 18 novih modulov po zaklenjeni kanonični specifikaciji.

Že narejeni (preskočeni):
devorum, energetica, jyotir, lapidaria, liberumbrae, lunaris, mystaia,
mysticamesoamericana, nordicamystica, numerariumcosmicum, numyra, occultum,
oraculumvisionis, pranaymica
"""
import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent))
from generiraj_module import generiraj_modul

IZHODNA_MAPA = Path('/home/claude/astra_moduli')

MODULI = [
    (
        'Aetheris',
        'Energijsko polje in avra — barvna analiza, energijske plasti, čiščenje polja.',
        {
            'ikona': '💚',
            'barva': '#67e8f9',
            'kategorija': 'ZEMLJA',
            'tags': ['avra', 'energija', 'cakre', 'polje'],
            'minimalna_vloga': 'S1',
            'vidnost': 'prijavljeni',
            'ime_izvirno': 'Aether',
        }
    ),
    (
        'Animaris',
        'Živalski duh in totem — odkrivanje totemske živali, sporočila živalskega kraljestva.',
        {
            'ikona': '🐺',
            'barva': '#6ee7b7',
            'kategorija': 'ZEMLJA',
            'tags': ['totem', 'zival', 'duh', 'animaris'],
            'minimalna_vloga': 'S1',
            'ime_izvirno': 'Animaris',
        }
    ),
    (
        'BotanicaSacra',
        'Sveta botanika — zdravilne rastline, zeliščarstvo, rastlinska magija in cvetni jeziki.',
        {
            'ikona': '🌿',
            'barva': '#4ade80',
            'kategorija': 'ZEMLJA',
            'tags': ['rastline', 'zeliscje', 'botanika', 'magija'],
            'minimalna_vloga': 'S0',
            'ime_izvirno': 'Botanica Sacra',
        }
    ),
    (
        'Celestara',
        'Nebeški atlas — zvezde, ozvezdja, planetarne ure in kozmični ritmi.',
        {
            'ikona': '🌟',
            'barva': '#c4b5fd',
            'kategorija': 'NEBO',
            'tags': ['zvezde', 'nebo', 'planeti', 'kozmos'],
            'minimalna_vloga': 'S0',
        }
    ),
    (
        'CorpusMysticum',
        'Knjižnica zavesti — enciklopedija ezoteričnih znanj, simbolov in tradicij.',
        {
            'ikona': '📚',
            'barva': '#a78bfa',
            'kategorija': 'SVET',
            'tags': ['knjiznica', 'enciklopedija', 'znanje', 'ezoterija'],
            'minimalna_vloga': 'S0',
            'tip': 'izvajalec',
            'nivo': 3,
            'vidnost': 'vsi',
        }
    ),
    (
        'CosmicaScientia',
        'Kozmična znanost — biopijski ritmi, Schmannove resonance, kozmobiologija.',
        {
            'ikona': '🔭',
            'barva': '#818cf8',
            'kategorija': 'NEBO',
            'tags': ['bioritem', 'schumann', 'resonanca', 'kozmobiologija'],
            'minimalna_vloga': 'S1',
            'bere_iz': ['senzornasanasa'],
        }
    ),
    (
        'QiVitalis',
        'Qi in vitalna energija — kitajska energijska medicina, meridianski sistem, qi gong.',
        {
            'ikona': '🐉',
            'barva': '#34d399',
            'kategorija': 'ZEMLJA',
            'tags': ['qi', 'energija', 'meridiani', 'kitajska'],
            'minimalna_vloga': 'S1',
        }
    ),
    (
        'Sephirotica',
        'Kabala in drevo življenja — sefiroti, poti, gematria in kabalistična meditacija.',
        {
            'ikona': '✡️',
            'barva': '#fbbf24',
            'kategorija': 'SIMBOLI',
            'tags': ['kabala', 'sefiroti', 'drevo', 'gematria'],
            'minimalna_vloga': 'S2',
            'vidnost': 'prijavljeni',
        }
    ),
    (
        'Seraphica',
        'Angelska hijerarhija — angeli, arhangeli, angelska sporočila in varstvo.',
        {
            'ikona': '👼',
            'barva': '#fde68a',
            'kategorija': 'NEBO',
            'tags': ['angeli', 'arhangeli', 'nebo', 'varstvo'],
            'minimalna_vloga': 'S0',
            'varuh': 'Arhangel Rafael',
        }
    ),
    (
        'Somnaris',
        'Sanjska knjiga — razlaga sanj, lucidarno sanjanje, simbolika in arhetipski vzorci.',
        {
            'ikona': '🌙',
            'barva': '#6d28d9',
            'kategorija': 'POTI',
            'tags': ['sanje', 'lucidno', 'simboli', 'arhetip'],
            'minimalna_vloga': 'S0',
            'cache_ttl': 86400,
        }
    ),
    (
        'Stelaris',
        'Astrološki motor — natalni horoskop, tranziti, aspekti in planetarne energije.',
        {
            'ikona': '🌌',
            'barva': '#818cf8',
            'kategorija': 'NEBO',
            'tags': ['astrologija', 'horoskop', 'planeti', 'aspekti'],
            'minimalna_vloga': 'S1',
            'tip': 'sestavljalec',
            'nivo': 2,
            'potrebuje': ['datum_rojstva', 'kraj_rojstva'],
            'cache_ttl': 3600,
        }
    ),
    (
        'Synera',
        'Sinastria in odnosi — analiza kompatibilnosti, energijski vzorci v razmerjih.',
        {
            'ikona': '🔮',
            'barva': '#e879f9',
            'kategorija': 'VIP',
            'tags': ['sinastria', 'razmerja', 'kompatibilnost', 'vip'],
            'minimalna_vloga': 'S3',
            'placljivo': True,
            'vidnost': 'prijavljeni',
            'tip': 'izvajalec',
            'nivo': 3,
            'potrebuje': ['datum_rojstva_1', 'datum_rojstva_2'],
        }
    ),
    (
        'Transmutaria',
        'Alkimija in transmutacija — hermetična filozofija, alkilmistični procesi, simboli.',
        {
            'ikona': '⚗️',
            'barva': '#f59e0b',
            'kategorija': 'POTI',
            'tags': ['alkimija', 'transmutacija', 'hermetika', 'simboli'],
            'minimalna_vloga': 'S1',
        }
    ),
    (
        'UmbraeCodex',
        'Kodeks senc — senca po Jungu, integracija temne strani, arhetipske sence.',
        {
            'ikona': '🌑',
            'barva': '#374151',
            'kategorija': 'POTI',
            'tags': ['senca', 'jung', 'integracija', 'arhetip'],
            'minimalna_vloga': 'S2',
            'vidnost': 'prijavljeni',
            'cache_ttl': 86400,
        }
    ),
    (
        'ViaAnimae',
        'Pot duše — karmični vzorci, duševne lekcije, namen duše in akašični zapisi.',
        {
            'ikona': '🕊️',
            'barva': '#f0abfc',
            'kategorija': 'POTI',
            'tags': ['karma', 'dusa', 'akaski', 'namen'],
            'minimalna_vloga': 'S1',
            'potrebuje': ['datum_rojstva'],
        }
    ),
    (
        'Vibramystica',
        'Zvočna mistika — zvočno zdravljenje, frekvenčna terapija, mantre in binaural beats.',
        {
            'ikona': '🎵',
            'barva': '#c084fc',
            'kategorija': 'ZEMLJA',
            'tags': ['zvok', 'frekvenca', 'mantra', 'binaural'],
            'minimalna_vloga': 'S0',
            'izvajanje_tip': 'ui',
        }
    ),
    (
        'AegypticaArcana',
        'Egipčanska skrivnost — hieroglifska modrost, božanstva, Kniga mrtvih, tarot Thoth.',
        {
            'ikona': '𓂀',
            'barva': '#d97706',
            'kategorija': 'SIMBOLI',
            'tags': ['egipt', 'hieroglifi', 'bogovi', 'thoth'],
            'minimalna_vloga': 'S1',
            'cache_ttl': 86400,
        }
    ),
    (
        'SenzornasaNasa',
        'Kozmični senzorji — sončeva aktivnost (NASA DONKI), geomagnetna aktivnost (NOAA SWPC), lokalno vreme (OpenWeather).',
        {
            'id': 'senzornasanasa',
            'ikona': '🛰️',
            'barva': '#67e8f9',
            'kategorija': 'NEBO',
            'tags': ['nasa', 'senzorji', 'vreme', 'geomagnetno', 'sonce'],
            'minimalna_vloga': 'gost',
            'javno_vidno': False,
            'vidnost': 'skriti',
            'dovoljenja': ['branje', 'rocni_zagon'],
            'ima_prikaz': False,
            'izvajanje_tip': 'cron',
            'api_only': True,
            'interval': 900,
            'ob_zagonu': True,
            'prioriteta': 100,
            'cache_ttl': 900,
            'cache_strategija': 'casovna',
            'vir': 'zunanji_api',
            'pise_v': [
                'PODATKI/moduli/senzornasanasa/snapshot.json',
                'PODATKI/moduli/senzornasanasa/zgodovina.json',
            ],
        }
    ),
]


def main():
    IZHODNA_MAPA.mkdir(parents=True, exist_ok=True)
    uspesni = []
    neuspesni = []

    for ime, opis, opcije in MODULI:
        rezultat = generiraj_modul(IZHODNA_MAPA, ime, opis, opcije)
        if rezultat['uspeh']:
            uspesni.append(rezultat['ime'])
            print(f"  ✅ {rezultat['ime']}  ({rezultat['id']})")
        else:
            neuspesni.append(ime)
            print(f"  ❌ {ime}: {rezultat['napaka']}")

    print(f"\nSkupaj: {len(uspesni)} uspešnih, {len(neuspesni)} neuspešnih.")
    return len(neuspesni) == 0


if __name__ == '__main__':
    print(f"Generiram {len(MODULI)} modulov v {IZHODNA_MAPA}...\n")
    ok = main()
    sys.exit(0 if ok else 1)
