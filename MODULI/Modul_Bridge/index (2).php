<?php
/**
 * ============================================================
 * POT: MODULI/Modul_Bridge/index.php
 * 📅 VERZIJA: v116 (18.6.2026 21:05)
 * ============================================================
 *
 * 🏛️ NIVO: MODUL (Modul_Bridge – orkestrator)
 *
 * 📰 NAMEN:
 *     Centralna vstopna točka za upravljanje, testiranje in
 *     generiranje modulov. Deluje znotraj sistema ali samostojno.
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - (izvedbeni skript – ni javnih funkcij)
 *
 * 📡 ODVISNOSTI:
 *     - jedro/sistem_preveri.php
 *     - jedro/sistemske_funkcije.php (če sistem obstaja)
 *     - embed/mini_sistem.php (če sistema ni)
 *
 * 🚫 PREPOVEDI:
 *     - Brez poslovne logike
 *     - Brez neposrednih klicev SISTEM/
 *     - Brez die(), exit()
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
 *     modul, bridge, orkestrator, vstop
 * ============================================================
 */
declare(strict_types=1);

// VARNOST – namesto die() uporabimo return
if (!defined('BRIDGE_VARNOST')) {
    http_response_code(403);
    header('Location: /');
    return;
}

// Modul_Bridge ima dovoljenje za __DIR__ ker je samostojna enota
require_once __DIR__ . '/jedro/sistem_preveri.php';

if (SISTEM_OBSTAJA) {
    require_once __DIR__ . '/jedro/sistemske_funkcije.php';
} else {
    require_once __DIR__ . '/embed/mini_sistem.php';
}

bridge_inicializacija();

$akcija = $_GET['akcija'] ?? 'pregled';

switch ($akcija) {
    case 'pregled':
        bridge_prikazi_pregled();
        break;
    case 'testnik':
        bridge_prikazi_testnik();
        break;
    case 'generiraj':
        bridge_prikazi_generator();
        break;
    case 'pakiraj':
        bridge_pakiraj_modul();
        break;
    default:
        bridge_prikazi_pregled();
        break;
}
