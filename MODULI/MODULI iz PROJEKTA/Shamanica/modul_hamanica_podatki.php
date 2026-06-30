<?php
/**
 * MODUL: Shamanica
 * PODATKI: modul_hamanica_podatki.php
 * VERZIJA: 1.0.0 (24.6.2026)
 *
 * NAMEN:
 *     Statični podatki in konfiguracija modula Shamanica
 *     Element: OGENJ
 *     Barva: #ff5722
 */

declare(strict_types=1);

define('MODUL_hamanica_IME', 'Shamanica');
define('MODUL_hamanica_VERZIJA', '1.0.0');
define('MODUL_hamanica_ELEMENT', 'OGENJ');
define('MODUL_hamanica_BARVA', '#ff5722');
define('MODUL_hamanica_IKONA', '🔥');

/**
 * Pridobi vse podatke modula
 */
function modul_hamanica_pridobi_podatke(): array {
    return [
        'ime' => 'Shamanica',
        'id' => 'hamanica',
        'element' => 'OGENJ',
        'barva' => '#ff5722',
        'ikona' => '🔥',
        'verzija' => '1.0.0',
        'kljucne_besede' => ["šaman","ogenj","obredi","duhovno"],
        'opis' => 'Shamanica modul v svetu OGENJ'
    ];
}