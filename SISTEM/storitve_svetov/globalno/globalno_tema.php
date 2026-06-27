<?php
/**
 * ---------------------------------------------------------
 * POT: SISTEM/storitve_svetov/globalno/globalno_teme.php
 * v111 (27.5.2026 05:45)
 * ---------------------------------------------------------
 * OPIS: Upravljanje tem (vizualni override)
 * ---------------------------------------------------------
 *
 * ODVISNOSTI:
 * - pot.php
 * - SISTEM/kernel/baze/upravljalec_baz.php
 *
 * UPORABA:
 * - GLOBALNO/vmesnik/teme/
 *
 * FUNKCIJE:
 * - globalno_tema_beri() – branje teme
 * - globalno_tema_nastavi() – nastavitev aktivne teme
 * - globalno_tema_aktivna() – aktivna tema
 * - globalno_tema_css() – pridobitev CSS poti
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

function globalno_tema_beri(string $ime): ?array
{
$teme = baza_beri('globalno_teme');

foreach ($teme as $tema) {
    if ($tema['ime'] === $ime) {
        return $tema;
    }
}

return null;
}

function globalno_tema_nastavi(string $ime): array
{
$tema = globalno_tema_beri($ime);

if ($tema === null) {
    return [
        'status' => 'napaka',
        'status_koda' => 404,
        'sporocilo' => "Tema '$ime' ne obstaja."
    ];
}

seja_nastavi('aktivna_tema', $ime);

return [
    'status' => 'uspeh',
    'status_koda' => 200,
    'sporocilo' => "Aktivna tema nastavljena na '$ime'."
];
}

function globalno_tema_aktivna(): string
{
return seja_pridobi('aktivna_tema', 'standard');
}

function globalno_tema_css(): string
{
$aktivna = globalno_tema_aktivna();
$tema = globalno_tema_beri($aktivna);

if ($tema === null || !isset($tema['css_pot'])) {
    return GLOBALNO . '/vmesnik/teme/standard/slog.css';
}

return $tema['css_pot'];
}