<?php
/**
 * MODUL: Chakrarium
 * PODATKI: modul_hakrarium_podatki.php
 * VERZIJA: 1.0.0 (24.6.2026)
 */

declare(strict_types=1);

define('MODUL_hakrarium_IME', 'Chakrarium');
define('MODUL_hakrarium_VERZIJA', '1.0.0');

function modul_hakrarium_pridobi_podatke(): array {
    return [
        'ime' => 'Chakrarium',
        'id' => 'hakrarium',
        'verzija' => '1.0.0',
        'kljucne_besede' => ["čakre","energija","centri","interaktivno"],
        'opis' => 'Chakrarium modul'
    ];
}