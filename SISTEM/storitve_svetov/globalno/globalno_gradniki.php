<?php
/**
 * ---------------------------------------------------------
 * POT: SISTEM/storitve_svetov/globalno/globalno_gradniki.php
 * v111 (27.5.2026 05:45)
 * ---------------------------------------------------------
 * OPIS: Priprava gradnikov za GLOBALNO/render/
 * ---------------------------------------------------------
 *
 * ODVISNOSTI:
 * - pot.php
 * - SISTEM/kernel/baze/upravljalec_baz.php
 *
 * UPORABA:
 * - GLOBALNO/render/gradniki/
 *
 * FUNKCIJE:
 * - globalno_gradnik_dodaj() – dodajanje gradnika
 * - globalno_gradnik_odstrani() – odstranitev gradnika
 * - globalno_gradnik_posodobi() – posodobitev gradnika
 * - globalno_gradniki_po_vlogi() – gradniki glede na vlogo
 * - globalno_gradnik_prikazi() – prikaz gradnika
 *
 * PREPOVEDI:
 * - Brez echo, print_r, var_dump
 * - Brez HTML (samo podatki)
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

function globalno_gradnik_dodaj(string $ime, array $podatki): array
{
$gradniki = baza_beri('globalno_gradniki');

// Preveri ali gradnik že obstaja
foreach ($gradniki as $gradnik) {
    if ($gradnik['ime'] === $ime) {
        return [
            'status' => 'napaka',
            'status_koda' => 409,
            'sporocilo' => "Gradnik '$ime' že obstaja."
        ];
    }
}

$noviGradnik = [
    'id' => uniqid('grad_', true),
    'ime' => $ime,
    'tip' => $podatki['tip'] ?? 'element',
    'pot' => $podatki['pot'] ?? '',
    'vloga_min' => $podatki['vloga_min'] ?? VLOGA_GOST,
    'aktiviran' => $podatki['aktiviran'] ?? true,
    'meta' => $podatki['meta'] ?? [],
    'ustvarjeno' => time()
];

baza_zapisi('globalno_gradniki', $noviGradnik);

return [
    'status' => 'uspeh',
    'status_koda' => 201,
    'sporocilo' => "Gradnik '$ime' uspešno dodan."
];
}

function globalno_gradnik_odstrani(string $ime): array
{
$gradniki = baza_beri('globalno_gradniki');
$najden = null;

foreach ($gradniki as $gradnik) {
    if ($gradnik['ime'] === $ime) {
        $najden = $gradnik;
        break;
    }
}

if ($najden === null) {
    return [
        'status' => 'napaka',
        'status_koda' => 404,
        'sporocilo' => "Gradnik '$ime' ne obstaja."
    ];
}

baza_zbrisi('globalno_gradniki', $najden['id']);

return [
    'status' => 'uspeh',
    'status_koda' => 200,
    'sporocilo' => "Gradnik '$ime' uspešno odstranjen."
];
}

function globalno_gradnik_posodobi(string $ime, array $podatki): array
{
$gradniki = baza_beri('globalno_gradniki');
$najden = null;

foreach ($gradniki as $gradnik) {
    if ($gradnik['ime'] === $ime) {
        $najden = $gradnik;
        break;
    }
}

if ($najden === null) {
    return [
        'status' => 'napaka',
        'status_koda' => 404,
        'sporocilo' => "Gradnik '$ime' ne obstaja."
    ];
}

$dovoljenaPolja = ['tip', 'pot', 'vloga_min', 'aktiviran', 'meta'];
$posodobitve = [];

foreach ($dovoljenaPolja as $polje) {
    if (isset($podatki[$polje])) {
        $posodobitve[$polje] = $podatki[$polje];
    }
}

if (!empty($posodobitve)) {
    $posodobitve['nazadnje_posodobljeno'] = time();
    baza_posodobi('globalno_gradniki', $najden['id'], $posodobitve);
}

return [
    'status' => 'uspeh',
    'status_koda' => 200,
    'sporocilo' => "Gradnik '$ime' uspešno posodobljen."
];
}

function globalno_gradniki_po_vlogi(int $vloga): array
{
$vsiGradniki = baza_beri('globalno_gradniki');
$rezultat = [];

foreach ($vsiGradniki as $gradnik) {
    if ($gradnik['aktiviran'] && $vloga >= $gradnik['vloga_min']) {
        $rezultat[] = $gradnik;
    }
}

return $rezultat;
}

function globalno_gradnik_prikazi(string $ime, array $parametri = []): string
{
$gradniki = baza_beri('globalno_gradniki');
$najden = null;

foreach ($gradniki as $gradnik) {
    if ($gradnik['ime'] === $ime) {
        $najden = $gradnik;
        break;
    }
}

if ($najden === null || !$najden['aktiviran']) {
    return '';
}

$potGradnika = $najden['pot'];

if (!file_exists($potGradnika)) {
    return "<!-- Gradnik '$ime' ne obstaja na poti: $potGradnika -->";
}

// Render gradnika (pasivno, samo prikaz)
ob_start();
extract($parametri);
include $potGradnika;
return ob_get_clean();
}