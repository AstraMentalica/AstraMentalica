<?php
/**
 * MODUL: Ssekki
 * PODATKI: modul_sekki_podatki.php
 * VERZIJA: 1.0.0 (24.6.2026)
 */

declare(strict_types=1);

define('MODUL_sekki_IME', 'Ssekki');
define('MODUL_sekki_VERZIJA', '1.0.0');

function modul_sekki_pridobi_podatke(): array {
    return [
        'ime' => 'Ssekki',
        'id' => 'sekki',
        'verzija' => '1.0.0',
        'kljucne_besede' => ["seimei","geomancija","japonsko","divinacija"],
        'opis' => 'Ssekki modul'
    ];
}