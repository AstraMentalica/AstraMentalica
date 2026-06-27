<?php
/**
 * MODUL: NordicaMystica
 * PODATKI: modul_ordicaystica_podatki.php
 * VERZIJA: 1.0.0 (24.6.2026)
 */

declare(strict_types=1);

define('MODUL_ordicaystica_IME', 'NordicaMystica');
define('MODUL_ordicaystica_VERZIJA', '1.0.0');

function modul_ordicaystica_pridobi_podatke(): array {
    return [
        'ime' => 'NordicaMystica',
        'id' => 'ordicaystica',
        'verzija' => '1.0.0',
        'kljucne_besede' => ["nordijsko","mitologija","vikingi","magija"],
        'opis' => 'NordicaMystica modul'
    ];
}