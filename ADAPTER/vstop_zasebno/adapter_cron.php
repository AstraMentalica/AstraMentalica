<?php
/**
 * ---------------------------------------------------------
 * POT: ADAPTER/vhod_zasebno/adapter_cron.php
 * v112 (5.6.2026)
 * ---------------------------------------------------------
 * OPIS: Vstopna točka za cron zahteve
 * ---------------------------------------------------------
 *
 * ODVISNOSTI:
 * - pot.php
 * - ADAPTER/adapter.php
 *
 * UPORABA:
 * - crontab (php cli.php cron)
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

$GLOBALS['ADAPTER_FORCIRANI_KANAL'] = 'cli';
$GLOBALS['ADAPTER_CRON_NACIN'] = true;

adapter_zagon();