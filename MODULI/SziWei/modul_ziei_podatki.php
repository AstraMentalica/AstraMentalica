<?php
/**
 * MODUL: SziWei
 * PODATKI: modul_ziei_podatki.php
 * VERZIJA: 1.0.0 (24.6.2026)
 */

declare(strict_types=1);

define('MODUL_ziei_IME', 'SziWei');
define('MODUL_ziei_VERZIJA', '1.0.0');

function modul_ziei_pridobi_podatke(): array {
    return [
        'ime' => 'SziWei',
        'id' => 'ziei',
        'verzija' => '1.0.0',
        'kljucne_besede' => ["ziwei","astrologija","kitajsko","cesarsko"],
        'opis' => 'SziWei modul'
    ];
}