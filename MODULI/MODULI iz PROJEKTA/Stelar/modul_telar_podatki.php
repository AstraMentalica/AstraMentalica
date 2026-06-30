<?php
/**
 * MODUL: Stelar
 * PODATKI: modul_telar_podatki.php
 * VERZIJA: 1.0.0 (24.6.2026)
 */

declare(strict_types=1);

define('MODUL_telar_IME', 'Stelar');
define('MODUL_telar_VERZIJA', '1.0.0');

function modul_telar_pridobi_podatke(): array {
    return [
        'ime' => 'Stelar',
        'id' => 'telar',
        'verzija' => '1.0.0',
        'kljucne_besede' => ["zvezde","astral","energija","potovanja"],
        'opis' => 'Stelar modul'
    ];
}