<?php
/**
 * MODUL: SzodiacApi
 * PODATKI: modul_zodiacpi_podatki.php
 * VERZIJA: 1.0.0 (24.6.2026)
 */

declare(strict_types=1);

define('MODUL_zodiacpi_IME', 'SzodiacApi');
define('MODUL_zodiacpi_VERZIJA', '1.0.0');

function modul_zodiacpi_pridobi_podatke(): array {
    return [
        'ime' => 'SzodiacApi',
        'id' => 'zodiacpi',
        'verzija' => '1.0.0',
        'kljucne_besede' => ["zodiak","astrologija","horoskop","zvezde"],
        'opis' => 'SzodiacApi modul'
    ];
}