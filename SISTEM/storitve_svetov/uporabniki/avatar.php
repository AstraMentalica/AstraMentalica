<?php
/**
 * ============================================================
 * POT: SISTEM/storitve_svetov/uporabniki/avatar.php
 * 📅 VERZIJA: v115 (14.6.2026 18:00)
 * ============================================================
 *
 * 🏛️ NIVO: STORITEV (BUSINESS LAYER)
 *
 * 📰 NAMEN:
 *     Vstopna točka za avatar sistem (Duhovni Varuh).
 *     Zbere in izvozi funkcije iz podmap.
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - (izvožene iz avatar_napredovanje.php, avatar_tocke.php, itd.)
 *
 * 📡 ODVISNOSTI:
 *     - avatar/avatar_napredovanje.php
 *     - avatar/avatar_tocke.php
 *     - avatar/avatar_nagrade.php
 *     - avatar/avatar_ai.php
 *
 * 🚫 PREPOVEDI:
 *     - Brez __DIR__
 *     - Brez echo, print_r, var_dump
 *
 * 📌 STATUS:
 *     Stabilno
 *
 * 📅 ZGODOVINA:
 *     - v115: uskladitev s Header Standard v115
 *
 * 👤 AVTOR:
 *     AstraMentalica Mojster
 *
 * 🌐 JEZIK:
 *     sl
 *
 * 🏷️ OZNAKE:
 *     storitev, uporabniki, avatar, varuh
 * ============================================================
 */
declare(strict_types=1);

defined('SISTEM_VARNOST') or die('Direkten dostop ni dovoljen.');

require_once __DIR__ . '/avatar/avatar_napredovanje.php';
require_once __DIR__ . '/avatar/avatar_tocke.php';
require_once __DIR__ . '/avatar/avatar_nagrade.php';
require_once __DIR__ . '/avatar/avatar_ai.php';