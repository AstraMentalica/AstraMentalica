<?php
/**
 * ============================================================
 * POT: SISTEM/kernel/jedro/02_napake.php
 * 📅 VERZIJA: v115 (14.6.2026 18:00)
 * ============================================================
 *
 * 🏛️ NIVO: KERNEL N3 (JEDRO)
 *
 * 📰 NAMEN:
 *     Upravljanje napak – zajemanje, beleženje in prikaz.
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - napake_izvedi(array $zahteva): array
 *
 * 📡 ODVISNOSTI:
 *     - pot.php
 *
 * 🚫 PREPOVEDI:
 *     - Brez __DIR__
 *     - Brez echo, print_r, var_dump
 *     - Brez die(), exit()
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
 *     kernel, jedro, napake
 * ============================================================
 */
declare(strict_types=1);

defined('SISTEM_VARNOST') or die('Direkten dostop ni dovoljen.');

$GLOBALS['SISTEM_NAPAKE'] = [];

function napake_izvedi(array $zahteva): array
{
    set_exception_handler(function(Throwable $e) {
        $napaka = [
            'sporocilo' => $e->getMessage(),
            'datoteka' => $e->getFile(),
            'vrstica' => $e->getLine(),
            'cas' => time(),
            'sled' => $e->getTraceAsString()
        ];
        $GLOBALS['SISTEM_NAPAKE'][] = $napaka;
        error_log('[NAPAKA] ' . $e->getMessage() . ' v ' . $e->getFile() . ':' . $e->getLine());
    });
    
    $zahteva['sistem']['napake'] = $GLOBALS['SISTEM_NAPAKE'];
    return $zahteva;
}