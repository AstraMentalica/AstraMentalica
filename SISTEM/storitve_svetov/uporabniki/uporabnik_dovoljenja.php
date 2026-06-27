<?php
/**
 * ---------------------------------------------------------
 * POT: SISTEM/storitve_svetov/uporabniki/uporabnik_dovoljenja.php
 * v111 (27.5.2026 05:15)
 * ---------------------------------------------------------
 * OPIS: Upravljanje uporabniških dovoljenj
 * ---------------------------------------------------------
 *
 * ODVISNOSTI:
 * - pot.php
 * - SISTEM/kernel/baze/upravljalec_baz.php
 *
 * UPORABA:
 * - SISTEM/kernel/middleware/rbac.php
 *
 * FUNKCIJE:
 * - uporabniki_dovoljenja_pridobi() – pridobi dovoljenja uporabnika
 * - uporabniki_dovoljenja_dodaj() – doda dovoljenje
 * - uporabniki_dovoljenja_odstrani() – odstrani dovoljenje
 * - uporabniki_ima_dovoljenje() – preveri dovoljenje
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

function uporabniki_dovoljenja_pridobi(string $uporabnikId): array
{
$uporabnik = baza_beri_enega('uporabniki', $uporabnikId);

if ($uporabnik === null) {
    return [];
}

return $uporabnik['dovoljenja'] ?? [];
}

function uporabniki_dovoljenja_dodaj(string $uporabnikId, string $dovoljenje): bool
{
$uporabnik = baza_beri_enega('uporabniki', $uporabnikId);

if ($uporabnik === null) {
    return false;
}

$dovoljenja = $uporabnik['dovoljenja'] ?? [];

if (!in_array($dovoljenje, $dovoljenja)) {
    $dovoljenja[] = $dovoljenje;
    baza_posodobi('uporabniki', $uporabnikId, ['dovoljenja' => $dovoljenja]);
}

return true;
}

function uporabniki_dovoljenja_odstrani(string $uporabnikId, string $dovoljenje): bool
{
$uporabnik = baza_beri_enega('uporabniki', $uporabnikId);

if ($uporabnik === null) {
    return false;
}

$dovoljenja = $uporabnik['dovoljenja'] ?? [];
$novaDovoljenja = array_filter($dovoljenja, function($d) use ($dovoljenje) {
    return $d !== $dovoljenje;
});

if (count($novaDovoljenja) !== count($dovoljenja)) {
    baza_posodobi('uporabniki', $uporabnikId, ['dovoljenja' => array_values($novaDovoljenja)]);
}

return true;
}

function uporabniki_ima_dovoljenje(string $uporabnikId, string $dovoljenje): bool
{
$dovoljenja = uporabniki_dovoljenja_pridobi($uporabnikId);
return in_array($dovoljenje, $dovoljenja);
}