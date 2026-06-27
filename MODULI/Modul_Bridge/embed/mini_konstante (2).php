<?php
/**
 * ============================================================
 * POT: MODULI/Modul_Bridge/embed/mini_konstante.php
 * 📅 VERZIJA: v116 (18.6.2026 21:05)
 * ============================================================
 *
 * 🏛️ NIVO: MODUL (Modul_Bridge/embed)
 *
 * 📰 NAMEN:
 *     Mini konstante za samostojno delovanje Bridge-a
 *     kadar ASTRAMENTALICA sistem ne obstaja.
 *
 * 🚫 PREPOVEDI:
 *     - Brez logike
 *     - Brez require_once
 *     - Brez die(), exit()
 *
 * 📌 STATUS:
 *     Stabilno
 *
 * 📅 ZGODOVINA:
 *     - v116: uskladitev s Header Standard v116
 *
 * 👤 AVTOR:
 *     AstraMentalica Mojster
 *
 * 🌐 JEZIK:
 *     sl
 *
 * 🏷️ OZNAKE:
 *     modul, bridge, embed, konstante
 * ============================================================
 */
declare(strict_types=1);

// VARNOST – namesto die() uporabimo return (enak vzorec kot index.php)
if (!defined('BRIDGE_VARNOST')) {
    http_response_code(403);
    return;
}

// Poti (relativno od embed/ mape navzgor)
if (!defined('MINI_ROOT')) {
    define('MINI_ROOT', __DIR__ . '/../../');   // root sistema
}
if (!defined('MINI_BRIDGE')) {
    define('MINI_BRIDGE', __DIR__ . '/../');    // MODULI/Modul_Bridge/
}
if (!defined('MINI_MODULI')) {
    define('MINI_MODULI', MINI_ROOT . 'MODULI'); // MODULI/ – ploski, brez kategorij
}

// RBAC vloge – ujemanje z vrednostmi pravega sistema
if (!defined('MINI_VLOGA_GOST')) {
    define('MINI_VLOGA_GOST', 0);
}
if (!defined('MINI_VLOGA_UPORABNIK')) {
    define('MINI_VLOGA_UPORABNIK', 20);
}
if (!defined('MINI_VLOGA_ADMIN')) {
    define('MINI_VLOGA_ADMIN', 100);
}

// Cache
if (!defined('MINI_CACHE_TTL')) {
    define('MINI_CACHE_TTL', 3600);
}

// Ime aplikacije za prikaz
if (!defined('MINI_IME_APP')) {
    define('MINI_IME_APP', 'Modul Bridge');
}
