<?php
/**
 * MODUL: Sliuren
 * PODATKI: modul_liuren_podatki.php
 * VERZIJA: 1.0.0 (24.6.2026)
 */

declare(strict_types=1);

define('MODUL_liuren_IME', 'Sliuren');
define('MODUL_liuren_VERZIJA', '1.0.0');

function modul_liuren_pridobi_podatke(): array {
    return [
        'ime' => 'Sliuren',
        'id' => 'liuren',
        'verzija' => '1.0.0',
        'kljucne_besede' => ["kempo","borilno","duhovno","japonsko"],
        'opis' => 'Sliuren modul'
    ];
}