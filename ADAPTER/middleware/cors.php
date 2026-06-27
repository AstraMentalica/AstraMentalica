<?php
/**
 * ============================================================
 * POT: ADAPTER/middleware/cors.php
 * 📅 VERZIJA: v115 (14.6.2026 18:00)
 * ============================================================
 *
 * 🏛️ NIVO: ADAPTER
 *
 * 📰 NAMEN:
 *     CORS – deljenje virov med domenami.
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - cors_nastavi_glave(): void
 *     - cors_preveri_metodo(string $metoda): bool
 *     - cors_dovoljen_izvor(string $izvor): bool
 *     - cors_dodaj_domeno(string $domena): void
 *     - cors_odstrani_domeno(string $domena): void
 *
 * 📡 ODVISNOSTI:
 *     - pot.php (RAZVOJNI_NACIN)
 *
 * ⚡ UPORABA:
 *     - Kliče se iz ADAPTER/adapter.php (prvi middleware)
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
 *     adapter, middleware, cors
 * ============================================================
 */
declare(strict_types=1);

defined('SISTEM_VARNOST') or die('Direkten dostop ni dovoljen.');

// ============================================================
// 1. NASTAVITVE
// ============================================================
$GLOBALS['CORS_NASTAVITVE'] = [
    'dovoljene_izvore' => [],
    'dovoljene_metode' => ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
    'dovoljene_glave' => ['Content-Type', 'Authorization', 'X-API-Key', 'X-Requested-With', 'X-CSRF-Token'],
    'izpostavljene_glave' => ['X-API-Version', 'X-Rate-Limit'],
    'max_starost' => 86400,
    'dovoli_credentiale' => true
];

// ============================================================
// 2. POMOŽNE FUNKCIJE
// ============================================================

function cors_pridobi_dovoljene_izvore(): array
{
    $dovoljeni = $GLOBALS['CORS_NASTAVITVE']['dovoljene_izvore'];
    
    if (empty($dovoljeni) && defined('RAZVOJNI_NACIN') && RAZVOJNI_NACIN) {
        return ['*'];
    }
    
    return $dovoljeni;
}

function cors_dovoljen_izvor(string $izvor): bool
{
    $dovoljeni = cors_pridobi_dovoljene_izvore();
    
    if (in_array('*', $dovoljeni)) {
        return true;
    }
    
    return in_array($izvor, $dovoljeni);
}

function cors_nastavi_glave(): void
{
    if (headers_sent()) {
        return;
    }
    
    $nastavitve = $GLOBALS['CORS_NASTAVITVE'];
    $izvor = $_SERVER['HTTP_ORIGIN'] ?? '';
    
    if (cors_dovoljen_izvor($izvor)) {
        $allowedOrigin = (in_array('*', cors_pridobi_dovoljene_izvore())) ? '*' : $izvor;
        header('Access-Control-Allow-Origin: ' . $allowedOrigin);
        
        if ($nastavitve['dovoli_credentiale']) {
            header('Access-Control-Allow-Credentials: true');
        }
        
        header('Access-Control-Allow-Methods: ' . implode(', ', $nastavitve['dovoljene_metode']));
        header('Access-Control-Allow-Headers: ' . implode(', ', $nastavitve['dovoljene_glave']));
        
        if (!empty($nastavitve['izpostavljene_glave'])) {
            header('Access-Control-Expose-Headers: ' . implode(', ', $nastavitve['izpostavljene_glave']));
        }
        
        header('Access-Control-Max-Age: ' . $nastavitve['max_starost']);
    }
}

function cors_preveri_metodo(string $metoda): bool
{
    $dovoljene = $GLOBALS['CORS_NASTAVITVE']['dovoljene_metode'];
    return in_array(strtoupper($metoda), $dovoljene);
}

function cors_dodaj_domeno(string $domena): void
{
    $dovoljeni = &$GLOBALS['CORS_NASTAVITVE']['dovoljene_izvore'];
    if (!in_array($domena, $dovoljeni)) {
        $dovoljeni[] = $domena;
    }
}

function cors_odstrani_domeno(string $domena): void
{
    $dovoljeni = &$GLOBALS['CORS_NASTAVITVE']['dovoljene_izvore'];
    $key = array_search($domena, $dovoljeni);
    if ($key !== false) {
        unset($dovoljeni[$key]);
        $dovoljeni = array_values($dovoljeni);
    }
}

// ============================================================
// 3. IZVEDBA
// ============================================================
$metoda = $_SERVER['REQUEST_METHOD'] ?? '';
if (!cors_preveri_metodo($metoda)) {
    http_response_code(405);
    header('Allow: ' . implode(', ', $GLOBALS['CORS_NASTAVITVE']['dovoljene_metode']));
    echo json_encode([
        'status' => 'error',
        'status_koda' => 405,
        'sporocilo' => 'Metoda ' . $metoda . ' ni dovoljena.',
        'dovoljene_metode' => $GLOBALS['CORS_NASTAVITVE']['dovoljene_metode']
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

if ($metoda === 'OPTIONS') {
    cors_nastavi_glave();
    http_response_code(204);
    exit;
}

cors_nastavi_glave();