<?php
declare(strict_types=1);
/**
 * UPORABNIKI/passport.php – Wrapper za PASSPORT (osebni zapisi)
 */
require_once __DIR__ . '/../pot.php';
defined('SIDRO_AKTIVNO') or die('Direkten dostop ni dovoljen.');
require_once ADAPTER . '/adapter.php';

adapter_obdelaj_zahtevo([
    'svet' => 'UPORABNIKI',
    'pot'  => 'passport',
    'metoda' => $_SERVER['REQUEST_METHOD'] ?? 'GET',
    'get'  => $_GET,
    'post' => $_POST
]);