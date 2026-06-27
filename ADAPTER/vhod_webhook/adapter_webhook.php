<?php
/**
 * ---------------------------------------------------------
 * POT: ADAPTER/vhod_webhook/adapter_webhook.php
 * v112 (5.6.2026)
 * ---------------------------------------------------------
 * OPIS: Vstopna točka za webhook zahteve
 * ---------------------------------------------------------
 *
 * ODVISNOSTI:
 * - pot.php
 * - ADAPTER/adapter.php
 *
 * UPORABA:
 * - Zunanji webhook klici (Stripe, GitHub, Discord...)
 *
 * FUNKCIJE:
 * - adapter_doloci_kanal_iz_webhook() – če obstaja, določi kanal
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

// SIDRO
require_once ROOT . '/pot.php';

// Zagon adapterja
require_once POT_ADAPTER . '/adapter.php';

// Določi kanal glede na vsebino webhook-a
if (function_exists('adapter_doloci_kanal_iz_webhook')) {
    $GLOBALS['ADAPTER_FORCIRANI_KANAL'] = adapter_doloci_kanal_iz_webhook();
} else {
    // Privzeto API kanal za webhook
    $GLOBALS['ADAPTER_FORCIRANI_KANAL'] = 'api';
}

adapter_zagon();