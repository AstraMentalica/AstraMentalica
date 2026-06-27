<?php
/**
 * MODUL: Szazen
 * PODATKI: modul_zazen_podatki.php
 * VERZIJA: 1.0.0 (24.6.2026)
 */

declare(strict_types=1);

define('MODUL_zazen_IME', 'Szazen');
define('MODUL_zazen_VERZIJA', '1.0.0');

function modul_zazen_pridobi_podatke(): array {
    return [
        'ime' => 'Szazen',
        'id' => 'zazen',
        'verzija' => '1.0.0',
        'kljucne_besede' => ["zazen","zen","meditacija","sedenje"],
        'opis' => 'Szazen modul'
    ];
}