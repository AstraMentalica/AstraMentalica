<?php
/**
 * MODUL: Lunaris
 * PODATKI: modul_unaris_podatki.php
 * VERZIJA: 1.0.0 (24.6.2026)
 *
 * NAMEN:
 *     Statični podatki in konfiguracija modula Lunaris
 *     Element: VODA
 *     Barva: #2196f3
 */

declare(strict_types=1);

define('MODUL_unaris_IME', 'Lunaris');
define('MODUL_unaris_VERZIJA', '1.0.0');
define('MODUL_unaris_ELEMENT', 'VODA');
define('MODUL_unaris_BARVA', '#2196f3');
define('MODUL_unaris_IKONA', '🌊');

/**
 * Pridobi vse podatke modula
 */
function modul_unaris_pridobi_podatke(): array {
    return [
        'ime' => 'Lunaris',
        'id' => 'unaris',
        'element' => 'VODA',
        'barva' => '#2196f3',
        'ikona' => '🌊',
        'verzija' => '1.0.0',
        'kljucne_besede' => ["luna","cikli","faze","plima","čustva"],
        'opis' => 'Lunaris modul v svetu VODA'
    ];
}