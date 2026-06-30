<?php
/**
 * MODUL: Meditara
 * PODATKI: modul_editara_podatki.php
 * VERZIJA: 1.0.0 (24.6.2026)
 *
 * NAMEN:
 *     Statični podatki in konfiguracija modula Meditara
 *     Element: VODA
 *     Barva: #2196f3
 */

declare(strict_types=1);

define('MODUL_editara_IME', 'Meditara');
define('MODUL_editara_VERZIJA', '1.0.0');
define('MODUL_editara_ELEMENT', 'VODA');
define('MODUL_editara_BARVA', '#2196f3');
define('MODUL_editara_IKONA', '🌊');

/**
 * Pridobi vse podatke modula
 */
function modul_editara_pridobi_podatke(): array {
    return [
        'ime' => 'Meditara',
        'id' => 'editara',
        'element' => 'VODA',
        'barva' => '#2196f3',
        'ikona' => '🌊',
        'verzija' => '1.0.0',
        'kljucne_besede' => ["meditacija","mir","čustva","vodene"],
        'opis' => 'Meditara modul v svetu VODA'
    ];
}