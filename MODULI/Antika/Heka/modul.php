<?php
/**
 * ============================================================
 * MODUL: Heka
 * POT: MODULI/Antika/Heka/modul.php
 * KATEGORIJA: Antika
 * 📅 VERZIJA: v1.0.0 (27.06.2026 13:29)
 * ============================================================
 *
 * 🏛️ NIVO: MODUL
 *
 * 📰 NAMEN:
 *     Magija in zaščita — egipčanska magija, zaščitni simboli in rituali.
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - modul_heka_akcija(string $akcija, array $podatki): array
 *
 * 📡 ODVISNOSTI:
 *     - Modul_Bridge
 *
 * 🚫 PREPOVEDI:
 *     - Brez direktnih klicev v SISTEM/
 *     - Brez $_SESSION, $_POST, $_GET direktno
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
 *     modul, heka, antika
 * ============================================================
 */

declare(strict_types=1);

// ── POIŠČI BRIDGE ──────────────────────────────────────────
$bridgePoti = [
    __DIR__ . '/../../Modul_Bridge/modul_bridge.php',
];

$bridgeNajden = false;
foreach ($bridgePoti as $pot) {
    if (file_exists($pot)) {
        require_once $pot;
        $bridgeNajden = true;
        break;
    }
}

if (!$bridgeNajden) {
    header('Content-Type: application/json');
    echo json_encode(['napaka' => 'Modul_Bridge ni najden']);
    exit;
}

// ── STANDARDNI ODZIVI ──────────────────────────────────────
if (!function_exists('odziv_uspeh')) {
    function odziv_uspeh(array $vsebina, string $sporocilo = ''): array {
        return ['status' => 'uspeh', 'status_koda' => 200, 'sporocilo' => $sporocilo, 'vsebina' => $vsebina];
    }
    function odziv_napaka(string $sporocilo, int $koda = 400): array {
        return ['status' => 'napaka', 'status_koda' => $koda, 'sporocilo' => $sporocilo, 'vsebina' => []];
    }
}

// ============================
// VSTOPNA TOČKA MODULA
// ============================

function modul_heka_akcija(string $akcija, array $podatki = []): array {
    if (!Modul_Bridge::vloga_preveri('S1')) {
        return odziv_napaka('Dostop zavrnjen', 403);
    }

    return match($akcija) {
        'info'  => _modul_heka_info($podatki),
        'domov' => _modul_heka_domov($podatki),
        default => odziv_napaka("Neznana akcija: $akcija", 400),
    };
}

// ============================
// AKCIJE
// ============================

function _modul_heka_info(array $podatki): array {
    $uporabnik = Modul_Bridge::uporabnik_pridobi();
    return odziv_uspeh([
        'ime'       => 'Heka',
        'id'        => 'heka',
        'kategorija'=> 'Antika',
        'verzija'   => '1.0.0',
        'opis'      => 'Magija in zaščita — egipčanska magija, zaščitni simboli in rituali.',
        'uporabnik' => $uporabnik['ime'] ?? 'Gost',
    ], 'Informacije o modulu');
}

function _modul_heka_domov(array $podatki): array {
    return odziv_uspeh([
        'sporocilo' => 'Pozdravljen v modulu Heka!',
        'cas'       => time(),
    ], 'Domov');
}

// ── ČE SE KLIČE DIREKTNO ──────────────────────────────────
if (basename($_SERVER['SCRIPT_FILENAME'] ?? '') === 'modul.php' && !defined('SISTEM_OBSTAJA')) {
    $akcija  = $_REQUEST['akcija'] ?? 'domov';
    $odziv   = modul_heka_akcija($akcija, $_REQUEST);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($odziv, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}
