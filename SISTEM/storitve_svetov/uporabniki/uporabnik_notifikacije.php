<?php
/**
 * ---------------------------------------------------------
 * POT: SISTEM/storitve_svetov/uporabniki/uporabnik_notifikacije.php
 * v111 (27.5.2026 16:30)
 * ---------------------------------------------------------
 * OPIS: Notifikacije uporabnikom – pošiljanje obvestil
 * ---------------------------------------------------------
 *
 * ODVISNOSTI:
 * - pot.php
 * - SISTEM/kernel/baze/upravljalec_baz.php
 *
 * UPORABA:
 * - SISTEM/storitve_svetov/globalno/globalno_prikaz.php
 *
 * FUNKCIJE:
 * - uporabniki_notifikacije_poslji(), uporabniki_notifikacije_pridobi()
 * - uporabniki_notifikacije_prebrano(), uporabniki_notifikacije_zbrisi()
 * - uporabniki_notifikacije_statistika()
 *
 * PREPOVEDI:
 * - Brez echo, print_r, var_dump
 *
 * STATUS: Stabilno
 *
 * ZGODOVINA:
 * - v111: FAZA 46 – implementacija
 *
 * ---------------------------------------------------------
 * AVTOR: AstraMentalica Mojster
 * ---------------------------------------------------------
 */

declare(strict_types=1);

function uporabniki_notifikacije_poslji(string $uporabnikId, string $naslov, string $sporocilo, string $tip = 'info', ?string $povezava = null): ?string
{
$notifikacija = [
    'id' => uniqid('notif_', true),
    'uporabnik_id' => $uporabnikId,
    'naslov' => $naslov,
    'sporocilo' => $sporocilo,
    'tip' => $tip,
    'povezava' => $povezava,
    'prebrano' => false,
    'ustvarjeno' => time(),
    'prebrano_cas' => null
];

$id = baza_zapisi('notifikacije', $notifikacija);

// Sproži dogodek za real-time notifikacije
dogodek_sprozi('uporabnik.notifikacija', [
    'uporabnik_id' => $uporabnikId,
    'notifikacija' => $notifikacija
]);

return $id;
}

function uporabniki_notifikacije_poslji_mnozico(array $uporabnikiId, string $naslov, string $sporocilo, string $tip = 'info', ?string $povezava = null): array
{
$poslane = [];
foreach ($uporabnikiId as $id) {
    $result = uporabniki_notifikacije_poslji($id, $naslov, $sporocilo, $tip, $povezava);
    if ($result !== null) {
        $poslane[] = $id;
    }
}
return $poslane;
}

function uporabniki_notifikacije_pridobi(string $uporabnikId, int $limit = 50, int $offset = 0, bool $samoNeprebrane = false): array
{
$vse = baza_beri('notifikacije');
$filtrirane = array_filter($vse, function($n) use ($uporabnikId, $samoNeprebrane) {
    if ($n['uporabnik_id'] !== $uporabnikId) return false;
    if ($samoNeprebrane && $n['prebrano']) return false;
    return true;
});

// Razvrsti po času (novejše prve)
usort($filtrirane, function($a, $b) {
    return $b['ustvarjeno'] <=> $a['ustvarjeno'];
});

return array_slice($filtrirane, $offset, $limit);
}

function uporabniki_notifikacije_prebrano(string $notifikacijaId): bool
{
$notifikacija = baza_beri_enega('notifikacije', $notifikacijaId);
if ($notifikacija === null) {
    return false;
}

return baza_posodobi('notifikacije', $notifikacijaId, [
    'prebrano' => true,
    'prebrano_cas' => time()
]);
}

function uporabniki_notifikacije_prebrano_vse(string $uporabnikId): int
{
$vse = uporabniki_notifikacije_pridobi($uporabnikId, 1000);
$stevilo = 0;

foreach ($vse as $n) {
    if (!$n['prebrano']) {
        uporabniki_notifikacije_prebrano($n['id']);
        $stevilo++;
    }
}

return $stevilo;
}

function uporabniki_notifikacije_zbrisi(string $notifikacijaId): bool
{
return baza_zbrisi('notifikacije', $notifikacijaId);
}

function uporabniki_notifikacije_zbrisi_vse(string $uporabnikId): int
{
$vse = uporabniki_notifikacije_pridobi($uporabnikId, 1000);
$stevilo = 0;

foreach ($vse as $n) {
    if (baza_zbrisi('notifikacije', $n['id'])) {
        $stevilo++;
    }
}

return $stevilo;
}

function uporabniki_notifikacije_statistika(string $uporabnikId): array
{
$vse = uporabniki_notifikacije_pridobi($uporabnikId, 1000);
$neprebrane = 0;
$poTipih = [];

foreach ($vse as $n) {
    if (!$n['prebrano']) {
        $neprebrane++;
    }
    
    $tip = $n['tip'];
    $poTipih[$tip] = ($poTipih[$tip] ?? 0) + 1;
}

return [
    'skupaj' => count($vse),
    'neprebrane' => $neprebrane,
    'po_tipih' => $poTipih,
    'zadnja' => !empty($vse) ? $vse[0] : null
];
}

function uporabniki_notifikacije_pocisti(int $starejseOd = 2592000): int
{
$vse = baza_beri('notifikacije');
$meja = time() - $starejseOd;
$izbrisanih = 0;

foreach ($vse as $n) {
    if ($n['ustvarjeno'] < $meja && $n['prebrano']) {
        baza_zbrisi('notifikacije', $n['id']);
        $izbrisanih++;
    }
}

return $izbrisanih;
}