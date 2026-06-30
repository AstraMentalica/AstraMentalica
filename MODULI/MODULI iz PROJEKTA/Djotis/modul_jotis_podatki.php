<?php
/**
 * MODUL: Djotis
 * PODATKI: modul_jotis_podatki.php
 * VERZIJA: 1.0.0 (24.6.2026)
 */

declare(strict_types=1);

define('MODUL_jotis_IME', 'Djotis');
define('MODUL_jotis_VERZIJA', '1.0.0');

function modul_jotis_pridobi_podatke(): array {
    return [
        'ime' => 'Djotis',
        'id' => 'jotis',
        'verzija' => '1.0.0',
        'kljucne_besede' => ["vedska","astrologija","jyotish","zvezde"],
        'opis' => 'Djotis modul'
    ];
}