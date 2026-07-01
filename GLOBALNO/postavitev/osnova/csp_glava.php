<?php
/**
 * ---------------------------------------------------------
 * POT: GLOBALNO/postavitev/osnova/csp_glava.php
 * v111 (27.5.2026 14:30)
 * ---------------------------------------------------------
 * OPIS: CSP varnostne glave – Content Security Policy
 * ---------------------------------------------------------
 *
 * ODVISNOSTI:
 * - pot.php
 *
 * UPORABA:
 * - GLOBALNO/render/postavitev/*.php
 *
 * FUNKCIJE:
 * - csp_generiraj_nonce(), csp_nastavi_glave()
 * - csp_dodaj_politiko()
 *
 * PREPOVEDI:
 * - Brez echo, print_r, var_dump
 *
 * STATUS: Stabilno
 *
 * ZGODOVINA:
 * - v111: FAZA 20+ – implementacija
 *
 * ---------------------------------------------------------
 * AVTOR: AstraMentalica Mojster
 * ---------------------------------------------------------
 */

declare(strict_types=1);

$GLOBALS['CSP_POLITIKE'] = [];

function csp_generiraj_nonce(): string
{
    return bin2hex(random_bytes(16));
}

function csp_dodaj_politiko(string $politika): void
{
    $GLOBALS['CSP_POLITIKE'][] = $politika;
}

function csp_nastavi_glave(string $nonce = ''): void
{
    if (headers_sent()) {
        return;
    }
    
    $politike = [
        "default-src 'self'",
        "script-src 'self' 'unsafe-inline' 'unsafe-eval'" . ($nonce ? " 'nonce-$nonce'" : ''),
        "style-src 'self' 'unsafe-inline'",
        "img-src 'self' data: https:",
        "font-src 'self'",
        "connect-src 'self'",
        "frame-src 'self'",
        "object-src 'none'",
        "base-uri 'self'",
        "form-action 'self'"
    ];
    
    // Dodaj dodatne politike
    foreach ($GLOBALS['CSP_POLITIKE'] as $dodatna) {
        $politike[] = $dodatna;
    }
    
    $cspGlava = implode('; ', $politike);
    header("Content-Security-Policy: $cspGlava");
    
    // Dodatne varnostne glave
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: DENY');
    header('X-XSS-Protection: 1; mode=block');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Permissions-Policy: geolocation=(), microphone=(), camera=()');
}

// Samodejno nastavi glave, ce se klic funkcije
if (!function_exists('headers_sent') || !headers_sent()) {
    // Ne izvajamo samodejno – pusti, da postavitev poklice
}