<?php
/**
 * ---------------------------------------------------------
 * POT: ADAPTER/vhod_zasebno/adapter_ai.php
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

defined('SISTEM_VARNOST') or die('Direkten dostop ni dovoljen.');

require_once ROOT . '/pot.php';
require_once POT_ADAPTER . '/adapter.php';

$GLOBALS['ADAPTER_FORCIRANI_KANAL'] = 'ai';

adapter_zagon();
?>