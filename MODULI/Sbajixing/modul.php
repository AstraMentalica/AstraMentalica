<?php
/**
 * MODUL: Sbajixing
 * POT: MODULI/Sbajixing/modul.php
 * VERZIJA: 1.0.0 (24.6.2026)
 *
 * 🏛️ NIVO: MODUL
 *
 * 📰 NAMEN:
 *     Standardna vstopna točka za Sbajixing modul
 *     Avtomatsko generirano
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - modul_sbajixing_akcija(string $akcija, array $podatki): array
 *
 * 👤 AVTOR:
 *     AstraMentalica Mojster (avtomatsko)
 *
 * 🌐 JEZIK:
 *     sl
 * ============================================================
 */

declare(strict_types=1);

if (!defined('BRIDGE_VARNOST') && !defined('SISTEM_VARNOST')) {
    die('Direktni dostop je prepovedan');
}

// ============================
// VSTOPNA TOČKA MODULA
// ============================

function modul_sbajixing_akcija(string $akcija, array $podatki = []): array {
    if (!Modul_Bridge::vloga_preveri('S0')) {
        return odziv_napaka('Dostop zavrnjen', 403);
    }

    return match($akcija) {
        'info'  => _modul_sbajixing_akcija_info($podatki),
        'domov' => _modul_sbajixing_akcija_domov($podatki),
        default => odziv_napaka("Neznana akcija: $akcija", 400),
    };
}

// ============================
// AKCIJE
// ============================

function _modul_sbajixing_akcija_info(array $podatki): array {
    return odziv_uspeh([
        'ime'       => 'Sbajixing',
        'id'        => 'sbajixing',
        'verzija'   => '1.0.0',
        'opis'      => 'Modul Sbajixing',
        'uporabnik' => Modul_Bridge::uporabnik_pridobi()['ime'] ?? 'Gost',
    ], 'Informacije o modulu');
}

function _modul_sbajixing_akcija_domov(array $podatki): array {
    return odziv_uspeh([
        'sporocilo' => 'Pozdravljen v modulu Sbajixing!',
        'cas'       => time(),
    ], 'Domov');
}

// ── DIREKTEN KLIC ──────────────────
if (basename($_SERVER['SCRIPT_FILENAME'] ?? '') === 'modul.php' && !defined('SISTEM_OBSTAJA')) {
    $akcija  = $_REQUEST['akcija'] ?? 'domov';
    $podatki = $_REQUEST;
    $odziv   = modul_sbajixing_akcija($akcija, $podatki);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($odziv, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}