<?php
/**
 * MODUL: Smushin
 * PODATKI: modul_mushin_podatki.php
 * VERZIJA: 1.0.0 (24.6.2026)
 */

declare(strict_types=1);

define('MODUL_mushin_IME', 'Smushin');
define('MODUL_mushin_VERZIJA', '1.0.0');

function modul_mushin_pridobi_podatke(): array {
    return [
        'ime' => 'Smushin',
        'id' => 'mushin',
        'verzija' => '1.0.0',
        'kljucne_besede' => ["mushin","zen","um","praznina"],
        'opis' => 'Smushin modul'
    ];
}