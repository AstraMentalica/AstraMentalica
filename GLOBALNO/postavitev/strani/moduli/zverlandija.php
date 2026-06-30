<?php
/**
 * ============================================================
 * GLOBALNO: strani/moduli/zverlandija.php
 * POT: GLOBALNO/postavitev/strani/moduli/zverlandija.php
 * ============================================================
 *
 * Namen:
 *     Front page za modul Zverlandija — otroški svet magičnih živali.
 * ============================================================
 */

declare(strict_types=1);

require_once __DIR__ . '/../modul_layout.php';

$zverlandija = $vsebina['zverlandija'] ?? [
    'naslov' => '🦊 Zverlandija',
    'opis' => 'Otroški svet poln magičnih živali, ki pišejo zgodbe in učijo skozi igro in dogodivščine.',
    'povezave' => [
        ['ime' => '🏠 Domov', 'href' => '?modul=zverlandija&akcija=domov', 'aktivna' => true],
        ['ime' => '🌲 Gozd', 'href' => '?modul=zverlandija&akcija=svet&svet_id=gozd', 'aktivna' => false],
        ['ime' => '🌊 Morje', 'href' => '?modul=zverlandija&akcija=svet&svet_id=morje', 'aktivna' => false],
        ['ime' => '☁️ Nebo', 'href' => '?modul=zverlandija&akcija=svet&svet_id=nebo', 'aktivna' => false],
        ['ime' => '⛰️ Gora', 'href' => '?modul=zverlandija&akcija=svet&svet_id=gora', 'aktivna' => false],
        ['ime' => '🏠 Mesto', 'href' => '?modul=zverlandija&akcija=svet&svet_id=mesto', 'aktivna' => false],
    ],
    'polja' => [
        ['ime' => 'izberi_zival', 'oznaka' => 'Izberi žival', 'tip' => 'select', 'options' => [
            ['v' => 'zorza', 'i' => '🐱 Ris Zorza'],
            ['v' => 'bimbam', 'i' => '🐰 Zajec Bimbam'],
            ['v' => 'luna', 'i' => '🦉 Sova Luna'],
            ['v' => 'muki', 'i' => '🐻 Medved Muki'],
            ['v' => 'koral', 'i' => '🐋 Kit Koral'],
            ['v' => 'fjoli', 'i' => '🐬 Delfin Fjoli'],
            ['v' => 'raka', 'i' => '🦀 Rak Raka'],
            ['v' => 'bela', 'i' => '🐠 Ribica Bela'],
            ['v' => 'orjak', 'i' => '🦅 Orel Orjak'],
            ['v' => 'fija', 'i' => '🔥 Feniks Fija'],
            ['v' => 'viti', 'i' => '🌪️ Vetrovnik Viti'],
            ['v' => 'koka', 'i' => '🐐 Koza Koka'],
            ['v' => 'osol', 'i' => '🦅 Orel Osol'],
            ['v' => 'ruj', 'i' => '🐆 Ris Ruj'],
            ['v' => 'mia', 'i' => '🐱 Mačka Mia'],
            ['v' => 'pajk', 'i' => '🐶 Pes Pajk'],
            ['v' => 'gaga', 'i' => '🕊️ Golob Gaga'],
        ]],
        ['ime' => 'tip_naloge', 'oznaka' => 'Tip naloge', 'tip' => 'select', 'options' => [
            ['v' => 'uganka', 'i' => '🧩 Uganka'],
            ['v' => 'naravoslovje', 'i' => '🌿 Naravoslovje'],
            ['v' => 'matematika', 'i' => '🔢 Matematika'],
            ['v' => 'ustvarjalnost', 'i' => '🎨 Ustvarjalnost'],
        ]],
    ],
    'postavke' => [
        ['besedilo' => '🦊 Zverlandija — otroški svet, kjer žive magične živali, ki pišejo zgodbe in učijo skozi igro.', 'uid' => 'info'],
        ['besedilo' => '🌲 Gozd živali — Ris, Zajec, Sova, Medved', 'uid' => 'gozd'],
        ['besedilo' => '🌊 Morje čarov — Kit, Delfin, Rak, Ribica', 'uid' => 'morje'],
        ['besedilo' => '☁️ Nebesna krila — Orel, Feniks, Vetrovnik', 'uid' => 'nebo'],
        ['besedilo' => '⛰️ Gorska skrivnost — Koza, Orel, Ris', 'uid' => 'gora'],
        ['besedilo' => '🏠 Mesto meščanov — Mačka, Pes, Golob', 'uid' => 'mesto'],
    ],
];

echo globalno_modul_layout_izrisi($zverlandija);