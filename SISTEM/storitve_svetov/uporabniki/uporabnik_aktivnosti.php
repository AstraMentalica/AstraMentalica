<?php
/**
 * ============================================================
 * POT: SISTEM/storitve_svetov/uporabniki/uporabnik_aktivnosti.php
 * 📅 VERZIJA: v115 (14.6.2026 18:00)
 * ============================================================
 *
 * 🏛️ NIVO: STORITEV (BUSINESS LAYER)
 *
 * 📰 NAMEN:
 *     Beleženje uporabniških aktivnosti.
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - uporabniki_aktivnosti_zabelezi(string $uporabnikId, string $tip, array $podatki): void
 *     - uporabniki_aktivnosti_zadnje(string $uporabnikId, int $limit): array
 *     - uporabniki_aktivnosti_po_datumu(string $uporabnikId, int $od, int $do): array
 *
 * 📡 ODVISNOSTI:
 *     - SISTEM/kernel/jedro/07_dogodki.php
 *     - SISTEM/kernel/baze/upravljalec_baz.php
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
 *     storitev, uporabniki, aktivnosti
 * ============================================================
 */
declare(strict_types=1);

defined('SISTEM_VARNOST') or die('Direkten dostop ni dovoljen.');

function uporabniki_aktivnosti_zabelezi(string $uporabnikId, string $tip, array $podatki = []): void
{
    $aktivnost = [
        'id' => uniqid('akt_', true),
        'uporabnik_id' => $uporabnikId,
        'tip' => $tip,
        'podatki' => $podatki,
        'cas' => time(),
        'ip' => varnost_pridobi_ip()
    ];
    
    baza_zapisi('aktivnosti', $aktivnost);
    
    // Sproži dogodek za morebitne poslušalce
    dogodek_sprozi('uporabnik.aktivnost', $aktivnost);
}

function uporabniki_aktivnosti_zadnje(string $uporabnikId, int $limit = 10): array
{
    $vseAktivnosti = baza_beri('aktivnosti');
    
    $uporabnikove = array_filter($vseAktivnosti, function($aktivnost) use ($uporabnikId) {
        return ($aktivnost['uporabnik_id'] ?? '') === $uporabnikId;
    });
    
    usort($uporabnikove, function($a, $b) {
        return $b['cas'] <=> $a['cas'];
    });
    
    return array_slice($uporabnikove, 0, $limit);
}

function uporabniki_aktivnosti_po_datumu(string $uporabnikId, int $od, int $do): array
{
    $vseAktivnosti = baza_beri('aktivnosti');
    
    return array_filter($vseAktivnosti, function($aktivnost) use ($uporabnikId, $od, $do) {
        return ($aktivnost['uporabnik_id'] ?? '') === $uporabnikId &&
               $aktivnost['cas'] >= $od && 
               $aktivnost['cas'] <= $do;
    });
}