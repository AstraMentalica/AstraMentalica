<?php
/**
 * ============================================================
 * POT: SISTEM/kernel/zaganjalnik.php
 * 📅 VERZIJA: v118 (19.6.2026 00:30)
 * ============================================================
 *
 * 🏛️ NIVO: KERNEL (N3)
 *
 * 📰 NAMEN:
 *     Bootstrap sistema – naloži jedro, knjižnice in pomožne datoteke.
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - zaganjalnik_izvedi(array $zahteva): array
 *     - zaganjalnik_pridobi_faze(): array
 *     - zaganjalnik_pridobi_zaznamke(): array
 *
 * 📡 ODVISNOSTI:
 *     - pot.php
 *     - SISTEM/kernel/nastavitve.php
 *     - SISTEM/kernel/knjiznice/avtonalagalnik.php
 *     - SISTEM/kernel/jedro/*.php
 *
 * 🚫 PREPOVEDI:
 *     - Brez die(), exit()
 *     - Brez echo, print_r, var_dump
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
 *     kernel, bootstrap, zaganjalnik
 * ============================================================
 */
declare(strict_types=1);

// VARNOST – namesto die() uporabimo return
if (!defined('SISTEM_VARNOST')) {
    http_response_code(403);
    header('Location: /');
    return;
}

// ============================================================
// NALOŽI NASTAVITVE IN KNJIŽNICE
// ============================================================
require_once __DIR__ . '/nastavitve.php';
require_once __DIR__ . '/env_loader.php';

// POT_SEF definira env_loader.php že sam. Ne nalagamo dvakrat.
// env_loader.php se požene spodaj prek avtonalagalnika in sam
// prebere .env, .env_api, .env_baza iz POT_SEF.

require_once __DIR__ . '/knjiznice/avtonalagalnik.php';

// ============================================================
// KONFIGURACIJA FAZ
// ============================================================

$GLOBALS['ZAGANJALNIK_FAZE'] = [
    '01_upravljalec_svetov',
    '02_napake',
    '03_varnost',
    '04_seja',
    '05_pravice',
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
// GLAVNA FUNKCIJA
// ============================================================

function zaganjalnik_izvedi(array $zahteva): array
{
    _zaganjalnik_nalozi_jedro();
    _zaganjalnik_nalozi_baze();
    _zaganjalnik_nalozi_knjiznice();

    $zahteva['sistem']['jedro_zagnano'] = true;
    $zahteva['sistem']['zaznamki']      = $GLOBALS['ZAGANJALNIK_ZAZNAMKI'];

    return [
        'status'      => 'uspeh',
        'status_koda' => 200,
        'sporocilo'   => 'Jedro zagnano.',
        'vsebina'     => $zahteva,
    ];
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
        } elseif (NASTAVITVE_DEBUG) {
            error_log("[ZAGANJALNIK] Manjka faza: $faza");
        }
    }
}

function _zaganjalnik_nalozi_baze(): void
{
    // Najprej naloži adapterje in interface-e
    $adapterji = [
        POT_BAZE . '/interface',
        POT_BAZE . '/adapter_json.php',
        POT_BAZE . '/adapter_sqlite.php',
        POT_BAZE . '/adapter_mysql.php',
        POT_BAZE . '/query_builder.php',
    ];

    foreach ($adapterji as $pot) {
        if (is_dir($pot)) {
            foreach (glob($pot . '/*.php') as $datoteka) {
                require_once $datoteka;
            }
        } elseif (file_exists($pot)) {
            require_once $pot;
        }
    }

    // Nato naloži upravljalec (ki instantira adapterje)
    $potBaza = POT_BAZE . '/upravljalec_baz.php';
    if (file_exists($potBaza)) {
        require_once $potBaza;
    }
}

function _zaganjalnik_nalozi_knjiznice(): void
{
    // Samodejno naloži vse pomožne knjižnice iz mape
    // (npr. tiste v SISTEM/kernel/knjiznice/, razen avtonalagalnika samega)
    $potKnjiznic = __DIR__ . '/knjiznice';
    if (is_dir($potKnjiznic)) {
        // Preskočimo avtonalagalnik, da ne bi nalagali dvakrat
        avto_nalozi_mapo($potKnjiznic, 'knjiznica', ['avtonalagalnik.php']);
    }
}

function zaganjalnik_pridobi_faze(): array
{
    return $GLOBALS['ZAGANJALNIK_FAZE'];
}

function zaganjalnik_pridobi_zaznamke(): array
{
    return $GLOBALS['ZAGANJALNIK_ZAZNAMKI'];
}