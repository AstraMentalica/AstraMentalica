<?php
/**
 * MODUL: Orakleum
 * PODATKI: modul_rakleum_podatki.php
 * VERZIJA: 1.0.0 (24.6.2026)
 */

declare(strict_types=1);

define('MODUL_rakleum_IME', 'Orakleum');
define('MODUL_rakleum_VERZIJA', '1.0.0');

function modul_rakleum_pridobi_podatke(): array {
    return [
        'ime' => 'Orakleum',
        'id' => 'rakleum',
        'verzija' => '1.0.0',
        'kljucne_besede' => ["orakelj","vedeževanje","sistem","napovedi"],
        'opis' => 'Orakleum modul'
    ];
}