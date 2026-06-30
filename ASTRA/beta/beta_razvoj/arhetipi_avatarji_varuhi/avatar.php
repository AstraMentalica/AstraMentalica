<?php
/**
 * ---------------------------------------------------------
 * POT: SISTEM/storitve_svetov/uporabniki/avatar.php
 * v210 (10.06.2026)
 * ---------------------------------------------------------
 * OPIS: Vstopna točka za avatar sistem (Duhovni Varuh)
 * ---------------------------------------------------------
 * VRSTNI RED:
 *     1. avatar_napredovanje.php  ← stopnje, arhetipi, shranjevanje
 *     2. avatar_tocke.php         ← točke, XP, streak
 *     3. avatar_nagrade.php       ← zakladnica, dosežki
 *     4. avatar_ai.php            ← DeepSeek AI Varuh
 * ---------------------------------------------------------
 */
declare(strict_types=1);

require_once __DIR__ . '/avatar/avatar_napredovanje.php';
require_once __DIR__ . '/avatar/avatar_tocke.php';
require_once __DIR__ . '/avatar/avatar_nagrade.php';
require_once __DIR__ . '/avatar/avatar_ai.php';
