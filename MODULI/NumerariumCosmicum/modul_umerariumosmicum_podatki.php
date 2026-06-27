<?php
/**
 * MODUL: NumerariumCosmicum
 * PODATKI: modul_umerariumosmicum_podatki.php
 * VERZIJA: 1.0.0 (24.6.2026)
 */

declare(strict_types=1);

define('MODUL_umerariumosmicum_IME', 'NumerariumCosmicum');
define('MODUL_umerariumosmicum_VERZIJA', '1.0.0');

function modul_umerariumosmicum_pridobi_podatke(): array {
    return [
        'ime' => 'NumerariumCosmicum',
        'id' => 'umerariumosmicum',
        'verzija' => '1.0.0',
        'kljucne_besede' => ["numerologija","števila","kozmos","vibracije"],
        'opis' => 'NumerariumCosmicum modul'
    ];
}