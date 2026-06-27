<?php
/**
 * ============================================================
 * POT: MODULI/Modul_Bridge/generator/generiraj_modul.php
 * 📅 VERZIJA: v119 (19.6.2026 03:00)
 * ============================================================
 *
 * 🏛️ NIVO: BRIDGE / GENERATOR
 *
 * 📰 NAMEN:
 *     Ustvari nov modul po ZAKLENJENI kanonični specifikaciji.
 *     Generira: modul.php + podatki/manifest.json + podatki/api.json
 *               + podatki/izhod.json + podatki/modul.md
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - generiraj_modul(string $ime, string $opis, array $opcije = []): array
 *
 * 📌 STATUS:
 *     Stabilno
 *
 * 📅 ZGODOVINA:
 *     - v119: zaklenjena kanonična specifikacija (manifest+api+izhod+md)
 *     - v118: nova struktura (brez kategorij, s podatki/ mapo)
 *     - v114: stara struktura (s kategorijami)
 *
 * 👤 AVTOR:
 *     AstraMentalica Mojster
 *
 * 🌐 JEZIK:
 *     sl
 *
 * 🏷️ OZNAKE:
 *     bridge, generator, modul
 * ============================================================
 */

declare(strict_types=1);

/**
 * Ustvari nov modul po zaklenjeni specifikaciji.
 *
 * @param string $ime     PascalCase ime modula (npr. "Stelaris")
 * @param string $opis    Kratek opis modula
 * @param array  $opcije  Neobvezne nastavitve, glej privzete spodaj
 * @return array           ['uspeh' => bool, 'pot' => string, ...] ali ['uspeh' => false, 'napaka' => string]
 */
function generiraj_modul(string $ime, string $opis, array $opcije = []): array {
    // ── Validacija ─────────────────────────────────────────
    if (!preg_match('/^[A-Z][a-zA-Z0-9]+$/', $ime)) {
        return ['uspeh' => false, 'napaka' => 'Ime modula mora biti v PascalCase (npr. Stelaris, OrakleumTarot).'];
    }

    $pot_modula  = MINI_MODULI . '/' . $ime . '/';
    $pot_podatki = $pot_modula . 'podatki/';

    if (is_dir($pot_modula)) {
        return ['uspeh' => false, 'napaka' => "Modul '$ime' že obstaja."];
    }

    // ── Privzete opcije (vse polja iz zaklenjene specifikacije) ──
    $o = array_merge([
        'id'                    => strtolower($ime),
        'tip'                   => 'zbiralec',          // zbiralec | sestavljalec | izvajalec
        'nivo'                  => 1,
        'verzija'               => '1.0.0',
        'status'                => 'testni',            // testni | stabilen | zastarel
        'demo'                  => false,
        'zacasen'               => false,

        'minimalna_vloga'       => 'S0',
        'plan'                  => 'osnova',
        'javno_vidno'           => true,
        'placljivo'             => false,
        'otroski'               => false,
        'vidnost'               => 'vsi',                // vsi | prijavljeni | skriti | admin
        'dovoljenja'            => ['branje'],

        'ima_prikaz'            => true,
        'ikona'                 => '✨',
        'barva'                 => '#818cf8',
        'kategorija'            => 'SIMBOLI',
        'dovoljene_postavitve'  => ['standard'],
        'tags'                  => [],
        'jeziki'                => ['sl'],
        'varuh'                 => null,
        'duhec'                 => null,

        'izvajanje_tip'         => 'ui',                 // ui | cron | api_only
        'api_only'              => false,
        'interval'              => null,
        'ob_zagonu'             => false,
        'prioriteta'            => 50,
        'bootstrap'             => null,

        'cache_omogocen'        => true,
        'cache_ttl'             => 3600,
        'cache_strategija'      => 'parameter',          // parameter | casovna | brez
        'cisti_ob_zagonu'       => false,

        'log_omogocen'          => true,
        'log_nivo'              => 'info',

        'potrebuje'             => [],
        'opcijsko_vhod'         => [],
        'vir'                   => 'uporabnik',          // uporabnik | zunanji_api | sistem
        'pise_v'                => null,                 // null → privzeto PODATKI/moduli/{id}/
        'bere_iz'               => [],
        'min_sistem'            => '2.0.0',

        'avtor'                 => 'AstraMentalica Mojster',
        'licenca'               => 'Zaprta koda',
    ], $opcije);

    $id    = $o['id'];
    $datum = date('d.m.Y H:i');
    $zdaj  = date('Y-m-d\TH:i:s\Z');

    // ── Ustvari mape ──────────────────────────────────────
    if (!mkdir($pot_modula, 0755, true) && !is_dir($pot_modula)) {
        return ['uspeh' => false, 'napaka' => 'Ne morem ustvariti mape modula.'];
    }
    if (!mkdir($pot_podatki, 0755, true) && !is_dir($pot_podatki)) {
        return ['uspeh' => false, 'napaka' => 'Ne morem ustvariti mape podatki/.'];
    }

    // ── 1. podatki/manifest.json — KDO SEM ────────────────
    $manifest = [
        '_id'      => $id,
        '_verzija' => $o['verzija'],

        'modul' => [
            'id'        => $id,
            'ime'       => $ime,
            'tip'       => $o['tip'],
            'nivo'      => $o['nivo'],
            'verzija'   => $o['verzija'],
            'aktiviran' => true,
            'vstopna'   => 'modul.php',
            'opis'      => $opis,
            'status'    => $o['status'],
            'demo'      => $o['demo'],
            'zacasen'   => $o['zacasen'],
        ],

        'dostop' => [
            'minimalna_vloga' => $o['minimalna_vloga'],
            'plan'            => $o['plan'],
            'javno_vidno'     => $o['javno_vidno'],
            'placljivo'       => $o['placljivo'],
            'otroski'         => $o['otroski'],
            'vidnost'         => $o['vidnost'],
            'dovoljenja'      => $o['dovoljenja'],
        ],

        'cache' => [
            'omogocen' => $o['cache_omogocen'],
            'ttl'      => $o['cache_ttl'],
        ],

        'ui' => [
            'ima_prikaz'           => $o['ima_prikaz'],
            'ikona'                => $o['ikona'],
            'barva'                => $o['barva'],
            'kategorija'           => $o['kategorija'],
            'dovoljene_postavitve' => $o['dovoljene_postavitve'],
            'tags'                 => $o['tags'],
            'jeziki'               => $o['jeziki'],
        ],

        'izvajanje' => [
            'tip'        => $o['izvajanje_tip'],
            'api_only'   => $o['api_only'],
            'interval'   => $o['interval'],
            'ob_zagonu'  => $o['ob_zagonu'],
            'prioriteta' => $o['prioriteta'],
            'bootstrap'  => $o['bootstrap'],
        ],

        'migracije' => [
            'obstajajo' => false,
            'zadnja'    => null,
        ],

        'integriteta' => [
            'checksum' => null,
        ],

        'log' => [
            'omogocen' => $o['log_omogocen'],
            'nivo'     => $o['log_nivo'],
        ],

        'cas' => [
            'ustvarjen'   => $zdaj,
            'posodobljen' => $zdaj,
            'zadnji_zagon' => null,
        ],
    ];
    file_put_contents(
        $pot_podatki . 'manifest.json',
        json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
    );

    // ── 2. podatki/api.json — KAKO ME KLIČEŠ ──────────────
    $api = [
        '_id'      => $id,
        '_verzija' => $o['verzija'],

        'kanali' => ['api'],

        'vstop' => array_filter([
            'web'  => 'modul.php',
            'cron' => $o['izvajanje_tip'] === 'cron' ? 'cron.php' : null,
        ]),

        'javne_metode' => [],

        'http_poti' => [
            '/' . $id . '/info',
            '/' . $id . '/domov',
        ],
    ];
    file_put_contents(
        $pot_podatki . 'api.json',
        json_encode($api, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
    );

    // ── 3. podatki/izhod.json — KAJ POTREBUJEM IN PROIZVEDEM ──
    $pise_v = $o['pise_v'] ?? ['PODATKI/moduli/' . $id . '/'];

    $izhod = [
        '_id'      => $id,
        '_verzija' => $o['verzija'],

        'vhod' => [
            'potrebuje' => $o['potrebuje'],
            'opcijsko'  => $o['opcijsko_vhod'],
            'vir'       => $o['vir'],
            'validacija' => null,
            'omejitve'  => [
                'max_velikost' => null,
            ],
        ],

        'izhod' => [
            'format' => 'json',
            'pise_v' => $pise_v,
        ],

        'odvisnosti' => [
            'bere_iz'             => $o['bere_iz'],
            'prepovedani_moduli'  => [],
            'ne_pozna'            => 'vse_ostalo',
            'kompatibilnost'      => [
                'min_sistem' => $o['min_sistem'],
                'max_sistem' => null,
            ],
        ],

        'cache' => [
            'omogocen'         => $o['cache_omogocen'],
            'ttl'              => $o['cache_ttl'],
            'strategija'       => $o['cache_strategija'],
            'cisti_ob_zagonu'  => $o['cisti_ob_zagonu'],
        ],

        'ui' => [
            'varuh'  => $o['varuh'],
            'duhec'  => $o['duhec'],
        ],

        'dogodki' => [
            'poslusa' => [],
            'oddaja'  => [],
        ],
    ];
    file_put_contents(
        $pot_podatki . 'izhod.json',
        json_encode($izhod, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
    );

    // ── 4. podatki/modul.md — DOKUMENTACIJA ZA ČLOVEKA ────
    file_put_contents(
        $pot_podatki . 'modul.md',
        _generator_modul_md($ime, $id, $opis, $o, $datum)
    );

    // ── 5. modul.php ──────────────────────────────────────
    file_put_contents($pot_modula . 'modul.php', _generator_modul_php($ime, $id, $opis, $datum));

    // ── 6. .htaccess ──────────────────────────────────────
    file_put_contents($pot_modula . '.htaccess', _generator_htaccess());

    // ── 7. .gitkeep v podatki/ (in cache/, temp/ ker so dovoljene mape) ──
    file_put_contents($pot_podatki . '.gitkeep', '');
    mkdir($pot_modula . 'cache/', 0755, true);
    mkdir($pot_modula . 'temp/', 0755, true);
    file_put_contents($pot_modula . 'cache/.gitkeep', '');
    file_put_contents($pot_modula . 'temp/.gitkeep', '');

    return [
        'uspeh' => true,
        'pot'   => $pot_modula,
        'ime'   => $ime,
        'id'    => $id,
    ];
}

// ── ZASEBNI GENERATORJI VSEBINE ─────────────────────────────

function _generator_htaccess(): string {
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
<FilesMatch "\.(json|yml|yaml|xml|ini|env|md)$">
    Order Deny,Allow
    Deny from all
</FilesMatch>
HTX;
}

function _generator_modul_md(string $ime, string $id, string $opis, array $o, string $datum): string {
    $tagsStr        = empty($o['tags']) ? '(ni)' : implode(', ', $o['tags']);
    $jezikiStr      = implode(', ', $o['jeziki']);
    $postavitveStr  = implode(', ', $o['dovoljene_postavitve']);
    $dovoljenjaStr  = implode(', ', $o['dovoljenja']);
    $varuhStr       = $o['varuh'] ?? '(ni)';
    $duhecStr       = $o['duhec'] ?? '(ni)';
    $bereIzStr      = empty($o['bere_iz']) ? '(nič)' : implode(', ', $o['bere_iz']);

    // Heredoc ne zna klicati funkcij v interpolaciji – izračunaj vnaprej
    $javnoVidnoStr = $o['javno_vidno'] ? 'Da' : 'Ne';
    $placljivoStr  = $o['placljivo']   ? 'Da' : 'Ne';
    $otroskiStr    = $o['otroski']     ? 'Da' : 'Ne';
    $imaPrikazStr  = $o['ima_prikaz']  ? 'Da' : 'Ne';
    $apiOnlyStr    = $o['api_only']    ? 'Da' : 'Ne';

    return <<<MD
# {$ime}

**ID:** {$id}
**Verzija:** {$o['verzija']}
**Tip:** {$o['tip']}
**Nivo:** {$o['nivo']}
**Status:** {$o['status']}

---

## Avtor

{$o['avtor']}

---

## Licenca

{$o['licenca']}

---

## Opis

{$opis}

---

## Dostop

- **Minimalna vloga:** {$o['minimalna_vloga']}
- **Plan:** {$o['plan']}
- **Javno vidno:** {$javnoVidnoStr}
- **Plačljivo:** {$placljivoStr}
- **Otroški:** {$otroskiStr}
- **Vidnost:** {$o['vidnost']}
- **Dovoljenja:** {$dovoljenjaStr}

---

## UI

- **Ima prikaz:** {$imaPrikazStr}
- **Ikona:** {$o['ikona']}
- **Barva:** {$o['barva']}
- **Kategorija:** {$o['kategorija']}
- **Dovoljene postavitve:** {$postavitveStr}
- **Tags:** {$tagsStr}
- **Jeziki:** {$jezikiStr}
- **Varuh:** {$varuhStr}
- **Duhec:** {$duhecStr}

---

## Odvisnosti

- **Bere iz:** {$bereIzStr}
- **Prepovedani moduli:** (nič)
- **Ne pozna:** vse ostalo
- **Kompatibilnost:** Sistem >={$o['min_sistem']}

---

## Izvajanje

- **Tip:** {$o['izvajanje_tip']}
- **API only:** {$apiOnlyStr}
- **Prioriteta:** {$o['prioriteta']}

---

## Namestitev

1. Kopiraj mapo modula v `MODULI/{$ime}/`
2. Aktiviraj modul v sistemu (registriraj v `PODATKI/registri/moduli_register.json`)
3. Poženi `php ASTRA/razvoj/orodja/generator.php --full`

---

## Testirano na

- Sistem >={$o['min_sistem']}
- PHP 8.1, 8.2, 8.3

---

## Changelog

### {$o['verzija']} ({$datum})
- Prva izdaja

---

## Uporaba

```bash
curl http://example.com/{$id}/info
curl http://example.com/{$id}/domov
```

## Primeri

### Dobi informacije o modulu

```bash
curl -X GET http://example.com/{$id}/info
```
MD;
}

function _generator_modul_php(string $ime, string $id, string $opis, string $datum): string {
    return <<<PHP
<?php
/**
 * ============================================================
 * MODUL: $ime
 * POT: MODULI/$ime/modul.php
 * 📅 VERZIJA: v1.0.0 ($datum)
 * ============================================================
 *
 * 🏛️ NIVO: MODUL
 *
 * 📰 NAMEN:
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
 *     - Brez branja \$_SESSION
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
 *     modul, $id
 * ============================================================
 */

declare(strict_types=1);

// ── POIŠČI BRIDGE (modul ne pozna sidra, samo Modul_Bridge) ──
\$bridgePoti = [
    __DIR__ . '/../Modul_Bridge/modul_bridge.php',
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
    if (!Modul_Bridge::vloga_preveri('S0')) {
        return odziv_napaka('Dostop zavrnjen', 403);
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
        'ime'       => '$ime',
        'id'        => '$id',
        'verzija'   => '1.0.0',
        'opis'      => '$opis',
        'uporabnik' => \$uporabnik['ime'] ?? 'Gost',
    ], 'Informacije o modulu');
}

function _modul_{$id}_domov(array \$podatki): array {
    return odziv_uspeh([
        'sporocilo' => 'Pozdravljen v modulu $ime!',
        'cas'       => time(),
    ], 'Domov');
}

// ── ČE SE KLIČE DIREKTNO (brez Bridge-a) ──────────────────
if (basename(\$_SERVER['SCRIPT_FILENAME'] ?? '') === 'modul.php' && !defined('SISTEM_OBSTAJA')) {
    \$akcija  = \$_REQUEST['akcija'] ?? 'domov';
    \$podatki = \$_REQUEST;
    \$odziv   = modul_{$id}_akcija(\$akcija, \$podatki);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(\$odziv, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}
PHP;
}
