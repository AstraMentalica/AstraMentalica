<?php
/**
 * ============================================================
 * POT: SISTEM/sistem_runtime/knjiznice/cas.php
 * ============================================================
 * 
 * 📦 NAMEN:
 *     Časovne funkcije
 * 
 * 🔧 FUNKCIJE:
 *     - sistem_cas(): int
 *     - sistem_cas_mili(): float
 *     - sistem_cas_formatiran(string $format = 'Y-m-d H:i:s', ?int $timestamp = null): string
 * 
 * @author ASTRAMENTALICA
 * @version 1.0.0
 * @since FAZA 1
 * ============================================================
 */

function sistem_cas(): int
{
    return time();
}

function sistem_cas_mili(): float
{
    return microtime(true);
}

function sistem_cas_formatiran(string $format = 'Y-m-d H:i:s', ?int $timestamp = null): string
{
    if ($timestamp === null) {
        $timestamp = sistem_cas();
    }
    
    return date($format, $timestamp);
}

function sistem_cas_trajanje(float $zacetek): float
{
    return sistem_cas_mili() - $zacetek;
}