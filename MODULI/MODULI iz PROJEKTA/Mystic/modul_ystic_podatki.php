<?php
/**
 * MODUL: Mystic
 * PODATKI: modul_ystic_podatki.php
 * VERZIJA: 1.0.0 (24.6.2026)
 */

declare(strict_types=1);

define('MODUL_ystic_IME', 'Mystic');
define('MODUL_ystic_VERZIJA', '1.0.0');

function modul_ystic_pridobi_podatke(): array {
    return [
        'ime' => 'Mystic',
        'id' => 'ystic',
        'verzija' => '1.0.0',
        'kljucne_besede' => ["misticizem","duhovnost","prakse","kontemplacija"],
        'opis' => 'Mystic modul'
    ];
}