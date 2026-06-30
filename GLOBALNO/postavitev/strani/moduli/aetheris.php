<?php
/**
 * ============================================================
 * GLOBALNO: strani/moduli/aetheris.php
 * POT: GLOBALNO/postavitev/strani/moduli/aetheris.php
 * ============================================================
 *
 * Namen:
 *     Front page za modul Aetheris — forum energetskega polja in aure.
 * ============================================================
 */

declare(strict_types=1);

require_once __DIR__ . '/../modul_layout.php';

$aetheris = $vsebina['aetheris'] ?? [
    'naslov' => '💚 Aetheris',
    'opis' => 'Eter in višje ravni — forum energetskega polja, a ure, barvne analize in čiščenje.',
    'povezave' => [
        ['ime' => 'Domov', 'href' => '?svet=domov', 'aktivna' => false],
        ['ime' => 'Forum', 'href' => '?modul=aetheris&akcija=forum', 'aktivna' => true],
        ['ime' => 'Avra', 'href' => '?modul=aetheris&akcija=avra', 'aktivna' => false],
        ['ime' => 'Analiza', 'href' => '?modul=aetheris&akcija=analiza', 'aktivna' => false],
    ],
    'polja' => [
        ['ime' => 'objava', 'oznaka' => 'Nova objava', 'tip' => 'textarea', 'place' => 'Tvoja sporočila ...'],
        ['ime' => 'barva', 'oznaka' => 'Barva aura', 'tip' => 'select', 'options' => [
            ['v' => 'modra', 'i' => '🔵 Modra'],
            ['v' => 'zelena', 'i' => '🟢 Zelena'],
            ['v' => 'rumena', 'i' => '🟡 Rumena'],
            ['v' => 'vijola', 'i' => '🟣 Vijola'],
        ]],
    ],
    'postavke' => [
        ['besedilo' => '💬 Kako čistimo energetsko polje?'],
        ['besedilo' => '🔮 Barvna terapija za začetnike'],
        ['besedilo' => '🌿 Naravni načini za čiščenje a ure'],
        ['besedilo' => '📊 Moja aura — dnevna meritev'],
    ],
];

echo globalno_modul_layout_izrisi($aetheris);
