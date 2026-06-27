<?php
/**
 * MODUL: Herbarica
 * PODATKI: modul_erbarica_podatki.php
 * VERZIJA: 1.0.0 (24.6.2026)
 *
 * NAMEN:
 *     Statični podatki in konfiguracija modula Herbarica
 *     Element: ZEMLJA
 *     Barva: #4caf50
 */

declare(strict_types=1);

define('MODUL_erbarica_IME', 'Herbarica');
define('MODUL_erbarica_VERZIJA', '1.0.0');
define('MODUL_erbarica_ELEMENT', 'ZEMLJA');
define('MODUL_erbarica_BARVA', '#4caf50');
define('MODUL_erbarica_IKONA', '🌿');

/**
 * Pridobi vse podatke modula
 */
function modul_erbarica_pridobi_podatke(): array {
    return [
        'ime' => 'Herbarica',
        'id' => 'erbarica',
        'element' => 'ZEMLJA',
        'barva' => '#4caf50',
        'ikona' => '🌿',
        'verzija' => '1.0.0',
        'kljucne_besede' => ["zelišča","rastline","zdravljenje","naravno"],
        'opis' => 'Herbarica modul v svetu ZEMLJA'
    ];
}