<?php
/**
 * ============================================================
 * POT: MODULI/Modul_Bridge/embed/mini_cache.php
 * 📅 VERZIJA: v116 (18.6.2026 21:05)
 * ============================================================
 *
 * 🏛️ NIVO: MODUL (Modul_Bridge/embed)
 *
 * 📰 NAMEN:
 *     Mini cache – shranjevanje v seji, brez PODATKI/.
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - mini_cache_shrani(string $kljuc, $vrednost, int $ttl): bool
 *     - mini_cache_preberi(string $kljuc): mixed
 *     - mini_cache_brisi(string $kljuc): bool
 *     - mini_cache_pocisti(): void
 *
 * 📌 STATUS:
 *     Stabilno
 *
 * 📅 ZGODOVINA:
 *     - v116: uskladitev s Header Standard v116,
 *             odstranjeni vsi die() in exit()
 *
 * 👤 AVTOR:
 *     AstraMentalica Mojster
 *
 * 🌐 JEZIK:
 *     sl
 *
 * 🏷️ OZNAKE:
 *     modul, bridge, embed, cache
 * ============================================================
 */
declare(strict_types=1);

// VARNOST – namesto die() uporabimo return (enak vzorec kot index.php)
if (!defined('BRIDGE_VARNOST')) {
    http_response_code(403);
    return;
}

function mini_cache_shrani(string $kljuc, $vrednost, int $ttl = MINI_CACHE_TTL): bool
{
    if (!isset($_SESSION['mini_cache'])) {
        $_SESSION['mini_cache'] = [];
    }
    $_SESSION['mini_cache'][$kljuc] = [
        'vrednost' => $vrednost,
        'potek' => time() + $ttl,
    ];
    return true;
}

function mini_cache_preberi(string $kljuc): mixed
{
    if (!isset($_SESSION['mini_cache'][$kljuc])) {
        return null;
    }
    $item = $_SESSION['mini_cache'][$kljuc];
    if ($item['potek'] < time()) {
        unset($_SESSION['mini_cache'][$kljuc]);
        return null;
    }
    return $item['vrednost'];
}

function mini_cache_brisi(string $kljuc): bool
{
    if (isset($_SESSION['mini_cache'][$kljuc])) {
        unset($_SESSION['mini_cache'][$kljuc]);
        return true;
    }
    return false;
}

function mini_cache_pocisti(): void
{
    $_SESSION['mini_cache'] = [];
}
