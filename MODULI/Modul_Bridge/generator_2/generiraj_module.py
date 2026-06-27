#!/usr/bin/env python3
"""
generiraj_module.py
====================
Python ekvivalent generiraj_modul.php — uporabljen za DEJANSKO generiranje
in validacijo modulov, ker PHP ni na voljo v tem okolju.

Proizvaja IDENTIČNO strukturo kot PHP generator:
    MODULI/{Ime}/
    ├── modul.php
    ├── .htaccess
    ├── cache/.gitkeep
    ├── temp/.gitkeep
    └── podatki/
        ├── manifest.json
        ├── api.json
        ├── izhod.json
        ├── modul.md
        └── .gitkeep

Sledi zaklenjeni specifikaciji iz:
- zaklenjena_struktura_manifestov.txt
- zaklenjena_pravila_MODULI.txt
"""
import json
import re
from pathlib import Path
from datetime import datetime, timezone


def generiraj_modul(izhodna_mapa: Path, ime: str, opis: str, opcije: dict | None = None) -> dict:
    """Ustvari nov modul po zaklenjeni kanonični specifikaciji."""

    if not re.match(r'^[A-Z][a-zA-Z0-9]+$', ime):
        return {'uspeh': False, 'napaka': 'Ime modula mora biti v PascalCase (npr. Stelaris, OrakleumTarot).'}

    pot_modula = izhodna_mapa / ime
    pot_podatki = pot_modula / 'podatki'

    if pot_modula.exists():
        return {'uspeh': False, 'napaka': f"Modul '{ime}' že obstaja."}

    # ── Privzete opcije (vse polja iz zaklenjene specifikacije) ──
    o = {
        'id': ime.lower(),
        'tip': 'zbiralec',
        'nivo': 1,
        'verzija': '1.0.0',
        'status': 'testni',
        'demo': False,
        'zacasen': False,

        'minimalna_vloga': 'S0',
        'plan': 'osnova',
        'javno_vidno': True,
        'placljivo': False,
        'otroski': False,
        'vidnost': 'vsi',
        'dovoljenja': ['branje'],

        'ima_prikaz': True,
        'ikona': '✨',
        'barva': '#818cf8',
        'kategorija': 'SIMBOLI',
        'dovoljene_postavitve': ['standard'],
        'tags': [],
        'jeziki': ['sl'],
        'varuh': None,
        'duhec': None,

        'izvajanje_tip': 'ui',
        'api_only': False,
        'interval': None,
        'ob_zagonu': False,
        'prioriteta': 50,
        'bootstrap': None,

        'cache_omogocen': True,
        'cache_ttl': 3600,
        'cache_strategija': 'parameter',
        'cisti_ob_zagonu': False,

        'log_omogocen': True,
        'log_nivo': 'info',

        'potrebuje': [],
        'opcijsko_vhod': [],
        'vir': 'uporabnik',
        'pise_v': None,
        'bere_iz': [],
        'min_sistem': '2.0.0',

        'avtor': 'AstraMentalica Mojster',
        'licenca': 'Zaprta koda',
    }
    if opcije:
        o.update(opcije)

    id_ = o['id']
    datum = datetime.now().strftime('%d.%m.%Y %H:%M')
    zdaj = datetime.now(timezone.utc).strftime('%Y-%m-%dT%H:%M:%SZ')

    pot_modula.mkdir(parents=True, exist_ok=True)
    pot_podatki.mkdir(parents=True, exist_ok=True)
    (pot_modula / 'cache').mkdir(exist_ok=True)
    (pot_modula / 'temp').mkdir(exist_ok=True)

    # ── 1. manifest.json — KDO SEM ────────────────────────
    manifest = {
        '_id': id_,
        '_verzija': o['verzija'],
        'modul': {
            'id': id_,
            'ime': ime,
            'ime_prikazno': ime,
            'ime_izvirno': o.get('ime_izvirno', ime),
            'tip': o['tip'],
            'nivo': o['nivo'],
            'verzija': o['verzija'],
            'aktiviran': True,
            'vstopna': 'modul.php',
            'opis': opis,
            'status': o['status'],
            'demo': o['demo'],
            'zacasen': o['zacasen'],
        },
        'dostop': {
            'minimalna_vloga': o['minimalna_vloga'],
            'plan': o['plan'],
            'javno_vidno': o['javno_vidno'],
            'placljivo': o['placljivo'],
            'otroski': o['otroski'],
            'vidnost': o['vidnost'],
            'dovoljenja': o['dovoljenja'],
        },
        'cache': {
            'omogocen': o['cache_omogocen'],
            'ttl': o['cache_ttl'],
        },
        'ui': {
            'ima_prikaz': o['ima_prikaz'],
            'ikona': o['ikona'],
            'barva': o['barva'],
            'kategorija': o['kategorija'],
            'dovoljene_postavitve': o['dovoljene_postavitve'],
            'tags': o['tags'],
            'jeziki': o['jeziki'],
        },
        'izvajanje': {
            'tip': o['izvajanje_tip'],
            'api_only': o['api_only'],
            'interval': o['interval'],
            'ob_zagonu': o['ob_zagonu'],
            'prioriteta': o['prioriteta'],
            'bootstrap': o['bootstrap'],
        },
        'migracije': {
            'obstajajo': False,
            'zadnja': None,
        },
        'integriteta': {
            'checksum': None,
        },
        'log': {
            'omogocen': o['log_omogocen'],
            'nivo': o['log_nivo'],
        },
        'cas': {
            'ustvarjen': zdaj,
            'posodobljen': zdaj,
            'zadnji_zagon': None,
        },
    }
    (pot_podatki / 'manifest.json').write_text(
        json.dumps(manifest, ensure_ascii=False, indent=4), encoding='utf-8'
    )

    # ── 2. api.json — KAKO ME KLIČEŠ ──────────────────────
    vstop = {'web': 'modul.php'}
    if o['izvajanje_tip'] == 'cron':
        vstop['cron'] = 'cron.php'

    api = {
        '_id': id_,
        '_verzija': o['verzija'],
        'kanali': ['api'],
        'vstop': vstop,
        'javne_metode': [],
        'http_poti': [
            f'/{id_}/info',
            f'/{id_}/domov',
        ],
    }
    (pot_podatki / 'api.json').write_text(
        json.dumps(api, ensure_ascii=False, indent=4), encoding='utf-8'
    )

    # ── 3. izhod.json — KAJ POTREBUJEM IN PROIZVEDEM ──────
    pise_v = o['pise_v'] if o['pise_v'] is not None else [f'PODATKI/moduli/{id_}/']

    izhod = {
        '_id': id_,
        '_verzija': o['verzija'],
        'vhod': {
            'potrebuje': o['potrebuje'],
            'opcijsko': o['opcijsko_vhod'],
            'vir': o['vir'],
            'validacija': None,
            'omejitve': {
                'max_velikost': None,
            },
        },
        'izhod': {
            'format': 'json',
            'pise_v': pise_v,
        },
        'odvisnosti': {
            'bere_iz': o['bere_iz'],
            'prepovedani_moduli': [],
            'ne_pozna': 'vse_ostalo',
            'kompatibilnost': {
                'min_sistem': o['min_sistem'],
                'max_sistem': None,
            },
        },
        'cache': {
            'omogocen': o['cache_omogocen'],
            'ttl': o['cache_ttl'],
            'strategija': o['cache_strategija'],
            'cisti_ob_zagonu': o['cisti_ob_zagonu'],
        },
        'ui': {
            'varuh': o['varuh'],
            'duhec': o['duhec'],
        },
        'dogodki': {
            'poslusa': [],
            'oddaja': [],
        },
    }
    (pot_podatki / 'izhod.json').write_text(
        json.dumps(izhod, ensure_ascii=False, indent=4), encoding='utf-8'
    )

    # ── 4. modul.md — DOKUMENTACIJA ZA ČLOVEKA ────────────
    (pot_podatki / 'modul.md').write_text(
        _generator_modul_md(ime, id_, opis, o, datum), encoding='utf-8'
    )

    # ── 5. modul.php ───────────────────────────────────────
    (pot_modula / 'modul.php').write_text(
        _generator_modul_php(ime, id_, opis, datum), encoding='utf-8'
    )

    # ── 6. .htaccess ───────────────────────────────────────
    (pot_modula / '.htaccess').write_text(_generator_htaccess(), encoding='utf-8')

    # ── 7. .gitkeep datoteke ───────────────────────────────
    (pot_podatki / '.gitkeep').write_text('', encoding='utf-8')
    (pot_modula / 'cache' / '.gitkeep').write_text('', encoding='utf-8')
    (pot_modula / 'temp' / '.gitkeep').write_text('', encoding='utf-8')

    return {'uspeh': True, 'pot': str(pot_modula), 'ime': ime, 'id': id_}


def _generator_htaccess() -> str:
    return """# ---------------------------------------------------------
# .htaccess – varnostna zaščita modula
# ---------------------------------------------------------

# Blokiraj direktni dostop do vseh PHP datotek
<FilesMatch "\\.(php|php8|phtml)$">
    Order Deny,Allow
    Deny from all
</FilesMatch>

# Dovoli dostop samo do vstopnih točk
<FilesMatch "^(modul\\.php|index\\.php)$">
    Order Allow,Deny
    Allow from all
</FilesMatch>

# Dovoli statične datoteke
<FilesMatch "\\.(css|js|jpg|jpeg|png|gif|svg|ico|webp|mp3|mp4|woff|woff2|ttf)$">
    Order Allow,Deny
    Allow from all
</FilesMatch>

# Zaščiti konfiguracijske datoteke
<FilesMatch "\\.(json|yml|yaml|xml|ini|env|md)$">
    Order Deny,Allow
    Deny from all
</FilesMatch>
"""


def _generator_modul_md(ime: str, id_: str, opis: str, o: dict, datum: str) -> str:
    tags_str = '(ni)' if not o['tags'] else ', '.join(o['tags'])
    jeziki_str = ', '.join(o['jeziki'])
    postavitve_str = ', '.join(o['dovoljene_postavitve'])
    dovoljenja_str = ', '.join(o['dovoljenja'])
    varuh_str = o['varuh'] or '(ni)'
    duhec_str = o['duhec'] or '(ni)'
    bere_iz_str = '(nič)' if not o['bere_iz'] else ', '.join(o['bere_iz'])

    da_ne = lambda v: 'Da' if v else 'Ne'

    return f"""# {ime}

**ID:** {id_}
**Verzija:** {o['verzija']}
**Tip:** {o['tip']}
**Nivo:** {o['nivo']}
**Status:** {o['status']}

---

## Avtor

{o['avtor']}

---

## Licenca

{o['licenca']}

---

## Opis

{opis}

---

## Dostop

- **Minimalna vloga:** {o['minimalna_vloga']}
- **Plan:** {o['plan']}
- **Javno vidno:** {da_ne(o['javno_vidno'])}
- **Plačljivo:** {da_ne(o['placljivo'])}
- **Otroški:** {da_ne(o['otroski'])}
- **Vidnost:** {o['vidnost']}
- **Dovoljenja:** {dovoljenja_str}

---

## UI

- **Ima prikaz:** {da_ne(o['ima_prikaz'])}
- **Ikona:** {o['ikona']}
- **Barva:** {o['barva']}
- **Kategorija:** {o['kategorija']}
- **Dovoljene postavitve:** {postavitve_str}
- **Tags:** {tags_str}
- **Jeziki:** {jeziki_str}
- **Varuh:** {varuh_str}
- **Duhec:** {duhec_str}

---

## Odvisnosti

- **Bere iz:** {bere_iz_str}
- **Prepovedani moduli:** (nič)
- **Ne pozna:** vse ostalo
- **Kompatibilnost:** Sistem >={o['min_sistem']}

---

## Izvajanje

- **Tip:** {o['izvajanje_tip']}
- **API only:** {da_ne(o['api_only'])}
- **Prioriteta:** {o['prioriteta']}

---

## Namestitev

1. Kopiraj mapo modula v `MODULI/{ime}/`
2. Aktiviraj modul v sistemu (registriraj v `PODATKI/registri/moduli_register.json`)
3. Poženi `php ASTRA/razvoj/orodja/generator.php --full`

---

## Testirano na

- Sistem >={o['min_sistem']}
- PHP 8.1, 8.2, 8.3

---

## Changelog

### {o['verzija']} ({datum})
- Prva izdaja

---

## Uporaba

```bash
curl http://example.com/{id_}/info
curl http://example.com/{id_}/domov
```

## Primeri

### Dobi informacije o modulu

```bash
curl -X GET http://example.com/{id_}/info
```
"""


def _generator_modul_php(ime: str, id_: str, opis: str, datum: str) -> str:
    opis_esc = opis.replace("'", "\\'")
    return f"""<?php
/**
 * ============================================================
 * MODUL: {ime}
 * POT: MODULI/{ime}/modul.php
 * 📅 VERZIJA: v1.0.0 ({datum})
 * ============================================================
 *
 * 🏛️ NIVO: MODUL
 *
 * 📰 NAMEN:
 *     {opis}
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - modul_{id_}_akcija(string $akcija, array $podatki): array
 *
 * 📡 ODVISNOSTI:
 *     - Modul_Bridge (demo ali pravi sistem)
 *
 * 🚫 PREPOVEDI:
 *     - Brez direktnih klicev v SISTEM/
 *     - Brez branja $_SESSION
 *     - Brez pisanja izven lastne mape (razen preko Bridge-a)
 *
 * 📌 STATUS:
 *     Razvoj
 *
 * 👤 AVTOR:
 *     AstraMentalica Mojster
 *
 * 🌐 JEZIK:
 *     sl
 *
 * 🏷️ OZNAKE:
 *     modul, {id_}
 * ============================================================
 */

declare(strict_types=1);

// ── POIŠČI BRIDGE (modul ne pozna sidra, samo Modul_Bridge) ──
$bridgePoti = [
    __DIR__ . '/../Modul_Bridge/index.php',
    __DIR__ . '/../Modul_Bridge/jedro/sistemske_funkcije.php',
];

$bridgeNajden = false;
foreach ($bridgePoti as $pot) {{
    if (file_exists($pot)) {{
        require_once $pot;
        $bridgeNajden = true;
        break;
    }}
}}

if (!$bridgeNajden) {{
    header('Content-Type: application/json');
    echo json_encode(['napaka' => 'Modul_Bridge ni najden']);
    exit;
}}

// ── STANDARDNI ODZIVI ──────────────────────────────────────
if (!function_exists('odziv_uspeh')) {{
    function odziv_uspeh(array $vsebina, string $sporocilo = ''): array {{
        return ['status' => 'uspeh', 'status_koda' => 200, 'sporocilo' => $sporocilo, 'vsebina' => $vsebina];
    }}
    function odziv_napaka(string $sporocilo, int $koda = 400): array {{
        return ['status' => 'napaka', 'status_koda' => $koda, 'sporocilo' => $sporocilo, 'vsebina' => []];
    }}
}}

// ============================
// VSTOPNA TOČKA MODULA
// ============================

/**
 * Glavna funkcija modula — kliče jo SISTEM preko Modul_Bridge.
 *
 * @param string $akcija   Kaj modul mora narediti
 * @param array  $podatki  Parametri za akcijo
 * @return array           Rezultat (vedno array, nikoli HTML)
 */
function modul_{id_}_akcija(string $akcija, array $podatki = []): array {{
    if (!Modul_Bridge::vloga_preveri('S0')) {{
        return odziv_napaka('Dostop zavrnjen', 403);
    }}

    return match($akcija) {{
        'info'  => _modul_{id_}_info($podatki),
        'domov' => _modul_{id_}_domov($podatki),
        default => odziv_napaka("Neznana akcija: $akcija", 400),
    }};
}}

// ============================
// AKCIJE
// ============================

function _modul_{id_}_info(array $podatki): array {{
    $uporabnik = Modul_Bridge::uporabnik_pridobi();

    return odziv_uspeh([
        'ime'       => '{ime}',
        'id'        => '{id_}',
        'verzija'   => '1.0.0',
        'opis'      => '{opis_esc}',
        'uporabnik' => $uporabnik['ime'] ?? 'Gost',
    ], 'Informacije o modulu');
}}

function _modul_{id_}_domov(array $podatki): array {{
    return odziv_uspeh([
        'sporocilo' => 'Pozdravljen v modulu {ime}!',
        'cas'       => time(),
    ], 'Domov');
}}

// ── ČE SE KLIČE DIREKTNO (brez Bridge-a) ──────────────────
if (basename($_SERVER['SCRIPT_FILENAME'] ?? '') === 'modul.php' && !defined('SISTEM_OBSTAJA')) {{
    $akcija  = $_REQUEST['akcija'] ?? 'domov';
    $podatki = $_REQUEST;
    $odziv   = modul_{id_}_akcija($akcija, $podatki);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($odziv, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}}
"""
