<?php
/**
 * MODUL: aetheris2
 * PODATKI: modul_aetheris2_podatki.php
 * VERZIJA: 1.0.0 (24.6.2026)
 */

declare(strict_types=1);

define('MODUL_aetheris2_IME', 'aetheris2');
define('MODUL_aetheris2_VERZIJA', '1.0.0');

function modul_aetheris2_pridobi_podatke(): array {
    return [
        'ime' => 'aetheris2',
        'id' => 'aetheris2',
        'verzija' => '1.0.0',
        'kljucne_besede' => ["eter","energija","napredno","vibracije"],
        'opis' => 'aetheris2 modul'
    ];
}