<?php
/**
 * ============================================================
 * POT: SISTEM/kernel/jedro/09_ponudniki.php
 * 📅 VERZIJA: v115 (14.6.2026 18:00)
 * ============================================================
 *
 * 🏛️ NIVO: KERNEL N3 (JEDRO)
 *
 * 📰 NAMEN:
 *     Upravljanje ponudnikov (service providers).
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - ponudniki_izvedi(array $zahteva): array
 *     - ponudnik_registriraj(string $ime, callable $factory): void
 *     - ponudnik_pridobi(string $ime)
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
 *     kernel, jedro, ponudniki
 * ============================================================
 */
declare(strict_types=1);

defined('SISTEM_VARNOST') or die('Direkten dostop ni dovoljen.');

$GLOBALS['SISTEM_PONUDNIKI'] = [];
$GLOBALS['SISTEM_PONUDNIKI_INSTANCE'] = [];

function ponudnik_registriraj(string $ime, callable $factory): void
{
    $GLOBALS['SISTEM_PONUDNIKI'][$ime] = $factory;
}

function ponudnik_pridobi(string $ime)
{
    if (isset($GLOBALS['SISTEM_PONUDNIKI_INSTANCE'][$ime])) {
        return $GLOBALS['SISTEM_PONUDNIKI_INSTANCE'][$ime];
    }
    
    if (!isset($GLOBALS['SISTEM_PONUDNIKI'][$ime])) {
        return null;
    }
    
    $factory = $GLOBALS['SISTEM_PONUDNIKI'][$ime];
    $GLOBALS['SISTEM_PONUDNIKI_INSTANCE'][$ime] = $factory();
    
    return $GLOBALS['SISTEM_PONUDNIKI_INSTANCE'][$ime];
}

function ponudniki_izvedi(array $zahteva): array
{
    $zahteva['sistem']['ponudniki_registrirani'] = count($GLOBALS['SISTEM_PONUDNIKI']);
    return $zahteva;
}