<?php
/**
 * ============================================================
 * POT: SISTEM/kernel/jedro/01_upravljalec_svetov.php
 * 📅 VERZIJA: v115 (14.6.2026 18:00)
 * ============================================================
 *
 * 🏛️ NIVO: KERNEL N3 (JEDRO)
 *
 * 📰 NAMEN:
 *     Upravljalec svetov – generira whitelist dovoljenih svetov.
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - upravljalec_svetov_izvedi(array $zahteva): array
 *
 * 📡 ODVISNOSTI:
 *     - pot.php (POT_CACHE)
 *
 * 🚫 PREPOVEDI:
 *     - Brez __DIR__
 *     - Brez echo, print_r, var_dump
 *     - Brez business logike
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
 *     kernel, jedro, whitelist
 * ============================================================
 */
declare(strict_types=1);

defined('SISTEM_VARNOST') or die('Direkten dostop ni dovoljen.');

function upravljalec_svetov_izvedi(array $zahteva): array
{
    $dovoljeni_svetovi = ['GLOBALNO', 'UPORABNIKI', 'MODULI', 'API', 'ASTRA', 'ADMIN', 'VODA', 'ZRAK', 'ETER', 'ZEMLJA', 'OGENJ'];
    
    $whitelist_pot = POT_CACHE . '/whitelist.cache';
    $mapa = dirname($whitelist_pot);
    if (!is_dir($mapa)) {
        mkdir($mapa, 0755, true);
    }
    
    file_put_contents($whitelist_pot, serialize($dovoljeni_svetovi));
    
    $zahteva['sistem']['whitelist'] = $dovoljeni_svetovi;
    $zahteva['sistem']['whitelist_generiran'] = time();
    
    return $zahteva;
}