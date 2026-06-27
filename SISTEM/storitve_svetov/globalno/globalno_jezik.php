<?php
/**
 * ---------------------------------------------------------
 * POT: SISTEM/storitve_svetov/globalno/globalno_jezik.php
 * v111 (27.5.2026 05:45)
 * ---------------------------------------------------------
 * OPIS: Upravljanje jezika in prevodov
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
 * - globalno_jezik_nastavi() – nastavitev jezika
 * - globalno_jezik_aktivni() – aktivni jezik
 * - globalno_prevod() – prevod niza
 * - __() – globalna funkcija za prevod
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

$GLOBALS['GLOBALNO_PREVODI'] = [];

function globalno_jezik_nastavi(string $jezik): array
{
$dovoljeniJeziki = ['sl', 'en', 'de', 'fr', 'it', 'hr', 'sr'];

if (!in_array($jezik, $dovoljeniJeziki)) {
    return [
        'status' => 'napaka',
        'status_koda' => 400,
        'sporocilo' => "Jezik '$jezik' ni podprt."
    ];
}

seja_nastavi('jezik', $jezik);

// Naloži prevode za ta jezik
$potPrevodi = POT_PODATKI_GLOBALNO . '/prevodi/' . $jezik . '.json';
if (file_exists($potPrevodi)) {
    $vsebina = file_get_contents($potPrevodi);
    $GLOBALS['GLOBALNO_PREVODI'] = json_decode($vsebina, true) ?? [];
} else {
    $GLOBALS['GLOBALNO_PREVODI'] = [];
}

return [
    'status' => 'uspeh',
    'status_koda' => 200,
    'sporocilo' => "Jezik nastavljen na '$jezik'."
];
}

function globalno_jezik_aktivni(): string
{
return seja_pridobi('jezik', 'sl');
}

function globalno_prevod(string $kljuc, array $parametri = []): string
{
$prevod = $GLOBALS['GLOBALNO_PREVODI'][$kljuc] ?? $kljuc;

foreach ($parametri as $kljucParam => $vrednost) {
    $prevod = str_replace('{' . $kljucParam . '}', $vrednost, $prevod);
}

return $prevod;
}

function __(string $kljuc, array $parametri = []): string
{
return globalno_prevod($kljuc, $parametri);
}