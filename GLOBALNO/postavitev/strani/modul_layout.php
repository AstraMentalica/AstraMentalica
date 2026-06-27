<?php
/**
 * ============================================================
 * GLOBALNO: modul_layout.php
 * POT: GLOBALNO/postavitev/strani/modul_layout.php
 * ============================================================
 *
 * Namen:
 *     Primer layouta za posamezen modul.
 *     Modul mu poda vsebino, layout pa jo sestavi iz gradnikov.
 * ============================================================
 */

declare(strict_types=1);

require_once __DIR__ . '/layouti.php';
require_once __DIR__ . '/gradniki/gumb.php';
require_once __DIR__ . '/gradniki/kartica.php';
require_once __DIR__ . '/gradniki/obrazec.php';
require_once __DIR__ . '/gradniki/seznam.php';
require_once __DIR__ . '/gradniki/navigacija.php';

if (!function_exists('globalno_modul_layout_izrisi')) {
    function globalno_modul_layout_izrisi(array $podatki = []): string
    {
        $naslov = (string)($podatki['naslov'] ?? 'Modul');
        $opis = (string)($podatki['opis'] ?? 'Sestavljen modul');
        $povezave = $podatki['povezave'] ?? [];
        $postavke = $podatki['postavke'] ?? [];
        $polja = $podatki['polja'] ?? [];

        $html = '';
        $html .= globalno_gradnik_navigacija(is_array($povezave) ? $povezave : [], ['razred' => 'modul-navigacija']);
        $html .= globalno_gradnik_kartica($naslov, '<p>' . htmlspecialchars($opis) . '</p>', [
            'razred' => 'modul-uvod',
            'gumbi' => [globalno_gradnik_gumb('Osveži', 'sekundarni')],
        ]);
        $html .= globalno_gradnik_kartica('Postavke', globalno_gradnik_obrazec('?akcija=shrani', is_array($polja) ? $polja : [], ['razred' => 'modul-obrazec']), [
            'razred' => 'modul-obrazec-kartica',
        ]);
        $html .= globalno_gradnik_kartica('Vsebina', globalno_gradnik_seznam(is_array($postavke) ? $postavke : [], ['razred' => 'modul-seznam']), [
            'razred' => 'modul-vsebina',
        ]);

        return $html;
    }
}
