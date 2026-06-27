<?php
/**
 * MODUL: Sunmei
 * PODATKI: modul_unmei_podatki.php
 * VERZIJA: 1.0.0 (24.6.2026)
 */

declare(strict_types=1);

define('MODUL_unmei_IME', 'Sunmei');
define('MODUL_unmei_VERZIJA', '1.0.0');

function modul_unmei_pridobi_podatke(): array {
    return [
        'ime' => 'Sunmei',
        'id' => 'unmei',
        'verzija' => '1.0.0',
        'kljucne_besede' => ["unmei","usoda","japonsko","astrologija"],
        'opis' => 'Sunmei modul'
    ];
}