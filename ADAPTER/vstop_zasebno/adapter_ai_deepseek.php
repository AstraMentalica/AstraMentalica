<?php
/**
 * ---------------------------------------------------------
 * POT: ADAPTER/vhod_zasebno/adapter_ai_deepseek.php
 * v111 (27.5.2026 04:00)
 * ---------------------------------------------------------
 * OPIS: Vstopna točka za AI zahteve
 * ---------------------------------------------------------
 *
 * ODVISNOSTI:
 * - pot.php
 * - ADAPTER/adapter.php
 *
 * UPORABA:
 * - ai.php (preko adapterja)
 *
 * FUNKCIJE:
 * - (samo vključitev adapterja z forsiranim kanalom)
 *
 * PREPOVEDI:
 * - Brez business logike
 *
 * STATUS: Stabilno
 *
 * ZGODOVINA:
 * - v112: FAZA ROOT – implementacija
 *
 * ---------------------------------------------------------
 * AVTOR: AstraMentalica Mojster
 * ---------------------------------------------------------
 */
declare(strict_types=1);

require_once __DIR__ . '/pot.php';

$GLOBALS['ADAPTER_FORCIRANI_KANAL'] = 'deepseek';
require_once POT_ADAPTER . '/adapter.php';

adapter_zagon();