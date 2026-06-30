<?php
/**
 * ============================================================
 * GLOBALNO: strani/moduli/tarot.php
 * POT: GLOBALNO/postavitev/strani/moduli/tarot.php
 * ============================================================
 *
 * Namen:
 *     Front page za modul Tarot — arhetipske karte.
 * ============================================================
 */

declare(strict_types=1);

require_once __DIR__ . '/../modul_layout.php';

$tarot = $vsebina['tarot'] ?? [
    'naslov' => '🃏 Tarot',
    'opis' => 'Arhetipske karte — razlage in globinska simbolika za samorazumevanje.',
    'povezave' => [
        ['ime' => 'Domov', 'href' => '?svet=domov', 'aktivna' => false],
        ['ime' => 'Karte', 'href' => '?modul=tarot&akcija=karte', 'aktivna' => true],
        ['ime' => 'Razlaga', 'href' => '?modul=tarot&akcija=razlaga', 'aktivna' => false],
        ['ime' => 'Zgodovina', 'href' => '?modul=tarot&akcija=zgodovina', 'aktivna' => false],
    ],
    'polja' => [
        ['ime' => 'vprasanje', 'oznaka' => 'Tvoje vprašanje', 'tip' => 'text', 'place' => 'Vpiši vprašanje za karte ...'],
        ['ime' => 'znamenje', 'oznaka' => 'Znamenje', 'tip' => 'select', 'options' => [
            ['v' => 'dan', 'i' => '☀️ Dan'],
            ['v' => 'mesec', 'i' => '🌙 Mesec'],
            ['v' => 'zvezda', 'i' => '⭐ Zvezda'],
        ]],
    ],
    'postavke' => [
        ['besedilo' => '🔮 Starševa karta — arhetip modrosti'],
        ['besedilo' => '🌙 Luna — spremembe in intuicija'],
        ['besedilo' => '⚖️ Pravica — ravnotežje in odločitve'],
        ['besedilo' => '💀 Smrt — preobrazba in dokončanje'],
    ],
];

echo globalno_modul_layout_izrisi($tarot);
