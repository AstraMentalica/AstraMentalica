<?php
/**
 * ============================================================
 *  POT: UPORABNIKI/prikaz/sistem/uporabnik_ponastavi_geslo.php
 *  
 *  v112
 * ============================================================
 * 
 * 📦 NAMEN: Wrapper za ponastavitev gesla (kliče adapter)
 * 
 * 🔧 FUNKCIJE:
 *     - Preusmeritev na adapter za obdelavo
 * 
 * 📡 ODVISNOSTI:
 *     - pot.php
 *     - ADAPTER/adapter.php
 * 
 * ⚠️ UPORABA:
 *     Ko uporabnik klikne povezavo za ponastavitev gesla
 * 
 * 🚫 PREPOVEDI:
 *     - Brez HTML izpisa
 *     - Brez business logike
 * 
 * 
 */

declare(strict_types=1);

require_once __DIR__ . '/../../../pot.php';
defined('SIDRO_AKTIVNO') or die('Direkten dostop ni dovoljen.');
require_once ADAPTER . '/adapter.php';

adapter_obdelaj_zahtevo([
    'svet' => 'UPORABNIKI',
    'pot'  => 'ponastavi_geslo',
    'metoda' => $_SERVER['REQUEST_METHOD'] ?? 'GET',
    'get'  => $_GET,
    'post' => $_POST
]);