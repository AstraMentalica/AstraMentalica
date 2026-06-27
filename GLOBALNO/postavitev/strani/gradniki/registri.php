<?php
/**
 * ============================================================
 * GLOBALNO: gradniki/registri.php
 * POT: GLOBALNO/postavitev/strani/gradniki/registri.php
 * ============================================================
 *
 * Namen:
 *     Osnovni katalog gradnikov za strani in module.
 *     To je samo opisna registracija, brez poslovne logike.
 *
 * Uporaba:
 *     - layouti lahko preberejo, kateri gradniki obstajajo
 *     - moduli lahko kasneje zahtevajo svoje gradnike po imenu
 *
 * Pravila:
 *     - Imena so slovenska in kratka
 *     - Gradnik je samostojen kos UI
 *     - Brez dostopa do baze in brez session logike
 * ============================================================
 */

declare(strict_types=1);

if (!function_exists('globalno_gradniki_registry')) {
    function globalno_gradniki_registry(): array
    {
        return [
            'glava' => [
                'ime' => 'glava',
                'tip' => 'layout',
                'opis' => 'Glava strani',
                'pot' => 'GLOBALNO/postavitev/strani/gradniki/glava.php',
            ],
            'noga' => [
                'ime' => 'noga',
                'tip' => 'layout',
                'opis' => 'Noga strani',
                'pot' => 'GLOBALNO/postavitev/strani/gradniki/noga.php',
            ],
            'navigacija' => [
                'ime' => 'navigacija',
                'tip' => 'layout',
                'opis' => 'Navigacija in povezave',
                'pot' => 'GLOBALNO/postavitev/strani/gradniki/navigacija.php',
            ],
            'kartica' => [
                'ime' => 'kartica',
                'tip' => 'component',
                'opis' => 'Splošna kartica za vsebine',
                'pot' => 'GLOBALNO/postavitev/strani/gradniki/kartica.php',
            ],
            'obrazec' => [
                'ime' => 'obrazec',
                'tip' => 'component',
                'opis' => 'Obrazec za vnos podatkov',
                'pot' => 'GLOBALNO/postavitev/strani/gradniki/obrazec.php',
            ],
            'gumb' => [
                'ime' => 'gumb',
                'tip' => 'component',
                'opis' => 'Gumb za akcije',
                'pot' => 'GLOBALNO/postavitev/strani/gradniki/gumb.php',
            ],
            'seznam' => [
                'ime' => 'seznam',
                'tip' => 'component',
                'opis' => 'Seznam elementov ali kartic',
                'pot' => 'GLOBALNO/postavitev/strani/gradniki/seznam.php',
            ],
        ];
    }
}

if (!function_exists('globalno_gradnik_najdi')) {
    function globalno_gradnik_najdi(string $ime): ?array
    {
        $registry = globalno_gradniki_registry();
        return $registry[$ime] ?? null;
    }
}
