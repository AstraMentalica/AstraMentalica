<?php
/**
 * MODUL: MysticaMesoAmericana
 * PODATKI: modul_ysticaesomericana_podatki.php
 * VERZIJA: 1.0.0 (24.6.2026)
 */

declare(strict_types=1);

define('MODUL_ysticaesomericana_IME', 'MysticaMesoAmericana');
define('MODUL_ysticaesomericana_VERZIJA', '1.0.0');

function modul_ysticaesomericana_pridobi_podatke(): array {
    return [
        'ime' => 'MysticaMesoAmericana',
        'id' => 'ysticaesomericana',
        'verzija' => '1.0.0',
        'kljucne_besede' => ["maje","azteki","mistika","amerika"],
        'opis' => 'MysticaMesoAmericana modul'
    ];
}