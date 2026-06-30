<?php
/**
 * ============================================================
 * GLOBALNO: strani/moduli/senzornasa.php
 * POT: GLOBALNO/postavitev/strani/moduli/senzornasa.php
 * ============================================================
 *
 * Namen:
 *     Front page za modul SenzorNasa — kozmični senzorji, sončeva aktivnost, vreme.
 * ============================================================
 */

declare(strict_types=1);

require_once __DIR__ . '/../modul_layout.php';

$senzornasa = $vsebina['senzornasa'] ?? [
    'naslov' => '🛰️ SenzorNasa',
    'opis' => 'Kozmični senzorji — sončeva aktivnost (NASA DONKI), geomagnetna aktivnost (NOAA), vreme.',
    'povezave' => [
        ['ime' => 'Domov', 'href' => '?svet=domov', 'aktivna' => false],
        ['ime' => 'Senzorji', 'href' => '?modul=senzornasa&akcija=senzorji', 'aktivna' => true],
        ['ime' => 'Zgodovina', 'href' => '?modul=senzornasa&akcija=zgodovina', 'aktivna' => false],
        ['ime' => 'Nastavitve', 'href' => '?modul=senzornasa&akcija=nastavitve', 'aktivna' => false],
    ],
    'polja' => [
        ['ime' => 'interval', 'oznaka' => 'Interval', 'tip' => 'select', 'options' => [
            ['v' => '1h', 'i' => '1 ura'],
            ['v' => '6h', 'i' => '6 ur'],
            ['v' => '24h', 'i' => '24 ur'],
            ['v' => '7d', 'i' => '7 dni'],
        ]],
        ['ime' => 'vir', 'oznaka' => 'Vir podatkov', 'tip' => 'select', 'options' => [
            ['v' => 'nasa', 'i' => 'NASA DONKI'],
            ['v' => 'noaa', 'i' => 'NOAA'],
            ['v' => 'vse', 'i' => 'Vsi viri'],
        ]],
    ],
    'postavke' => [
        ['besedilo' => '🌡️ Sončeva temperatura — 4.5M °K'],
        ['besedilo' => '💨 Solar wind — 380 km/s'],
        ['besedilo' => '🌌 Kp indeks — 3 (miran)'],
        ['besedilo' => '🔭 X-žarki — C1.0 (slabo)'],
    ],
];

echo globalno_modul_layout_izrisi($senzornasa);
