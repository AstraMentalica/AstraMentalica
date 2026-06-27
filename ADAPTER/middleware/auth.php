<?php
/**
 * ============================================================
 * POT: ADAPTER/middleware/auth.php
 * 📅 VERZIJA: v115 (14.6.2026 18:00)
 * ============================================================
 *
 * 🏛️ NIVO: ADAPTER
 *
 * 📰 NAMEN:
 *     Avtentikacija (JWT, API ključi, seja).
 *     Preverja ali je uporabnik overjen.
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - adapter_middleware_auth(string $kanal, string $pot, string $akcija): ?array
 *     - adapter_middleware_zahtevaj_auth(string $kanal, string $pot, string $akcija): ?array
 *     - adapter_middleware_pridobi_token(): ?string
 *     - adapter_middleware_preveri_jwt(string $token): ?array
 *     - adapter_middleware_preveri_api_kljuc(?string $apiKljuc): bool
 *     - adapter_middleware_preveri_sejo(): ?array
 *     - adapter_middleware_je_javna_pot(string $pot, string $akcija): bool
 *     - adapter_middleware_dodaj_javno_pot(string $pot): void
 *     - adapter_middleware_dodaj_javno_akcijo(string $akcija): void
 *
 * 📡 ODVISNOSTI:
 *     - (nobene – uporablja samo superglobale)
 *
 * 🤝 SOODVISNOSTI:
 *     - SISTEM/kernel/jedro/04_seja.php (če obstaja)
 *
 * ⚡ UPORABA:
 *     - Kliče se iz ADAPTER/adapter.php pred obdelavo zahteve
 *
 * 🚫 PREPOVEDI:
 *     - Brez business logike
 *     - Brez echo, print_r, var_dump (razen exit pri napaki)
 *     - Brez direktnih poti (uporabi konstante!)
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
 *     adapter, middleware, auth
 * ============================================================
 */
declare(strict_types=1);

defined('SISTEM_VARNOST') or die('Direkten dostop ni dovoljen.');

// ============================================================
// 1. NASTAVITVE
// ============================================================
$GLOBALS['AUTH_NASTAVITVE'] = [
    'jwt_required' => false,
    'api_key_required' => false,
    'session_required' => false,
    'public_paths' => [],
    'public_actions' => [
        'ping', 'health', 'prijava', 'registracija', 'pozabljeno_geslo'
    ]
];

// ============================================================
// 2. POMOŽNE FUNKCIJE
// ============================================================

function adapter_middleware_pridobi_token(): ?string
{
    $headers = function_exists('getallheaders') ? getallheaders() : [];
    
    if (isset($headers['Authorization'])) {
        $auth = $headers['Authorization'];
        if (preg_match('/Bearer\s+(.*)$/i', $auth, $matches)) {
            return $matches[1];
        }
    }
    
    if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
        $auth = $_SERVER['HTTP_AUTHORIZATION'];
        if (preg_match('/Bearer\s+(.*)$/i', $auth, $matches)) {
            return $matches[1];
        }
    }
    
    return $_GET['token'] ?? null;
}

function adapter_middleware_preveri_jwt(string $token): ?array
{
    if (function_exists('jwt_preveri')) {
        return jwt_preveri($token);
    }
    
    if (defined('RAZVOJNI_NACIN') && RAZVOJNI_NACIN) {
        if ($token === 'demo_token_123') {
            return [
                'uporabnik_id' => 1,
                'email' => 'demo@test.com',
                'vloga' => VLOGA_ADMIN,
                'ime' => 'Demo User'
            ];
        }
    }
    
    return null;
}

function adapter_middleware_preveri_api_kljuc(?string $apiKljuc): bool
{
    if (empty($apiKljuc)) {
        return false;
    }
    
    $envKljuc = getenv('API_KEY');
    if ($envKljuc && $apiKljuc === $envKljuc) {
        return true;
    }
    
    if (defined('API_KEY') && $apiKljuc === API_KEY) {
        return true;
    }
    
    return false;
}

function adapter_middleware_preveri_sejo(): ?array
{
    if (!function_exists('seja_je_prijavljen')) {
        return null;
    }
    
    if (!seja_je_prijavljen()) {
        return null;
    }
    
    $uporabnik = seja_pridobi_uporabnika();
    if ($uporabnik && isset($uporabnik['id'])) {
        return [
            'uporabnik_id' => $uporabnik['id'],
            'email' => $uporabnik['elektronski_naslov'] ?? '',
            'vloga' => $uporabnik['vloga'] ?? VLOGA_GOST,
            'ime' => $uporabnik['ime'] ?? '',
            'tip' => 'session'
        ];
    }
    
    return null;
}

function adapter_middleware_je_javna_pot(string $pot, string $akcija): bool
{
    $nastavitve = $GLOBALS['AUTH_NASTAVITVE'];
    
    if (in_array($akcija, $nastavitve['public_actions'])) {
        return true;
    }
    
    foreach ($nastavitve['public_paths'] as $javnaPot) {
        if (strpos($pot, $javnaPot) === 0) {
            return true;
        }
    }
    
    return false;
}

function adapter_middleware_auth(string $kanal = 'web', string $pot = '', string $akcija = ''): ?array
{
    $nastavitve = $GLOBALS['AUTH_NASTAVITVE'];
    
    if (adapter_middleware_je_javna_pot($pot, $akcija)) {
        return null;
    }
    
    switch ($kanal) {
        case 'api':
        case 'ai':
            $token = adapter_middleware_pridobi_token();
            if ($token) {
                $uporabnik = adapter_middleware_preveri_jwt($token);
                if ($uporabnik) {
                    return $uporabnik;
                }
            }
            
            if ($nastavitve['api_key_required'] || defined('API_KEY_REQUIRED')) {
                $apiKljuc = $_SERVER['HTTP_X_API_KEY'] ?? $_GET['api_key'] ?? null;
                if (adapter_middleware_preveri_api_kljuc($apiKljuc)) {
                    return ['tip' => 'api', 'vloga' => VLOGA_ADMIN];
                }
            }
            break;
            
        case 'web':
        default:
            if ($nastavitve['session_required']) {
                $uporabnik = adapter_middleware_preveri_sejo();
                if ($uporabnik) {
                    return $uporabnik;
                }
            }
            break;
    }
    
    if ($nastavitve['jwt_required'] || $nastavitve['api_key_required'] || $nastavitve['session_required']) {
        return false;
    }
    
    return null;
}

function adapter_middleware_zahtevaj_auth(string $kanal = 'web', string $pot = '', string $akcija = ''): ?array
{
    $uporabnik = adapter_middleware_auth($kanal, $pot, $akcija);
    
    if ($uporabnik === false) {
        http_response_code(401);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'status' => 'error',
            'status_koda' => 401,
            'sporocilo' => 'Avtentikacija zahtevana. Prosimo, prijavite se.',
            'napake' => ['Authentication required']
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    return $uporabnik;
}

function adapter_middleware_dodaj_javno_pot(string $pot): void
{
    if (!in_array($pot, $GLOBALS['AUTH_NASTAVITVE']['public_paths'])) {
        $GLOBALS['AUTH_NASTAVITVE']['public_paths'][] = $pot;
    }
}

function adapter_middleware_dodaj_javno_akcijo(string $akcija): void
{
    if (!in_array($akcija, $GLOBALS['AUTH_NASTAVITVE']['public_actions'])) {
        $GLOBALS['AUTH_NASTAVITVE']['public_actions'][] = $akcija;
    }
}