<?php
/**
 * MODUL: Aeternum
 * PODATKI: modul_eternum_podatki.php
 * VERZIJA: 1.0.0 (24.6.2026)
 */

declare(strict_types=1);

define('MODUL_eternum_IME', 'Aeternum');
define('MODUL_eternum_VERZIJA', '1.0.0');

function modul_eternum_pridobi_podatke(): array {
    return [
        'ime' => 'Aeternum',
        'id' => 'eternum',
        'verzija' => '1.0.0',
        'kljucne_besede' => ["večnost","nesmrtnost","cikli","duša"],
        'opis' => 'Aeternum modul'
    ];
}