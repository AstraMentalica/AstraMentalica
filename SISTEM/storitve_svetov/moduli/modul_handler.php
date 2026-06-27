<?php
/**
 * ============================================================
 * POT: SISTEM/storitve_svetov/moduli/modul_handler.php
 * 📅 VERZIJA: v115 (14.6.2026 18:00)
 * ============================================================
 *
 * 🏛️ NIVO: STORITEV (BUSINESS LAYER)
 *
 * 📰 NAMEN:
 *     Handler za module – izvajanje modulov preko Modul_Bridge.
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - modul_handler_izvedi(array $zahteva): array
 *
 * 📡 ODVISNOSTI:
 *     - pot.php
 *     - MODUL_Bridge (če obstaja)
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
 *     storitev, moduli, handler
 * ============================================================
 */
declare(strict_types=1);

defined('SISTEM_VARNOST') or die('Direkten dostop ni dovoljen.');

function modul_handler_izvedi(array $zahteva): array
{
    $imeModula = $zahteva['parametri']['modul'] ?? $zahteva['vsebina']['modul'] ?? '';
    $akcija = $zahteva['parametri']['akcija'] ?? $zahteva['vsebina']['akcija'] ?? '';
    $podatki = array_merge($zahteva['parametri'] ?? [], $zahteva['vsebina'] ?? []);
    
    if (empty($imeModula)) {
        return [
            'status' => 'napaka',
            'status_koda' => 400,
            'sporocilo' => 'Manjka ime modula.'
        ];
    }
    
    // Poskusi preko Modul_Bridge
    $bridge_pot = POT_MODULI . '/Modul_Bridge/index.php';
    if (file_exists($bridge_pot)) {
        require_once $bridge_pot;
        if (function_exists('modul_bridge_izvedi')) {
            return modul_bridge_izvedi($imeModula, $akcija, $podatki);
        }
    }
    
    // Fallback – direktno iskanje modula
    $pot_modula = POT_MODULI . '/' . $imeModula . '/modul.php';
    if (!file_exists($pot_modula)) {
        return [
            'status' => 'napaka',
            'status_koda' => 404,
            'sporocilo' => 'Modul "' . $imeModula . '" ne obstaja.'
        ];
    }
    
    require_once $pot_modula;
    $funkcija = 'modul_' . strtolower($imeModula) . '_izvedi';
    
    if (!function_exists($funkcija)) {
        return [
            'status' => 'napaka',
            'status_koda' => 500,
            'sporocilo' => 'Modul "' . $imeModula . '" nima vstopne funkcije.'
        ];
    }
    
    $rezultat = $funkcija($akcija, $podatki);
    
    return [
        'status' => 'uspeh',
        'status_koda' => 200,
        'vsebina' => $rezultat,
        'kanal' => $zahteva['kanal'] ?? 'splet'
    ];
}