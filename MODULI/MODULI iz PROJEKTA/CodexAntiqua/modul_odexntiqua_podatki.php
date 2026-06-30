<?php
/**
 * MODUL: CodexAntiqua
 * PODATKI: modul_odexntiqua_podatki.php
 * VERZIJA: 1.0.0 (24.6.2026)
 */

declare(strict_types=1);

define('MODUL_odexntiqua_IME', 'CodexAntiqua');
define('MODUL_odexntiqua_VERZIJA', '1.0.0');

function modul_odexntiqua_pridobi_podatke(): array {
    return [
        'ime' => 'CodexAntiqua',
        'id' => 'odexntiqua',
        'verzija' => '1.0.0',
        'kljucne_besede' => ["starodavno","besedila","rokopisi","zgodovina"],
        'opis' => 'CodexAntiqua modul'
    ];
}