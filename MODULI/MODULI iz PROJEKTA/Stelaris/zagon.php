<?php
/**
 * Zagon Stelaris astrološke aplikacije
 * Glavna vstopna točka sistema
 */
// Avtoloader za razrede
spl_autoload_register(function ($razred) {
    $pot = __DIR__ . '/' . str_replace('\\', '/', $razred) . '.php';
    if (file_exists($pot)) {
        require_once $pot;
    }
});

// Zagon aplikacije
$krmilnik = new Stelaris\Krmilniki\ApiKrmilnik();
$krmilnik->obdelajZahtevo();