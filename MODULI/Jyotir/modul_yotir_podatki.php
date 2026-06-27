<?php
/**
 * MODUL: Jyotir
 * PODATKI: modul_yotir_podatki.php
 * VERZIJA: 1.0.0 (24.6.2026)
 */

declare(strict_types=1);

define('MODUL_yotir_IME', 'Jyotir');
define('MODUL_yotir_VERZIJA', '1.0.0');

function modul_yotir_pridobi_podatke(): array {
    return [
        'ime' => 'Jyotir',
        'id' => 'yotir',
        'verzija' => '1.0.0',
        'kljucne_besede' => ["svetloba","astrologija","kozmos","vplivi"],
        'opis' => 'Jyotir modul'
    ];
}