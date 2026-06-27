<?php
/**
 * MODUL: AuroraMystica
 * PODATKI: modul_uroraystica_podatki.php
 * VERZIJA: 1.0.0 (24.6.2026)
 *
 * NAMEN:
 *     Statični podatki in konfiguracija modula AuroraMystica
 *     Element: ETER
 *     Barva: #9c27b0
 */

declare(strict_types=1);

define('MODUL_uroraystica_IME', 'AuroraMystica');
define('MODUL_uroraystica_VERZIJA', '1.0.0');
define('MODUL_uroraystica_ELEMENT', 'ETER');
define('MODUL_uroraystica_BARVA', '#9c27b0');
define('MODUL_uroraystica_IKONA', '🌟');

/**
 * Pridobi vse podatke modula
 */
function modul_uroraystica_pridobi_podatke(): array {
    return [
        'ime' => 'AuroraMystica',
        'id' => 'uroraystica',
        'element' => 'ETER',
        'barva' => '#9c27b0',
        'ikona' => '🌟',
        'verzija' => '1.0.0',
        'kljucne_besede' => ["aurora","nebo","svetloba","kozmos"],
        'opis' => 'AuroraMystica modul v svetu ETER'
    ];
}