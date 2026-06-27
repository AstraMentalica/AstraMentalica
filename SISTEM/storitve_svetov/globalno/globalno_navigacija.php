<?php
/**
 * ---------------------------------------------------------
 * POT: SISTEM/storitve_svetov/globalno/globalno_navigacija.php
 * v111 (27.5.2026 05:45)
 * ---------------------------------------------------------
 * OPIS: Upravljanje navigacijskih menijev
 * ---------------------------------------------------------
 *
 * ODVISNOSTI:
 * - pot.php
 * - SISTEM/kernel/baze/upravljalec_baz.php
 *
 * UPORABA:
 * - GLOBALNO/render/
 *
 * FUNKCIJE:
 * - globalno_navigacija_menu() – pridobitev menija
 * - globalno_navigacija_dodaj_element() – dodajanje elementa
 * - globalno_navigacija_odstrani_element() – odstranitev elementa
 * - globalno_navigacija_aktivna() – preverjanje aktivne poti
 *
 * PREPOVEDI:
 * - Brez echo, print_r, var_dump
 *
 * STATUS: Stabilno
 *
 * ZGODOVINA:
 * - v111: FAZA 2b – implementacija
 *
 * ---------------------------------------------------------
 * AVTOR: AstraMentalica Mojster
 * ---------------------------------------------------------
 */

declare(strict_types=1);

function globalno_navigacija_menu(string $pozicija, int $vloga): array
{
$vsiElementi = baza_beri('globalno_navigacija');
$aktivnaPot = $_GET['svet'] ?? 'GLOBALNO';
$rezultat = [];

foreach ($vsiElementi as $element) {
    if ($element['pozicija'] !== $pozicija) {
        continue;
    }
    
    if ($element['aktiviran'] && $vloga >= $element['vloga_min']) {
        $element['aktivno'] = ($element['pot'] === $aktivnaPot);
        $rezultat[] = $element;
    }
}

// Razvrsti po vrstnem redu
usort($rezultat, function($a, $b) {
    return ($a['vrstni_red'] ?? 0) <=> ($b['vrstni_red'] ?? 0);
});

return $rezultat;
}

function globalno_navigacija_dodaj_element(array $element): array
{
$obveznaPolja = ['naslov', 'pot', 'pozicija'];
foreach ($obveznaPolja as $polje) {
    if (!isset($element[$polje])) {
        return [
            'status' => 'napaka',
            'status_koda' => 400,
            'sporocilo' => "Manjka obvezno polje: $polje"
        ];
    }
}

$element['vloga_min'] = $element['vloga_min'] ?? VLOGA_GOST;
$element['aktiviran'] = $element['aktiviran'] ?? true;
$element['ikona'] = $element['ikona'] ?? '';
$element['podmeni'] = $element['podmeni'] ?? [];
$element['vrstni_red'] = $element['vrstni_red'] ?? 0;
$element['ustvarjeno'] = time();
$element['id'] = uniqid('nav_', true);

baza_zapisi('globalno_navigacija', $element);

return [
    'status' => 'uspeh',
    'status_koda' => 201,
    'sporocilo' => "Element '{$element['naslov']}' dodan v navigacijo."
];
}

function globalno_navigacija_odstrani_element(string $id): array
{
$elementi = baza_beri('globalno_navigacija');
$najden = null;

foreach ($elementi as $element) {
    if ($element['id'] === $id) {
        $najden = $element;
        break;
    }
}

if ($najden === null) {
    return [
        'status' => 'napaka',
        'status_koda' => 404,
        'sporocilo' => 'Element ne obstaja.'
    ];
}

baza_zbrisi('globalno_navigacija', $id);

return [
    'status' => 'uspeh',
    'status_koda' => 200,
    'sporocilo' => "Element '{$najden['naslov']}' odstranjen iz navigacije."
];
}

function globalno_navigacija_aktivna(string $pot): bool
{
$aktivnaPot = $_GET['svet'] ?? 'GLOBALNO';
return $pot === $aktivnaPot;
}