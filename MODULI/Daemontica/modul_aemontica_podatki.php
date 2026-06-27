<?php
/**
 * MODUL: Daemontica
 * PODATKI: modul_aemontica_podatki.php
 * VERZIJA: 1.0.0 (24.6.2026)
 */

declare(strict_types=1);

define('MODUL_aemontica_IME', 'Daemontica');
define('MODUL_aemontica_VERZIJA', '1.0.0');

function modul_aemontica_pridobi_podatke(): array {
    return [
        'ime' => 'Daemontica',
        'id' => 'aemontica',
        'verzija' => '1.0.0',
        'kljucne_besede' => ["demoni","bitja","duhovno","zaščita"],
        'opis' => 'Daemontica modul'
    ];
}