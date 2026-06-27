<?php
/**
 * ============================================================
 * POT: SISTEM/kernel/jedro/08_kavlji.php
 * 📅 VERZIJA: v115 (14.6.2026 18:00)
 * ============================================================
 *
 * 🏛️ NIVO: KERNEL N3 (JEDRO)
 *
 * 📰 NAMEN:
 *     Upravljanje kavljev (hook system).
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - kavlji_izvedi(array $zahteva): array
 *     - kavelj_dodaj(string $ime, callable $callback, int $prioriteta = 10): void
 *     - kavelj_izvedi(string $ime, $vrednost)
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
 *     kernel, jedro, kavlji
 * ============================================================
 */
declare(strict_types=1);

defined('SISTEM_VARNOST') or die('Direkten dostop ni dovoljen.');

$GLOBALS['SISTEM_KAVLJI'] = [];

function kavelj_dodaj(string $ime, callable $callback, int $prioriteta = 10): void
{
    if (!isset($GLOBALS['SISTEM_KAVLJI'][$ime])) {
        $GLOBALS['SISTEM_KAVLJI'][$ime] = [];
    }
    
    $GLOBALS['SISTEM_KAVLJI'][$ime][] = [
        'callback' => $callback,
        'prioriteta' => $prioriteta
    ];
    
    usort($GLOBALS['SISTEM_KAVLJI'][$ime], function($a, $b) {
        return $a['prioriteta'] <=> $b['prioriteta'];
    });
}

function kavelj_izvedi(string $ime, $vrednost)
{
    if (!isset($GLOBALS['SISTEM_KAVLJI'][$ime])) {
        return $vrednost;
    }
    
    $rezultat = $vrednost;
    foreach ($GLOBALS['SISTEM_KAVLJI'][$ime] as $kavelj) {
        try {
            $rezultat = $kavelj['callback']($rezultat);
        } catch (Throwable $e) {
            error_log('[KAVELJ] Napaka pri kavlju ' . $ime . ': ' . $e->getMessage());
        }
    }
    
    return $rezultat;
}

function kavlji_izvedi(array $zahteva): array
{
    $zahteva = kavelj_izvedi('zahteva_normalizirana', $zahteva);
    $zahteva['sistem']['kavlji_registrirani'] = count($GLOBALS['SISTEM_KAVLJI']);
    return $zahteva;
}