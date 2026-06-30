<?php
/**
 * MODUL: Codex
 * PODATKI: modul_odex_podatki.php
 * VERZIJA: 1.0.0 (24.6.2026)
 */

declare(strict_types=1);

define('MODUL_odex_IME', 'Codex');
define('MODUL_odex_VERZIJA', '1.0.0');

function modul_odex_pridobi_podatke(): array {
    return [
        'ime' => 'Codex',
        'id' => 'odex',
        'verzija' => '1.0.0',
        'kljucne_besede' => ["koda","modrost","znanje","zbirka"],
        'opis' => 'Codex modul'
    ];
}