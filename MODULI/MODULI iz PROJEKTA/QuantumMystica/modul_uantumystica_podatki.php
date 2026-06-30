<?php
/**
 * MODUL: QuantumMystica
 * PODATKI: modul_uantumystica_podatki.php
 * VERZIJA: 1.0.0 (24.6.2026)
 *
 * NAMEN:
 *     Statični podatki in konfiguracija modula QuantumMystica
 *     Element: ETER
 *     Barva: #9c27b0
 */

declare(strict_types=1);

define('MODUL_uantumystica_IME', 'QuantumMystica');
define('MODUL_uantumystica_VERZIJA', '1.0.0');
define('MODUL_uantumystica_ELEMENT', 'ETER');
define('MODUL_uantumystica_BARVA', '#9c27b0');
define('MODUL_uantumystica_IKONA', '🌟');

/**
 * Pridobi vse podatke modula
 */
function modul_uantumystica_pridobi_podatke(): array {
    return [
        'ime' => 'QuantumMystica',
        'id' => 'uantumystica',
        'element' => 'ETER',
        'barva' => '#9c27b0',
        'ikona' => '🌟',
        'verzija' => '1.0.0',
        'kljucne_besede' => ["kvantno","dimenzije","resničnost","mistik"],
        'opis' => 'QuantumMystica modul v svetu ETER'
    ];
}