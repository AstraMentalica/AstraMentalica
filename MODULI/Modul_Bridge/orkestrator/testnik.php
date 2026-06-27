<?php
/**
 * ============================================================
 * POT: MODULI/Modul_Bridge/orkestrator/testnik.php
 * 📅 VERZIJA: v114 (18.6.2026)
 * ============================================================
 *
 * 🏛️ NIVO: BRIDGE / ORKESTRATOR
 *
 * 📰 NAMEN:
 *     Testiranje modulov — pošlje testni klic na modul.php
 *     in vrne strukturiran odziv za prikaz v Bridge UI.
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - testnik_izvedi_klic(string $pot_modula, string $akcija, array $parametri): array
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
 *     bridge, orkestrator, testnik
 * ============================================================
 */

declare(strict_types=1);

function testnik_izvedi_klic(string $pot_modula, string $akcija = 'info', array $parametri = []): array {
    $modul_php = rtrim($pot_modula, '/') . '/modul.php';

    if (!file_exists($modul_php)) {
        return [
            'uspeh'   => false,
            'napaka'  => 'modul.php ne obstaja.',
            'odziv'   => null,
            'cas_ms'  => 0,
        ];
    }

    // Simuliraj GET parametre za modul
    $zacetek = hrtime(true);

    // Klic z output buffering
    $_GET_orig     = $_GET;
    $_REQUEST_orig = $_REQUEST;
    $_GET          = array_merge($parametri, ['akcija' => $akcija]);
    $_REQUEST      = $_GET;

    ob_start();

    try {
        include $modul_php;
        $izhod = ob_get_clean();
    } catch (Throwable $e) {
        ob_end_clean();
        $_GET     = $_GET_orig;
        $_REQUEST = $_REQUEST_orig;
        return [
            'uspeh'  => false,
            'napaka' => 'Izjema: ' . $e->getMessage(),
            'odziv'  => null,
            'cas_ms' => 0,
        ];
    }

    $_GET     = $_GET_orig;
    $_REQUEST = $_REQUEST_orig;

    $cas_ms  = round((hrtime(true) - $zacetek) / 1_000_000, 2);
    $razcleni = json_decode($izhod, true);

    return [
        'uspeh'     => ($razcleni['status'] ?? '') === 'uspeh',
        'izhod_raw' => $izhod,
        'odziv'     => $razcleni,
        'cas_ms'    => $cas_ms,
    ];
}
