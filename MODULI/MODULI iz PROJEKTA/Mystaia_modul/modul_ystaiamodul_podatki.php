<?php
/**
 * MODUL: Mystaia_modul
 * PODATKI: modul_ystaiamodul_podatki.php
 * VERZIJA: 1.0.0 (24.6.2026)
 */

declare(strict_types=1);

define('MODUL_ystaiamodul_IME', 'Mystaia_modul');
define('MODUL_ystaiamodul_VERZIJA', '1.0.0');

function modul_ystaiamodul_pridobi_podatke(): array {
    return [
        'ime' => 'Mystaia_modul',
        'id' => 'ystaiamodul',
        'verzija' => '1.0.0',
        'kljucne_besede' => ["mystaia","modularno","platforma","sistem"],
        'opis' => 'Mystaia_modul modul'
    ];
}