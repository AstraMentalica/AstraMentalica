<?php
/**
 * ============================================================
 * POT: SISTEM/kernel/jedro/10_middleware.php
 * 📅 VERZIJA: v115 (14.6.2026 18:00)
 * ============================================================
 *
 * 🏛️ NIVO: KERNEL N3 (JEDRO)
 *
 * 📰 NAMEN:
 *     Upravljanje middleware sloja.
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - middleware_izvedi(array $zahteva): array
 *     - middleware_dodaj(callable $middleware): void
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
 *     kernel, jedro, middleware
 * ============================================================
 */
declare(strict_types=1);

defined('SISTEM_VARNOST') or die('Direkten dostop ni dovoljen.');

$GLOBALS['SISTEM_MIDDLEWARE'] = [];

function middleware_dodaj(callable $middleware): void
{
    $GLOBALS['SISTEM_MIDDLEWARE'][] = $middleware;
}

function middleware_izvedi(array $zahteva): array
{
    foreach ($GLOBALS['SISTEM_MIDDLEWARE'] as $middleware) {
        try {
            $zahteva = $middleware($zahteva);
        } catch (Throwable $e) {
            error_log('[MIDDLEWARE] Napaka: ' . $e->getMessage());
        }
    }
    
    return $zahteva;
}