<?php
/**
 * ============================================================
 * POT: SISTEM/kernel/jedro/07_dogodki.php
 * 📅 VERZIJA: v115 (14.6.2026 18:00)
 * ============================================================
 *
 * 🏛️ NIVO: KERNEL N3 (JEDRO)
 *
 * 📰 NAMEN:
 *     Upravljanje dogodkov (event bus).
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - dogodki_izvedi(array $zahteva): array
 *     - dogodek_poslusi(string $ime, callable $callback, int $prioriteta = 10): void
 *     - dogodek_sprozi(string $ime, array $podatki = []): void
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
 *     kernel, jedro, dogodki
 * ============================================================
 */
declare(strict_types=1);

defined('SISTEM_VARNOST') or die('Direkten dostop ni dovoljen.');

$GLOBALS['SISTEM_DOGODKI'] = [];

function dogodek_poslusi(string $ime, callable $callback, int $prioriteta = 10): void
{
    if (!isset($GLOBALS['SISTEM_DOGODKI'][$ime])) {
        $GLOBALS['SISTEM_DOGODKI'][$ime] = [];
    }
    
    $GLOBALS['SISTEM_DOGODKI'][$ime][] = [
        'callback' => $callback,
        'prioriteta' => $prioriteta
    ];
    
    usort($GLOBALS['SISTEM_DOGODKI'][$ime], function($a, $b) {
        return $a['prioriteta'] <=> $b['prioriteta'];
    });
}

function dogodek_sprozi(string $ime, array $podatki = []): void
{
    if (!isset($GLOBALS['SISTEM_DOGODKI'][$ime])) {
        return;
    }
    
    foreach ($GLOBALS['SISTEM_DOGODKI'][$ime] as $poslusalec) {
        try {
            ($poslusalec['callback'])($podatki);
        } catch (Throwable $e) {
            error_log('[DOGODEK] Napaka v poslušalcu za ' . $ime . ': ' . $e->getMessage());
        }
    }
}

function dogodki_izvedi(array $zahteva): array
{
    $zahteva['sistem']['dogodki_registrirani'] = count($GLOBALS['SISTEM_DOGODKI']);
    return $zahteva;
}