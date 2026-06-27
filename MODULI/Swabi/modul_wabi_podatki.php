<?php
/**
 * MODUL: Swabi
 * PODATKI: modul_wabi_podatki.php
 * VERZIJA: 1.0.0 (24.6.2026)
 */

declare(strict_types=1);

define('MODUL_wabi_IME', 'Swabi');
define('MODUL_wabi_VERZIJA', '1.0.0');

function modul_wabi_pridobi_podatke(): array {
    return [
        'ime' => 'Swabi',
        'id' => 'wabi',
        'verzija' => '1.0.0',
        'kljucne_besede' => ["wabi","sabi","japonsko","estetika"],
        'opis' => 'Swabi modul'
    ];
}