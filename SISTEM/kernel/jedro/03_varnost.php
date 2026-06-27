<?php
/**
 * ============================================================
 * POT: SISTEM/kernel/jedro/03_varnost.php
 * 📅 VERZIJA: v115 (14.6.2026 18:00)
 * ============================================================
 *
 * 🏛️ NIVO: KERNEL N3 (JEDRO)
 *
 * 📰 NAMEN:
 *     Varnostne funkcije – sanitizacija, validacija.
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - varnost_izvedi(array $zahteva): array
 *     - varnost_sanitiziraj(string $vnos): string
 *     - varnost_pridobi_ip(): string
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
 *     kernel, jedro, varnost
 * ============================================================
 */
declare(strict_types=1);

defined('SISTEM_VARNOST') or die('Direkten dostop ni dovoljen.');

function varnost_sanitiziraj(string $vnos): string
{
    return htmlspecialchars(trim($vnos), ENT_QUOTES, 'UTF-8');
}

function varnost_pridobi_ip(): string
{
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
    } elseif (!empty($_SERVER['HTTP_X_REAL_IP'])) {
        $ip = $_SERVER['HTTP_X_REAL_IP'];
    }
    return $ip;
}

function varnost_izvedi(array $zahteva): array
{
    foreach ($zahteva['parametri'] as $kljuc => $vrednost) {
        if (is_string($vrednost)) {
            $zahteva['parametri'][$kljuc] = varnost_sanitiziraj($vrednost);
        }
    }
    
    if (is_array($zahteva['vsebina'])) {
        foreach ($zahteva['vsebina'] as $kljuc => $vrednost) {
            if (is_string($vrednost)) {
                $zahteva['vsebina'][$kljuc] = varnost_sanitiziraj($vrednost);
            }
        }
    }
    
    return $zahteva;
}