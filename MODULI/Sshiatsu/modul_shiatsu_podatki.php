<?php
/**
 * MODUL: Sshiatsu
 * PODATKI: modul_shiatsu_podatki.php
 * VERZIJA: 1.0.0 (24.6.2026)
 */

declare(strict_types=1);

define('MODUL_shiatsu_IME', 'Sshiatsu');
define('MODUL_shiatsu_VERZIJA', '1.0.0');

function modul_shiatsu_pridobi_podatke(): array {
    return [
        'ime' => 'Sshiatsu',
        'id' => 'shiatsu',
        'verzija' => '1.0.0',
        'kljucne_besede' => ["shiatsu","terapija","japonsko","masaža"],
        'opis' => 'Sshiatsu modul'
    ];
}