<?php
/**
 * ---------------------------------------------------------
 * POT: SISTEM/storitve_svetov/uporabniki/uporabnik_google_oauth.php
 * v111 (02.06.2026)
 * ---------------------------------------------------------
 * OPIS: Google OAuth 2.0 prijava in registracija
 * ---------------------------------------------------------
 */
declare(strict_types=1);

class GoogleOAuth
{
    private string $clientId;
    private string $clientSecret;
    private string $redirectUri;
    private string $authorizeUrl = 'https://accounts.google.com/o/oauth2/v2/auth';
    private string $tokenUrl = 'https://oauth2.googleapis.com/token';
    private string $userInfoUrl = 'https://www.googleapis.com/oauth2/v2/userinfo';

    public function __construct()
    {
        $this->clientId = getenv('GOOGLE_CLIENT_ID') ?: '';
        $this->clientSecret = getenv('GOOGLE_CLIENT_SECRET') ?: '';
        $this->redirectUri = getenv('GOOGLE_REDIRECT_URI') ?: '';
    }

    public function jeKonfiguriran(): bool
    {
        return !empty($this->clientId) && !empty($this->clientSecret);
    }

    public function pridobiPrijavnoPovezavo(string $stanje = ''): string
    {
        $params = [
            'client_id' => $this->clientId,
            'redirect_uri' => $this->redirectUri,
            'response_type' => 'code',
            'scope' => 'email profile',
            'access_type' => 'online',
            'prompt' => 'select_account'
        ];

        if (!empty($stanje)) {
            $params['state'] = $stanje;
        }

        return $this->authorizeUrl . '?' . http_build_query($params);
    }

    public function obdelajPovratek(string $koda): ?array
    {
        // Zamenjaj kodo za access token
        $tokenPodatki = $this->pridobiAccessToken($koda);
        if (!$tokenPodatki || empty($tokenPodatki['access_token'])) {
            return null;
        }

        // Pridobi podatke o uporabniku
        $uporabnik = $this->pridobiUporabnika($tokenPodatki['access_token']);
        if (!$uporabnik || empty($uporabnik['email'])) {
            return null;
        }

        return [
            'email' => $uporabnik['email'],
            'ime' => $uporabnik['name'] ?? explode('@', $uporabnik['email'])[0],
            'google_id' => $uporabnik['id'] ?? null,
            'slika' => $uporabnik['picture'] ?? null,
            'preverjen_email' => $uporabnik['verified_email'] ?? false
        ];
    }

    private function pridobiAccessToken(string $koda): ?array
    {
        $podatki = [
            'code' => $koda,
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'redirect_uri' => $this->redirectUri,
            'grant_type' => 'authorization_code'
        ];

        $ch = curl_init($this->tokenUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($podatki));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $odgovor = curl_exec($ch);
        $httpKoda = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpKoda !== 200) {
            return null;
        }

        return json_decode($odgovor, true);
    }

    private function pridobiUporabnika(string $accessToken): ?array
    {
        $ch = curl_init($this->userInfoUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $accessToken
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $odgovor = curl_exec($ch);
        $httpKoda = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpKoda !== 200) {
            return null;
        }

        return json_decode($odgovor, true);
    }
}

// Globalna funkcija za enostaven dostop
function google_oauth(): GoogleOAuth
{
    return new GoogleOAuth();
}