<?php
/**
 * MODUL: SwuXing
 * PODATKI: modul_wuing_podatki.php
 * VERZIJA: 1.0.0 (24.6.2026)
 *
 * NAMEN:
 *     Statični podatki in konfiguracija modula SwuXing
 *     Element: ZEMLJA
 *     Barva: #4caf50
 */

declare(strict_types=1);

define('MODUL_wuing_IME', 'SwuXing');
define('MODUL_wuing_VERZIJA', '1.0.0');
define('MODUL_wuing_ELEMENT', 'ZEMLJA');
define('MODUL_wuing_BARVA', '#4caf50');
define('MODUL_wuing_IKONA', '🌿');

/**
 * Pridobi vse podatke modula
 */
function modul_wuing_pridobi_podatke(): array {
    return [
        'ime' => 'SwuXing',
        'id' => 'wuing',
        'element' => 'ZEMLJA',
        'barva' => '#4caf50',
        'ikona' => '🌿',
        'verzija' => '1.0.0',
        'kljucne_besede' => ["wuxing","elementi","cikel","kitajsko"],
        'opis' => 'SwuXing modul v svetu ZEMLJA'
    ];
}