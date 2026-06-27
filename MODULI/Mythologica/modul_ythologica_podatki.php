<?php
/**
 * MODUL: Mythologica
 * PODATKI: modul_ythologica_podatki.php
 * VERZIJA: 1.0.0 (24.6.2026)
 *
 * NAMEN:
 *     Statični podatki in konfiguracija modula Mythologica
 *     Element: ZRAK
 *     Barva: #00bcd4
 */

declare(strict_types=1);

define('MODUL_ythologica_IME', 'Mythologica');
define('MODUL_ythologica_VERZIJA', '1.0.0');
define('MODUL_ythologica_ELEMENT', 'ZRAK');
define('MODUL_ythologica_BARVA', '#00bcd4');
define('MODUL_ythologica_IKONA', '💨');

/**
 * Pridobi vse podatke modula
 */
function modul_ythologica_pridobi_podatke(): array {
    return [
        'ime' => 'Mythologica',
        'id' => 'ythologica',
        'element' => 'ZRAK',
        'barva' => '#00bcd4',
        'ikona' => '💨',
        'verzija' => '1.0.0',
        'kljucne_besede' => ["miti","legende","zgodbe","arhetipi"],
        'opis' => 'Mythologica modul v svetu ZRAK'
    ];
}