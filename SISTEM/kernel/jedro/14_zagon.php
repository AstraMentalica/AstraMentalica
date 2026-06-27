<?php
/**
 * ============================================================
 * POT: SISTEM/kernel/jedro/14_zagon.php
 * 📅 VERZIJA: v115 (14.6.2026 18:00)
 * ============================================================
 *
 * 🏛️ NIVO: KERNEL N3 (JEDRO)
 *
 * 📰 NAMEN:
 *     Zagon sistema – registracija osnovnih komponent.
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - zagon_izvedi(array $zahteva): array
 *
 * 📡 ODVISNOSTI:
 *     - pot.php
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
 *     kernel, jedro, zagon
 * ============================================================
 */
declare(strict_types=1);

defined('SISTEM_VARNOST') or die('Direkten dostop ni dovoljen.');

function zagon_izvedi(array $zahteva): array
{
    $zahteva['sistem']['zagon_izveden'] = true;
    $zahteva['sistem']['zagon_cas'] = time();
    return $zahteva;
}