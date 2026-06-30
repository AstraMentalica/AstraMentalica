<?php
/**
 * ============================================================
 * POT: ASTRA/ai_proxy.php
 * 📅 VERZIJA: v100 (28.6.2026)
 * ============================================================
 *
 * 🏛️ NIVO: API PROXY
 *
 * 📰 NAMEN:
 *     Varni prehod med frontendom in AI modelom.
 *     Frontend nikoli ne vidi API ključa.
 *     Proxy preveri sejo, zgradi system prompt iz
 *     kanonični_varuhi.json in pokliče OpenRouter.
 *
 * 🔧 ENDPOINT:
 *     POST /ASTRA/ai_proxy.php
 *     Content-Type: application/json
 *
 *     Telo zahtevka:
 *     {
 *       "varuh_id":  "stellarion",          // kateri varuh
 *       "sporocila": [                       // zgodovina pogovora
 *         {"role": "user",      "content": "Pozdravljeni"},
 *         {"role": "assistant", "content": "Zvezde so..."}
 *       ],
 *       "model":    "deepseek/deepseek-chat-v3:free"  // opcijsko
 *     }
 *
 *     Odgovor (uspeh):
 *     {
 *       "status":   "uspeh",
 *       "odgovor":  "Besedilo varuha...",
 *       "model":    "deepseek/deepseek-chat-v3:free",
 *       "varuh_id": "stellarion",
 *       "xp":       5
 *     }
 *
 * 🔧 MODELI (OpenRouter brezplačni):
 *     deepseek/deepseek-chat-v3:free       → privzeti (najboljši)
 *     qwen/qwen3-coder:free
 *     google/gemma-3-27b-it:free
 *     meta-llama/llama-3.3-70b-instruct:free
 *     mistralai/devstral-small:free
 *
 * 📡 ODVISNOSTI:
 *     - pot.php (POT_SEF, POT_ENTITETE)
 *     - pot_vmesnik.php (POT_ENTITETE_VARUHI)
 *     - SISTEM/storitve_svetov/ekonomija/inventar_storitev.php
 *     - SISTEM/storitve_svetov/ekonomija/tocke_storitev.php
 *     - PODATKI/sef/.env_api  (OPENROUTER_API_KEY)
 *
 * 🚫 PREPOVEDI:
 *     - Brez __DIR__
 *     - Brez izpisa API ključa
 *     - Brez bypass seja preverjanja
 *
 * 📌 STATUS: Stabilno
 * 📅 ZGODOVINA:
 *     - v100: prva implementacija – OpenRouter, session zaščita,
 *             system prompt iz kanonični_varuhi.json,
 *             XP za vsako sporočilo
 * 👤 AVTOR: AstraMentalica Mojster
 * ============================================================
 */
declare(strict_types=1);

// ── Varovalka ──────────────────────────────────────────────
if (!defined('SISTEM_VARNOST')) {
    // Direkten HTTP klic – naloži sistem
    require_once __DIR__ . '/../pot.php';
    require_once POT_GLOBALNO . '/vmesnik/pot_vmesnik.php';
    require_once POT_KERNEL   . '/nastavitve.php';
    require_once POT_KERNEL   . '/env_loader.php';
    require_once POT_KERNEL   . '/knjiznice/avtonalagalnik.php';
    require_once POT_JEDRO    . '/04_seja.php';
    require_once POT_JEDRO    . '/05_pravice.php';
    require_once POT_STORITVE . '/ekonomija/inventar_storitev.php';
    require_once POT_STORITVE . '/ekonomija/tocke_storitev.php';
    require_once POT_STORITVE . '/entitete/entiteta_storitev.php';
}

header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');

// ── CORS (samo lasten domain) ──────────────────────────────
$dovoljeniDomeni = [
    'https://' . ($_SERVER['HTTP_HOST'] ?? 'localhost'),
];
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if (in_array($origin, $dovoljeniDomeni, true)) {
    header("Access-Control-Allow-Origin: $origin");
    header('Access-Control-Allow-Credentials: true');
}
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Methods: POST');
    header('Access-Control-Allow-Headers: Content-Type');
    http_response_code(204);
    exit;
}

// ── Samo POST ──────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    _proxy_napaka('Samo POST.', 405);
}

// ── Seja + vloga ───────────────────────────────────────────
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$uid   = $_SESSION['uid']            ?? null;
$vloga = (int)($_SESSION['uporabnik_vloga'] ?? VLOGA_GOST);

// Gosti ne smejo klicati AI (S0 minimum)
if ($uid === null || $vloga < VLOGA_S0) {
    _proxy_napaka('Prijava potrebna.', 401);
}

// ── Preberi telo ───────────────────────────────────────────
$raw  = file_get_contents('php://input');
$vnos = json_decode($raw, true);

if (!$vnos || json_last_error() !== JSON_ERROR_NONE) {
    _proxy_napaka('Neveljaven JSON.', 400);
}

$varuhId   = preg_replace('/[^a-z0-9_]/', '', (string)($vnos['varuh_id'] ?? 'stellarion'));
$sporocila = $vnos['sporocila'] ?? [];
$zeleniModel = (string)($vnos['model'] ?? '');

// Validiraj sporočila
if (!is_array($sporocila) || count($sporocila) === 0) {
    _proxy_napaka('Sporočila so prazna.', 400);
}
if (count($sporocila) > 50) {
    _proxy_napaka('Preveč sporočil v zgodovini (max 50).', 400);
}

// Validiraj vloge v sporočilih
foreach ($sporocila as $msg) {
    if (!in_array($msg['role'] ?? '', ['user', 'assistant'], true)) {
        _proxy_napaka('Neveljavna vloga v sporočilu.', 400);
    }
    if (mb_strlen((string)($msg['content'] ?? '')) > 4000) {
        _proxy_napaka('Sporočilo je predolgo.', 400);
    }
}

// Zadnje sporočilo mora biti user
if (($sporocila[count($sporocila) - 1]['role'] ?? '') !== 'user') {
    _proxy_napaka('Zadnje sporočilo mora biti user.', 400);
}

// ── Varuh ──────────────────────────────────────────────────
$varuh = entiteta_varuh($varuhId);
if (!$varuh) {
    _proxy_napaka("Varuh ne obstaja: $varuhId", 404);
}

// Preveri ali je varuh odklenjen za tega uporabnika
$odklenjeni = entiteta_odklenjeni($uid);
if (!isset($odklenjeni[$varuhId])) {
    _proxy_napaka('Ta varuh ni odklenjen.', 403);
}

// ── Model ──────────────────────────────────────────────────
const DOVOLJENI_MODELI = [
    'deepseek/deepseek-chat-v3:free',        // privzeti – najboljši za dialog
    'qwen/qwen3-coder:free',
    'google/gemma-3-27b-it:free',
    'meta-llama/llama-3.3-70b-instruct:free',
    'mistralai/devstral-small:free',
];

// Admin lahko izbere model, sicer privzeti
$model = 'deepseek/deepseek-chat-v3:free';
if ($vloga >= VLOGA_ADMIN && $zeleniModel && in_array($zeleniModel, DOVOLJENI_MODELI, true)) {
    $model = $zeleniModel;
} elseif ($zeleniModel && in_array($zeleniModel, DOVOLJENI_MODELI, true)) {
    $model = $zeleniModel;  // S0+ sme izbirati med dovoljenimi
}

// ── System prompt ──────────────────────────────────────────
$systemPrompt = _proxy_system_prompt($varuh, $uid, $vloga);

// ── API ključ ──────────────────────────────────────────────
$apiKljuc = _proxy_env_kljuc('OPENROUTER_API_KEY');
if (!$apiKljuc) {
    error_log('[AI_PROXY] Manjka OPENROUTER_API_KEY v .env_api');
    _proxy_napaka('AI storitev trenutno nedostopna.', 503);
}

// ── Klic OpenRouter ────────────────────────────────────────
$payload = json_encode([
    'model'    => $model,
    'messages' => [
        ['role' => 'system', 'content' => $systemPrompt],
        ...$sporocila,
    ],
    'max_tokens'  => 600,
    'temperature' => 0.85,
]);

$kontekst = stream_context_create([
    'http' => [
        'method'  => 'POST',
        'header'  => implode("\r\n", [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $apiKljuc,
            'HTTP-Referer: ' . (KOREN_URL ?? 'https://astramentalica.si'),
            'X-Title: AstraMentalica',
        ]),
        'content' => $payload,
        'timeout' => 30,
        'ignore_errors' => true,
    ],
]);

$odgovorRaw = @file_get_contents('https://openrouter.ai/api/v1/chat/completions', false, $kontekst);

if ($odgovorRaw === false) {
    error_log('[AI_PROXY] OpenRouter nedosegljiv');
    _proxy_napaka('Kozmična vez je prekinjena. Poskusi znova.', 502);
}

$odgovorData = json_decode($odgovorRaw, true);

// Preveri napako OpenRouterja
if (isset($odgovorData['error'])) {
    $errMsg = $odgovorData['error']['message'] ?? 'Neznana napaka';
    error_log("[AI_PROXY] OpenRouter napaka: $errMsg");
    _proxy_napaka('AI model trenutno ni dosegljiv.', 502);
}

$besedilo = $odgovorData['choices'][0]['message']['content'] ?? null;
if (!$besedilo) {
    _proxy_napaka('Prazen odgovor od modela.', 502);
}

// ── XP za sporočilo ────────────────────────────────────────
$xpRezultat = tocke_za_akcijo($uid, 'vpogled_zapisan', ['varuh' => $varuhId]);
$xpDodano   = $xpRezultat['xp'] ?? 0;

// ── Odgovor ────────────────────────────────────────────────
echo json_encode([
    'status'   => 'uspeh',
    'odgovor'  => $besedilo,
    'model'    => $model,
    'varuh_id' => $varuhId,
    'varuh_ikona' => $varuh['ikona'] ?? '✨',
    'xp'       => $xpDodano,
], JSON_UNESCAPED_UNICODE);

// ============================================================
// INTERNE FUNKCIJE
// ============================================================

/**
 * Zgradi system prompt iz zavesti varuha + kontekst uporabnika.
 */
function _proxy_system_prompt(array $varuh, string $uid, int $vloga): string
{
    $zavest    = $varuh['zavest'] ?? [];
    $ime       = $varuh['ime'] ?? 'Varuh';
    $element   = $varuh['element'] ?? '';
    $tip       = $varuh['tip'] ?? '';

    // Osnova iz zavest.temelj (kanonični JSON) ali zeleni sistem string
    $temelj = $zavest['temelj'] ?? $varuh['temeljna_modrost'] ?? '';

    // Posebnosti vedenja
    $posebnosti = $zavest['posebnosti'] ?? [];
    $ni         = $zavest['ni'] ?? [];
    $nacin      = $zavest['nacin_soocanja'] ?? '';
    $tema       = $zavest['primarna_tema'] ?? '';

    // Glasovni stil
    $glasSlog = $varuh['glasovni_profil']['slog_kljuc'] ?? $varuh['glasovni_profil']['stil'] ?? '';

    // Kontekst uporabnika (minimalen – ne razkrivaj osebnih podatkov)
    $vloga_ime = match(true) {
        $vloga >= VLOGA_S5    => 'Mojster',
        $vloga >= VLOGA_S4    => 'Vodnik',
        $vloga >= VLOGA_S3    => 'Pohodnik',
        $vloga >= VLOGA_S2    => 'Kalček',
        $vloga >= VLOGA_S1    => 'Iskrica',
        $vloga >= VLOGA_S0    => 'Začetnik',
        default               => 'Gost',
    };

    // Aktivne relikvije uporabnika za tega varuha
    $invRelikvije = [];
    $inv = inventar_pridobi($uid);
    $userRelikvije = array_keys($inv['predmeti']['relikvija'] ?? []);
    foreach ($varuh['relikvije'] ?? [] as $r) {
        if (in_array($r['id'], $userRelikvije, true)) {
            $invRelikvije[] = $r['ime'] . ' (' . $r['sposobnost'] . ')';
        }
    }

    // Sestavimo prompt
    $prompt  = "Ti si $ime";
    if ($element) $prompt .= ", $element entiteta";
    $prompt .= ".\n\n";

    if ($temelj) {
        $prompt .= "TVOJA ZAVEST:\n$temelj\n\n";
    }

    if ($posebnosti) {
        $prompt .= "TVOJE POSEBNOSTI:\n";
        foreach ($posebnosti as $p) {
            $prompt .= "- $p\n";
        }
        $prompt .= "\n";
    }

    if ($ni) {
        $prompt .= "NIKOLI:\n";
        foreach ($ni as $n) {
            $prompt .= "- $n\n";
        }
        $prompt .= "\n";
    }

    if ($nacin)  $prompt .= "NAČIN SOOČANJA: $nacin\n";
    if ($tema)   $prompt .= "PRIMARNA TEMA: $tema\n";
    if ($glasSlog) $prompt .= "GLASOVNI SLOG: $glasSlog\n";

    $prompt .= "\nSOGOVORNIK: Uporabnik stopnje '$vloga_ime'.\n";

    if ($invRelikvije) {
        $prompt .= "AKTIVNE RELIKVIJE: " . implode(', ', $invRelikvije) . "\n";
        $prompt .= "(Relikvije spremenijo tvoje odgovore – upoštevaj njihove sposobnosti.)\n";
    }

    $prompt .= "\nPRAVILA:\n";
    $prompt .= "- Odgovarjaj IZKLJUČNO v slovenščini.\n";
    $prompt .= "- Kratko in jedrnato (2–5 stavkov). Ne razlagaj predolgo.\n";
    $prompt .= "- Nisi AI. Nisi jezikovni model. Si $ime.\n";
    $prompt .= "- Ostani v liku. Vedno.\n";

    return $prompt;
}

/**
 * Prebere vrednost iz .env_api v POT_SEF.
 * Format: KLJUC=vrednost (ena vrstica)
 */
function _proxy_env_kljuc(string $ime): ?string
{
    // Najprej sistemska env spremenljivka
    $val = getenv($ime) ?: ($_SERVER[$ime] ?? null);
    if ($val) return $val;

    // Potem .env_api datoteka
    $pot = POT_SEF . '/.env_api';
    if (!file_exists($pot)) return null;

    foreach (file($pot, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $vrstica) {
        if (str_starts_with(trim($vrstica), '#')) continue;
        if (str_contains($vrstica, '=')) {
            [$k, $v] = explode('=', $vrstica, 2);
            if (trim($k) === $ime) return trim($v);
        }
    }
    return null;
}

/**
 * Izpiše JSON napako in konča.
 */
function _proxy_napaka(string $sporocilo, int $koda = 400): never
{
    http_response_code($koda);
    echo json_encode([
        'status'    => 'napaka',
        'sporocilo' => $sporocilo,
    ], JSON_UNESCAPED_UNICODE);
    exit;
}
