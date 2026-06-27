<?php
/**
 * MODUL: GeometricaSacra
 * PODATKI: modul_eometricaacra_podatki.php
 * VERZIJA: 1.0.0 (24.6.2026)
 *
 * NAMEN:
 *     Statični podatki in konfiguracija modula GeometricaSacra
 *     Element: ZEMLJA
 *     Barva: #4caf50
 */

declare(strict_types=1);

define('MODUL_eometricaacra_IME', 'GeometricaSacra');
define('MODUL_eometricaacra_VERZIJA', '1.0.0');
define('MODUL_eometricaacra_ELEMENT', 'ZEMLJA');
define('MODUL_eometricaacra_BARVA', '#4caf50');
define('MODUL_eometricaacra_IKONA', '🌿');

/**
 * Pridobi vse podatke modula
 */
function modul_eometricaacra_pridobi_podatke(): array {
    return [
        'ime' => 'GeometricaSacra',
        'id' => 'eometricaacra',
        'element' => 'ZEMLJA',
        'barva' => '#4caf50',
        'ikona' => '🌿',
        'verzija' => '1.0.0',
        'kljucne_besede' => ["geometrija","sakralno","vzorci","sveto"],
        'opis' => 'GeometricaSacra modul v svetu ZEMLJA'
    ];
}