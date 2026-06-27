<?php
/**
 * MODUL: Occultum
 * PODATKI: modul_ccultum_podatki.php
 * VERZIJA: 1.0.0 (24.6.2026)
 */

declare(strict_types=1);

define('MODUL_ccultum_IME', 'Occultum');
define('MODUL_ccultum_VERZIJA', '1.0.0');

function modul_ccultum_pridobi_podatke(): array {
    return [
        'ime' => 'Occultum',
        'id' => 'ccultum',
        'verzija' => '1.0.0',
        'kljucne_besede' => ["okultno","znanost","prakse","skrivno"],
        'opis' => 'Occultum modul'
    ];
}