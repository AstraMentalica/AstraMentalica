<?php
/**
 * MODUL: BotanicSacra
 * PODATKI: modul_otanicacra_podatki.php
 * VERZIJA: 1.0.0 (24.6.2026)
 */

declare(strict_types=1);

define('MODUL_otanicacra_IME', 'BotanicSacra');
define('MODUL_otanicacra_VERZIJA', '1.0.0');

function modul_otanicacra_pridobi_podatke(): array {
    return [
        'ime' => 'BotanicSacra',
        'id' => 'otanicacra',
        'verzija' => '1.0.0',
        'kljucne_besede' => ["rastline","sakralno","sveto","botanika"],
        'opis' => 'BotanicSacra modul'
    ];
}