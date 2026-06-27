<?php
/**
 * ============================================================
 * POT: AI/varnost.php
 * 📅 VERZIJA: v121_SIDRO_OKOLJA (18.6.2026)
 * ============================================================
 * VLOGA:
 * - Edino vstopno in varnostno sidro za celoten AI sistem.
 * - Določi korenski ROOT projekta in vse AI podmape.
 * - Preprečuje nepooblaščen dostop.
 * ============================================================
 */

declare(strict_types=1);

// Nastavitve za sledenje napakam (brez tihih sesuvanj!)
ini_set('display_errors', '1');
error_reporting(E_ALL);

// Varnostne oznake
if (!defined('AI_VSTOP')) {
    define('AI_VSTOP', true);
}
if (!defined('SISTEM_VARNOST')) {
    define('SISTEM_VARNOST', true);
}

// 1. DOLOČITEV ABSOLUTNEGA KORENA (ROOT) PROJEKTA
if (!defined('ROOT')) {
    define('ROOT', '/home/orakleum/public_html');
}

// 2. DOLOČITEV VSEH AI POTI (Samo absolutne konstante, brez relativnega uganjevanja)
if (!defined('POT_AI')) {
    define('POT_AI', ROOT . '/AI');
}
if (!defined('POT_AI_NALOGE')) {
    define('POT_AI_NALOGE', POT_AI . '/sistemskiAI/naloge');
}
if (!defined('POT_AI_POROCILA')) {
    define('POT_AI_POROCILA', POT_AI . '/sistemskiAI/porocila');
}
if (!defined('POT_AI_NEPOTRJENO')) {
    define('POT_AI_NEPOTRJENO', POT_AI . '/nepotrjeno');
}
if (!defined('POT_AI_KARANTENA')) {
    define('POT_AI_KARANTENA', POT_AI . '/karantena');
}

// 3. DOLOČITEV SISTEMSKIH POTI (Če jih agenti potrebujejo za analizo)
if (!defined('POT_SISTEM')) {
    define('POT_SISTEM', ROOT . '/SISTEM');
}
if (!defined('POT_GLOBALNO')) {
    define('POT_GLOBALNO', ROOT . '/GLOBALNO');
}
if (!defined('POT_ADAPTER')) {
    define('POT_ADAPTER', ROOT . '/ADAPTER');
}
if (!defined('POT_MODULI')) {
    define('POT_MODULI', ROOT . '/MODULI');
}