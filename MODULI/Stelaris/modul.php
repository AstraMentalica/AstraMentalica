<?php
/**
 * ============================================================
 * MODUL: Stelaris
 * POT: MODULI/Stelaris/modul.php
 * 📅 VERZIJA: v1.0.0 (22.06.2026 10:21)
 * ============================================================
 *
 * 🏛️ NIVO: MODUL
 *
 * 📰 NAMEN:
 *     Astrološki motor — natalni horoskop, tranziti, aspekti in planetarne energije.
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - modul_stelaris_akcija(string $akcija, array $podatki): array
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
 *     AstraMentalica Mojster
 *
 * 🌐 JEZIK:
 *     sl
 *
 * 🏷️ OZNAKE:
 *     modul, stelaris
 * ============================================================
 */

declare(strict_types=1);

// ── POIŠČI BRIDGE (modul ne pozna sidra, samo Modul_Bridge) ──
$bridgePoti = [
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

/**
 * Glavna funkcija modula — kliče jo SISTEM preko Modul_Bridge.
 *
 * @param string $akcija   Kaj modul mora narediti
 * @param array  $podatki  Parametri za akcijo
 * @return array           Rezultat (vedno array, nikoli HTML)
 */
function modul_stelaris_akcija(string $akcija, array $podatki = []): array {
    if (!Modul_Bridge::vloga_preveri('S0')) {
        return odziv_napaka('Dostop zavrnjen', 403);
    }

    return match($akcija) {
        'info'  => _modul_stelaris_info($podatki),
        'domov' => _modul_stelaris_domov($podatki),
        default => odziv_napaka("Neznana akcija: $akcija", 400),
    };
}

// ============================
// AKCIJE
// ============================

function _modul_stelaris_info(array $podatki): array {
    $uporabnik = Modul_Bridge::uporabnik_pridobi();

    return odziv_uspeh([
        'ime'       => 'Stelaris',
        'id'        => 'stelaris',
        'verzija'   => '1.0.0',
        'opis'      => 'Astrološki motor — natalni horoskop, tranziti, aspekti in planetarne energije.',
        'uporabnik' => $uporabnik['ime'] ?? 'Gost',
    ], 'Informacije o modulu');
}

function _modul_stelaris_domov(array $podatki): array {
    return odziv_uspeh([
        'sporocilo' => 'Pozdravljen v modulu Stelaris!',
        'cas'       => time(),
    ], 'Domov');
}

// ── ČE SE KLIČE DIREKTNO (brez Bridge-a) ──────────────────
if (basename($_SERVER['SCRIPT_FILENAME'] ?? '') === 'modul.php' && !defined('SISTEM_OBSTAJA')) {
    $akcija  = $_REQUEST['akcija'] ?? 'domov';
    $podatki = $_REQUEST;
    $odziv   = modul_stelaris_akcija($akcija, $podatki);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($odziv, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}
