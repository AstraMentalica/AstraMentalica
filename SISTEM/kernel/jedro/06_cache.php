<?php
/**
 * ============================================================
 * POT: SISTEM/kernel/jedro/06_cache.php
 * 📅 VERZIJA: v115 (14.6.2026 18:00)
 * ============================================================
 *
 * 🏛️ NIVO: KERNEL N3 (JEDRO)
 *
 * 📰 NAMEN:
 *     Upravljanje predpomnilnika (cache).
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - cache_izvedi(array $zahteva): array
 *     - cache_shrani(string $kljuc, $vrednost, int $ttl = 3600): bool
 *     - cache_preberi(string $kljuc)
 *     - cache_brisi(string $kljuc): bool
 *     - cache_pocisti(): void
 *
 * 📡 ODVISNOSTI:
 *     - pot.php (POT_CACHE)
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
 *     kernel, jedro, cache
 * ============================================================
 */
declare(strict_types=1);

defined('SISTEM_VARNOST') or die('Direkten dostop ni dovoljen.');

function cache_pot(string $kljuc): string
{
    $mapa = POT_CACHE;
    if (!is_dir($mapa)) {
        mkdir($mapa, 0755, true);
    }
    return POT_CACHE . '/' . md5($kljuc) . '.cache';
}

function cache_shrani(string $kljuc, $vrednost, int $ttl = 3600): bool
{
    $podatki = [
        'vrednost' => $vrednost,
        'potek' => time() + $ttl
    ];
    $pot = cache_pot($kljuc);
    return file_put_contents($pot, serialize($podatki), LOCK_EX) !== false;
}

function cache_preberi(string $kljuc)
{
    $pot = cache_pot($kljuc);
    
    if (!file_exists($pot)) {
        return null;
    }
    
    $podatki = unserialize(file_get_contents($pot));
    
    if (!is_array($podatki) || $podatki['potek'] < time()) {
        if (file_exists($pot)) {
            unlink($pot);
        }
        return null;
    }
    
    return $podatki['vrednost'];
}

function cache_brisi(string $kljuc): bool
{
    $pot = cache_pot($kljuc);
    if (file_exists($pot)) {
        return unlink($pot);
    }
    return true;
}

function cache_pocisti(): void
{
    $mapa = POT_CACHE;
    if (!is_dir($mapa)) {
        return;
    }
    
    foreach (glob($mapa . '/*.cache') as $datoteka) {
        unlink($datoteka);
    }
}

function cache_izvedi(array $zahteva): array
{
    $zahteva['sistem']['cache_omogocen'] = true;
    return $zahteva;
}