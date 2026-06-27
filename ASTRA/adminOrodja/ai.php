<?php
declare(strict_types=1);

/**
 * ---------------------------------------------------------
 * ROOT/ai.php
 * VERZIJA: v112 (5.6.2026)
 * ---------------------------------------------------------
 * OPIS: AI kanal vstop. Vse gre skozi adapter.
 * ---------------------------------------------------------
 * PRAVILO: Ne kliče direktno SISTEM!
 * ---------------------------------------------------------
 */

require_once __DIR__ . '/pot.php';

$GLOBALS['ADAPTER_FORCIRANI_KANAL'] = 'ai';
require_once POT_ADAPTER . '/adapter.php';

adapter_zagon();