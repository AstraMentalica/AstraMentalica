<?php
/**
 * ============================================================
 * POT: SISTEM/kernel/nastavitve.php
 * 📅 VERZIJA: v118 (19.6.2026 00:30)
 * ============================================================
 *
 * 🏛️ NIVO: KERNEL (N3)
 *
 * 📰 NAMEN:
 *     Globalne sistemske nastavitve – samo definicije.
 *     Brez vključevanja drugih datotek.
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - (nobene – samo konstante)
 *
 * 📡 ODVISNOSTI:
 *     - pot.php (za konstante, če jih rabiš)
 *
 * 🚫 PREPOVEDI:
 *     - Brez require_once
 *     - Brez logike
 *     - Brez echo
 *     - Brez die(), exit()
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
 *     kernel, nastavitve, konstante
 * ============================================================
 */
declare(strict_types=1);

defined('SISTEM_VARNOST') or die('Direkten dostop ni dovoljen.');

// ============================================================
// OSNOVNE NASTAVITVE
// ============================================================
define('NASTAVITVE_CASOVNA_CONA', 'Europe/Ljubljana');
define('NASTAVITVE_JEZIK', 'sl');
define('NASTAVITVE_NAZIV', 'AstraMentalica');
define('NASTAVITVE_VERZIJA', 'v118');
define('NASTAVITVE_DEBUG', true);

// ============================================================
// ČASOVNE MEJE
// ============================================================
define('NASTAVITVE_MAX_IZVAJANJE', 120);          // sekund
define('NASTAVITVE_MAX_POMNILNIK', '1024M');
define('NASTAVITVE_POTEK_SEJE', 3600);            // sekund

// ============================================================
// VARNOST
// ============================================================
define('NASTAVITVE_MAX_POSKUSOV', 5);
define('NASTAVITVE_BLOKIRA_IP', true);
define('NASTAVITVE_CSRF_AKTIVEN', true);

// ============================================================
// CACHE
// ============================================================
define('NASTAVITVE_CACHE_TTL', 3600);              // sekund
define('NASTAVITVE_CACHE_TIP', 'json');            // json | redis | memcached

// ============================================================
// SISTEMSKE POTI (dodatne, če jih rabiš)
// ============================================================
// Upoštevaj, da so glavne poti že v pot.php – tukaj samo definiraj
// tiste, ki so specifične za kernel, če jih potrebuješ.
// define('NASTAVITVE_POT_ZACASNO', POT_SISTEM . '/tmp');