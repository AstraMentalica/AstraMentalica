<?php
/**
 * ---------------------------------------------------------
 * POT: SISTEM/storitve_svetov/globalno/globalno_postavitve.php
 * v111 (27.5.2026 05:45)
 * ---------------------------------------------------------
 * OPIS: Upravljanje postavitev (layoutov)
 * ---------------------------------------------------------
 *
 * ODVISNOSTI:
 * - pot.php
 * - SISTEM/kernel/baze/upravljalec_baz.php
 *
 * UPORABA:
 * - GLOBALNO/render/postavitev/
 *
 * FUNKCIJE:
 * - globalno_postavitev_beri() – branje postavitve
 * - globalno_postavitev_nastavi() – nastavitev aktivne postavitve
 * - globalno_postavitev_aktivna() – aktivna postavitev
 * - globalno_postavitev_prikazi() – prikaz postavitve
 *
 * PREPOVEDI:
 * - Brez echo, print_r, var_dump (razen prikaza)
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

function globalno_postavitev_beri(string $ime): ?array
{
$postavitve = baza_beri('globalno_postavitve');

foreach ($postavitve as $postavitev) {
    if ($postavitev['ime'] === $ime) {
        return $postavitev;
    }
}

return null;
}

function globalno_postavitev_nastavi(string $ime): array
{
$postavitev = globalno_postavitev_beri($ime);

if ($postavitev === null) {
    return [
        'status' => 'napaka',
        'status_koda' => 404,
        'sporocilo' => "Postavitev '$ime' ne obstaja."
    ];
}

seja_nastavi('aktivna_postavitev', $ime);

return [
    'status' => 'uspeh',
    'status_koda' => 200,
    'sporocilo' => "Aktivna postavitev nastavljena na '$ime'."
];
}

function globalno_postavitev_aktivna(): string
{
$aktivna = seja_pridobi('aktivna_postavitev', 'osnovna');
$postavitev = globalno_postavitev_beri($aktivna);

if ($postavitev === null) {
    return 'osnovna';
}

return $aktivna;
}

function globalno_postavitev_prikazi(array $vsebina): void
{
$aktivna = globalno_postavitev_aktivna();
$postavitev = globalno_postavitev_beri($aktivna);

if ($postavitev === null || !file_exists($postavitev['pot'])) {
    // Privzeta postavitev
    echo '<!DOCTYPE html><html><head><title>' . IME_APLIKACIJE . '</title></head><body>';
    echo $vsebina['vsebina'] ?? '';
    echo '</body></html>';
    return;
}

// Vključi postavitev
extract($vsebina);
include $postavitev['pot'];
}