<?php
/**
 * ============================================================
 * POT: MODULI/Modul_Bridge/generator/generiraj_modul.php
 * 📅 VERZIJA: v2.0 (kanonična)
 * ============================================================
 * 
 * 📦 NAMEN:
 *     Ustvari nov modul po kanonični specifikaciji.
 *     - Brez kategorij (modul gre direktno v MODULI/Ime/)
 *     - 3 JSON datoteke (manifest, api, izhod)
 *     - modul.md dokumentacija
 *     - modul.php z Bridge povezavo
 *     - .htaccess zaščita
 * 
 * 🔧 JAVNE FUNKCIJE:
 *     - generiraj_modul(string $ime, string $opis, int $min_vloga): array
 * 
 * 📌 STATUS:
 *     Stabilno
 * 
 * 👤 AVTOR:
 *     AstraMentalica Mojster
 * 
 * 🌐 JEZIK:
 *     sl
 * 
 * 🏷️ OZNAKE:
 *     generator, modul, kanonični
 * ============================================================
 */

declare(strict_types=1);

// ─────────────────────────────────────────────────────────────
// 1. GLAVNA FUNKCIJA
// ─────────────────────────────────────────────────────────────

function generiraj_modul(
    string $ime,
    string $opis,
    int    $min_vloga = 0
): array {
    // ── Validacija ─────────────────────────────────────────────
    if (!preg_match('/^[A-Z][a-zA-Z0-9]+$/', $ime)) {
        return ['uspeh' => false, 'napaka' => 'Ime modula mora biti v PascalCase (npr. Stelaris, Codex).'];
    }

    $id = strtolower($ime);
    $pot_modula = MINI_MODULI . '/' . $ime . '/';
    $pot_podatki = $pot_modula . 'podatki/';
    $datum = date('c');
    $datum_clovek = date('d.m.Y H:i');

    if (is_dir($pot_modula)) {
        return ['uspeh' => false, 'napaka' => "Modul '$ime' že obstaja."];
    }

    // ── Ustvari mape ──────────────────────────────────────────
    if (!mkdir($pot_modula, 0755, true) && !is_dir($pot_modula)) {
        return ['uspeh' => false, 'napaka' => 'Ne morem ustvariti mape modula.'];
    }
    if (!mkdir($pot_podatki, 0755, true) && !is_dir($pot_podatki)) {
        return ['uspeh' => false, 'napaka' => 'Ne morem ustvariti mape podatki/.'];
    }

    // ─────────────────────────────────────────────────────────────
    // 2. manifest.json – KDO SEM
    // ─────────────────────────────────────────────────────────────
    $manifest = [
        '_id' => $id,
        '_verzija' => '1.0.0',

        'modul' => [
            'id' => $id,
            'ime_prikazno' => $ime,
            'ime_izvirno' => $ime,
            'tip' => 'zbiralec',
            'nivo' => 1,
            'verzija' => '1.0.0',
            'aktiviran' => true,
            'vstopna' => 'modul.php',
            'opis' => $opis,
            'status' => 'razvoj',
            'demo' => false,
            'zacasen' => false
        ],

        'dostop' => [
            'minimalna_vloga' => _vloga_int_v_string($min_vloga),
            'plan' => 'osnova',
            'javno_vidno' => true,
            'placljivo' => false,
            'otroski' => false,
            'vidnost' => 'vsi',
            'dovoljenja' => ['branje']
        ],

        'cache' => [
            'omogocen' => true,
            'ttl' => 3600
        ],

        'ui' => [
            'ima_prikaz' => true,
            'ikona' => '✨',
            'barva' => '#818cf8',
            'kategorija' => 'SPLOSNO',
            'dovoljene_postavitve' => ['standard'],
            'tags' => [$id],
            'jeziki' => ['sl']
        ],

        'izvajanje' => [
            'tip' => 'ui',
            'api_only' => false,
            'interval' => null,
            'ob_zagonu' => false,
            'prioriteta' => 100,
            'bootstrap' => null
        ],

        'migracije' => [
            'obstajajo' => false,
            'zadnja' => null
        ],

        'integriteta' => [
            'checksum' => null
        ],

        'log' => [
            'omogocen' => true,
            'nivo' => 'info'
        ],

        'cas' => [
            'ustvarjen' => $datum,
            'posodobljen' => $datum,
            'zadnji_zagon' => null
        ]
    ];

    file_put_json($pot_podatki . 'manifest.json', $manifest);

    // ─────────────────────────────────────────────────────────────
    // 3. api.json – KAKO ME KLIČEŠ
    // ─────────────────────────────────────────────────────────────
    $api = [
        '_id' => $id,
        '_verzija' => '1.0.0',

        'kanali' => ['api'],

        'vstop' => [
            'web' => 'modul.php'
        ],

        'javne_metode' => [
            'info',
            'domov'
        ],

        'http_poti' => [
            '/' . $id . '/info',
            '/' . $id . '/domov'
        ]
    ];

    file_put_json($pot_podatki . 'api.json', $api);

    // ─────────────────────────────────────────────────────────────
    // 4. izhod.json – KAJ POTREBUJEM IN KAJ PROIZVEDEM
    // ─────────────────────────────────────────────────────────────
    $izhod = [
        '_id' => $id,
        '_verzija' => '1.0.0',

        'vhod' => [
            'potrebuje' => [],
            'opcijsko' => [],
            'vir' => 'uporabnik',
            'validacija' => null,
            'omejitve' => [
                'max_velikost' => null
            ]
        ],

        'izhod' => [
            'format' => 'json',
            'pise_v' => [
                'PODATKI/moduli/' . $id . '/'
            ]
        ],

        'odvisnosti' => [
            'bere_iz' => [],
            'prepovedani_moduli' => [],
            'ne_pozna' => 'vse_ostalo',
            'kompatibilnost' => [
                'min_sistem' => '2.0.0',
                'max_sistem' => null
            ]
        ],

        'cache' => [
            'omogocen' => true,
            'ttl' => 3600,
            'strategija' => 'casovna',
            'cisti_ob_zagonu' => false
        ],

        'ui' => [
            'varuh' => null,
            'duhec' => null
        ],

        'dogodki' => [
            'poslusa' => [],
            'oddaja' => []
        ]
    ];

    file_put_json($pot_podatki . 'izhod.json', $izhod);

    // ─────────────────────────────────────────────────────────────
    // 5. modul.md – DOKUMENTACIJA ZA ČLOVEKA
    // ─────────────────────────────────────────────────────────────
    file_put_contents($pot_podatki . 'modul.md', _generiraj_md($ime, $id, $opis, $min_vloga, $datum_clovek));

    // ─────────────────────────────────────────────────────────────
    // 6. modul.php – VSTOPNA TOČKA (z Bridge povezavo)
    // ─────────────────────────────────────────────────────────────
    file_put_contents($pot_modula . 'modul.php', _generiraj_modul_php($ime, $id, $opis));

    // ─────────────────────────────────────────────────────────────
    // 7. .htaccess – ZAŠČITA
    // ─────────────────────────────────────────────────────────────
    file_put_contents($pot_modula . '.htaccess', _generiraj_htaccess());

    // ─────────────────────────────────────────────────────────────
    // 8. .gitkeep – da so mape v GITu
    // ─────────────────────────────────────────────────────────────
    file_put_contents($pot_podatki . '.gitkeep', '');

    return [
        'uspeh' => true,
        'pot' => $pot_modula,
        'ime' => $ime,
        'id' => $id
    ];
}

// ─────────────────────────────────────────────────────────────
// 9. POMOŽNE FUNKCIJE (generatorji vsebine)
// ─────────────────────────────────────────────────────────────

function _generiraj_htaccess(): string {
    return <<<'HTX'
# ---------------------------------------------------------
# .htaccess – varnostna zaščita modula
# ---------------------------------------------------------

# Blokiraj direktni dostop do vseh PHP datotek
<FilesMatch "\.(php|php8|phtml)$">
    Order Deny,Allow
    Deny from all
</FilesMatch>

# Dovoli dostop samo do vstopnih točk
<FilesMatch "^(modul\.php|index\.php)$">
    Order Allow,Deny
    Allow from all
</FilesMatch>

# Dovoli statične datoteke
<FilesMatch "\.(css|js|jpg|jpeg|png|gif|svg|ico|webp|mp3|mp4|woff|woff2|ttf)$">
    Order Allow,Deny
    Allow from all
</FilesMatch>

# Zaščiti konfiguracijske datoteke
<FilesMatch "\.(json|yml|yaml|xml|ini|env)$">
    Order Deny,Allow
    Deny from all
</FilesMatch>
HTX;
}

function _generiraj_modul_php(string $ime, string $id, string $opis): string {
    return <<<PHP
<?php
/**
 * ============================================================
 * MODUL: $ime
 * POT: MODULI/$ime/modul.php
 * 📅 VERZIJA: 1.0.0
 * ============================================================
 *
 * 📦 NAMEN:
 *     $opis
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - modul_{$id}_akcija(string \$akcija, array \$podatki): array
 *
 * 📡 ODVISNOSTI:
 *     - Modul_Bridge (demo ali pravi sistem)
 *
 * 🚫 PREPOVEDI:
 *     - Brez direktnih klicev v SISTEM/
 *     - Brez branja \$_SESSION, \$_GET, \$_POST
 *     - Brez pisanja v PODATKI/ (razen svojih map preko Bridge-a)
 *     - Brez __DIR__ izven lastne mape
 *
 * 🌐 JEZIK:
 *     sl
 * ============================================================
 */

declare(strict_types=1);

// ── POIŠČI BRIDGE ──────────────────────────────────────────
\$bridgePoti = [
    __DIR__ . '/../Modul_Bridge/modul_bridge.php',
    __DIR__ . '/../../Modul_Bridge/modul_bridge.php',
    __DIR__ . '/../../../Modul_Bridge/modul_bridge.php',
];

\$bridgeNajden = false;
foreach (\$bridgePoti as \$pot) {
    if (file_exists(\$pot)) {
        require_once \$pot;
        \$bridgeNajden = true;
        break;
    }
}

if (!\$bridgeNajden) {
    // Fallback – če Bridge ne obstaja, vrni napako
    header('Content-Type: application/json');
    echo json_encode(['napaka' => 'Modul_Bridge ni najden']);
    exit;
}

// ── STANDARDNI ODZIVI ──────────────────────────────────────
if (!function_exists('odziv_uspeh')) {
    function odziv_uspeh(array \$vsebina, string \$sporocilo = ''): array {
        return ['status' => 'uspeh', 'status_koda' => 200, 'sporocilo' => \$sporocilo, 'vsebina' => \$vsebina];
    }
    function odziv_napaka(string \$sporocilo, int \$koda = 400): array {
        return ['status' => 'napaka', 'status_koda' => \$koda, 'sporocilo' => \$sporocilo, 'vsebina' => []];
    }
}

// ============================
// VSTOPNA TOČKA MODULA
// ============================

/**
 * Glavna funkcija modula — kliče jo SISTEM preko Modul_Bridge.
 *
 * @param string \$akcija   Kaj modul mora narediti
 * @param array  \$podatki  Parametri za akcijo
 * @return array           Rezultat (vedno array, nikoli HTML)
 */
function modul_{$id}_akcija(string \$akcija, array \$podatki = []): array {
    // Preveri dostop preko Bridge-a
    if (!Modul_Bridge::vloga_preveri('S0')) {
        return ['napaka' => 'Dostop zavrnjen'];
    }

    return match(\$akcija) {
        'info'  => _modul_{$id}_info(\$podatki),
        'domov' => _modul_{$id}_domov(\$podatki),
        default => odziv_napaka("Neznana akcija: \$akcija", 400),
    };
}

// ============================
// AKCIJE
// ============================

function _modul_{$id}_info(array \$podatki): array {
    \$uporabnik = Modul_Bridge::uporabnik_pridobi();

    return odziv_uspeh([
        'ime' => '$ime',
        'id' => '$id',
        'verzija' => '1.0.0',
        'opis' => '$opis',
        'uporabnik' => \$uporabnik['ime'] ?? 'Gost',
    ], 'Informacije o modulu');
}

function _modul_{$id}_domov(array \$podatki): array {
    return odziv_uspeh([
        'sporocilo' => 'Pozdravljen v modulu $ime!',
        'cas' => time(),
    ], 'Domov');
}

// ── ČE SE KLIČE DIREKTNO (brez Bridge-a) ──────────────────
if (basename(\$_SERVER['SCRIPT_FILENAME']) === 'modul.php' && !defined('SISTEM_OBSTAJA')) {
    \$akcija = \$_REQUEST['akcija'] ?? 'domov';
    \$podatki = \$_REQUEST;
    \$odziv = modul_{$id}_akcija(\$akcija, \$podatki);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(\$odziv, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}
PHP;
}

function _generiraj_md(string $ime, string $id, int $min_vloga, string $datum): string {
    $vloga = _vloga_int_v_string($min_vloga);
    return <<<MD
# $ime

**ID:** $id
**Verzija:** 1.0.0
**Tip:** zbiralec
**Nivo:** 1
**Status:** razvoj

---

## Avtor

AstraMentalica

---

## Licenca

Zaprta koda

---

## Opis

$ime — $ime modul.

---

## Dostop

- **Minimalna vloga:** $vloga
- **Plan:** osnova
- **Javno vidno:** Da
- **Plačljivo:** Ne
- **Otroški:** Ne
- **Vidnost:** vsi
- **Dovoljenja:** branje

---

## UI

- **Ima prikaz:** Da
- **Ikona:** ✨
- **Barva:** #818cf8
- **Kategorija:** SPLOSNO
- **Dovoljene postavitve:** standard
- **Tags:** $id
- **Jeziki:** sl
- **Varuh:** (ni)
- **Duhec:** (ni)

---

## Odvisnosti

- **Bere iz:** (nič)
- **Prepovedani moduli:** (nič)
- **Ne pozna:** vse ostalo
- **Kompatibilnost:** Sistem >=2.0.0

---

## Izvajanje

- **Tip:** ui
- **API only:** Ne
- **Prioriteta:** 100

---

## Namestitev

1. Kopiraj mapo modula v `MODULI/$ime/`
2. Aktiviraj modul v sistemu
3. Poženi `php ASTRA/razvoj/orodja/generator.php --full`

---

## Testirano na

- Sistem 2.0.0
- PHP 8.1, 8.2, 8.3

---

## Changelog

### 1.0.0 ($datum)
- Inicializacija modula z Modul Bridge

---

## Uporaba

```bash
curl http://example.com/$id/info
curl http://example.com/$id/domov