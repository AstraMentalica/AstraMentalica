<?php
/**
 * MODUL: SyneraVip
 * PODATKI: modul_yneraip_podatki.php
 * VERZIJA: 1.0.0 (24.6.2026)
 */

declare(strict_types=1);

define('MODUL_yneraip_IME', 'SyneraVip');
define('MODUL_yneraip_VERZIJA', '1.0.0');

function modul_yneraip_pridobi_podatke(): array {
    return [
        'ime' => 'SyneraVip',
        'id' => 'yneraip',
        'verzija' => '1.0.0',
        'kljucne_besede' => ["sinergija","vip","napredno","energija"],
        'opis' => 'SyneraVip modul'
    ];
}