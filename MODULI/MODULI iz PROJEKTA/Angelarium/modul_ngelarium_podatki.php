<?php
/**
 * MODUL: Angelarium
 * PODATKI: modul_ngelarium_podatki.php
 * VERZIJA: 1.0.0 (24.6.2026)
 *
 * NAMEN:
 *     Statični podatki in konfiguracija modula Angelarium
 *     Element: ETER
 *     Barva: #9c27b0
 */

declare(strict_types=1);

define('MODUL_ngelarium_IME', 'Angelarium');
define('MODUL_ngelarium_VERZIJA', '1.0.0');
define('MODUL_ngelarium_ELEMENT', 'ETER');
define('MODUL_ngelarium_BARVA', '#9c27b0');
define('MODUL_ngelarium_IKONA', '🌟');

/**
 * Pridobi vse podatke modula
 */
function modul_ngelarium_pridobi_podatke(): array {
    return [
        'ime' => 'Angelarium',
        'id' => 'ngelarium',
        'element' => 'ETER',
        'barva' => '#9c27b0',
        'ikona' => '🌟',
        'verzija' => '1.0.0',
        'kljucne_besede' => ["angeli","arhangeli","nebo","hierarhija"],
        'opis' => 'Angelarium modul v svetu ETER'
    ];
}