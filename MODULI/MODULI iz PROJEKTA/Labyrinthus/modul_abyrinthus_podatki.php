<?php
/**
 * MODUL: Labyrinthus
 * PODATKI: modul_abyrinthus_podatki.php
 * VERZIJA: 1.0.0 (24.6.2026)
 */

declare(strict_types=1);

define('MODUL_abyrinthus_IME', 'Labyrinthus');
define('MODUL_abyrinthus_VERZIJA', '1.0.0');

function modul_abyrinthus_pridobi_podatke(): array {
    return [
        'ime' => 'Labyrinthus',
        'id' => 'abyrinthus',
        'verzija' => '1.0.0',
        'kljucne_besede' => ["labirint","mandala","meditacija","vzorci"],
        'opis' => 'Labyrinthus modul'
    ];
}