# STANDARDI KODE
> Preberi najprej: USTAVA.md, ARHITEKTURA.md
> Ta dokument definira **kako pišemo kodo** — PHP, poimenovanje, komentarji, struktura.

---

## 1. JEZIK

**Vse je v slovenščini.** Brez izjem razen:
- PHP rezervirane besede: `echo`, `return`, `true`, `false`, `null`, `class`, `function`...
- HTML tagi: `<div>`, `<form>`, `<input>`...
- SQL rezervirane besede: `SELECT`, `WHERE`, `JOIN`...
- Globalne PHP konstante: `PHP_EOL`, `PHP_INT_MAX`...

```php
// ✅ pravilno
function geslo_preveri(string $geslo): bool {
    $dolzina = strlen($geslo);
    return $dolzina >= 8;
}

// ❌ napačno (angleško IN camelCase — glej razdelek 2 za pravilo imen funkcij)
function checkPassword(string $password): bool {
    $length = strlen($password);
    return $length >= 8;
}
```

---

## 2. POIMENOVANJE

### Datoteke in mape
```
mape/            → male_črke z podčrtajem (snake_case)
datoteke.php     → male_črke z podčrtajem (snake_case)
```

Primeri:
```
SISTEM/kernel/jedro/01_napake.php     ✅
MODULI/osnovni/Codex/modul.php        ✅  (ime modula je z veliko — lastno ime)
GLOBALNO/gradniki/gumb.php            ✅
GLOBALNO/gradniki/GlavniGumb.php      ❌  (ne CamelCase za datoteke)
```

### Funkcije
```
podrocje_akcija(), z glagolom v akciji (format po JEZIKOVNI_STANDARD.md)
```
```php
function pravice_preveri(): bool {}
function modul_nalozi(string $ime): array {}
function uporabnik_shrani(array $podatki): bool {}
function seja_vrni(): array {}
function cache_izbrisi(string $kljuc): void {}
```

### Spremenljivke
```
camelCase, opisno ime, brez okrajšav (spremenljivke ostanejo camelCase — pravilo snake_case velja za FUNKCIJE, ne za spremenljivke)
```
```php
$uporabniškoIme = 'Janez';      // ✅
$aktivnaSeja = true;             // ✅
$steviloPoskusov = 3;            // ✅

$un = 'Janez';                  // ❌ (okrajšava)
$active = true;                 // ❌ (angleško)
$n = 3;                         // ❌ (neopisno)
```

### Konstante
```
VELIKA_ČRKE z podčrtajem
```
```php
define('POT_KOREN', __DIR__);
define('RAZLIČICA', '1.0');
define('MAX_POSKUSOV', 5);
```

### Razredi (Classes)
```
PascalCase, samostalnik
```
```php
class UporabnikUpravnik {}
class ModulNalagalnik {}
class SejaUpravnik {}
```

### Metode v razredih
```
snake_case, glagol na začetku (enako kot funkcije — glej JEZIKOVNI_STANDARD.md)
```
```php
class UporabnikUpravnik {
    public function preveri_prijavo(): bool {}
    public function nalozi_profil(int $id): array {}
    private function sanitiziraj_vnos(string $vnos): string {}
}
```

---

## 3. STRUKTURA DATOTEKE

Vsaka PHP datoteka ima **obvezno glavo**:

# ASTRAMENTALICA HEADER STANDARD

Standard določa enotno glavo za vse PHP in JavaScript datoteke v sistemu AstraMentalica.

Glava ni namenjena zgolj dokumentaciji, temveč predstavlja arhitekturno pogodbo datoteke.

Iz glave mora biti takoj razvidno:

- kaj datoteka počne
- v katerem nivoju arhitekture deluje
- katere komponente uporablja
- s katerimi datotekami sodeluje
- česa ne sme početi
- kdo jo vzdržuje
- kakšna je zgodovina sprememb

## KANONIČNI REFERENČNI HEADER

To je uradni referenčni primer.

Vsaka PHP datoteka mora uporabljati enako strukturo, enak vrstni red in enako vizualno obliko.

Spremenijo se samo vrednosti posameznih polj.

```php
    <?php

    /**
     * ============================================================
     * POT: SISTEM/kernel/jedro/05_pravice.php
     * 📅 VERZIJA: v114 (9.6.2026 15:00)
     * ============================================================
     *
     * 🏛️ NIVO: SISTEM N3
     *
     * 📰 NAMEN:
     *     Upravljanje uporabniških pravic (RBAC).
     *
     * 🔧 JAVNE FUNKCIJE:
     *     - pravice_preveri_vlogo(int $potrebna): bool
     *     - pravice_ima_dovoljenje(string $dovoljenje): bool
     *     - pravice_registriraj_dovoljenje(string $ime, int $vloga): void
     *
     * 📡 ODVISNOSTI:
     *     - SISTEM/kernel/jedro/03_varnost.php
     *     - POT_PODATKI . '/sistem/registri/pravice.json'
     *
     * 🤝 SOODVISNOSTI:
     *     - SISTEM/kernel/jedro/04_seja.php
     *     - SISTEM/kernel/jedro/06_cache.php
     *
     * ⚡ UPORABA:
     *     - Kliče se iz middleware ali neposredno v storitvah.
     *
     * 🚫 PREPOVEDI:
     *     - Brez echo, print_r, var_dump
     *     - Brez die(), exit()
     *     - Brez direktnih poti (uporabi konstante!)
     *     - Brez direktnega branja $_SESSION
     *
     * 📌 STATUS:
     *     Stabilno
     *
     * 📅 ZGODOVINA:
     *     - v114: uskladitev s Header Standard v114
     *     - v113: dodane oznake in jezik
     *     - v112: prva implementacija
     *
     * 👤 AVTOR:
     *     AstraMentalica Mojster
     *
     * 🌐 JEZIK:
     *     sl
     *
     * 🏷️ OZNAKE:
     *     kernel, jedro, pravice, rbac
     * ============================================================
     */
    declare(strict_types=1);

    defined('SISTEM_VARNOST') or die('Direkten dostop ni dovoljen.');

```

 ## Za datoteke ki so del jedra (N3) — dodaj zaklenjenost:

```php
/**
 * DATOTEKA: 02_varnost.php
 * NAMEN:    Varnostne funkcije: hash, CSRF, sanitizacija, rate limit
 * NIVO:     3
 * ODVISNO:  01_napake.php
 * VERZIJA:  1.0
 * DATUM:    2026-01-01
 * ⚠ JEDRO: Ne spreminjaj brez razloga. Zapiši spremembo spodaj.
 */
```

## RAZŠIRJENI ELEMENTI

| Element | Kdaj |
|----------|----------|
| 🔧 JAVNE FUNKCIJE | Če datoteka vsebuje javne funkcije ali metode |
| 📡 ODVISNOSTI | Če uporablja druge komponente |
| 🤝 SOODVISNOSTI | Če sodeluje z drugimi datotekami istega sklopa |
| ⚡ UPORABA | Če namen uporabe ni samoumeven |
| ✅ DOVOLJENO | Samo GLOBALNO in razvojne module |
| 📅 ZGODOVINA | Priporočljivo za vse datoteke |

## ARHITEKTURNI NIVOJI

N1 - vstop (SISTEM/api.php)
N2 - business logika in tehnični izhod (SISTEM/storitve_svetov/, SISTEM/kanali/)
N3 - sistemska mehanika (SISTEM/kernel/, SISTEM/kernel/jedro/)

## SISTEM N3

Jedrne sistemske komponente.

Primeri:

- SISTEM/kernel/jedro/
- SISTEM/kernel/kontrakti/

Pravila:

- brez HTML
- brez renderiranja
- brez poslovne logike modulov

## STORITEV N2

Sistemske storitve.

Primeri:

- SISTEM/storitve_svetov/

Pravila:

- uporablja kernel
- izvaja sistemske procese
- ne vsebuje prikaza

## ADAPTER

Povezovalni sloj sistema.

Primeri:

- ADAPTER/izhod_kanali/
- ADAPTER/odzivi/

Pravila:

- brez business logike
- pretvorba vhodov in izhodov
- komunikacija med svetovi

## MODUL

Samostojna funkcionalna enota.

Primeri:

- MODULI/ORAKLEUM/
- MODULI/CRM/
- MODULI/ANALITIKA/

Pravila:

- komunikacija preko Modul_Bridge -> adapterja
- brez direktnega klica GLOBALNO/
- brez direktnega klica drugih modulov
- vračanje RESPONSE CONTRACT

## GLOBALNO

Pasivni prikazni sloj.

Primeri:

- GLOBALNO/render/

Pravila:

- brez poslovne logike
- brez dostopa do podatkovnih virov
- samo prikaz

## FRONTEND

Odjemalska logika.

Primeri:

- GLOBALNO/frontend/

Pravila:

- UI logika
- runtime funkcije
- komunikacija preko API

## ZAKLENJEN VRSTNI RED POLJ

1. POT
2. 📅 VERZIJA
3. 🏛️ NIVO
4. 📰 NAMEN
5. 🔧 JAVNE FUNKCIJE
6. 📡 ODVISNOSTI
7. 🤝 SOODVISNOSTI
8. ⚡ UPORABA
9. 🚫 PREPOVEDI
10. ✅ DOVOLJENO
11. 📌 STATUS
12. 📅 ZGODOVINA
13. 👤 AVTOR
14. 🌐 JEZIK
15. 🏷️ OZNAKE

## OBVEZNOST PO TIPU DATOTEKE

| Tip datoteke | Obvezni elementi |
|--------------|------------------|
| SISTEM/kernel/jedro/ | POT, VERZIJA, NIVO, NAMEN, FUNKCIJE, ODVISNOSTI, PREPOVEDI, STATUS, AVTOR, JEZIK, OZNAKE |
| SISTEM/storitve_svetov/ | POT, VERZIJA, NIVO, NAMEN, FUNKCIJE, ODVISNOSTI, PREPOVEDI, STATUS, AVTOR, JEZIK, OZNAKE |
| ADAPTER/kanali/ | POT, VERZIJA, NIVO, NAMEN, FUNKCIJE, ODVISNOSTI, PREPOVEDI, STATUS, AVTOR, JEZIK, OZNAKE |
| MODULI/.../ | POT, VERZIJA, NIVO, NAMEN, FUNKCIJE, ODVISNOSTI, SOODVISNOSTI, PREPOVEDI, STATUS, AVTOR, JEZIK, OZNAKE |
| GLOBALNO/render/ | POT, VERZIJA, NIVO, NAMEN, SOODVISNOSTI, PREPOVEDI, DOVOLJENO, STATUS, AVTOR, JEZIK, OZNAKE |
| GLOBALNO/frontend/ | POT, VERZIJA, NIVO, NAMEN, FUNKCIJE, ODVISNOSTI, PREPOVEDI, STATUS, AVTOR, JEZIK, OZNAKE |


## OBVEZNA PHP ZAŠČITA

Vsaka PHP datoteka mora neposredno za headerjem vsebovati:

    declare(strict_types=1);

    defined('SISTEM_VARNOST') or die('Direkten dostop ni dovoljen.');

## PRAVILO V ENI STAVKI

Vsaka PHP ali JavaScript datoteka v sistemu AstraMentalica mora vsebovati standardizirano glavo, ki določa njen arhitekturni nivo, namen, odvisnosti, omejitve in sledljivost ter s tem predstavlja preverljivo arhitekturno pogodbo sistema.

---

## 4. KOMENTARJI

### Kdaj komentirati
Komentiraj **zakaj**, ne **kaj**. Kar dela koda je vidno — zakaj je manj očitno.

```php
// ✅ dober komentar
// Omejimo na 5 poskusov ker po tem blokira IP (varnostna politika)
if ($število_poskusov > 5) {
    blokirajIP($ip);
}

// ❌ slab komentar (pove samo kaj koda počne — to je že vidno)
// Preverimo če je število poskusov večje od 5
if ($število_poskusov > 5) {
    blokirajIP($ip);
}
```

### Blok komentarji za funkcije
```php
/**
 * Preveri ali ima uporabnik pravico do akcije.
 *
 * @param int    $uporabnikId  ID uporabnika
 * @param string $akcija       Ime akcije (npr. 'uredi_profil')
 * @return bool  true = ima pravico, false = nima
 */
function ima_pravico(int $uporabnikId, string $akcija): bool {
    // ...
}
```

### Sekcije znotraj datoteke
```php
// ============================
// INICIALIZACIJA
// ============================

// ============================
// VALIDACIJA
// ============================

// ============================
// SHRAMBA
// ============================
```

### Začasna koda (TODO)
```php
// TODO: Dodati cache ko bo Redis na voljo — [2026-01-15]
// FIXME: Popraviti sanitizacijo posebnih znakov — [2026-02-01]
// HACK: Začasna rešitev — zamenjati z lastnim razredom
```

---

## 5. NAPAKE IN IZJEME

### Osnovno pravilo
```
Nikoli ne pustimo napake da "pade" tiho.
Vsaka napaka: ujeta → zabeležena → sporoči smiselno.
```

### Struktura try/catch
```php
try {
    $rezultat = shraniPodatke($podatki);
} catch (InvalidArgumentException $e) {
    // Pričakovana napaka — slab vnos od uporabnika
    napaka_zabelezi('OPOZORILO', $e->getMessage(), __FILE__, __LINE__);
    napaka_vrni('Neveljavni podatki: ' . $e->getMessage());
} catch (Exception $e) {
    // Nepričakovana napaka — sistemska težava
    napaka_zabelezi('NAPAKA', $e->getMessage(), __FILE__, __LINE__);
    napaka_vrni('Sistemska napaka. Poskusite znova.');
}
```

### Nikoli
```php
// ❌ nikoli
die('Napaka!');
exit(1);

// ❌ nikoli — pokažemo stack trace uporabniku
throw new Exception($e->getMessage());  // z originalnim stack trace

// ✅ pravilno
throw new Exception('Napaka pri shranjevanju profila.');
```

---

## 6. VARNOST — OBVEZNA PRAVILA

### Vhodni podatki
```php
// VSE kar pride od zunaj — sanitiziraj VEDNO, v N1 (prvi nivo -> SISTEM/), takoj
// Nikoli ne zaupaj $_GET, $_POST, $_COOKIE direktno

// ✅ pravilno (v api.php N1)
$ime = sanitizirajBesedilo($_POST['ime'] ?? '');
$id  = filtrirajInt($_GET['id'] ?? 0);

// ❌ napačno (direktna uporaba)
$ime = $_POST['ime'];
$id  = $_GET['id'];
```

### SQL (ko delaš direktno)
```php
// ✅ vedno prepared statements
$stavek = $baza->prepare('SELECT * FROM uporabniki WHERE id = :id');
$stavek->execute([':id' => $id]);

// ❌ nikoli konkatenacija
$poizvedba = "SELECT * FROM uporabniki WHERE id = " . $id;
```

### Seje
```php
// Seje upravljamo samo čez N3 (tretji nivo -> SISTEM/kernel/jedro/) /seja.php
// Nikoli session_start() direktno v logiki ali frontends
zaženiSejo();     // ✅ klic funkcije iz jedra
session_start();  // ❌ direktno
```

---

## 7. DOLŽINE IN MEJE

| Enota | Meja |
|---|---|
| Vrstice na datoteko | max 300 |
| Vrstice na funkcijo | max 40 |
| Parametri funkcije | max 4 (potem array) |
| Gnezdenje (indentacija) | max 4 nivoji |
| Dolžina vrstice | max 120 znakov |

Ko preseže mejo — razdeli.

---

## 8. INDENTACIJA IN FORMATIRANJE

- Indentacija: **4 presledki** (ne tab)
- Oklepaji na **isti vrstici** za funkcije/pogoje:
```php
// ✅
function geslo_preveri(string $geslo): bool {
    if (strlen($geslo) < 8) {
        return false;
    }
    return true;
}

// ❌
function geslo_preveri(string $geslo): bool
{
    if (strlen($geslo) < 8)
    {
        return false;
    }
}
```

- Presledek **pred in po** operatorjih:
```php
$vsota = $a + $b;          // ✅
$vsota=$a+$b;              // ❌
```

- Prazen prostor med logičnimi bloki:
```php
// ✅
$id = (int) $_GET['id'];

$uporabnik = uporabnik_najdi($id);

if (!$uporabnik) {
    napaka_vrni('Uporabnik ne obstaja.');
}

return $uporabnik;
```

---

## 9. VRAČANJE VREDNOSTI

Funkcije vračajo **dosleden tip**:

```php
// ✅ vedno array (prazen array = brez rezultatov, ne false)
function iskanje(string $iskanje): array {
    if (empty($iskanje)) return [];
    // ...
    return $rezultati;
}

// ✅ bool za operacije
function shrani(array $podatki): bool {
    // ...
    return true; // ali false ob napaki
}

// ✅ null samo ko ima semantičen pomen (ni najdeno)
function uporabnik_najdi(int $id): ?array {
    // ...
    return null; // uporabnik ne obstaja
}
```

---

## 10. PRIMER — CELOTNA DATOTEKA

```php
<?php
/**
 * DATOTEKA: prijava.php
 * NAMEN:    Logika prijave uporabnika — preveri credentials, zažene sejo
 * NIVO:     2
 * ODVISNO:  jedro/02_varnost.php, jedro/03_seja.php, baze/shramba.php
 * VERZIJA:  1.0
 * DATUM:    2026-01-01
 */

// ============================
// PREVERJANJE VNOSA
// ============================

/**
 * Preveri in sanitizira prijavne podatke.
 *
 * @param array $vnos  Surovi POST podatki
 * @return array       Čisti podatki ali prazno ob napaki
 */
function prijava_preveri_vnos(array $vnos): array {
    $email = sanitiziraj_email($vnos['email'] ?? '');
    $geslo = sanitiziraj_besedilo($vnos['geslo'] ?? '');

    if (empty($email) || empty($geslo)) {
        return [];
    }

    return ['email' => $email, 'geslo' => $geslo];
}

// ============================
// PRIJAVA
// ============================

/**
 * Izvede prijavo uporabnika.
 *
 * @param array $podatki  Čisti prijavni podatki
 * @return array          Rezultat: ['uspeh' => bool, 'sporocilo' => string]
 */
function uporabnik_prijavi(array $podatki): array {
    $podatki = prijava_preveri_vnos($podatki);

    if (empty($podatki)) {
        return ['uspeh' => false, 'sporocilo' => 'Manjkajoči podatki.'];
    }

    try {
        $uporabnik = uporabnik_najdi_po_emailu($podatki['email']);

        if (!$uporabnik || !varnost_preveri_hash($podatki['geslo'], $uporabnik['geslo_hash'])) {
            // Ne povemo ali je napaka email ali geslo (varnostno)
            return ['uspeh' => false, 'sporocilo' => 'Napačni prijavni podatki.'];
        }

        seja_zacni();
        seja_nastavi_uporabnika($uporabnik['id']);

        return ['uspeh' => true, 'sporocilo' => 'Prijava uspešna.'];

    } catch (Exception $e) {
        napaka_zabelezi('NAPAKA', $e->getMessage(), __FILE__, __LINE__);
        return ['uspeh' => false, 'sporocilo' => 'Sistemska napaka.'];
    }
}
```

---

*STANDARDI.md — verzija 1.0*
