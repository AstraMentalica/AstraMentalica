<?php
/**
 * MODUL: SqiMapper
 * PODATKI: modul_qiapper_podatki.php
 * VERZIJA: 1.0.0 (24.6.2026)
 */

declare(strict_types=1);

define('MODUL_qiapper_IME', 'SqiMapper');
define('MODUL_qiapper_VERZIJA', '1.0.0');

function modul_qiapper_pridobi_podatke(): array {
    return [
        'ime' => 'SqiMapper',
        'id' => 'qiapper',
        'verzija' => '1.0.0',
        'kljucne_besede' => ["qi","zemljevid","energija","kartografija"],
        'opis' => 'SqiMapper modul'
    ];
}