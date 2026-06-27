<?php
/**
 * ============================================================
 * POT: SISTEM/storitve/globalno/funkcije_znacke.php
 * ============================================================
 * 
 * @package AstraMentalica\Storitve\Globalno
 * 
 * 📦 NAMEN:
 *     Backend logika za značke (badge helperji)
 *     Vrača podatkovne strukture za prikaz značk.
 * 
 * 🔧 FUNKCIJE:
 *     - znacka_pridobi_za_vlogo(int $vloga): array
 *     - znacka_pridobi_za_status(string $status): array
 *     - znacka_pridobi_za_modul(string $ime_modula): ?array
 * 
 * @author ASTRAMENTALICA
 * @version 1.0.0
 * @since FAZA 2b
 * ============================================================
 */

namespace AstraMentalica\Storitve\Globalno;

use AstraMentalica\Runtime\shramba_beri;

function znacka_pridobi_za_vlogo(int $vloga): array
{
    $znacke = [
        0 => ['ime' => 'Gost', 'barva' => 'siva', 'ikona' => '👤'],
        10 => ['ime' => 'S0 - Začetnik', 'barva' => 'zelena', 'ikona' => '🌱'],
        20 => ['ime' => 'S1 - Raziskovalec', 'barva' => 'modra', 'ikona' => '🔍'],
        30 => ['ime' => 'S2 - Učenec', 'barva' => 'vijolična', 'ikona' => '📚'],
        40 => ['ime' => 'S3 - Mojster', 'barva' => 'zlata', 'ikona' => '⭐'],
        50 => ['ime' => 'S4 - Guru', 'barva' => 'rubinasta', 'ikona' => '👑'],
        60 => ['ime' => 'S5 - Razsvetljeni', 'barva' => 'diamantna', 'ikona' => '💎'],
        100 => ['ime' => 'Admin', 'barva' => 'rdeča', 'ikona' => '⚡']
    ];
    
    return $znacke[$vloga] ?? $znacke[0];
}

function znacka_pridobi_za_status(string $status): array
{
    $znacke = [
        'active' => ['ime' => 'Aktiven', 'barva' => 'zelena', 'ikona' => '✅'],
        'inactive' => ['ime' => 'Neaktiven', 'barva' => 'siva', 'ikona' => '⭕'],
        'installed' => ['ime' => 'Nameščen', 'barva' => 'modra', 'ikona' => '📦'],
        'error' => ['ime' => 'Napaka', 'barva' => 'rdeča', 'ikona' => '❌'],
        'warning' => ['ime' => 'Opozorilo', 'barva' => 'oranžna', 'ikona' => '⚠️'],
        'success' => ['ime' => 'Uspeh', 'barva' => 'zelena', 'ikona' => '🎉']
    ];
    
    return $znacke[$status] ?? $znacke['inactive'];
}

function znacka_pridobi_za_modul(string $ime_modula): ?array
{
    $moduli = shramba_beri('sistem/registri/moduli_reg');
    
    if (!isset($moduli[$ime_modula])) {
        return null;
    }
    
    $modul = $moduli[$ime_modula];
    $vloga_znacka = znacka_pridobi_za_vlogo($modul['vloga']);
    
    return [
        'ime' => $modul['ime'],
        'oznaka' => $modul['oznaka'],
        'verzija' => $modul['verzija'],
        'status' => znacka_pridobi_za_status($modul['status']),
        'vloga' => $vloga_znacka
    ];
}

function znacka_pridobi_za_uporabnika(array $uporabnik): array
{
    $vloga = $uporabnik['vloga'] ?? 0;
    $vloga_znacka = znacka_pridobi_za_vlogo($vloga);
    
    return [
        'uporabnik' => $uporabnik['ime'] ?? 'Neznan',
        'vloga' => $vloga_znacka,
        'clan_od' => $uporabnik['created_at'] ?? null
    ];
}

function znacka_pridobi_raven(int $tocke): array
{
    if ($tocke < 100) {
        return ['raven' => 1, 'ime' => 'Novinec', 'naslednja' => 100 - $tocke];
    }
    if ($tocke < 500) {
        return ['raven' => 2, 'ime' => 'Raziskovalec', 'naslednja' => 500 - $tocke];
    }
    if ($tocke < 2000) {
        return ['raven' => 3, 'ime' => 'Učenec', 'naslednja' => 2000 - $tocke];
    }
    if ($tocke < 5000) {
        return ['raven' => 4, 'ime' => 'Mojster', 'naslednja' => 5000 - $tocke];
    }
    if ($tocke < 10000) {
        return ['raven' => 5, 'ime' => 'Guru', 'naslednja' => 10000 - $tocke];
    }
    return ['raven' => 6, 'ime' => 'Razsvetljeni', 'naslednja' => 0];
}