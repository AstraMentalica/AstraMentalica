<?php
/**
 * ============================================================
 * POT: ADAPTER/zahteve/adapter_normalizacija.php
 * 📅 VERZIJA: v114 (9.6.2026 18:00)
 * ============================================================
 *
 * 🏛️ NIVO: ADAPTER
 *
 * 📰 NAMEN:
 *     Normalizacija surove zahteve v standardni format.
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - adapter_normaliziraj_iz_superglobalov(): AdapterZahteva
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
 *     adapter, zahteve, normalizacija
 * ============================================================
 */
declare(strict_types=1);

function adapter_normaliziraj_iz_superglobalov(): AdapterZahteva
{
    $pot = $_GET['svet'] ?? 'GLOBALNO';
    $metoda = $_SERVER['REQUEST_METHOD'] ?? 'DOBI';
    $parametri = [];
    
    // Parametri iz GET (razen 'svet')
    foreach ($_GET as $kljuc => $vrednost) {
        if ($kljuc !== 'svet') {
            $parametri[$kljuc] = $vrednost;
        }
    }
    
    // Vsebina iz POST ali JSON
    $vsebina = null;
    if ($metoda === 'OBJAVA' || $metoda === 'POSODOBI' || $metoda === 'BRISI') {
        $vhod = file_get_contents('php://input');
        if ($vhod) {
            $json = json_decode($vhod, true);
            if (is_array($json)) {
                $vsebina = $json;
            } else {
                // Ni JSON, uporabi POST
                if (!empty($_POST)) {
                    $parametri = array_merge($parametri, $_POST);
                }
            }
        } elseif (!empty($_POST)) {
            $parametri = array_merge($parametri, $_POST);
        }
    }
    
    // Glave
    $glave = [];
    foreach ($_SERVER as $kljuc => $vrednost) {
        if (strpos($kljuc, 'HTTP_') === 0) {
            $imeGlave = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($kljuc, 5)))));
            $glave[$imeGlave] = $vrednost;
        }
    }
    
    // IP naslov
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
    } elseif (!empty($_SERVER['HTTP_X_REAL_IP'])) {
        $ip = $_SERVER['HTTP_X_REAL_IP'];
    }
    
    // Določi kanal
    $kanal = $GLOBALS['ADAPTER_FORCIRANI_KANAL'] ?? null;
    if ($kanal === null) {
        $skripta = $_SERVER['SCRIPT_NAME'] ?? '';
        if (strpos($skripta, 'api.php') !== false) {
            $kanal = 'api';
        } elseif (strpos($skripta, 'ai.php') !== false) {
            $kanal = 'ai';
        } elseif (strpos($skripta, 'telegram.php') !== false) {
            $kanal = 'telegram';
        } elseif (strpos($skripta, 'facebook.php') !== false) {
            $kanal = 'facebook';
        } elseif (PHP_SAPI === 'cli') {
            $kanal = 'cli';
        } else {
            $kanal = 'splet';
        }
    }
    
    $zahteva = new AdapterZahteva([
        'pot' => $pot,
        'metoda' => $metoda,
        'parametri' => $parametri,
        'vsebina' => $vsebina,
        'glave' => $glave,
        'ip' => $ip,
        'kanal' => $kanal
    ]);
    
    return $zahteva;
}