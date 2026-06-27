<?php
/**
 * ============================================================
 * POT: ADAPTER/middleware/csrf.php
 * 📅 VERZIJA: v115 (14.6.2026 18:00)
 * ============================================================
 *
 * 🏛️ NIVO: ADAPTER
 *
 * 📰 NAMEN:
 *     CSRF zaščita pred napadi.
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - csrf_preveri(?string $token = null): bool
 *     - csrf_generiraj(bool $regeneriraj = false): string
 *     - csrf_pocisti(): void
 *     - csrf_hidden_input(): string
 *     - csrf_meta_tag(): string
 *     - csrf_metoda_zahteva_zascito(string $metoda): bool
 *
 * 📡 ODVISNOSTI:
 *     - (nobene – uporablja samo $_SESSION)
 *
 * ⚡ UPORABA:
 *     - Kliče se iz ADAPTER/adapter.php za nevarne metode
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
 *     adapter, middleware, csrf
 * ============================================================
 */
declare(strict_types=1);

defined('SISTEM_VARNOST') or die('Direkten dostop ni dovoljen.');

// ============================================================
// 1. NASTAVITVE
// ============================================================
$GLOBALS['CSRF_NASTAVITVE'] = [
    'dovoljene_metode' => ['POST', 'PUT', 'PATCH', 'DELETE'],
    'token_dolzina' => 32,
    'token_ime' => 'csrf_token',
    'header_ime' => 'X-CSRF-Token'
];

// ============================================================
// 2. POMOŽNE FUNKCIJE
// ============================================================

function csrf_metoda_zahteva_zascito(string $metoda): bool
{
    return in_array(strtoupper($metoda), $GLOBALS['CSRF_NASTAVITVE']['dovoljene_metode']);
}

function csrf_pridobi_token_iz_zahteve(): ?string
{
    $nastavitve = $GLOBALS['CSRF_NASTAVITVE'];
    $tokenIme = $nastavitve['token_ime'];
    $headerIme = $nastavitve['header_ime'];
    
    if (isset($_POST[$tokenIme])) {
        return $_POST[$tokenIme];
    }
    
    if (isset($_GET[$tokenIme])) {
        return $_GET[$tokenIme];
    }
    
    $input = file_get_contents('php://input');
    if ($input) {
        $data = json_decode($input, true);
        if (is_array($data) && isset($data[$tokenIme])) {
            return $data[$tokenIme];
        }
    }
    
    $headers = function_exists('getallheaders') ? getallheaders() : [];
    
    if (isset($headers[$headerIme])) {
        return $headers[$headerIme];
    }
    
    if (isset($_SERVER['HTTP_' . str_replace('-', '_', strtoupper($headerIme))])) {
        return $_SERVER['HTTP_' . str_replace('-', '_', strtoupper($headerIme))];
    }
    
    return null;
}

function csrf_preveri(?string $token = null): bool
{
    $metoda = $_SERVER['REQUEST_METHOD'] ?? 'GET';
    if (!csrf_metoda_zahteva_zascito($metoda)) {
        return true;
    }
    
    if (session_status() !== PHP_SESSION_ACTIVE) {
        return true;
    }
    
    if ($token === null) {
        $token = csrf_pridobi_token_iz_zahteve();
    }
    
    $sessionToken = $_SESSION['csrf_token'] ?? null;
    
    if (empty($token) || empty($sessionToken)) {
        return false;
    }
    
    return hash_equals($sessionToken, $token);
}

function csrf_generiraj(bool $regeneriraj = false): string
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        if (function_exists('seja_zacni')) {
            seja_zacni();
        } else {
            session_start();
        }
    }
    
    if ($regeneriraj || !isset($_SESSION['csrf_token'])) {
        $dolzina = $GLOBALS['CSRF_NASTAVITVE']['token_dolzina'];
        $_SESSION['csrf_token'] = bin2hex(random_bytes($dolzina));
    }
    
    return $_SESSION['csrf_token'];
}

function csrf_pocisti(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        unset($_SESSION['csrf_token']);
    }
}

function csrf_hidden_input(): string
{
    $token = csrf_generiraj();
    $ime = $GLOBALS['CSRF_NASTAVITVE']['token_ime'];
    return '<input type="hidden" name="' . htmlspecialchars($ime) . '" value="' . htmlspecialchars($token) . '">';
}

function csrf_meta_tag(): string
{
    $token = csrf_generiraj();
    $ime = $GLOBALS['CSRF_NASTAVITVE']['header_ime'];
    return '<meta name="csrf-token" content="' . htmlspecialchars($token) . '">';
}

// Alias za združljivost
function adapter_middleware_csrf_preveri(): bool
{
    return csrf_preveri();
}

function adapter_middleware_csrf_generiraj(): string
{
    return csrf_generiraj();
}