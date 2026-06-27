<?php
/**
 * ---------------------------------------------------------
 * POT: ADAPTER/vhod_webhook/adapter_socket.php
 * v112 (5.6.2026)
 * ---------------------------------------------------------
 * OPIS: Vstopna točka za socket/webSocket zahteve
 * ---------------------------------------------------------
 *
 * ODVISNOSTI:
 * - pot.php
 * - ADAPTER/adapter.php
 *
 * UPORABA:
 * - WebSocket strežnik
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

$GLOBALS['ADAPTER_FORCIRANI_KANAL'] = 'socket';

adapter_zagon();