<?php
/**
 * MODUL: Sbazi
 * PODATKI: modul_bazi_podatki.php
 * VERZIJA: 1.0.0 (24.6.2026)
 *
 * NAMEN:
 *     Statični podatki in konfiguracija modula Sbazi
 *     Element: ZEMLJA
 *     Barva: #4caf50
 */

declare(strict_types=1);

define('MODUL_bazi_IME', 'Sbazi');
define('MODUL_bazi_VERZIJA', '1.0.0');
define('MODUL_bazi_ELEMENT', 'ZEMLJA');
define('MODUL_bazi_BARVA', '#4caf50');
define('MODUL_bazi_IKONA', '🌿');

/**
 * Pridobi vse podatke modula
 */
function modul_bazi_pridobi_podatke(): array {
    return [
        'ime' => 'Sbazi',
        'id' => 'bazi',
        'element' => 'ZEMLJA',
        'barva' => '#4caf50',
        'ikona' => '🌿',
        'verzija' => '1.0.0',
        'kljucne_besede' => ["bazi","astrologija","kitajsko","analiza"],
        'opis' => 'Sbazi modul v svetu ZEMLJA'
    ];
}