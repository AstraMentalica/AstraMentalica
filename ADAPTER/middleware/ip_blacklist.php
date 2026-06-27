<?php
/**
 * ============================================================
 * POT: ADAPTER/middleware/ip_blacklist.php
 * 📅 VERZIJA: v115 (14.6.2026 18:00)
 * ============================================================
 *
 * 🏛️ NIVO: ADAPTER
 *
 * 📰 NAMEN:
 *     IP blacklist – blokiranje naslovov.
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - ip_blacklist_preveri(): bool
 *     - ip_blacklist_dodaj(string $ip, ?int $trajanje = null): bool
 *     - ip_blacklist_odstrani(string $ip): bool
 *     - ip_blacklist_pocisti_potekle(): int
 *     - ip_blacklist_pridobi_blokado(string $ip): ?array
 *
 * 📡 ODVISNOSTI:
 *     - pot.php (POT_PODATKI)
 *
 * ⚡ UPORABA:
 *     - Kliče se iz ADAPTER/adapter.php
 *
 * 🚫 PREPOVEDI:
 *     - Brez echo, print_r, var_dump (razen exit pri blokadi)
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
 *     adapter, middleware, ip, varnost
 * ============================================================
 */
declare(strict_types=1);

defined('SISTEM_VARNOST') or die('Direkten dostop ni dovoljen.');

// ============================================================
// 1. POMOŽNE FUNKCIJE
// ============================================================

function ip_blacklist_pridobi_seznam(): array
{
    $datoteka = POT_PODATKI . '/sistem/ip_blacklist.json';
    
    if (!file_exists($datoteka)) {
        return [];
    }
    
    $vsebina = file_get_contents($datoteka);
    if ($vsebina === false) {
        return [];
    }
    
    $seznam = json_decode($vsebina, true);
    return is_array($seznam) ? $seznam : [];
}

function ip_blacklist_preveri(): bool
{
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    if (empty($ip)) {
        return true;
    }
    
    $seznam = ip_blacklist_pridobi_seznam();
    $zdaj = time();
    
    foreach ($seznam as $blokiran) {
        if (!isset($blokiran['ip'])) {
            continue;
        }
        
        if ($blokiran['ip'] === $ip) {
            $potek = $blokiran['potek'] ?? null;
            if ($potek === null || $potek > $zdaj) {
                return false;
            }
        }
    }
    
    return true;
}

function ip_blacklist_dodaj(string $ip, ?int $trajanje = null): bool
{
    if (empty($ip)) {
        return false;
    }
    
    $seznam = ip_blacklist_pridobi_seznam();
    $datoteka = POT_PODATKI . '/sistem/ip_blacklist.json';
    
    foreach ($seznam as $item) {
        if ($item['ip'] === $ip) {
            return true;
        }
    }
    
    $seznam[] = [
        'ip' => $ip,
        'ustvarjeno' => time(),
        'potek' => $trajanje !== null ? time() + $trajanje : null
    ];
    
    $mapa = dirname($datoteka);
    if (!is_dir($mapa)) {
        mkdir($mapa, 0755, true);
    }
    
    return file_put_contents($datoteka, json_encode($seznam, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX) !== false;
}

function ip_blacklist_odstrani(string $ip): bool
{
    if (empty($ip)) {
        return false;
    }
    
    $seznam = ip_blacklist_pridobi_seznam();
    $datoteka = POT_PODATKI . '/sistem/ip_blacklist.json';
    
    $noviSeznam = array_filter($seznam, function($item) use ($ip) {
        return ($item['ip'] ?? '') !== $ip;
    });
    
    if (count($noviSeznam) === count($seznam)) {
        return true;
    }
    
    return file_put_contents($datoteka, json_encode(array_values($noviSeznam), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX) !== false;
}

function ip_blacklist_pocisti_potekle(): int
{
    $seznam = ip_blacklist_pridobi_seznam();
    $datoteka = POT_PODATKI . '/sistem/ip_blacklist.json';
    $zdaj = time();
    
    $noviSeznam = array_filter($seznam, function($item) use ($zdaj) {
        $potek = $item['potek'] ?? null;
        return $potek === null || $potek > $zdaj;
    });
    
    $odstranjenih = count($seznam) - count($noviSeznam);
    
    if ($odstranjenih > 0) {
        file_put_contents($datoteka, json_encode(array_values($noviSeznam), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX);
    }
    
    return $odstranjenih;
}

function ip_blacklist_pridobi_blokado(string $ip): ?array
{
    $seznam = ip_blacklist_pridobi_seznam();
    
    foreach ($seznam as $blokiran) {
        if (($blokiran['ip'] ?? '') === $ip) {
            return [
                'ip' => $blokiran['ip'],
                'ustvarjeno' => $blokiran['ustvarjeno'] ?? null,
                'potek' => $blokiran['potek'] ?? null,
                'je_trajna' => !isset($blokiran['potek']),
                'preostalo_sekund' => isset($blokiran['potek']) ? max(0, $blokiran['potek'] - time()) : null
            ];
        }
    }
    
    return null;
}

// ============================================================
// 2. IZVEDBA
// ============================================================
if (PHP_SAPI !== 'cli') {
    if (!ip_blacklist_preveri()) {
        http_response_code(403);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'status' => 'error',
            'status_koda' => 403,
            'sporocilo' => 'Dostop zavrnjen. Vaš IP naslov je blokiran.',
            'napake' => ['IP blacklisted']
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
}