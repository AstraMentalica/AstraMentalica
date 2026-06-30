<?php
/**
 * MODUL: CodexVerba
 * PODATKI: modul_odexerba_podatki.php
 * VERZIJA: 1.0.0 (24.6.2026)
 *
 * NAMEN:
 *     Statični podatki in konfiguracija modula CodexVerba
 *     Element: ZRAK
 *     Barva: #00bcd4
 */

declare(strict_types=1);

define('MODUL_odexerba_IME', 'CodexVerba');
define('MODUL_odexerba_VERZIJA', '1.0.0');
define('MODUL_odexerba_ELEMENT', 'ZRAK');
define('MODUL_odexerba_BARVA', '#00bcd4');
define('MODUL_odexerba_IKONA', '💨');

/**
 * Pridobi vse podatke modula
 */
function modul_odexerba_pridobi_podatke(): array {
    return [
        'ime' => 'CodexVerba',
        'id' => 'odexerba',
        'element' => 'ZRAK',
        'barva' => '#00bcd4',
        'ikona' => '💨',
        'verzija' => '1.0.0',
        'kljucne_besede' => ["besedila","mantre","izreki","sveto"],
        'opis' => 'CodexVerba modul v svetu ZRAK'
    ];
}