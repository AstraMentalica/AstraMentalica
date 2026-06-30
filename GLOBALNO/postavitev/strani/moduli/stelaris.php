<?php
/**
 * ============================================================
 * GLOBALNO: strani/moduli/stelaris.php
 * POT: GLOBALNO/postavitev/strani/moduli/stelaris.php
 * ============================================================
 *
 * Namen:
 *     Front page za modul Stelaris — zvezdno nebo, astrologija, horoskop.
 * ============================================================
 */

declare(strict_types=1);

require_once __DIR__ . '/../modul_layout.php';

$stelaris = $vsebina['stelaris'] ?? [
    'naslov' => '🌌 Stelaris',
    'opis' => 'Zvezdno nebo — astrološki motor, natalni horoskop, tranziti in aspekti.',
    'povezave' => [
        ['ime' => 'Domov', 'href' => '?svet=domov', 'aktivna' => false],
        ['ime' => 'Horoskop', 'href' => '?modul=stelaris&akcija=horoskop', 'aktivna' => true],
        ['ime' => 'Tranziti', 'href' => '?modul=stelaris&akcija=tranziti', 'aktivna' => false],
        ['ime' => 'Planeti', 'href' => '?modul=stelaris&akcija=planeti', 'aktivna' => false],
    ],
    'polja' => [
        ['ime' => 'datum', 'oznaka' => 'Datum rojstva', 'tip' => 'text', 'place' => 'YYYY-MM-DD'],
        ['ime' => 'ura', 'oznaka' => 'Ura rojstva', 'tip' => 'text', 'place' => 'HH:MM'],
        ['ime' => 'kraj', 'oznaka' => 'Kraj rojstva', 'tip' => 'text', 'place' => 'Mesto ...'],
    ],
    'postavke' => [
        ['besedilo' => '☀️ Sonce — identiteta'],
        ['besedilo' => '🌙 Luna — čustva'],
        ['besedilo' => '☿ Merkur — komunikacija'],
        ['besedilo' => '♀ Venera — ljubezen'],
        ['besedilo' => '♂ Mars — energija'],
        ['besedilo' => '♃ Jupiter — rast'],
        ['besedilo' => '♄ Saturn — urna'],
    ],
];

echo globalno_modul_layout_izrisi($stelaris);
