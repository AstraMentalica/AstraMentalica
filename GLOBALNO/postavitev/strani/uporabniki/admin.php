<?php
/**
 * ============================================================
 * GLOBALNO: postavitev/strani/uporabniki/admin.php
 * POT: GLOBALNO/postavitev/strani/uporabniki/admin.php
 * ============================================================
 *
 * Namen:
 *     Sestavljena admin stran uporabnika z vlogo admin.
 *     Samo prikaz – sistem pripravi podatke in dostop.
 * ============================================================
 */

declare(strict_types=1);

require_once __DIR__ . '/../layouti.php';
require_once __DIR__ . '/../gradniki/gumb.php';
require_once __DIR__ . '/../gradniki/kartica.php';
require_once __DIR__ . '/../gradniki/seznam.php';
require_once __DIR__ . '/../gradniki/navigacija.php';

$podatki = $vsebina['admin'] ?? $vsebina ?? [];
$statistika = is_array($podatki['statistika'] ?? null) ? $podatki['statistika'] : [];
$povezave = is_array($podatki['povezave'] ?? null) ? $podatki['povezave'] : [
    ['ime' => 'Svetovi', 'href' => '?svet=SISTEM&pot=svetovi'],
    ['ime' => 'Moduli', 'href' => '?svet=ASTRA&pot=moduli'],
    ['ime' => 'Uporabniki', 'href' => '?svet=UPORABNIKI&pot=seznam'],
];

$pregled = [];
$pregled[] = 'Aktivnih modulov: ' . (string)($statistika['moduli'] ?? 0);
$pregled[] = 'Uporabnikov: ' . (string)($statistika['uporabniki'] ?? 0);
$pregled[] = 'Zadnji zagon: ' . (string)($statistika['zadnji_zagon'] ?? 'ni podatka');

$vsebinaHtml = '';
$vsebinaHtml .= globalno_gradnik_navigacija($povezave, ['razred' => 'admin-navigacija']);
$vsebinaHtml .= globalno_gradnik_kartica('Nadzorna plošča', '<p>Upravljanje sistema in pregled stanja.</p>', [
    'razred' => 'admin-uvod',
    'gumbi' => [
        globalno_gradnik_gumb('Osveži', 'sekundarni'),
        globalno_gradnik_gumb('Odpri svetove', 'primarni'),
    ],
]);
$vsebinaHtml .= globalno_gradnik_kartica('Pregled', globalno_gradnik_seznam($pregled, ['razred' => 'admin-seznam']), ['razred' => 'admin-pregled']);

echo globalno_gradnik_kartica('Admin', $vsebinaHtml, ['razred' => 'admin-kartica']);
