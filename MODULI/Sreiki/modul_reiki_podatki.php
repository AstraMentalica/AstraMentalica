<?php
/**
 * MODUL: Sreiki
 * PODATKI: modul_reiki_podatki.php
 * VERZIJA: 1.0.0 (24.6.2026)
 */

declare(strict_types=1);

define('MODUL_reiki_IME', 'Sreiki');
define('MODUL_reiki_VERZIJA', '1.0.0');

function modul_reiki_pridobi_podatke(): array {
    return [
        'ime' => 'Sreiki',
        'id' => 'reiki',
        'verzija' => '1.0.0',
        'kljucne_besede' => ["reiki","zdravljenje","energija","japonsko"],
        'opis' => 'Sreiki modul'
    ];
}