<?php
/**
 * ============================================================
 * GLOBALNO: strani/moduli/mystaia.php
 * POT: GLOBALNO/postavitev/strani/moduli/mystaia.php
 * ============================================================
 *
 * Namen:
 *     Front page za modul Mystaia — notranji misteriji, iniciacije, trgovina.
 * ============================================================
 */

declare(strict_types=1);

require_once __DIR__ . '/../modul_layout.php';

$mystaia = $vsebina['mystaia'] ?? [
    'naslov' => '🌀 Mystaia',
    'opis' => 'Notranji misteriji — iniciacije, mistični poti, notranje transformacije in trgovina.',
    'povezave' => [
        ['ime' => 'Domov', 'href' => '?svet=domov', 'aktivna' => false],
        ['ime' => 'Iniciacije', 'href' => '?modul=mystaia&akcija=iniciacije', 'aktivna' => true],
        ['ime' => 'Trgovina', 'href' => '?modul=mystaia&akcija=trgovina', 'aktivna' => false],
        ['ime' => 'Poti', 'href' => '?modul=mystaia&akcija=poti', 'aktivna' => false],
    ],
    'polja' => [
        ['ime' => 'iskalnik', 'oznaka' => 'Iskalnica', 'tip' => 'text', 'place' => 'Išči po iniciacijah ...'],
    ],
    'postavke' => [
        ['besedilo' => '🕯️ Iniciacija svetlobe — začetnik'],
        ['besedilo' => '🌑 Senčna pot — za intermediate'],
        ['besedilo' => '✨ Preobrazba — za napredne'],
        ['besedilo' => '💎 Predmet: Križarvor — 12 zvezd'],
        ['besedilo' => '📜 Rokopis: Namenski zapiski'],
    ],
];

echo globalno_modul_layout_izrisi($mystaia);
