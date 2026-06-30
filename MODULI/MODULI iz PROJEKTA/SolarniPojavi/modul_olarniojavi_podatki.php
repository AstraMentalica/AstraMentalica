<?php
/**
 * MODUL: SolarniPojavi
 * PODATKI: modul_olarniojavi_podatki.php
 * VERZIJA: 1.0.0 (24.6.2026)
 *
 * NAMEN:
 *     Statični podatki in konfiguracija modula SolarniPojavi
 *     Element: OGENJ
 *     Barva: #ff5722
 */

declare(strict_types=1);

define('MODUL_olarniojavi_IME', 'SolarniPojavi');
define('MODUL_olarniojavi_VERZIJA', '1.0.0');
define('MODUL_olarniojavi_ELEMENT', 'OGENJ');
define('MODUL_olarniojavi_BARVA', '#ff5722');
define('MODUL_olarniojavi_IKONA', '🔥');

/**
 * Pridobi vse podatke modula
 */
function modul_olarniojavi_pridobi_podatke(): array {
    return [
        'ime' => 'SolarniPojavi',
        'id' => 'olarniojavi',
        'element' => 'OGENJ',
        'barva' => '#ff5722',
        'ikona' => '🔥',
        'verzija' => '1.0.0',
        'kljucne_besede' => ["sonce","solar","energija","pojavi"],
        'opis' => 'SolarniPojavi modul v svetu OGENJ'
    ];
}