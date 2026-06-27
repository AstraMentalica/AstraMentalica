<?php
/**
 * ============================================================
 * POT: MODULI/Modul_Bridge/jedro/sistem_preveri.php
 * 📅 VERZIJA: v114 (18.6.2026)
 * ============================================================
 *
 * 🏛️ NIVO: BRIDGE / JEDRO
 *
 * 📰 NAMEN:
 *     Preveri, ali ASTRAMENTALICA sistem obstaja.
 *     Mora biti vključen NAJPREJ — pred vsemi ostalimi datotekami.
 *
 * 🔧 JAVNE FUNKCIJE:
 *     (brez funkcij — samo definira konstanto)
 *
 * 📡 ODVISNOSTI:
 *     Nobenih
 *
 * 📌 STATUS:
 *     Stabilno
 *
 * 📅 ZGODOVINA:
 *     - v114: usklajen z jezikovnim standardom v114
 *     - v111: prva implementacija
 *
 * 👤 AVTOR:
 *     AstraMentalica Mojster
 *
 * 🌐 JEZIK:
 *     sl
 *
 * 🏷️ OZNAKE:
 *     bridge, jedro, sistem, preverjanje
 * ============================================================
 */

declare(strict_types=1);

// Prepreči večkratno definicijo
if (defined('SISTEM_OBSTAJA')) {
    return;
}

// Poišči pot.php (SIDRO ASTRAMENTALICE) v različnih lokacijah
$iskane_poti = [
    __DIR__ . '/../../../../pot.php',        // Standard: MODULI/Modul_Bridge/jedro/ → root
    __DIR__ . '/../../../../../pot.php',     // En nivo globlje
    $_SERVER['DOCUMENT_ROOT'] . '/pot.php',  // Koren domene
];

$sistem_obstaja = false;

foreach ($iskane_poti as $pot) {
    if (file_exists($pot)) {
        require_once $pot;
        $sistem_obstaja = true;
        break;
    }
}

define('SISTEM_OBSTAJA', $sistem_obstaja);
