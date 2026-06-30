<?php
/**
 * MODUL: Modul_Mystaia
 * PODATKI: modul_odulystaia_podatki.php
 * VERZIJA: 1.0.0 (24.6.2026)
 */

declare(strict_types=1);

define('MODUL_odulystaia_IME', 'Modul_Mystaia');
define('MODUL_odulystaia_VERZIJA', '1.0.0');

function modul_odulystaia_pridobi_podatke(): array {
    return [
        'ime' => 'Modul_Mystaia',
        'id' => 'odulystaia',
        'verzija' => '1.0.0',
        'kljucne_besede' => ["mystaia","platforma","mistika","modul"],
        'opis' => 'Modul_Mystaia modul'
    ];
}