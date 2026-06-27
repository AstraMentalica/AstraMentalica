<?php
/**
 * MODUL: Sshenlong
 * PODATKI: modul_shenlong_podatki.php
 * VERZIJA: 1.0.0 (24.6.2026)
 */

declare(strict_types=1);

define('MODUL_shenlong_IME', 'Sshenlong');
define('MODUL_shenlong_VERZIJA', '1.0.0');

function modul_shenlong_pridobi_podatke(): array {
    return [
        'ime' => 'Sshenlong',
        'id' => 'shenlong',
        'verzija' => '1.0.0',
        'kljucne_besede' => ["zmaj","kitajsko","duhovno","shen"],
        'opis' => 'Sshenlong modul'
    ];
}