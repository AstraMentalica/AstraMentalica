<?php
/**
 * ---------------------------------------------------------
 * POT: SISTEM/storitve_svetov/globalno/globalno_nastavitve.php
 * v111 (27.5.2026 05:45)
 * ---------------------------------------------------------
 * OPIS: Globalne nastavitve sistema
 * ---------------------------------------------------------
 *
 * ODVISNOSTI:
 * - pot.php
 * - SISTEM/kernel/baze/upravljalec_baz.php
 *
 * UPORABA:
 * - Zaganjalnik, GLOBALNO/render/
 *
 * FUNKCIJE:
 * - globalno_nastavitve_beri() – branje nastavitev
 * - globalno_nastavitve_posodobi() – posodobitev nastavitev
 * - globalno_nastavitve_privzeto() – nastavitev s privzeto vrednostjo
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

function globalno_nastavitve_beri(?string $kljuc = null)
{
$nastavitve = baza_beri('globalno_nastavitve');
$mapaNastavitev = [];

foreach ($nastavitve as $nastavitev) {
    $mapaNastavitev[$nastavitev['kljuc']] = $nastavitev['vrednost'];
}

if ($kljuc !== null) {
    return $mapaNastavitev[$kljuc] ?? null;
}

return $mapaNastavitev;
}

function globalno_nastavitve_posodobi(string $kljuc, $vrednost): array
{
$nastavitve = baza_beri('globalno_nastavitve');
$najden = null;

foreach ($nastavitve as $nastavitev) {
    if ($nastavitev['kljuc'] === $kljuc) {
        $najden = $nastavitev;
        break;
    }
}

if ($najden === null) {
    baza_zapisi('globalno_nastavitve', [
        'kljuc' => $kljuc,
        'vrednost' => $vrednost,
        'ustvarjeno' => time()
    ]);
} else {
    baza_posodobi('globalno_nastavitve', $najden['id'], [
        'vrednost' => $vrednost,
        'nazadnje_posodobljeno' => time()
    ]);
}

return [
    'status' => 'uspeh',
    'status_koda' => 200,
    'sporocilo' => "Nastavitev '$kljuc' posodobljena."
];
}

function globalno_nastavitve_privzeto(string $kljuc, $privzeto)
{
$vrednost = globalno_nastavitve_beri($kljuc);
return $vrednost !== null ? $vrednost : $privzeto;
}