<?php
/**
 * ---------------------------------------------------------
 * POT: SISTEM/storitve_svetov/uporabniki/uporabnik_peskovnik.php
 * v111 (27.5.2026 05:15)
 * ---------------------------------------------------------
 * OPIS: Upravljanje uporabniškega peskovnika
 * ---------------------------------------------------------
 *
 * ODVISNOSTI:
 * - pot.php
 * - pot.php funkcije: uporabnik_peskovnik(), uporabnik_moduli()
 *
 * UPORABA:
 * - SISTEM/storitve_svetov/moduli/
 *
 * FUNKCIJE:
 * - uporabniki_peskovnik_pot() – pot do peskovnika
 * - uporabniki_peskovnik_shrani() – shrani podatke
 * - uporabniki_peskovnik_preberi() – prebere podatke
 * - uporabniki_peskovnik_zbrisi() – zbriše podatke
 *
 * PREPOVEDI:
 * - Brez direktnega dostopa do filesystema (uporabi funkcije)
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

function uporabniki_peskovnik_pot(string $uporabnikId, string $podmapa = ''): string
{
$leto = date('Y');
$pot = POT_UPORABNIKI . '/' . $leto . '/' . $uporabnikId . '/';

if ($podmapa !== '') {
    $pot .= $podmapa . '/';
}

return $pot;
}

function uporabniki_peskovnik_shrani(string $uporabnikId, string $kljuc, $podatki): bool
{
$pot = uporabniki_peskovnik_pot($uporabnikId, 'podatki');

if (!is_dir($pot)) {
    mkdir($pot, 0755, true);
}

$datoteka = $pot . '/' . $kljuc . '.json';
$vsebina = json_encode($podatki, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

return file_put_contents($datoteka, $vsebina, LOCK_EX) !== false;
}

function uporabniki_peskovnik_preberi(string $uporabnikId, string $kljuc)
{
$pot = uporabniki_peskovnik_pot($uporabnikId, 'podatki');
$datoteka = $pot . '/' . $kljuc . '.json';

if (!file_exists($datoteka)) {
    return null;
}

$vsebina = file_get_contents($datoteka);
if ($vsebina === false) {
    return null;
}

return json_decode($vsebina, true);
}

function uporabniki_peskovnik_zbrisi(string $uporabnikId, string $kljuc): bool
{
$pot = uporabniki_peskovnik_pot($uporabnikId, 'podatki');
$datoteka = $pot . '/' . $kljuc . '.json';

if (file_exists($datoteka)) {
    return unlink($datoteka);
}

return true;
}

function uporabniki_peskovnik_modul_pot(string $uporabnikId, string $imeModula): string
{
return uporabniki_peskovnik_pot($uporabnikId, 'moduli/' . $imeModula);
}

function uporabniki_peskovnik_modul_shrani(string $uporabnikId, string $imeModula, string $kljuc, $podatki): bool
{
$pot = uporabniki_peskovnik_modul_pot($uporabnikId, $imeModula);

if (!is_dir($pot)) {
    mkdir($pot, 0755, true);
}

$datoteka = $pot . '/' . $kljuc . '.json';
$vsebina = json_encode($podatki, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

return file_put_contents($datoteka, $vsebina, LOCK_EX) !== false;
}

function uporabniki_peskovnik_modul_preberi(string $uporabnikId, string $imeModula, string $kljuc)
{
$pot = uporabniki_peskovnik_modul_pot($uporabnikId, $imeModula);
$datoteka = $pot . '/' . $kljuc . '.json';

if (!file_exists($datoteka)) {
    return null;
}

$vsebina = file_get_contents($datoteka);
if ($vsebina === false) {
    return null;
}

return json_decode($vsebina, true);
}