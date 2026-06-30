<?php
/**
 * MODUL: Modul_Orakleum
 * PODATKI: modul_odulrakleum_podatki.php
 * VERZIJA: 1.0.0 (24.6.2026)
 */

declare(strict_types=1);

define('MODUL_odulrakleum_IME', 'Modul_Orakleum');
define('MODUL_odulrakleum_VERZIJA', '1.0.0');

function modul_odulrakleum_pridobi_podatke(): array {
    return [
        'ime' => 'Modul_Orakleum',
        'id' => 'odulrakleum',
        'verzija' => '1.0.0',
        'kljucne_besede' => ["orakleum","napovedi","sistem","modul"],
        'opis' => 'Modul_Orakleum modul'
    ];
}