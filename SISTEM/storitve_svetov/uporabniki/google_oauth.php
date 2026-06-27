<?php
/**
 * ============================================================
 * POT: SISTEM/storitve_svetov/uporabniki/google_oauth.php
 * 📅 VERZIJA: v115 (14.6.2026 18:00)
 * ============================================================
 *
 * 🏛️ NIVO: STORITEV (BUSINESS LAYER)
 *
 * 📰 NAMEN:
 *     Google OAuth 2.0 – pridobi URL, obdelaj povratek.
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - google_oauth_je_konfiguriran(): bool
 *     - google_oauth_prijavni_url(): ?string
 *     - google_oauth_obdelaj_povratek(string $koda, string $stanje): array
 *
 * 📡 ODVISNOSTI:
 *     - SISTEM/kernel/jedro/04_seja.php
 *     - SISTEM/kernel/jedro/05_pravice.php
 *     - SISTEM/storitve_svetov/uporabniki/uporabnik_registracija.php
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
 *     storitev, uporabniki, google, oauth
 * ============================================================
 */
declare(strict_types=1);

defined('SISTEM_VARNOST') or die('Direkten dostop ni dovoljen.');

function google_oauth_je_konfiguriran(): bool
{
    return !empty(getenv('GOOGLE_CLIENT_ID')) && !empty(getenv('GOOGLE_CLIENT_SECRET'));
}

function google_oauth_prijavni_url(): ?string
{
    if (!google_oauth_je_konfiguriran()) {
        return null;
    }
    
    $stanje = bin2hex(random_bytes(16));
    seja_nastavi('google_oauth_stanje', $stanje);
    
    $params = [
        'client_id' => getenv('GOOGLE_CLIENT_ID'),
        'redirect_uri' => getenv('GOOGLE_REDIRECT_URI'),
        'response_type' => 'code',
        'scope' => 'email profile',
        'access_type' => 'online',
        'prompt' => 'select_account',
        'state' => $stanje
    ];
    
    return 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($params);
}

function google_oauth_obdelaj_povratek(string $koda, string $stanje): array
{
    // Preveri CSRF state
    $shranjeniStanje = seja_pridobi('google_oauth_stanje');
    seja_unset('google_oauth_stanje');
    
    if (empty($shranjeniStanje) || !hash_equals($shranjeniStanje, $stanje)) {
        return [
            'status' => 'napaka',
            'status_koda' => 403,
            'sporocilo' => 'Neveljaven varnostni žeton.'
        ];
    }
    
    // Zamenjaj kodo za access token
    $token = _google_oauth_pridobi_token($koda);
    if (!$token || empty($token['access_token'])) {
        return [
            'status' => 'napaka',
            'status_koda' => 502,
            'sporocilo' => 'Napaka pri pridobivanju Google žetona.'
        ];
    }
    
    // Pridobi podatke o uporabniku
    $googleUporabnik = _google_oauth_pridobi_uporabnika($token['access_token']);
    if (!$googleUporabnik || empty($googleUporabnik['email'])) {
        return [
            'status' => 'napaka',
            'status_koda' => 502,
            'sporocilo' => 'Napaka pri pridobivanju Google podatkov.'
        ];
    }
    
    // Registriraj ali prijavi
    return uporabniki_registriraj_google([
        'email' => $googleUporabnik['email'],
        'ime' => $googleUporabnik['name'] ?? '',
        'google_id' => $googleUporabnik['id'] ?? '',
        'slika' => $googleUporabnik['picture'] ?? null
    ]);
}

function _google_oauth_pridobi_token(string $koda): ?array
{
    $podatki = [
        'code' => $koda,
        'client_id' => getenv('GOOGLE_CLIENT_ID'),
        'client_secret' => getenv('GOOGLE_CLIENT_SECRET'),
        'redirect_uri' => getenv('GOOGLE_REDIRECT_URI'),
        'grant_type' => 'authorization_code'
    ];
    
    $ch = curl_init('https://oauth2.googleapis.com/token');
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => http_build_query($podatki),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 15,
        CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded']
    ]);
    $odgovor = curl_exec($ch);
    $httpKoda = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return ($httpKoda === 200) ? json_decode($odgovor, true) : null;
}

function _google_oauth_pridobi_uporabnika(string $accessToken): ?array
{
    $ch = curl_init('https://www.googleapis.com/oauth2/v2/userinfo');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 15,
        CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . $accessToken]
    ]);
    $odgovor = curl_exec($ch);
    $httpKoda = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return ($httpKoda === 200) ? json_decode($odgovor, true) : null;
}