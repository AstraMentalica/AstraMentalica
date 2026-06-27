<?php
/**
 * MODUL: Skanpo
 * PODATKI: modul_kanpo_podatki.php
 * VERZIJA: 1.0.0 (24.6.2026)
 */

declare(strict_types=1);

define('MODUL_kanpo_IME', 'Skanpo');
define('MODUL_kanpo_VERZIJA', '1.0.0');

function modul_kanpo_pridobi_podatke(): array {
    return [
        'ime' => 'Skanpo',
        'id' => 'kanpo',
        'verzija' => '1.0.0',
        'kljucne_besede' => ["shingon","budizem","japonsko","mistika"],
        'opis' => 'Skanpo modul'
    ];
}