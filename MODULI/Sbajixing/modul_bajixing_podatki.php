<?php
/**
 * MODUL: Sbajixing
 * PODATKI: modul_bajixing_podatki.php
 * VERZIJA: 1.0.0 (24.6.2026)
 *
 * NAMEN:
 *     Statični podatki in konfiguracija modula Sbajixing
 *     Element: ZEMLJA
 *     Barva: #4caf50
 */

declare(strict_types=1);

define('MODUL_bajixing_IME', 'Sbajixing');
define('MODUL_bajixing_VERZIJA', '1.0.0');
define('MODUL_bajixing_ELEMENT', 'ZEMLJA');
define('MODUL_bajixing_BARVA', '#4caf50');
define('MODUL_bajixing_IKONA', '🌿');

/**
 * Pridobi vse podatke modula
 */
function modul_bajixing_pridobi_podatke(): array {
    return [
        'ime' => 'Sbajixing',
        'id' => 'bajixing',
        'element' => 'ZEMLJA',
        'barva' => '#4caf50',
        'ikona' => '🌿',
        'verzija' => '1.0.0',
        'kljucne_besede' => ["ziwei","astrologija","cesarsko","doušu"],
        'opis' => 'Sbajixing modul v svetu ZEMLJA'
    ];
}