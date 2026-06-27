<?php
/**
 * MODUL: SauraMetrics
 * PODATKI: modul_auraetrics_podatki.php
 * VERZIJA: 1.0.0 (24.6.2026)
 */

declare(strict_types=1);

define('MODUL_auraetrics_IME', 'SauraMetrics');
define('MODUL_auraetrics_VERZIJA', '1.0.0');

function modul_auraetrics_pridobi_podatke(): array {
    return [
        'ime' => 'SauraMetrics',
        'id' => 'auraetrics',
        'verzija' => '1.0.0',
        'kljucne_besede' => ["aura","merjenje","energija","analiza"],
        'opis' => 'SauraMetrics modul'
    ];
}