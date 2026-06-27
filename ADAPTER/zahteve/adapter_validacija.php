<?php
/**
 * ============================================================
 * POT: ADAPTER/zahteve/adapter_validacija.php
 * 📅 VERZIJA: v114 (9.6.2026 18:00)
 * ============================================================
 *
 * 🏛️ NIVO: ADAPTER
 *
 * 📰 NAMEN:
 *     Validacija zahteve pred obdelavo.
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - adapter_validiraj_zahtevo(AdapterZahteva $zahteva): array
 *
 * 📡 ODVISNOSTI:
 *     - (nobene)
 *
 * 🚫 PREPOVEDI:
 *     - Brez echo, print_r, var_dump
 *     - Brez die(), exit()
 *     - Brez direktnih poti (uporabi konstante!)
 *
 * 📌 STATUS:
 *     Stabilno
 *
 * 📅 ZGODOVINA:
 *     - v114: uskladitev s Header Standard v114
 *
 * 👤 AVTOR:
 *     AstraMentalica Mojster
 *
 * 🌐 JEZIK:
 *     sl
 *
 * 🏷️ OZNAKE:
 *     adapter, zahteve, validacija
 * ============================================================
 */
declare(strict_types=1);

function adapter_validiraj_zahtevo(AdapterZahteva $zahteva): array
{
    $napake = [];
    
    // Preveri pot
    $pot = $zahteva->pot();
    if (empty($pot)) {
        $napake[] = 'Pot ne sme biti prazna';
    }
    
    // Preveri metodo
    $dovoljeneMetode = ['DOBI', 'OBJAVA', 'POSODOBI', 'BRISI', 'POSREDUJ'];
    if (!in_array($zahteva->metoda(), $dovoljeneMetode)) {
        $napake[] = 'Neveljavna metoda: ' . $zahteva->metoda();
    }
    
    // Preveri IP (ce je blacklistan)
    $ip = $zahteva->ip();
    if (!empty($ip) && function_exists('ip_blacklist_preveri')) {
        if (!ip_blacklist_preveri()) {
            $napake[] = 'IP naslov je blokiran';
        }
    }
    
    // Preveri CSRF za nevarne metode (razen za API in webhook)
    $kanal = $zahteva->kanal();
    $nevarneMetode = ['OBJAVA', 'POSODOBI', 'BRISI'];
    if (in_array($zahteva->metoda(), $nevarneMetode) && $kanal !== 'api' && $kanal !== 'webhook') {
        if (function_exists('csrf_preveri')) {
            $token = $zahteva->pridobiParam('csrf_token', '');
            if (!csrf_preveri($token)) {
                $napake[] = 'Neveljaven CSRF token';
            }
        }
    }
    
    return [
        'veljavno' => empty($napake),
        'napake' => $napake
    ];
}