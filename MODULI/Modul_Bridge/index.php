<?php
/**
 * ============================================================
 * POT: MODULI/Modul_Bridge/index.php
 * 📅 VERZIJA: v114 (18.6.2026)
 * ============================================================
 *
 * 🏛️ NIVO: BRIDGE
 *
 * 📰 NAMEN:
 *     Centralna vstopna točka Modul Bridge-a.
 *     Odloči ali uporabi pravi sistem ali mini embed stack.
 *     Usmeri zahtevo na pravo bridge funkcijo.
 *
 * 🔧 JAVNE FUNKCIJE:
 *     (brez — samo orchestration logika)
 *
 * 📡 ODVISNOSTI:
 *     - jedro/sistem_preveri.php     (SISTEM_OBSTAJA)
 *     - jedro/sistemske_funkcije.php (ko sistem obstaja)
 *     - embed/mini_sistem.php        (ko sistema ni)
 *
 * 🚫 PREPOVEDI:
 *     - Brez poslovne logike
 *     - Brez direktnih echo izven HTML renderja
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
 *     bridge, orkestrator, vstopna_tocka
 * ============================================================
 */

declare(strict_types=1);

// ── 1. PREVERI SISTEM ───────────────────────────────────────
require_once __DIR__ . '/jedro/sistem_preveri.php';

// ── 2. NALOŽI USTREZEN STACK ────────────────────────────────
if (SISTEM_OBSTAJA) {
    require_once __DIR__ . '/jedro/sistemske_funkcije.php';
} else {
    require_once __DIR__ . '/embed/mini_sistem.php';
}

// ── 3. INICIALIZACIJA (seja, vloga) ─────────────────────────
bridge_inicijalizacija();

// ── 4. USMERJANJE ───────────────────────────────────────────
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
}
