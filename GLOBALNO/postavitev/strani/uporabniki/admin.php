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

?>

<style>
.admin-kartica {
    background: rgba(20, 24, 34, 0.92);
    border: 1px solid rgba(255, 220, 150, 0.18);
    color: #eadfbf;
    box-shadow: 0 14px 42px rgba(0, 0, 0, 0.32);
}

.admin-kartica h3,
.admin-kartica h4,
.admin-kartica label,
.admin-kartica a,
.admin-kartica .admin-pregled,
.admin-kartica .admin-uvod {
    color: #eadfbf;
}

.admin-kartica .admin-navigacija,
.admin-kartica .navigacija-povezava {
    color: #d8b970;
}

.admin-kartica .seznam-postavka {
    color: #f4ead6;
}

.admin-kartica .kartica-vsebina {
    color: #f4ead6;
}
</style>
