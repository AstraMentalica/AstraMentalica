<?php
/**
 * MODUL: Sneidan
 * PODATKI: modul_neidan_podatki.php
 * VERZIJA: 1.0.0 (24.6.2026)
 */

declare(strict_types=1);

define('MODUL_neidan_IME', 'Sneidan');
define('MODUL_neidan_VERZIJA', '1.0.0');

function modul_neidan_pridobi_podatke(): array {
    return [
        'ime' => 'Sneidan',
        'id' => 'neidan',
        'verzija' => '1.0.0',
        'kljucne_besede' => ["neidan","alkimija","notranje","energija"],
        'opis' => 'Sneidan modul'
    ];
}