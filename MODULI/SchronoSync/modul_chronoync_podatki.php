<?php
/**
 * MODUL: SchronoSync
 * PODATKI: modul_chronoync_podatki.php
 * VERZIJA: 1.0.0 (24.6.2026)
 *
 * NAMEN:
 *     Statični podatki in konfiguracija modula SchronoSync
 *     Element: ETER
 *     Barva: #9c27b0
 */

declare(strict_types=1);

define('MODUL_chronoync_IME', 'SchronoSync');
define('MODUL_chronoync_VERZIJA', '1.0.0');
define('MODUL_chronoync_ELEMENT', 'ETER');
define('MODUL_chronoync_BARVA', '#9c27b0');
define('MODUL_chronoync_IKONA', '🌟');

/**
 * Pridobi vse podatke modula
 */
function modul_chronoync_pridobi_podatke(): array {
    return [
        'ime' => 'SchronoSync',
        'id' => 'chronoync',
        'element' => 'ETER',
        'barva' => '#9c27b0',
        'ikona' => '🌟',
        'verzija' => '1.0.0',
        'kljucne_besede' => ["čas","sinhronizacija","energija","temporealno"],
        'opis' => 'SchronoSync modul v svetu ETER'
    ];
}