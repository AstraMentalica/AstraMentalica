<?php
/**
 * ============================================================
 * POT: SISTEM/kernel/jedro/11_usmerjevalnik.php
 * 📅 VERZIJA: v115 (14.6.2026 18:00)
 * ============================================================
 *
 * 🏛️ NIVO: KERNEL N3 (JEDRO)
 *
 * 📰 NAMEN:
 *     Usmerjevalnik – povezuje poti s handlerji.
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - usmerjevalnik_izvedi(array $zahteva): array
 *     - usmerjevalnik_dodaj(string $pot, callable $handler): void
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
 *     kernel, jedro, usmerjevalnik
 * ============================================================
 */
declare(strict_types=1);

defined('SISTEM_VARNOST') or die('Direkten dostop ni dovoljen.');

$GLOBALS['SISTEM_USMERJEVALNIK'] = [];

function usmerjevalnik_dodaj(string $pot, callable $handler): void
{
    $GLOBALS['SISTEM_USMERJEVALNIK'][$pot] = $handler;
}

function usmerjevalnik_izvedi(array $zahteva): array
{
    $svet = $zahteva['pot'] ?? 'GLOBALNO';
    
    if (isset($GLOBALS['SISTEM_USMERJEVALNIK'][$svet])) {
        $handler = $GLOBALS['SISTEM_USMERJEVALNIK'][$svet];
        return $handler($zahteva);
    }
    
    return $zahteva;
}