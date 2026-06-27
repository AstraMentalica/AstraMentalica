<?php
/**
 * ============================================================
 * AI/pot_ai.php — SIDRO 2: AI SISTEM (LOČEN!)
 * VERZIJA: v116 (18.6.2026)
 * ============================================================
 * Ta datoteka definira VSE poti AI podsistema.
 * Odvisna od pot.php (POT_AI mora biti že definiran).
 * ============================================================
 */

declare(strict_types=1);

// Varnostni preverek — pot.php mora biti naložen pred nami
if (!defined('POT_AI')) {
    throw new RuntimeException('pot_ai.php zahteva pot.php (POT_AI ni definiran).');
}

// ============================================================
// AI KOREN
// ============================================================
define('AI_ROOT',        POT_AI);

// ============================================================
// AI PODSISTEMI
// ============================================================
define('AI_SISTEMSKI',    AI_ROOT . '/sistemskiAI');
define('AI_ARHITEKTURNI', AI_ROOT . '/arhitekturniAI');
define('AI_STRUKTURNI',   AI_ROOT . '/strukturniAI');
define('AI_VARUHI',       AI_ROOT . '/varuhiAI');
define('AI_AVATARJI',     AI_ROOT . '/avatarjiAI');
define('AI_DUHOVNI',      AI_ROOT . '/duhovniAI');
define('AI_RAZISKOVALNI', AI_ROOT . '/raziskovalniAI');
define('AI_POVEZOVALNI',  AI_ROOT . '/povezovalniAI');


// ============================================================
// AI NALOGE & WORKFLOW
// ============================================================
define('AI_SISTEM',        AI_ROOT . '/sistem');
define('AI_NALOGE',        AI_SISTEM . '/naloge');
define('AI_POROCILA',      AI_SISTEM . '/porocila');
define('AI_NEPOTRJENO',    AI_SISTEM . '/nepotrjeno');
define('AI_KARANTENA',     AI_SISTEM . '/karantena');
define('AI_JAVNI',         AI_ROOT . '/javni');
define('AI_AVATAR',        AI_JAVNI . '/avatarji');
define('AI_VARUH',         AI_JAVNI . '/varuhi');
define('AI_ZUNANJI',       AI_ROOT . '/zunanji');
define('AI_MERILEC',       AI_ZUNANJI . '/merilni');
define('AI_POVEZOVALEC',   AI_ZUNANJI . '/povezovalni');
define('AI_RAZISKOVALEC',  AI_ZUNANJI . '/raziskovalni');


// ============================================================
// AI PRAVILA & DOKUMENTACIJA
// ============================================================
define('AI_PRAVILA',      AI_SISTEM . '/pravila');
define('AI_VIZIJA',       AI_SISTEM . '/vizija');
define('AI_ZAVEST',       AI_JAVNI . '/zavest');
define('AI_MERITVE',      AI_ZUNANJI . '/meritve');
define('AI_PODATKI',      AI_ROOT . '/.baza');

// ============================================================
// AI VERZIJA
// ============================================================
define('AI_VERZIJA', 'v116');
