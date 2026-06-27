<?php
/**
 * MODUL: MedicinaOrientalis
 * PODATKI: modul_edicinarientalis_podatki.php
 * VERZIJA: 1.0.0 (24.6.2026)
 *
 * NAMEN:
 *     Statični podatki in konfiguracija modula MedicinaOrientalis
 *     Element: ZEMLJA
 *     Barva: #4caf50
 */

declare(strict_types=1);

define('MODUL_edicinarientalis_IME', 'MedicinaOrientalis');
define('MODUL_edicinarientalis_VERZIJA', '1.0.0');
define('MODUL_edicinarientalis_ELEMENT', 'ZEMLJA');
define('MODUL_edicinarientalis_BARVA', '#4caf50');
define('MODUL_edicinarientalis_IKONA', '🌿');

/**
 * Pridobi vse podatke modula
 */
function modul_edicinarientalis_pridobi_podatke(): array {
    return [
        'ime' => 'MedicinaOrientalis',
        'id' => 'edicinarientalis',
        'element' => 'ZEMLJA',
        'barva' => '#4caf50',
        'ikona' => '🌿',
        'verzija' => '1.0.0',
        'kljucne_besede' => ["kitajska","medicina","akupunktura","zelišča"],
        'opis' => 'MedicinaOrientalis modul v svetu ZEMLJA'
    ];
}