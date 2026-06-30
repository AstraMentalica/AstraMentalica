<?php
/**
 * ============================================================
 * MODUL: Animaris
 * POT: MODULI/Antika/Animaris/modul.php
 * 📅 VERZIJA: v1.0.0 (2026-06-28)
 * ============================================================
 *
 * 🏛️ NIVO: MODUL
 *
 * 📰 NAMEN:
 *     Animaris — duhovnost narave in živali v antičnem svetu. Živalski totemi, svete živali in mistični bestiarij.
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - modul_animaris_akcija(string $akcija, array $podatki): array
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
 *     Damir Šafarič
 *
 * 🌐 JEZIK:
 *     sl
 *
 * 🏷️ OZNAKE:
 *     modul, animaris
 * ============================================================
 */

declare(strict_types=1);

// ── POIŠČI BRIDGE ──────────────────────────────────────────
$bridgePoti = [
    __DIR__ . '/../../Modul_Bridge/modul_bridge.php',
    __DIR__ . '/../Modul_Bridge/modul_bridge.php',
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

function modul_animaris_akcija(string $akcija, array $podatki = []): array {
    if (!Modul_Bridge::vloga_preveri('S0')) {
        return odziv_napaka('Dostop zavrnjen', 403);
    }

    return match($akcija) {
        'info'  => _modul_animaris_info($podatki),
        'domov' => _modul_animaris_domov($podatki),
        default => odziv_napaka("Neznana akcija: $akcija", 400),
    };
}

// ============================
// AKCIJE
// ============================

function _modul_animaris_info(array $podatki): array {
    $uporabnik = Modul_Bridge::uporabnik_pridobi();
    return odziv_uspeh([
        'ime'       => 'Animaris',
        'id'        => 'animaris',
        'verzija'   => '1.0.0',
        'opis'      => 'Animaris — duhovnost narave in živali v antičnem svetu. Živalski totemi, svete živali in mistični bestiarij.',
        'uporabnik' => $uporabnik['ime'] ?? 'Gost',
    ], 'Informacije o modulu');
}

function _modul_animaris_domov(array $podatki): array {
    return odziv_uspeh([
        'sporocilo' => 'Pozdravljen v modulu Animaris!',
        'cas'       => time(),
    ], 'Domov');
}

// ── ČE SE KLIČE DIREKTNO ───────────────────────────────────
if (basename($_SERVER['SCRIPT_FILENAME'] ?? '') === 'modul.php' && !defined('SISTEM_OBSTAJA')) {
    $akcija  = $_REQUEST['akcija'] ?? 'domov';
    $podatki = $_REQUEST;
    $odziv   = modul_animaris_akcija($akcija, $podatki);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($odziv, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}
