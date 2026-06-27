<?php
/**
 * ============================================================
 * POT: SISTEM/storitve_svetov/ai/ai_storitev.php
 * 📅 VERZIJA: v118 (19.6.2026)
 * ============================================================
 *
 * 🏛️ NIVO: STORITEV (BUSINESS LAYER)
 *
 * 📰 NAMEN:
 *     Centralna AI storitev – DeepSeek API z zavest
 *     sistem za varuhe, avatarje in duhe.
 *     Bere API ključ iz PODATKI/sef/.env_api.
 *     Hrani zgodovino sej v PODATKI/ai/.
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - ai_posli_sporocilo(array $zahteva): array
 *     - ai_pridobi_arhetip(string $id): ?array
 *     - ai_pridobi_sejo(string $tip, string $id1, string $id2): array
 *     - ai_shrani_sejo(string $tip, string $id1, string $id2, array $seja): bool
 *     - ai_nalagalec_env(): array
 *
 * 📡 ODVISNOSTI:
 *     - pot.php (POT_PODATKI)
 *     - PODATKI/sef/.env_api
 *     - PODATKI/ai/arhetipi/*.json
 *
 * 🚫 PREPOVEDI:
 *     - Brez __DIR__
 *     - Brez echo, print_r, var_dump
 *     - Brez die(), exit()
 *     - Brez hardcoded API ključev
 *
 * 📌 STATUS:
 *     Stabilno
 *
 * 📅 ZGODOVINA:
 *     - v118: nova storitev – DeepSeek + zavest sistem
 *
 * 👤 AVTOR:
 *     AstraMentalica Mojster
 *
 * 🌐 JEZIK:
 *     sl
 *
 * 🏷️ OZNAKE:
 *     storitev, ai, deepseek, zavest, varuhi
 * ============================================================
 */
declare(strict_types=1);

if (!defined('SISTEM_VARNOST')) {
    http_response_code(403);
    header('Location: /');
    return;
}

// ============================================================
// 1. NALAGANJE .env_api
// ============================================================

function ai_nalagalec_env(): array
{
    static $env = null;
    if ($env !== null) {
        return $env;
    }

    $pot = POT_PODATKI . '/sef/.env_api';
    $env = [];

    if (!file_exists($pot)) {
        error_log('[AI] .env_api ne obstaja: ' . $pot);
        return $env;
    }

    foreach (file($pot, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $vrstica) {
        $vrstica = trim($vrstica);
        if ($vrstica === '' || str_starts_with($vrstica, '#')) {
            continue;
        }
        if (!str_contains($vrstica, '=')) {
            continue;
        }
        [$kljuc, $vrednost] = explode('=', $vrstica, 2);
        $env[trim($kljuc)] = trim($vrednost);
    }

    return $env;
}

function ai_pridobi_kljuc(string $ime): string
{
    $env = ai_nalagalec_env();
    return $env[$ime] ?? getenv($ime) ?: '';
}

// ============================================================
// 2. ARHETIP SISTEM
// ============================================================

/**
 * Naloži arhetip iz PODATKI/ai/arhetipi/{id}.json
 */
function ai_pridobi_arhetip(string $id): ?array
{
    $pot = POT_PODATKI . '/ai/arhetipi/' . preg_replace('/[^a-z0-9_-]/', '', strtolower($id)) . '.json';

    if (!file_exists($pot)) {
        error_log('[AI] Arhetip ne obstaja: ' . $id);
        return null;
    }

    $vsebina = file_get_contents($pot);
    $podatki  = json_decode($vsebina, true);

    return is_array($podatki) ? $podatki : null;
}

/**
 * Sestavi sistemski prompt iz arhetipa
 */
function ai_sestavi_prompt(array $arhetip, array $kontekst = []): string
{
    $bazni = $arhetip['sistemski_prompt'] ?? $arhetip['zavest']['temelj'] ?? 'Si AI asistent v AstraMentalici.';

    $deli = [$bazni];

    // Dodaj modulski kontekst (za varuhe modulov)
    if (!empty($kontekst['modulski_kontekst'])) {
        $mk = $kontekst['modulski_kontekst'];
        if (!empty($mk['posebna_znanja'])) {
            $deli[] = 'Tvoja področja: ' . implode(', ', $mk['posebna_znanja']) . '.';
        }
        if (!empty($mk['sistemski_dodatek'])) {
            $deli[] = $mk['sistemski_dodatek'];
        }
    }

    // Dodaj info o uporabniku
    if (!empty($kontekst['uporabnik'])) {
        $u = $kontekst['uporabnik'];
        if (!empty($u['ime'])) {
            $nagovor = $arhetip['zavest']['nagovor'] ?? 'popotnik';
            $deli[] = "Sogovornik se imenuje {$u['ime']} ({$nagovor}).";
        }
        if (!empty($u['arhetip'])) {
            $deli[] = "Njihov arhetip je: {$u['arhetip']}.";
        }
        if (!empty($u['stopnja'])) {
            $deli[] = "Stopnja zavesti: {$u['stopnja']}.";
        }
    }

    // Otroški način
    if (!empty($kontekst['otrok_nacin'])) {
        $deli[] = 'POZOR: Sogovornik je otrok. Govori preprosto, prijazno, s pravljičnim jezikom. Brez kompleksnih konceptov.';
    }

    return implode(' ', $deli);
}

// ============================================================
// 3. SEJA SISTEM
// ============================================================

/**
 * Pot do seje glede na tip
 * tip: avatar | varuh_modul | varuh_uporabnik | duh_modul | duh_uporabnik
 */
function _ai_seja_pot(string $tip, string $id1, string $id2): string
{
    return match($tip) {
        'avatar'           => POT_PODATKI . '/ai/avatarji/' . $id1 . '/' . $id2 . '_seja.json',
        'varuh_modul'      => POT_PODATKI . '/ai/varuhi/' . $id1 . '/' . $id2 . '_modul.json',
        'varuh_uporabnik'  => POT_PODATKI . '/ai/varuhi/' . $id1 . '/' . $id2 . '_seja.json',
        'duh_modul'        => POT_PODATKI . '/ai/duhovi/' . $id1 . '/' . $id2 . '_modul.json',
        'duh_uporabnik'    => POT_PODATKI . '/ai/duhovi/' . $id1 . '/' . $id2 . '_seja.json',
        default            => POT_PODATKI . '/ai/seje/' . $id1 . '_' . $id2 . '.json',
    };
}

function ai_pridobi_sejo(string $tip, string $id1, string $id2): array
{
    $pot = _ai_seja_pot($tip, $id1, $id2);

    if (!file_exists($pot)) {
        return [
            'tip'              => $tip,
            'id1'              => $id1,
            'id2'              => $id2,
            'ustvarjena'       => time(),
            'zadnja_aktivnost' => time(),
            'skupaj_sporocil'  => 0,
            'xp_iz_pogovora'   => 0,
            'kontekst'         => [],
            'zgodovina'        => [],
        ];
    }

    $vsebina = file_get_contents($pot);
    $podatki  = json_decode($vsebina, true);

    return is_array($podatki) ? $podatki : [];
}

function ai_shrani_sejo(string $tip, string $id1, string $id2, array $seja): bool
{
    $pot  = _ai_seja_pot($tip, $id1, $id2);
    $mapa = dirname($pot);

    if (!is_dir($mapa)) {
        mkdir($mapa, 0755, true);
    }

    $seja['zadnja_aktivnost'] = time();

    return file_put_contents(
        $pot,
        json_encode($seja, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
        LOCK_EX
    ) !== false;
}

// ============================================================
// 4. DEEPSEEK API KLIC
// ============================================================

function _ai_deepseek_klic(array $sporocila, string $sistemski_prompt, array $opcije = []): array
{
    $api_kljuc = ai_pridobi_kljuc('DEEPSEEK_API_KEY');
    $model     = ai_pridobi_kljuc('DEEPSEEK_MODEL') ?: 'deepseek-chat';
    $url       = ai_pridobi_kljuc('DEEPSEEK_URL')   ?: 'https://api.deepseek.com/v1/chat/completions';

    if (empty($api_kljuc) || str_starts_with($api_kljuc, 'sk-your')) {
        return [
            'uspeh'    => false,
            'napaka'   => 'DEEPSEEK_API_KEY ni nastavljen v PODATKI/sef/.env_api',
            'odgovor'  => null,
        ];
    }

    // Sestavi API sporočila
    $api_sporocila = [['role' => 'system', 'content' => $sistemski_prompt]];
    foreach ($sporocila as $s) {
        $api_sporocila[] = [
            'role'    => $s['vloga'] === 'user' ? 'user' : 'assistant',
            'content' => $s['vsebina'],
        ];
    }

    $telo = json_encode([
        'model'       => $model,
        'messages'    => $api_sporocila,
        'temperature' => (float)($opcije['temperatura'] ?? ai_pridobi_kljuc('DEEPSEEK_TEMPERATURE') ?: 0.7),
        'max_tokens'  => (int)($opcije['max_tokenov'] ?? ai_pridobi_kljuc('DEEPSEEK_MAX_TOKENS') ?: 2000),
    ]);

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $telo,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 60,
        CURLOPT_HTTPHEADER     => [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $api_kljuc,
        ],
    ]);

    $odgovor_raw = curl_exec($ch);
    $http_koda   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_napaka = curl_error($ch);
    curl_close($ch);

    if ($curl_napaka) {
        error_log('[AI] cURL napaka: ' . $curl_napaka);
        return ['uspeh' => false, 'napaka' => 'Omrežna napaka: ' . $curl_napaka, 'odgovor' => null];
    }

    $podatki = json_decode($odgovor_raw, true);

    if ($http_koda !== 200) {
        $sporocilo = $podatki['error']['message'] ?? 'HTTP ' . $http_koda;
        error_log('[AI] DeepSeek napaka: ' . $sporocilo);
        return ['uspeh' => false, 'napaka' => $sporocilo, 'odgovor' => null];
    }

    $besedilo = $podatki['choices'][0]['message']['content'] ?? '';

    return [
        'uspeh'    => true,
        'odgovor'  => $besedilo,
        'tokeni'   => $podatki['usage'] ?? [],
        'model'    => $podatki['model'] ?? $model,
    ];
}

// ============================================================
// 5. GLAVNA FUNKCIJA – posli sporocilo
// ============================================================

/**
 * Pošlji sporočilo varuhu/avatarju/duhu
 *
 * @param array $zahteva [
 *   'arhetip_id'     => 'stellarion',        // kateri arhetip
 *   'tip_seje'       => 'avatar',             // avatar|varuh_modul|duh_modul itd.
 *   'seja_id1'       => 'usr_001',            // uporabnik ID ali modul
 *   'seja_id2'       => 'stellarion',         // varuh/duh ID
 *   'sporocilo'      => 'Zdravo!',            // novo sporočilo
 *   'uporabnik'      => [...],                // opcijsko – info o uporabniku
 *   'otrok_nacin'    => false,                // opcijsko
 *   'shrani_sejo'    => true,                 // opcijsko
 * ]
 */
function ai_posli_sporocilo(array $zahteva): array
{
    $arhetip_id = $zahteva['arhetip_id']  ?? 'stellarion';
    $tip_seje   = $zahteva['tip_seje']    ?? 'avatar';
    $seja_id1   = $zahteva['seja_id1']    ?? 'anonimen';
    $seja_id2   = $zahteva['seja_id2']    ?? $arhetip_id;
    $sporocilo  = trim($zahteva['sporocilo'] ?? '');
    $shrani     = $zahteva['shrani_sejo'] ?? true;

    if (empty($sporocilo)) {
        return ['status' => 'napaka', 'status_koda' => 400, 'sporocilo' => 'Sporočilo je prazno.'];
    }

    // Naloži arhetip
    $arhetip = ai_pridobi_arhetip($arhetip_id);
    if (!$arhetip) {
        // Fallback – splošni prompt
        $arhetip = [
            'id'              => $arhetip_id,
            'sistemski_prompt' => 'Si duhovni pomočnik v AstraMentalici. Govoriš SLOVENSKO, poetično, 2-3 stavki.',
        ];
    }

    // Naloži sejo (zgodovino)
    $seja = ai_pridobi_sejo($tip_seje, $seja_id1, $seja_id2);

    // Sestavi kontekst
    $kontekst = [
        'uporabnik'     => $zahteva['uporabnik']  ?? [],
        'otrok_nacin'   => $zahteva['otrok_nacin'] ?? false,
        'modulski_kontekst' => $seja['modulski_kontekst'] ?? [],
    ];

    // Sestavi sistemski prompt
    $sistemski_prompt = ai_sestavi_prompt($arhetip, $kontekst);

    // Zadnjih 10 sporočil iz zgodovine
    $zgodovina = array_slice($seja['zgodovina'] ?? [], -10);

    // Dodaj novo sporočilo
    $zgodovina[] = ['vloga' => 'user', 'vsebina' => $sporocilo];

    // Kliči DeepSeek
    $rezultat = _ai_deepseek_klic($zgodovina, $sistemski_prompt, [
        'temperatura'  => $arhetip['temperatura']  ?? 0.7,
        'max_tokenov'  => $arhetip['max_tokenov']  ?? 2000,
    ]);

    if (!$rezultat['uspeh']) {
        return [
            'status'      => 'napaka',
            'status_koda' => 502,
            'sporocilo'   => $rezultat['napaka'],
            'vsebina'     => [],
        ];
    }

    $odgovor = $rezultat['odgovor'];

    // Posodobi sejo
    $seja['skupaj_sporocil'] = ($seja['skupaj_sporocil'] ?? 0) + 1;
    $seja['xp_iz_pogovora']  = ($seja['xp_iz_pogovora']  ?? 0) + 15;

    $seja['zgodovina'][] = ['vloga' => 'assistant', 'vsebina' => $odgovor, 'cas' => time()];

    // Omeji zgodovino na 50 sporočil
    if (count($seja['zgodovina']) > 50) {
        $seja['zgodovina'] = array_slice($seja['zgodovina'], -50);
    }

    if ($shrani) {
        ai_shrani_sejo($tip_seje, $seja_id1, $seja_id2, $seja);
    }

    // XP bonus za arhetip
    $xp_bonus = $arhetip['xp_bonus'] ?? 15;

    return [
        'status'      => 'uspeh',
        'status_koda' => 200,
        'vsebina'     => [
            'odgovor'      => $odgovor,
            'arhetip'      => $arhetip_id,
            'arhetip_ime'  => $arhetip['ime'] ?? $arhetip_id,
            'xp'           => $xp_bonus,
            'skupaj_sej'   => $seja['skupaj_sporocil'],
            'model'        => $rezultat['model'] ?? 'deepseek-chat',
        ],
    ];
}

// ============================================================
// 6. POMOŽNE FUNKCIJE
// ============================================================

/**
 * Vrne seznam vseh razpoložljivih arhetipov
 */
function ai_seznam_arhetipov(): array
{
    $mapa     = POT_PODATKI . '/ai/arhetipi/';
    $seznam   = [];

    if (!is_dir($mapa)) {
        return [];
    }

    foreach (glob($mapa . '*.json') as $datoteka) {
        $podatki = json_decode(file_get_contents($datoteka), true);
        if (is_array($podatki)) {
            $seznam[] = [
                'id'    => $podatki['id']   ?? basename($datoteka, '.json'),
                'ime'   => $podatki['ime']  ?? '',
                'ikona' => $podatki['ikona'] ?? '✨',
                'barva' => $podatki['barva'] ?? '#8b5cf6',
                'tip'   => $podatki['tip']  ?? 'varuh',
                'modul' => $podatki['modul'] ?? null,
            ];
        }
    }

    return $seznam;
}

/**
 * Briše zgodovino seje (reset pogovora)
 */
function ai_pocisti_sejo(string $tip, string $id1, string $id2): bool
{
    $seja = ai_pridobi_sejo($tip, $id1, $id2);
    $seja['zgodovina']       = [];
    $seja['skupaj_sporocil'] = 0;
    $seja['xp_iz_pogovora']  = 0;
    return ai_shrani_sejo($tip, $id1, $id2, $seja);
}

/**
 * Statistike AI storitve
 */
function ai_statistike(string $uporabnik_id): array
{
    $mapa = POT_PODATKI . '/ai/avatarji/' . $uporabnik_id . '/';

    if (!is_dir($mapa)) {
        return ['skupaj_sej' => 0, 'skupaj_sporocil' => 0, 'skupaj_xp' => 0];
    }

    $skupaj_sporocil = 0;
    $skupaj_xp       = 0;
    $stevilo_sej     = 0;

    foreach (glob($mapa . '*.json') as $datoteka) {
        $seja = json_decode(file_get_contents($datoteka), true);
        if (is_array($seja)) {
            $skupaj_sporocil += $seja['skupaj_sporocil'] ?? 0;
            $skupaj_xp       += $seja['xp_iz_pogovora']  ?? 0;
            $stevilo_sej++;
        }
    }

    return [
        'skupaj_sej'      => $stevilo_sej,
        'skupaj_sporocil' => $skupaj_sporocil,
        'skupaj_xp'       => $skupaj_xp,
    ];
}
