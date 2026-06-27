<?php
/**
 * MODUL: Skijou
 * PODATKI: modul_kijou_podatki.php
 * VERZIJA: 1.0.0 (24.6.2026)
 */

declare(strict_types=1);

define('MODUL_kijou_IME', 'Skijou');
define('MODUL_kijou_VERZIJA', '1.0.0');

function modul_kijou_pridobi_podatke(): array {
    return [
        'ime' => 'Skijou',
        'id' => 'kijou',
        'verzija' => '1.0.0',
        'kljucne_besede' => ["shugendo","japonsko","asket","gore"],
        'opis' => 'Skijou modul'
    ];
}