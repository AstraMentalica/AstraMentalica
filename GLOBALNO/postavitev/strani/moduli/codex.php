<?php
/**
 * ============================================================
 * GLOBALNO: strani/moduli/codex.php
 * POT: GLOBALNO/postavitev/strani/moduli/codex.php
 * ============================================================
 *
 * Namen:
 *     Front page za modul Codex — osebna knjiga, enciklopedija.
 * ============================================================
 */

declare(strict_types=1);

require_once __DIR__ . '/../modul_layout.php';

$codex = $vsebina['codex'] ?? [
    'naslov' => '📖 Codex',
    'opis' => 'Codex — osebna knjiga, enciklopedija tradicij, pojmov in simbolov. Tvoje notranje znanje.',
    'povezave' => [
        ['ime' => 'Domov', 'href' => '?svet=domov', 'aktivna' => false],
        ['ime' => 'Knjige', 'href' => '?modul=codex&akcija=knjige', 'aktivna' => true],
        ['ime' => 'Zaznamke', 'href' => '?modul=codex&akcija=zaznamke', 'aktivna' => false],
        ['ime' => 'Iskalnik', 'href' => '?modul=codex&akcija=iskanje', 'aktivna' => false],
    ],
    'polja' => [
        ['ime' => 'iskanje', 'oznaka' => 'Išči v knjigi', 'tip' => 'text', 'place' => 'Vnesi pojem ali simbol ...'],
        ['ime' => 'knjiga', 'oznaka' => 'Nova knjiga', 'tip' => 'text', 'place' => 'Ime knjige ...'],
    ],
    'postavke' => [
        ['besedilo' => '📜 Mudrosti — zbirka Citatov'],
        ['besedilo' => '📚 Tradicije — pregled vseh poti'],
        ['besedilo' => '🔣 Simboli — interpretacije'],
        ['besedilo' => '📖 Dnevnik — moje zapiski'],
        ['besedilo' => '🗂️ Kodeksi — zbirke po konceptih'],
    ],
];

echo globalno_modul_layout_izrisi($codex);
