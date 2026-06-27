<?php
/**
 * ============================================================
 * GLOBALNO: strani/moduli/primer.php
 * POT: GLOBALNO/postavitev/strani/moduli/primer.php
 * ============================================================
 *
 * Namen:
 *     Primer strani, ki uporablja modularni layout.
 * ============================================================
 */

declare(strict_types=1);

require_once __DIR__ . '/../modul_layout.php';

$primer = $vsebina['primer'] ?? [
    'naslov' => 'Primer modulskega pogleda',
    'opis' => 'Tukaj bo kasneje posamezen modul sestavil svojo postavitev.',
    'povezave' => [
        ['ime' => 'Domov', 'href' => '?svet=domov', 'aktivna' => true],
        ['ime' => 'Moduli', 'href' => '?svet=moduli'],
        ['ime' => 'Nastavitve', 'href' => '?svet=nastavitve'],
    ],
    'polja' => [
        ['ime' => 'ime', 'oznaka' => 'Ime modula', 'tip' => 'text', 'place' => 'Vpiši ime'],
        ['ime' => 'opis', 'oznaka' => 'Opis', 'tip' => 'text', 'place' => 'Kratek opis'],
    ],
    'postavke' => [
        ['besedilo' => 'Kartica 1'],
        ['besedilo' => 'Kartica 2'],
        ['besedilo' => 'Povezava', 'povezava' => '?svet=domov'],
    ],
];

echo globalno_modul_layout_izrisi($primer);
