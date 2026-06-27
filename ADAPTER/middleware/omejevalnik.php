<?php
/**
 * ============================================================
 * POT: ADAPTER/middleware/omejevalnik.php
 * 📅 VERZIJA: v115 (14.6.2026 18:00)
 * ============================================================
 *
 * 🏛️ NIVO: ADAPTER
 *
 * 📰 NAMEN:
 *     Rate limiting – omejevanje zahtev.
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - omejevalnik_preveri(): bool
 *     - omejevalnik_zabelezi_zahtevo(): void
 *     - omejevalnik_pocisti_stare(int $starejseOdSekund = 86400): int
 *
 * 📡 ODVISNOSTI:
 *     - pot.php (POT_PODATKI)
 *
 * ⚡ UPORABA:
 *     - Kliče se iz ADAPTER/adapter.php
 *
 * 🚫 PREPOVEDI:
 *     - Brez echo, print_r, var_dump (razen exit pri napaki)
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
 *     adapter, middleware, rate-limit
 * ============================================================
 */
declare(strict_types=1);

defined('SISTEM_VARNOST') or die('Direkten dostop ni dovoljen.');

// ============================================================
// 1. NASTAVITVE
// ============================================================
$GLOBALS['OMEJEVALNIK_NASTAVITVE'] = [
    'omejitev' => 100,
    'cas_okna' => 60,
];

// ============================================================
// 2. POMOŽNE FUNKCIJE
// ============================================================

function omejevalnik_pridobi_kljuc(): string
{
    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    $pot = $_SERVER['REQUEST_URI'] ?? '/';
    return md5($ip . '|' . $pot);
}

function omejevalnik_preveri(): bool
{
    $nastavitve = $GLOBALS['OMEJEVALNIK_NASTAVITVE'];
    $kljuc = omejevalnik_pridobi_kljuc();
    $potPodatkov = POT_PODATKI . '/sistem/omejevalnik/';
    
    if (!is_dir($potPodatkov)) {
        mkdir($potPodatkov, 0755, true);
    }
    
    $datoteka = $potPodatkov . '/' . $kljuc . '.json';
    $zdaj = time();
    
    if (!file_exists($datoteka)) {
        return true;
    }
    
    $podatki = json_decode(file_get_contents($datoteka), true);
    if (!is_array($podatki)) {
        return true;
    }
    
    if (isset($podatki['zahteve'])) {
        $podatki['zahteve'] = array_filter($podatki['zahteve'], function($cas) use ($zdaj, $nastavitve) {
            return $cas > $zdaj - $nastavitve['cas_okna'];
        });
        
        if (count($podatki['zahteve']) >= $nastavitve['omejitev']) {
            return false;
        }
    }
    
    return true;
}

function omejevalnik_zabelezi_zahtevo(): void
{
    $kljuc = omejevalnik_pridobi_kljuc();
    $potPodatkov = POT_PODATKI . '/sistem/omejevalnik/';
    
    if (!is_dir($potPodatkov)) {
        mkdir($potPodatkov, 0755, true);
    }
    
    $datoteka = $potPodatkov . '/' . $kljuc . '.json';
    $zdaj = time();
    
    $podatki = [];
    if (file_exists($datoteka)) {
        $podatki = json_decode(file_get_contents($datoteka), true);
        if (!is_array($podatki)) {
            $podatki = [];
        }
    }
    
    if (!isset($podatki['zahteve'])) {
        $podatki['zahteve'] = [];
    }
    if (!isset($podatki['ustvarjeno'])) {
        $podatki['ustvarjeno'] = $zdaj;
    }
    
    $podatki['zahteve'][] = $zdaj;
    $podatki['zadnja_zahteva'] = $zdaj;
    
    file_put_contents($datoteka, json_encode($podatki, JSON_PRETTY_PRINT), LOCK_EX);
}

function omejevalnik_pocisti_stare(int $starejseOdSekund = 86400): int
{
    $potPodatkov = POT_PODATKI . '/sistem/omejevalnik/';
    
    if (!is_dir($potPodatkov)) {
        return 0;
    }
    
    $zdaj = time();
    $stevec = 0;
    
    foreach (glob($potPodatkov . '*.json') as $datoteka) {
        $podatki = json_decode(file_get_contents($datoteka), true);
        if (is_array($podatki)) {
            $zadnja = $podatki['zadnja_zahteva'] ?? $podatki['ustvarjeno'] ?? 0;
            if ($zdaj - $zadnja > $starejseOdSekund) {
                unlink($datoteka);
                $stevec++;
            }
        }
    }
    
    return $stevec;
}

// ============================================================
// 3. IZVEDBA
// ============================================================
if (PHP_SAPI !== 'cli' && (!defined('RAZVOJNI_NACIN') || !RAZVOJNI_NACIN)) {
    if (!omejevalnik_preveri()) {
        http_response_code(429);
        header('Retry-After: 60');
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'status' => 'error',
            'status_koda' => 429,
            'sporocilo' => 'Preveč zahtev. Počakajte 60 sekund.',
            'napake' => ['Rate limit exceeded']
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
    omejevalnik_zabelezi_zahtevo();
}