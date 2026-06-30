<?php
/**
 * MODUL: Sonaris
 * PODATKI: modul_onaris_podatki.php
 * VERZIJA: 1.0.0 (24.6.2026)
 *
 * NAMEN:
 *     Statični podatki in konfiguracija modula Sonaris
 *     Element: VODA
 *     Barva: #2196f3
 */

declare(strict_types=1);

define('MODUL_onaris_IME', 'Sonaris');
define('MODUL_onaris_VERZIJA', '1.0.0');
define('MODUL_onaris_ELEMENT', 'VODA');
define('MODUL_onaris_BARVA', '#2196f3');
define('MODUL_onaris_IKONA', '🌊');

/**
 * Pridobi vse podatke modula
 */
function modul_onaris_pridobi_podatke(): array {
    return [
        'ime' => 'Sonaris',
        'id' => 'onaris',
        'element' => 'VODA',
        'barva' => '#2196f3',
        'ikona' => '🌊',
        'verzija' => '1.0.0',
        'kljucne_besede' => ["zvok","terapija","harmonija","frekvence"],
        'opis' => 'Sonaris modul v svetu VODA'
    ];
}