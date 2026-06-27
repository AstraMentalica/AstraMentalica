<?php
/**
 * MODUL: Skami
 * PODATKI: modul_kami_podatki.php
 * VERZIJA: 1.0.0 (24.6.2026)
 */

declare(strict_types=1);

define('MODUL_kami_IME', 'Skami');
define('MODUL_kami_VERZIJA', '1.0.0');

function modul_kami_pridobi_podatke(): array {
    return [
        'ime' => 'Skami',
        'id' => 'kami',
        'verzija' => '1.0.0',
        'kljucne_besede' => ["kami","japonsko","šinto","duhovi"],
        'opis' => 'Skami modul'
    ];
}