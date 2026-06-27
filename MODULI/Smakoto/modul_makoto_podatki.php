<?php
/**
 * MODUL: Smakoto
 * PODATKI: modul_makoto_podatki.php
 * VERZIJA: 1.0.0 (24.6.2026)
 */

declare(strict_types=1);

define('MODUL_makoto_IME', 'Smakoto');
define('MODUL_makoto_VERZIJA', '1.0.0');

function modul_makoto_pridobi_podatke(): array {
    return [
        'ime' => 'Smakoto',
        'id' => 'makoto',
        'verzija' => '1.0.0',
        'kljucne_besede' => ["shakyo","sutre","budizem","meditacija"],
        'opis' => 'Smakoto modul'
    ];
}