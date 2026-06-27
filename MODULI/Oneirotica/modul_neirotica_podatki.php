<?php
/**
 * MODUL: Oneirotica
 * PODATKI: modul_neirotica_podatki.php
 * VERZIJA: 1.0.0 (24.6.2026)
 *
 * NAMEN:
 *     Statični podatki in konfiguracija modula Oneirotica
 *     Element: ZRAK
 *     Barva: #00bcd4
 */

declare(strict_types=1);

define('MODUL_neirotica_IME', 'Oneirotica');
define('MODUL_neirotica_VERZIJA', '1.0.0');
define('MODUL_neirotica_ELEMENT', 'ZRAK');
define('MODUL_neirotica_BARVA', '#00bcd4');
define('MODUL_neirotica_IKONA', '💨');

/**
 * Pridobi vse podatke modula
 */
function modul_neirotica_pridobi_podatke(): array {
    return [
        'ime' => 'Oneirotica',
        'id' => 'neirotica',
        'element' => 'ZRAK',
        'barva' => '#00bcd4',
        'ikona' => '💨',
        'verzija' => '1.0.0',
        'kljucne_besede' => ["sanje","interpretacija","lucidno","snovenje"],
        'opis' => 'Oneirotica modul v svetu ZRAK'
    ];
}