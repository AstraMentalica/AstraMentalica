<?php
/**
 * MODUL: Sigillaris
 * PODATKI: modul_igillaris_podatki.php
 * VERZIJA: 1.0.0 (24.6.2026)
 */

declare(strict_types=1);

define('MODUL_igillaris_IME', 'Sigillaris');
define('MODUL_igillaris_VERZIJA', '1.0.0');

function modul_igillaris_pridobi_podatke(): array {
    return [
        'ime' => 'Sigillaris',
        'id' => 'igillaris',
        'verzija' => '1.0.0',
        'kljucne_besede' => ["sigili","simboli","magija","zaščita"],
        'opis' => 'Sigillaris modul'
    ];
}