<?php
/**
 * ============================================================
 * GLOBALNO: strani/moduli/runaris.php
 * POT: GLOBALNO/postavitev/strani/moduli/runaris.php
 * ============================================================
 *
 * Namen:
 *     Front page za modul Runaris — Elder Futhark rune, nordijska divinacija.
 * ============================================================
 */

declare(strict_types=1);

require_once __DIR__ . '/../modul_layout.php';

$runaris = $vsebina['runaris'] ?? [
    'naslov' => 'ᚱ Runaris',
    'opis' => 'Elder Futhark — 24 run, njihovi pomeni in metanje. Nordijska divinacija.',
    'povezave' => [
        ['ime' => 'Domov', 'href' => '?svet=domov', 'aktivna' => false],
        ['ime' => 'Rune', 'href' => '?modul=runaris&akcija=rune', 'aktivna' => true],
        ['ime' => 'Metanje', 'href' => '?modul=runaris&akcija=metanje', 'aktivna' => false],
        ['ime' => 'Zgodovina', 'href' => '?modul=runaris&akcija=zgodovina', 'aktivna' => false],
    ],
    'polja' => [
        ['ime' => 'vprasanje', 'oznaka' => 'Vprašanje za rune', 'tip' => 'text', 'place' => 'Vpiši vprašanje ...'],
        ['ime' => 'stevilo', 'oznaka' => 'Število run', 'tip' => 'select', 'options' => [
            ['v' => '1', 'i' => '1 runa'],
            ['v' => '3', 'i' => '3 rune'],
            ['v' => '5', 'i' => '5 run'],
        ]],
    ],
    'postavke' => [
        ['besedilo' => 'ᚠ Fehu — premoženje, priložnost'],
        ['besedilo' => 'ᚢ Uruz — moč, zdravje'],
        ['besedilo' => 'ᚦ Thurisaz — varuh, odločitev'],
        ['besedilo' => 'ᚨ Ansuz — božanski signal, beseda'],
        ['besedilo' => 'ᚱ Raido — potnik, premik'],
    ],
];

echo globalno_modul_layout_izrisi($runaris);
