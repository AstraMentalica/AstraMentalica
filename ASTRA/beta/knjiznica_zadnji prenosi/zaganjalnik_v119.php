<?php
/**
 * ============================================================
 * POT: SISTEM/kernel/zaganjalnik.php
 * 📅 VERZIJA: v119 (28.6.2026)
 * ============================================================
 *
 * 🏛️ NIVO: KERNEL (N3)
 *
 * 📰 NAMEN:
 *     Bootstrap sistema. Eksplicitni vrstni red zagona:
 *
 *         1. nastavitve.php   → sistemske konstante (debug, TTL, ...)
 *         2. env_loader.php   → naloži .env_* iz POT_SEF
 *         3. avtonalagalnik   → registrira razrede
 *         4. jedro/*.php      → faze 01–15 (upravljalec_svetov, seja, pravice ...)
 *         5. baze/*           → adapterji in upravljalec
 *         6. knjiznice/*      → pomožne knjižnice
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - zaganjalnik_izvedi(array $zahteva): array
 *     - zaganjalnik_pridobi_faze(): array
 *     - zaganjalnik_pridobi_zaznamke(): array
 *
 * 📡 ODVISNOSTI:
 *     - pot.php  (mora biti naložen pred klicem tega fajla)
 *     - SISTEM/kernel/nastavitve.php
 *     - SISTEM/kernel/env_loader.php
 *     - SISTEM/kernel/knjiznice/avtonalagalnik.php
 *     - SISTEM/kernel/jedro/*.php
 *
 * 🚫 PREPOVEDI:
 *     - Brez die(), exit()
 *     - Brez echo, print_r, var_dump
 *     - Brez __DIR__ (vse poti prek konstant iz pot.php)
 *
 * 📌 STATUS:
 *     Stabilno
 *
 * 📅 ZGODOVINA:
 *     - v119: eksplicitni vrstni red nastavitve→env→jedro;
 *             env_loader premaknjeno PRED avtonalagalnik, da so
 *             .env vrednosti na voljo razredom že pri registraciji;
 *             dodan opozorilni log če POT_SEF kaže znotraj public_html
 *     - v118: združitev s pot.php v118
 *     - v117: uskladitev s Header Standard v117
 *
 * 👤 AVTOR:
 *     AstraMentalica Mojster
 *
 * 🌐 JEZIK:
 *     sl
 *
 * 🏷️ OZNAKE:
 *     kernel, bootstrap, zaganjalnik
 * ============================================================
 */
declare(strict_types=1);

if (!defined('SISTEM_VARNOST')) {
    http_response_code(403);
    header('Location: /');
    return;
}

// ============================================================
// KORAK 1 – SISTEMSKE NASTAVITVE (konstante, ne logika)
// ============================================================
require_once POT_KERNEL . '/nastavitve.php';

// ============================================================
// KORAK 2 – ENV LOADER (prebere .env_api, .env_baza, .env_sistem)
//
// Naloži se takoj po nastavitvah, ker vse naslednje faze
// (avtonalagalnik, jedro, baze) lahko potrebujejo env vrednosti.
//
// POT_SEF je določen v pot.php – kaže na mapo IZVEN public_html
// (npr. /home/user/sef/). Če POT_SEF_IZVEN === false, to pomeni
// fallback na PODATKI/sef/ – sprejemljivo za razvoj, NE za produkcijo.
// ============================================================
require_once POT_KERNEL . '/env_loader.php';

// Opozorilo v developerskem načinu če sef ni izven weba
if (defined('RAZVOJNI_NACIN') && RAZVOJNI_NACIN && defined('POT_SEF_IZVEN') && !POT_SEF_IZVEN) {
    error_log('[ZAGANJALNIK] OPOZORILO: POT_SEF ni nastavljen (ASTRA_SEF_PATH).'
        . ' .env datoteke se berejo iz ' . POT_SEF . ' (znotraj projekta).'
        . ' Za produkcijo nastavi ASTRA_SEF_PATH na pot izven public_html.');
}

// ============================================================
// KORAK 3 – AVTONALAGALNIK RAZREDOV
// ============================================================
require_once POT_KERNEL . '/knjiznice/avtonalagalnik.php';

// ============================================================
// FAZE JEDRA (vrstni red je obvezen – ne menjaj)
// ============================================================
$GLOBALS['ZAGANJALNIK_FAZE'] = [
    '01_upravljalec_svetov',   // whitelist svetov + moduli po vlogi
    '02_napake',               // error handler
    '03_varnost',              // CSRF, sanitizacija
    '04_seja',                 // session management
    '05_pravice',              // RBAC
    '06_cache',
    '07_dogodki',
    '08_kavlji',
    '09_ponudniki',
    '10_middleware',
    '11_usmerjevalnik',
    '12_validacija',
    '13_api',
    '14_zagon',
    '15_pogon',
];

$GLOBALS['ZAGANJALNIK_ZAZNAMKI'] = [];

// ============================================================
// JAVNI VMESNIK
// ============================================================

function zaganjalnik_izvedi(array $zahteva): array
{
    _zaganjalnik_nalozi_jedro();
    _zaganjalnik_nalozi_baze();
    _zaganjalnik_nalozi_knjiznice();

    $zahteva['sistem']['jedro_zagnano'] = true;
    $zahteva['sistem']['zaznamki']      = $GLOBALS['ZAGANJALNIK_ZAZNAMKI'];
    $zahteva['sistem']['sef_izven']     = defined('POT_SEF_IZVEN') && POT_SEF_IZVEN;

    return [
        'status'      => 'uspeh',
        'status_koda' => 200,
        'sporocilo'   => 'Jedro zagnano.',
        'vsebina'     => $zahteva,
    ];
}

function zaganjalnik_pridobi_faze(): array
{
    return $GLOBALS['ZAGANJALNIK_FAZE'];
}

function zaganjalnik_pridobi_zaznamke(): array
{
    return $GLOBALS['ZAGANJALNIK_ZAZNAMKI'];
}

// ============================================================
// INTERNE FUNKCIJE
// ============================================================

function _zaganjalnik_nalozi_jedro(): void
{
    foreach ($GLOBALS['ZAGANJALNIK_FAZE'] as $faza) {
        $pot = POT_JEDRO . '/' . $faza . '.php';
        if (file_exists($pot)) {
            $GLOBALS['ZAGANJALNIK_ZAZNAMKI'][$faza] = microtime(true);
            require_once $pot;
            $GLOBALS['ZAGANJALNIK_ZAZNAMKI'][$faza . '_konec'] = microtime(true);
        } elseif (defined('NASTAVITVE_DEBUG') && NASTAVITVE_DEBUG) {
            error_log("[ZAGANJALNIK] Manjka faza: $faza ($pot)");
        }
    }
}

function _zaganjalnik_nalozi_baze(): void
{
    $adapterji = [
        POT_BAZE . '/interface',
        POT_BAZE . '/adapter_json.php',
        POT_BAZE . '/adapter_sqlite.php',
        POT_BAZE . '/adapter_mysql.php',
        POT_BAZE . '/query_builder.php',
    ];

    foreach ($adapterji as $pot) {
        if (is_dir($pot)) {
            foreach (glob($pot . '/*.php') ?: [] as $datoteka) {
                require_once $datoteka;
            }
        } elseif (file_exists($pot)) {
            require_once $pot;
        }
    }

    $potBaza = POT_BAZE . '/upravljalec_baz.php';
    if (file_exists($potBaza)) {
        require_once $potBaza;
    }
}

function _zaganjalnik_nalozi_knjiznice(): void
{
    $potKnjiznic = POT_KERNEL . '/knjiznice';
    if (is_dir($potKnjiznic) && function_exists('avto_nalozi_mapo')) {
        avto_nalozi_mapo($potKnjiznic, 'knjiznica', ['avtonalagalnik.php']);
    }
}
