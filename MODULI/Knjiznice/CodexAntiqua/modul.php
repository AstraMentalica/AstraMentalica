<?php
/**
 * ============================================================
 * MODUL: CodexAntiqua
 * POT: MODULI/Knjiznice/CodexAntiqua/modul.php
 * 📅 VERZIJA: v1.0.0 (2026-06-28)
 * ============================================================
 *
 * 🏛️ NIVO: MODUL
 *
 * 📰 NAMEN:
 *     Codex Antiqua — starodavni rokopisi in kodeksi iz vseh tradicij sveta.
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - modul_codexantiqua_akcija(string $akcija, array $podatki): array
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
 *     modul, codexantiqua
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

function modul_codexantiqua_akcija(string $akcija, array $podatki = []): array {
    if (!Modul_Bridge::vloga_preveri('S0')) {
        return odziv_napaka('Dostop zavrnjen', 403);
    }

    return match($akcija) {
        'info'  => _modul_codexantiqua_info($podatki),
        'domov' => _modul_codexantiqua_domov($podatki),
        default => odziv_napaka("Neznana akcija: $akcija", 400),
    };
}

// ============================
// AKCIJE
// ============================

function _modul_codexantiqua_info(array $podatki): array {
    $uporabnik = Modul_Bridge::uporabnik_pridobi();
    return odziv_uspeh([
        'ime'       => 'CodexAntiqua',
        'id'        => 'codexantiqua',
        'verzija'   => '1.0.0',
        'opis'      => 'Codex Antiqua — starodavni rokopisi in kodeksi iz vseh tradicij sveta.',
        'uporabnik' => $uporabnik['ime'] ?? 'Gost',
    ], 'Informacije o modulu');
}

function _modul_codexantiqua_domov(array $podatki): array {
    return odziv_uspeh([
        'sporocilo' => 'Pozdravljen v modulu CodexAntiqua!',
        'cas'       => time(),
    ], 'Domov');
}

// ── ČE SE KLIČE DIREKTNO ───────────────────────────────────
if (basename($_SERVER['SCRIPT_FILENAME'] ?? '') === 'modul.php' && !defined('SISTEM_OBSTAJA')) {
    $akcija  = $_REQUEST['akcija'] ?? 'domov';
    $podatki = $_REQUEST;
    $odziv   = modul_codexantiqua_akcija($akcija, $podatki);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($odziv, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}
