<?php
/**
 * MODUL: AlchymiaAurea
 * PODATKI: modul_lchymiaurea_podatki.php
 * VERZIJA: 1.0.0 (24.6.2026)
 *
 * NAMEN:
 *     Statični podatki in konfiguracija modula AlchymiaAurea
 *     Element: ZEMLJA
 *     Barva: #4caf50
 */

declare(strict_types=1);

define('MODUL_lchymiaurea_IME', 'AlchymiaAurea');
define('MODUL_lchymiaurea_VERZIJA', '1.0.0');
define('MODUL_lchymiaurea_ELEMENT', 'ZEMLJA');
define('MODUL_lchymiaurea_BARVA', '#4caf50');
define('MODUL_lchymiaurea_IKONA', '🌿');

/**
 * Pridobi vse podatke modula
 */
function modul_lchymiaurea_pridobi_podatke(): array {
    return [
        'ime' => 'AlchymiaAurea',
        'id' => 'lchymiaurea',
        'element' => 'ZEMLJA',
        'barva' => '#4caf50',
        'ikona' => '🌿',
        'verzija' => '1.0.0',
        'kljucne_besede' => ["alkimija","transmutacija","zlato","filozofija"],
        'opis' => 'AlchymiaAurea modul v svetu ZEMLJA'
    ];
}