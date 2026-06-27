# MODULI — KAKO DODAŠ NOVEGA

> Preberi najprej: USTAVA.md, ARHITEKTURA.md, STANDARDI.md
> Ta dokument opisuje **postopek dodajanja novega modula** in kaj vsak modul mora imeti.

---

## 1. KAJ JE MODUL

Modul je **svet znanja** — zaključena enota, ki dela eno stvar.

Vsak modul je v svoji mapi in:
- **Ne ve** za druge module
- **Ne kliče** drugih modulov direktno
- Komunicira z drugimi **samo preko Modul_Bridge**

---

## 2. STRUKTURA MODULA
MODULI/{ImeModula}/
├── modul.php ← edina vstopna točka (OBVEZNO)
└── podatki/ ← podatki modula (OBVEZNO)
├── manifest.json ← sistemske nastavitve (OBVEZNO)
├── api.json ← HTTP poti (OBVEZNO)
├── izhod.json ← shema in pisanje (OBVEZNO)
└── modul.md ← dokumentacija (PRIZOROČLJIVO)

text

---

## 3. KAJ MODUL SME

| Stvar | Opis |
|-------|------|
| `require_once __DIR__ . '/modul.php'` | Klic lastne logike |
| `__DIR__ . '/podatki/'` | Dostop do lastnih podatkov |
| `__DIR__ . '/cache/'` | Lastni cache (začasni podatki) |
| `__DIR__ . '/temp/'` | Lastne začasne datoteke |
| `Modul_Bridge::klic($akcija, $podatki)` | Klic sistema preko mostu |
| `Modul_Bridge::podatki_beri($kljuc)` | Branje demo/sistemskih podatkov |
| `Modul_Bridge::podatki_pisi($kljuc, $vrednost)` | Pisanje podatkov (z dovoljenjem) |
| `Modul_Bridge::uporabnik_pridobi()` | Dobi trenutnega uporabnika (ali demo) |
| `Modul_Bridge::vloga_preveri($zahtevana)` | Preveri vlogo (ali demo) |
| `Modul_Bridge::modul_klic($modul, $akcija, $podatki)` | Klic drugega modula preko Bridge |

---

## 4. KAJ MODUL NE SME

| Stvar | Zakaj |
|-------|-------|
| `require_once '../../pot.php'` | Modul ne ve za sidro |
| `require_once POT_SISTEM . '/...'` | Modul ne pozna sistemskih poti |
| `__DIR__ . '/../'` | Modul ne sme zapustiti svoje mape |
| `__DIR__ . '/../../'` | Modul ne sme zapustiti svoje mape |
| `__DIR__ . '/../../../SISTEM'` | Modul ne sme zapustiti svoje mape |
| Direkten klic v SISTEM/ | Krši izolacijo |
| `session_start()` | Modul ne upravlja sej |
| `$_SESSION` direktno | Modul ne upravlja sej |
| `$_POST, $_GET, $_REQUEST, $_COOKIE` | Modul ne dostopa direktno do HTTP |
| `global $db` ali `$GLOBALS` | Modul ne uporablja globalnih spremenljivk |
| `exec(), shell_exec(), proc_open()` | Modul ne ustvarja niti (razen z dovoljenjem) |
| `die(), exit()` v API akcijah | Modul ne sme ustavljati sistema |
| HTML v `modul.php` | Modul vrača podatke, ne prikaz |
| Klicanje drugih modulov direktno | Moduli so izolirani – grejo preko Bridge |

---

## 5. `modul.php` — OBVEZNA STRUKTURA

```php
<?php
/**
 * ============================================================
 * POT: MODULI/{ImeModula}/modul.php
 * 📅 VERZIJA: 1.0.0
 * ============================================================
 *
 * 🏛️ NIVO: MODUL
 *
 * 📰 NAMEN:
 *     Glavna logika modula {ImeModula}.
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - modul_{ime}_akcija(string $akcija, array $podatki): array
 *
 * 🚫 PREPOVEDI:
 *     - Brez HTML
 *     - Brez require_once na sistemske poti
 *     - Brez __DIR__ za izhod iz lastne mape
 *     - Brez $_POST, $_GET, $_SESSION direktno
 *     - Brez globalnih spremenljivk
 *
 * 🌐 JEZIK:
 *     sl
 * ============================================================
 */
declare(strict_types=1);

// ============================
// VSTOPNA TOČKA MODULA
// ============================

/**
 * Glavna funkcija modula — kliče jo Modul_Bridge.
 * Vedno sprejme akcijo in podatke, vrne array.
 *
 * @param string $akcija   Kaj modul mora narediti
 * @param array  $podatki  Parametri za akcijo
 * @return array           Rezultat (vedno array, nikoli HTML)
 */
function modul_{ime}_akcija(string $akcija, array $podatki = []): array {
    // 1. Preveri vlogo preko Bridge-a
    if (!Modul_Bridge::vloga_preveri('S0')) {
        return ['napaka' => 'Nimaš dostopa'];
    }

    // 2. Dobi uporabnika preko Bridge-a
    $uporabnik = Modul_Bridge::uporabnik_pridobi();

    // 3. Izvedi akcijo
    return match($akcija) {
        'info'    => ['ime' => '{ImeModula}', 'verzija' => '1.0.0'],
        default   => ['napaka' => 'Neznana akcija: ' . $akcija]
    };
}
6. manifest.json — KANONIČNI FORMAT
json
{
    "_id": "{ime}",
    "_verzija": "1.0.0",

    "modul": {
        "id": "{ime}",
        "ime": "{ImeModula}",
        "tip": "zbiralec",
        "nivo": 1,
        "verzija": "1.0.0",
        "aktiviran": true,
        "vstopna": "modul.php",
        "opis": "Opis modula",
        "status": "razvoj",
        "demo": false,
        "zacasen": false
    },

    "dostop": {
        "minimalna_vloga": "gost",
        "plan": "osnova",
        "javno_vidno": false,
        "placljivo": false,
        "otroski": false,
        "vidnost": "javni",
        "dovoljenja": ["branje"]
    },

    "cache": {
        "omogocen": true,
        "ttl": 3600
    },

    "ui": {
        "ima_prikaz": true,
        "ikona": "✨",
        "barva": "#818cf8",
        "kategorija": "splošno",
        "dovoljene_postavitve": ["standard"],
        "tags": [],
        "jeziki": ["sl"]
    },

    "izvajanje": {
        "tip": "ui",
        "api_only": false,
        "interval": null,
        "ob_zagonu": false,
        "prioriteta": 100,
        "bootstrap": null
    },

    "migracije": {
        "obstajajo": false,
        "zadnja": null
    },

    "integriteta": {
        "checksum": null
    },

    "log": {
        "omogocen": true,
        "nivo": "info"
    },

    "cas": {
        "ustvarjen": "2026-06-20T00:00:00Z",
        "posodobljen": "2026-06-20T00:00:00Z",
        "zadnji_zagon": null
    }
}
7. api.json — KAKO ME KLIČEŠ
json
{
    "_id": "{ime}",
    "_verzija": "1.0.0",

    "kanali": ["api"],

    "vstop": {
        "web": "modul.php"
    },

    "javne_metode": [
        "info"
    ],

    "http_poti": [
        "/{ime}/info"
    ]
}
8. izhod.json — KAJ POTREBUJEM IN KAJ PROIZVEDEM
json
{
    "_id": "{ime}",
    "_verzija": "1.0.0",

    "vhod": {
        "potrebuje": [],
        "opcijsko": [],
        "vir": "uporabnik",
        "validacija": null,
        "omejitve": {
            "max_velikost": null
        }
    },

    "izhod": {
        "format": "json",
        "pise_v": []
    },

    "odvisnosti": {
        "bere_iz": [],
        "prepovedani_moduli": [],
        "ne_pozna": "vse_ostalo",
        "kompatibilnost": {
            "min_sistem": "2.0.0",
            "max_sistem": null
        }
    },

    "cache": {
        "omogocen": true,
        "ttl": 3600,
        "strategija": "casovna",
        "cisti_ob_zagonu": false
    },

    "ui": {
        "varuh": null,
        "duhec": null
    },

    "dogodki": {
        "poslusa": [],
        "oddaja": []
    }
}
9. KAKO MODUL KLIČE SISTEM
php
function modul_moj_akcija(string $akcija, array $podatki = []): array {
    // 1. Preveri vlogo preko Bridge-a
    if (!Modul_Bridge::vloga_preveri('S1')) {
        return ['napaka' => 'Nimaš dostopa'];
    }

    // 2. Dobi uporabnika preko Bridge-a
    $uporabnik = Modul_Bridge::uporabnik_pridobi();

    // 3. Sistemski podatki → preko Bridge-a
    $sistemskiPodatki = Modul_Bridge::podatki_beri('sistem/nastavitve');

    // 4. Lastni cache → direktno (znotraj mape)
    $cache = json_decode(file_get_contents(__DIR__ . '/cache/tmp.json'), true);

    // 5. Tvoja logika
    $rezultat = ...;

    // 6. Shrani podatke → preko Bridge-a (z dovoljenjem)
    Modul_Bridge::podatki_pisi('moj_modul/rezultat', $rezultat);

    // 7. Klic drugega modula → preko Bridge-a
    $koledar = Modul_Bridge::modul_klic('koledar', 'dogodki_pridobi', ['datum' => 'danes']);

    // 8. Vrni JSON
    return ['uspeh' => true, 'vsebina' => $rezultat];
}
10. KAKO MODUL DELUJE IZVEN SISTEMA
Ko modul odstraniš iz sistema, Modul_Bridge samodejno zagotovi demo okolje:

Funkcija	Demo podatek
Modul_Bridge::uporabnik_pridobi()	['id' => 0, 'ime' => 'Demo', 'vloga' => 60]
Modul_Bridge::vloga_preveri()	true (demo admin)
Modul_Bridge::podatki_beri()	Prazni demo podatki
Modul_Bridge::podatki_pisi()	Shrani v demo mapo
Modul_Bridge::modul_klic()	Vrne demo podatke za drug modul
Modul ne ve razlike med pravim sistemom in demo načinom.

11. POSTOPEK — DODAJANJE NOVEGA MODULA
Korak 1: Ustvari mapo
text
MODULI/{ImeModula}/
Korak 2: Ustvari podatki/ mapo
text
MODULI/{ImeModula}/podatki/
Korak 3: Ustvari manifest.json
Zapolni vsa obvezna polja (glej razdelek 6).

Korak 4: Ustvari api.json
Zapolni vsa obvezna polja (glej razdelek 7).

Korak 5: Ustvari izhod.json
Zapolni vsa obvezna polja (glej razdelek 8).

Korak 6: Ustvari modul.php
Sledi strukturi iz razdelka 5.
Minimalno: funkcija modul_{ime}_akcija(string $akcija, array $podatki): array

Korak 7: (Neobvezno) Ustvari modul.md
Dokumentacija za človeka.

12. CHECKLIST — PRED AKTIVACIJO MODULA
text
[ ] MODULI/{ImeModula}/mapa obstaja
[ ] podatki/manifest.json — vsa polja izpolnjena
[ ] podatki/api.json — vsa polja izpolnjena
[ ] podatki/izhod.json — vsa polja izpolnjena
[ ] modul.php — ima vstopno funkcijo z ustreznim imenom
[ ] modul.php — vrača vedno array, nikoli HTML
[ ] modul.php — uporablja samo Modul_Bridge za zunanje stvari
[ ] modul.php — ne uporablja __DIR__ za izhod iz svoje mape
[ ] modul.php — ne uporablja $_POST, $_GET, $_SESSION direktno
[ ] modul.php — ne uporablja globalnih spremenljivk
[ ] Test — osnovna akcija vrne pričakovan array
[ ] Test — modul deluje samostojno (preko Modul_Bridge demo)
13. ČE KRŠIŠ PRAVILA
Kršitev	Kazen
require_once na sistemske poti	Modul zavrnjen
__DIR__ za izhod iz lastne mape	Modul zavrnjen
Direkten klic v SISTEM/	Modul zavrnjen
HTML v modul.php	Modul zavrnjen
session_start()	Modul zavrnjen
Direktno klicanje drugega modula	Modul zavrnjen
exec(), shell_exec() brez dovoljenja	Modul zavrnjen
14. KONČNA MANTRA
Modul pozna samo:

svojo mapo

svoj manifest

svoj izhod

svoj api

Modul_Bridge

Modul ne pozna:

SIDRA

SISTEM/

baz

session

HTTP

drugih modulov

filesystema izven svoje mape

To je to. Pravila so zaklenjena. 🕊️

MODULI.md — verzija 2.0 — zaklenjena