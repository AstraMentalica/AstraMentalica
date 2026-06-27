<?php
/**
 * MODUL: Mystica
 * PODATKI: modul_ystica_podatki.php
 * VERZIJA: 1.0.0 (24.6.2026)
 */

declare(strict_types=1);

define('MODUL_ystica_IME', 'Mystica');
define('MODUL_ystica_VERZIJA', '1.0.0');

function modul_ystica_pridobi_podatke(): array {
    return [
        'ime' => 'Mystica',
        'id' => 'ystica',
        'verzija' => '1.0.0',
        'kljucne_besede' => ["mistika","ezoterika","tradicija","znanje"],
        'opis' => 'Mystica modul'
    ];
}