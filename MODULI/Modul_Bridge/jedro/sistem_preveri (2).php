<?php
/**
 * ============================================================
 * POT: MODULI/Modul_Bridge/jedro/sistem_preveri.php
 * 📅 VERZIJA: v116 (18.6.2026 21:05)
 * ============================================================
 *
 * 🏛️ NIVO: MODUL (Modul_Bridge/jedro)
 *
 * 📰 NAMEN:
 *     Preveri ali ASTRAMENTALICA sistem obstaja.
 *     Mora se naložiti PRVA – pred vsem drugim.
 *     Definira konstanto SISTEM_OBSTAJA (true/false).
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - (nobene – samo define SISTEM_OBSTAJA)
 *
 * 📡 ODVISNOSTI:
 *     - pot.php (če sistem obstaja)
 *
 * 🚫 PREPOVEDI:
 *     - Brez die(), exit()
 *     - Brez poslovne logike
 *
 * 📌 STATUS:
 *     Stabilno
 *
 * 📅 ZGODOVINA:
 *     - v116: uskladitev s Header Standard v116,
 *             odstranjeni vsi die() in exit()
 *
 * 👤 AVTOR:
 *     AstraMentalica Mojster
 *
 * 🌐 JEZIK:
 *     sl
 *
 * 🏷️ OZNAKE:
 *     modul, bridge, jedro, sistem, preverjanje
 * ============================================================
 */
declare(strict_types=1);

// __DIR__ je dovoljen v Modul_Bridge ker je samostojna enota
$mozne_poti_sidra = [
    __DIR__ . '/../../../pot.php',          // MODULI/Modul_Bridge/jedro/ → root (standard)
    __DIR__ . '/../../../../pot.php',       // En nivo globlje (varnostna rezerva)
    ($_SERVER['DOCUMENT_ROOT'] ?? '') . '/pot.php', // Koren domene
];

$sistemObstaja = false;

foreach ($mozne_poti_sidra as $potSidra) {
    if (!empty($potSidra) && file_exists($potSidra)) {
        require_once $potSidra;
        $sistemObstaja = true;
        break;
    }
}

define('SISTEM_OBSTAJA', $sistemObstaja);

// Varnostna zastavica za embed datoteke
if (!defined('BRIDGE_VARNOST')) {
    define('BRIDGE_VARNOST', true);
}
