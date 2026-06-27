<?php
/**
 * ============================================================
 * POT: MODULI/SVETOVI/svetovi_vstop.php
 * VERZIJA: 1.0.0 (24.6.2026)
 * ============================================================
 *
 * 🏛️ NIVO: MODULI / SVETOVI
 *
 * 📰 NAMEN:
 *     Vstopna točka za Elementalne Svetove
 *     Kliče se iz adapter.php ko je ?svet=VODA|ZRAK|ETER|ZEMLJA|OGENJ
 *
 * 📌 STATUS:
 *     Aktivno
 * ============================================================
 */

declare(strict_types=1);

defined('SISTEM_VARNOST') or die('Direkten dostop ni dovoljen.');

require_once __DIR__ . '/svetovi_handler.php';

// Določi svet iz zahteve
$svet = $_GET['svet'] ?? 'VODA';

// Prikaži svet
svetovi_prikazi($svet);