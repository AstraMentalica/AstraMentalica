<?php
/**
 * MODUL: Syijing
 * PODATKI: modul_yijing_podatki.php
 * VERZIJA: 1.0.0 (24.6.2026)
 *
 * NAMEN:
 *     Statični podatki in konfiguracija modula Syijing
 *     Element: ZEMLJA
 *     Barva: #4caf50
 */

declare(strict_types=1);

define('MODUL_yijing_IME', 'Syijing');
define('MODUL_yijing_VERZIJA', '1.0.0');
define('MODUL_yijing_ELEMENT', 'ZEMLJA');
define('MODUL_yijing_BARVA', '#4caf50');
define('MODUL_yijing_IKONA', '🌿');

/**
 * Pridobi vse podatke modula
 */
function modul_yijing_pridobi_podatke(): array {
    return [
        'ime' => 'Syijing',
        'id' => 'yijing',
        'element' => 'ZEMLJA',
        'barva' => '#4caf50',
        'ikona' => '🌿',
        'verzija' => '1.0.0',
        'kljucne_besede' => ["iching","spremembe","vedeževanje","kitajsko"],
        'opis' => 'Syijing modul v svetu ZEMLJA'
    ];
}