<?php
/**
 * ---------------------------------------------------------
 * POT: ADAPTER/vhod_webhook/adapter_facebook.php
 * v112 (5.6.2026)
 * ---------------------------------------------------------
 * OPIS: Vstopna točka za Facebook zahteve
 * ---------------------------------------------------------
 *
 * ODVISNOSTI:
 * - pot.php
 * - ADAPTER/adapter.php
 *
 * UPORABA:
 * - facebook.php (webhook)
 *
 * PREPOVEDI:
 * - Brez business logike
 *
 * STATUS: Stabilno
 *
 * ZGODOVINA:
 * - v112: dodana varnostna konstanta
 *
 * AVTOR: AstraMentalica Mojster
 * ---------------------------------------------------------
 */

declare(strict_types=1);

defined('SISTEM_VARNOST') or die('Direkten dostop ni dovoljen.');

require_once ROOT . '/pot.php';
require_once POT_ADAPTER . '/adapter.php';

$GLOBALS['ADAPTER_FORCIRANI_KANAL'] = 'facebook';

adapter_zagon();