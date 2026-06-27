<?php
/**
 * ============================================================
 * POT: ADAPTER/middleware/dnevnik.php
 * 📅 VERZIJA: v115 (14.6.2026 18:00)
 * ============================================================
 *
 * 🏛️ NIVO: ADAPTER
 *
 * 📰 NAMEN:
 *     Logger middleware – beleženje zahtev.
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - dnevnik_zabelezi_zahtevo(?array $zahteva = null): void
 *     - dnevnik_zabelezi_odziv(array $odziv): void
 *     - dnevnik_pocisti_stare(int $maxStarostDni = 30): int
 *
 * 📡 ODVISNOSTI:
 *     - pot.php (POT_LOG)
 *
 * ⚡ UPORABA:
 *     - Kliče se iz ADAPTER/adapter.php
 *
 * 🚫 PREPOVEDI:
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
 *     adapter, middleware, dnevnik
 * ============================================================
 */
declare(strict_types=1);

defined('SISTEM_VARNOST') or die('Direkten dostop ni dovoljen.');

// ============================================================
// 1. GLOBALNE SPREMENLJIVKE
// ============================================================
$GLOBALS['DNEVNIK_ZACETEK'] = null;
$GLOBALS['DNEVNIK_ZADNJA_ZAHTEVA'] = null;

// ============================================================
// 2. POMOŽNE FUNKCIJE
// ============================================================

function dnevnik_zabelezi_zahtevo(?array $zahteva = null): void
{
    $GLOBALS['DNEVNIK_ZACETEK'] = microtime(true);
    
    $pot = POT_LOG . '/adapter.log';
    $mapa = dirname($pot);
    if (!is_dir($mapa)) {
        mkdir($mapa, 0755, true);
    }
    
    if ($zahteva === null) {
        $metoda = $_SERVER['REQUEST_METHOD'] ?? 'CLI';
        $uri = $_SERVER['REQUEST_URI'] ?? ($_SERVER['SCRIPT_NAME'] ?? 'cli');
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'cli';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '-';
        $kanal = $GLOBALS['ADAPTER_FORCIRANI_KANAL'] ?? 'web';
        $svet = $_GET['svet'] ?? '';
    } else {
        $metoda = $zahteva['metoda'] ?? 'GET';
        $uri = $zahteva['uri'] ?? ($zahteva['pot'] ?? '/');
        $ip = $zahteva['ip'] ?? 'unknown';
        $userAgent = $zahteva['user_agent'] ?? '-';
        $kanal = $zahteva['kanal'] ?? 'web';
        $svet = $zahteva['svet'] ?? '';
    }
    
    $vnos = sprintf(
        "[%s] [%s] [%s] [%s] [%s] [%s] [%s] %s\n",
        date('Y-m-d'),
        date('H:i:s'),
        str_pad($metoda, 6),
        $uri,
        $ip,
        $kanal,
        $svet,
        $userAgent
    );
    
    $GLOBALS['DNEVNIK_ZADNJA_ZAHTEVA'] = [
        'cas' => microtime(true),
        'metoda' => $metoda,
        'uri' => $uri,
        'ip' => $ip,
        'kanal' => $kanal
    ];
    
    file_put_contents($pot, $vnos, FILE_APPEND | LOCK_EX);
    dnevnik_rotiraj($pot);
}

function dnevnik_zabelezi_odziv(array $odziv): void
{
    if ($GLOBALS['DNEVNIK_ZACETEK'] === null) {
        return;
    }
    
    $trajanje = round((microtime(true) - $GLOBALS['DNEVNIK_ZACETEK']) * 1000, 2);
    $statusKoda = $odziv['status_koda'] ?? 200;
    $status = $odziv['status'] ?? 'success';
    
    $pot = POT_LOG . '/adapter.log';
    
    $vnos = sprintf(
        "[%s] [%s] [%sms] [%d] [%s] %s\n",
        date('Y-m-d'),
        date('H:i:s'),
        $trajanje,
        $statusKoda,
        strtoupper($status),
        $odziv['sporocilo'] ?? ''
    );
    
    file_put_contents($pot, $vnos, FILE_APPEND | LOCK_EX);
}

function dnevnik_rotiraj(string $pot, int $maxVelikostMB = 100, int $steviloArhivov = 5): void
{
    if (!file_exists($pot)) {
        return;
    }
    
    if (filesize($pot) < $maxVelikostMB * 1024 * 1024) {
        return;
    }
    
    for ($i = $steviloArhivov - 1; $i > 0; $i--) {
        $stari = $pot . '.' . $i;
        $novi = $pot . '.' . ($i + 1);
        if (file_exists($stari)) {
            @rename($stari, $novi);
        }
    }
    @rename($pot, $pot . '.1');
}

function dnevnik_pocisti_stare(int $maxStarostDni = 30): int
{
    $pot = POT_LOG . '/';
    
    if (!is_dir($pot)) {
        return 0;
    }
    
    $meja = time() - ($maxStarostDni * 24 * 60 * 60);
    $stevec = 0;
    
    foreach (glob($pot . '*.log*') as $datoteka) {
        if (filemtime($datoteka) < $meja) {
            unlink($datoteka);
            $stevec++;
        }
    }
    
    return $stevec;
}

function dnevnik_statistika(): array
{
    $pot = POT_LOG . '/';
    
    $datoteke = glob($pot . '*.log*');
    $velikost = 0;
    foreach ($datoteke as $datoteka) {
        $velikost += filesize($datoteka);
    }
    
    return [
        'stevilo_datotek' => count($datoteke),
        'velikost_bajtov' => $velikost,
        'velikost_mb' => round($velikost / 1024 / 1024, 2),
        'zadnja_zahteva' => $GLOBALS['DNEVNIK_ZADNJA_ZAHTEVA']
    ];
}

// ============================================================
// 3. IZVEDBA
// ============================================================
if (PHP_SAPI !== 'cli' && !defined('PHPUNIT_RUNNING')) {
    dnevnik_zabelezi_zahtevo();
}