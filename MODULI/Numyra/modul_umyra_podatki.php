<?php
/**
 * MODUL: Numyra
 * PODATKI: modul_umyra_podatki.php
 * VERZIJA: 1.0.0 (24.6.2026)
 */

declare(strict_types=1);

define('MODUL_umyra_IME', 'Numyra');
define('MODUL_umyra_VERZIJA', '1.0.0');

function modul_umyra_pridobi_podatke(): array {
    return [
        'ime' => 'Numyra',
        'id' => 'umyra',
        'verzija' => '1.0.0',
        'kljucne_besede' => ["numerologija","analiza","napovedi","števila"],
        'opis' => 'Numyra modul'
    ];
}