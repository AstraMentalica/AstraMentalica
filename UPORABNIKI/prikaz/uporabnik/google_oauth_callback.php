<?php
/**
 * ---------------------------------------------------------
 * POT: UPORABNIKI/prikaz/uporabnik/google_oauth_callback.php
 * VERZIJA: 1.0.0 (24.6.2026)
 * ---------------------------------------------------------
 * OPIS: Google OAuth callback obdelava
 * ---------------------------------------------------------
 */

declare(strict_types=1);

// Preveri ali je že prijavljen
if (seja_je_prijavljen()) {
    header('Location: ?svet=UPORABNIKI&pot=profil');
    exit;
}

// Preveri ali je prisoten authorization code
if (!isset($_GET['code'])) {
    header('Location: ?svet=UPORABNIKI&pot=prijava&napaka=google_oauth');
    exit;
}

$code = $_GET['code'];
$googleClientId = getenv('GOOGLE_CLIENT_ID');
$googleClientSecret = getenv('GOOGLE_CLIENT_SECRET');
$googleRedirectUri = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

if (!$googleClientId || !$googleClientSecret) {
    // Google OAuth ni konfiguriran
    header('Location: ?svet=UPORABNIKI&pot=prijava&napaka=google_ni_konfiguriran');
    exit;
}

// Izmenjaj authorization code za access token
$tokenUrl = 'https://oauth2.googleapis.com/token';
$tokenData = [
    'code' => $code,
    'client_id' => $googleClientId,
    'client_secret' => $googleClientSecret,
    'redirect_uri' => $googleRedirectUri,
    'grant_type' => 'authorization_code'
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $tokenUrl);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($tokenData));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/x-www-form-urlencoded'
]);

$tokenResponse = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 200 || !$tokenResponse) {
    header('Location: ?svet=UPORABNIKI&pot=prijava&napaka=google_token');
    exit;
}

$tokenData = json_decode($tokenResponse, true);
if (!isset($tokenData['access_token'])) {
    header('Location: ?svet=UPORABNIKI&pot=prijava&napaka=google_token');
    exit;
}

$accessToken = $tokenData['access_token'];

// Pridobi podatke o uporabniku iz Google
$userInfoUrl = 'https://www.googleapis.com/oauth2/v2/userinfo';
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $userInfoUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $accessToken
]);

$userInfoResponse = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 200 || !$userInfoResponse) {
    header('Location: ?svet=UPORABNIKI&pot=prijava&napaka=google_userinfo');
    exit;
}

$googleUser = json_decode($userInfoResponse, true);
if (!isset($googleUser['email']) || !isset($googleUser['id'])) {
    header('Location: ?svet=UPORABNIKI&pot=prijava&napaka=google_userinfo');
    exit;
}

// Pridobi podatke o uporabniku
$googleId = $googleUser['id'];
$email = $googleUser['email'];
$ime = $googleUser['name'] ?? $googleUser['given_name'] ?? 'Uporabnik';
$slika = $googleUser['picture'] ?? null;

// Preveri ali uporabnik že obstaja
$uporabniki = baza_beri('uporabniki');
$najden = null;

foreach ($uporabniki as $u) {
    // Preveri po Google ID ali email
    if (($u['google_id'] ?? null) === $googleId || $u['elektronski_naslov'] === $email) {
        $najden = $u;
        break;
    }
}

if ($najden) {
    // Uporabnik že obstaja - posodobi Google podatke če je potrebno
    $posodobitve = [
        'google_id' => $googleId,
        'google_slika' => $slika,
        'google_oauth' => true
    ];
    
    // Če je uporabnik prijavljen preko Google-a prvič, posodobi ime
    if (empty($najden['google_id'])) {
        $posodobitve['ime'] = $ime;
    }
    
    baza_posodobi('uporabniki', $najden['id'], $posodobitve);
    
    // Prijavi uporabnika
    $_SESSION['uporabnik_id'] = $najden['id'];
    $_SESSION['uporabnik_ime'] = $najden['ime'];
    $_SESSION['uporabnik_email'] = $najden['elektronski_naslov'];
    $_SESSION['uporabnik_vloga'] = $najden['vloga'];
    
    dogodek_sprozi('uporabnik.prijavljen_google', [
        'uporabnik_id' => $najden['id'],
        'email' => $email
    ]);
    
    header('Location: ?svet=UPORABNIKI&pot=profil');
    exit;
} else {
    // Ustvari novega uporabnika
    $noviUporabnik = [
        'id' => uniqid('usr_', true),
        'ime' => $ime,
        'elektronski_naslov' => $email,
        'hash_gesla' => password_hash(uniqid('google_', true), PASSWORD_BCRYPT), // Naključno geslo
        'aktiviran' => true,
        'vloga' => 10, // Osnovna uporabniška vloga
        'ustvarjen' => time(),
        'google_id' => $googleId,
        'google_slika' => $slika,
        'google_oauth' => true
    ];
    
    baza_zapisi('uporabniki', $noviUporabnik);
    
    // Avtomatska prijava
    $_SESSION['uporabnik_id'] = $noviUporabnik['id'];
    $_SESSION['uporabnik_ime'] = $noviUporabnik['ime'];
    $_SESSION['uporabnik_email'] = $noviUporabnik['elektronski_naslov'];
    $_SESSION['uporabnik_vloga'] = $noviUporabnik['vloga'];
    
    dogodek_sprozi('uporabnik.registriran_google', [
        'uporabnik_id' => $noviUporabnik['id'],
        'email' => $email
    ]);
    
    header('Location: ?svet=UPORABNIKI&pot=nastavitve');
    exit;
}