<?php
/**
 * ============================================================
 * POT: SISTEM/kernel/jedro/05_pravice.php
 * 📅 VERZIJA: v115 (14.6.2026 18:00)
 * ============================================================
 *
 * 🏛️ NIVO: KERNEL N3 (JEDRO)
 *
 * 📰 NAMEN:
 *     Upravljanje uporabniških pravic (RBAC).
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - pravice_izvedi(array $zahteva): array
 *     - pravice_ima_vlogo(int $zahtevana): bool
 *     - pravice_trenutna_vloga(): int
 *     - pravice_vloga_v_int(string|int $vloga): int
 *
 * 📡 ODVISNOSTI:
 *     - pot.php (VLOGA_* konstante)
 *     - SISTEM/kernel/jedro/04_seja.php
 *
 * 🚫 PREPOVEDI:
 *     - Brez __DIR__
 *     - Brez echo, print_r, var_dump
 *
 * 📌 STATUS:
 *     Stabilno
 *
 * 📅 ZGODOVINA:
 *     - v115: uskladitev s Header Standard v115
 *
 * 👤 AVTOR:
 *     AstraMentalica Mojster
 *
 * 🌐 JEZIK:
 *     sl
 *
 * 🏷️ OZNAKE:
 *     kernel, jedro, pravice, rbac
 * ============================================================
 */
declare(strict_types=1);

defined('SISTEM_VARNOST') or die('Direkten dostop ni dovoljen.');

function pravice_vloga_v_int(string|int $vloga): int
{
    if (is_int($vloga)) {
        return $vloga;
    }
    
    return match(strtoupper($vloga)) {
        'GOST' => VLOGA_GOST,
        'S0' => VLOGA_S0,
        'S1' => VLOGA_S1,
        'S2' => VLOGA_S2,
        'S3' => VLOGA_S3,
        'S4' => VLOGA_S4,
        'S5' => VLOGA_S5,
        'ADMIN' => VLOGA_ADMIN,
        default => VLOGA_GOST
    };
}

function pravice_trenutna_vloga(): int
{
    seja_zacni();
    return $_SESSION['uporabnik_vloga'] ?? VLOGA_GOST;
}

function pravice_ima_vlogo(int $zahtevana): bool
{
    return pravice_trenutna_vloga() >= $zahtevana;
}

function pravice_izvedi(array $zahteva): array
{
    $zahteva['sistem']['trenutna_vloga'] = pravice_trenutna_vloga();
    $zahteva['sistem']['pravice_preverjene'] = true;
    return $zahteva;
}