<?php
/**
 * ============================================================
 * POT: pot.php
 * 📅 VERZIJA: v115 (18.6.2026)
 * ============================================================
 *
 * USTAVA §1.0 — Absolutno sidro sistema
 * ============================================================
 */

declare(strict_types=1);

// ============================================================
// 1. KORENSKA POT
// ============================================================
define('ROOT', __DIR__);
define('POT_KOREN', ROOT . '/');

// ============================================================
// 2. SISTEMSKE POTI
// ============================================================
define('POT_SISTEM', ROOT . '/SISTEM');
define('POT_KERNEL', POT_SISTEM . '/kernel');
define('POT_JEDRO', POT_KERNEL . '/jedro');

// ============================================================
// 3. APLIKACIJSKE POTI
// ============================================================
define('POT_ADAPTER', ROOT . '/ADAPTER');
define('POT_GLOBALNO', ROOT . '/GLOBALNO');
define('POT_MODULI', ROOT . '/MODULI');
define('POT_UPORABNIKI', ROOT . '/UPORABNIKI');
define('POT_VSEBINA', ROOT . '/VSEBINA');
define('POT_ASTRA', ROOT . '/ASTRA');
define('POT_AI', ROOT . '/AI');

// ============================================================
// 4. AI SISTEM POTI (nove)
// ============================================================
define('POT_SISTEMSKI_AI', POT_AI . '/sistemskiAI');
define('POT_ARHITEKTURNI', POT_SISTEMSKI_AI . '/arhitekturniAI');
define('POT_STRUKTURNI', POT_SISTEMSKI_AI . '/strukturniAI');
define('POT_NALOGE', POT_SISTEMSKI_AI . '/naloge');
define('POT_POROCILA', POT_NALOGE . '/porocila');
define('POT_PRAVILA', POT_SISTEMSKI_AI . '/pravila');
define('POT_NEPOTrJENO', POT_SISTEMSKI_AI . '/nepotrjeno');
define('POT_KARANTENA', POT_SISTEMSKI_AI . '/karantena');
define('POT_STRUKTURE', POT_SISTEMSKI_AI . '/strukture');
define('POT_VIZIJA', POT_SISTEMSKI_AI . '/vizija');

// ============================================================
// 5. OSTALO
// ============================================================
define('SISTEM_VERZIJA', 'v115');
define('RAZVOJNI_NACIN', true);

echo "✅ pot.php v115 naložen\n";