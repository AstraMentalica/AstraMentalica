<?php
/**
 * MODUL: Crystallum
 * PODATKI: modul_rystallum_podatki.php
 * VERZIJA: 1.0.0 (24.6.2026)
 */

declare(strict_types=1);

define('MODUL_rystallum_IME', 'Crystallum');
define('MODUL_rystallum_VERZIJA', '1.0.0');

function modul_rystallum_pridobi_podatke(): array {
    return [
        'ime' => 'Crystallum',
        'id' => 'rystallum',
        'verzija' => '1.0.0',
        'kljucne_besede' => ["kristali","energija","lastnosti","zdravljenje"],
        'opis' => 'Crystallum modul'
    ];
}