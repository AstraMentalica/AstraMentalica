<?php
/**
 * MODUL: SfenShui
 * PODATKI: modul_fenhui_podatki.php
 * VERZIJA: 1.0.0 (24.6.2026)
 *
 * NAMEN:
 *     Statični podatki in konfiguracija modula SfenShui
 *     Element: ZEMLJA
 *     Barva: #4caf50
 */

declare(strict_types=1);

define('MODUL_fenhui_IME', 'SfenShui');
define('MODUL_fenhui_VERZIJA', '1.0.0');
define('MODUL_fenhui_ELEMENT', 'ZEMLJA');
define('MODUL_fenhui_BARVA', '#4caf50');
define('MODUL_fenhui_IKONA', '🌿');

/**
 * Pridobi vse podatke modula
 */
function modul_fenhui_pridobi_podatke(): array {
    return [
        'ime' => 'SfenShui',
        'id' => 'fenhui',
        'element' => 'ZEMLJA',
        'barva' => '#4caf50',
        'ikona' => '🌿',
        'verzija' => '1.0.0',
        'kljucne_besede' => ["fengshui","prostor","energija","harmonija"],
        'opis' => 'SfenShui modul v svetu ZEMLJA'
    ];
}