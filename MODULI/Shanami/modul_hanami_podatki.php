<?php
/**
 * MODUL: Shanami
 * PODATKI: modul_hanami_podatki.php
 * VERZIJA: 1.0.0 (24.6.2026)
 */

declare(strict_types=1);

define('MODUL_hanami_IME', 'Shanami');
define('MODUL_hanami_VERZIJA', '1.0.0');

function modul_hanami_pridobi_podatke(): array {
    return [
        'ime' => 'Shanami',
        'id' => 'hanami',
        'verzija' => '1.0.0',
        'kljucne_besede' => ["šaman","potovanja","duhovno","prakse"],
        'opis' => 'Shanami modul'
    ];
}