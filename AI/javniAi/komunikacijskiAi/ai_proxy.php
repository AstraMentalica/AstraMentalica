<?php
/**
 * DATOTEKA: ASTRA/ai_proxy.php
 * NAMEN:    Proxy za DeepSeek API klice iz brskalnika (artefakt/JS).
 *           Prebere ključ iz PODATKI/sef/.env_dipsi, posreduje na DeepSeek.
 * NIVO:     ASTRA (samo admin dostop)
 * ODVISNO:  PODATKI/sef/.env_dipsi
 * VERZIJA:  1.0
 * DATUM:    2026-06-18
 */

declare(strict_types=1);

// ============================================================
// CORS — dovoli samo od tvojega domenea (prilagodi!)
// ============================================================
$dovoljene_domene = [
    'http://localhost',
    'http://localhost:8080',
    'https://claude.ai',      // za artefakt v claude.ai
];

$izvor = $_SERVER['HTTP_ORIGIN'] ?? '';
if (in_array($izvor, $dovoljene_domene, true) || str_contains($izvor, 'claude.ai')) {
    header('Access-Control-Allow-Origin: ' . $izvor);
} else {
    header('Access-Control-Allow-Origin: *'); // med razvojem — po potrebi zoži
}
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// ============================================================
// ROOT in POT DO KLJUČA
// ============================================================
$root = realpath(__DIR__ . '/..');
if ($root === false) {
    http_response_code(500);
    echo json_encode(['napaka' => 'ROOT ni določljiv.']);
    exit;
}

$pot_env = $root . '/PODATKI/sef/.env_dipsi';

// ============================================================
// BRANJE API KLJUČA
// ============================================================
function preberi_kljuc(string $pot_env): string {
    if (!file_exists($pot_env)) return '';
    $vsebina = file_get_contents($pot_env);
    foreach (explode("\n", $vsebina) as $vrstica) {
        $vrstica = trim($vrstica);
        if (empty($vrstica) || !str_contains($vrstica, '=')) continue;
        [$ime, $vrednost] = explode('=', $vrstica, 2);
        $ime = trim($ime);
        if (in_array($ime, ['DEEPSEEK_API_KEY', 'DEEPSEEK_KLJUC', 'DS_API_KEY'], true)) {
            return trim($vrednost);
        }
    }
    return '';
}

$api_kljuc = preberi_kljuc($pot_env);

if (empty($api_kljuc)) {
    http_response_code(500);
    echo json_encode(['napaka' => 'API ključ ni najden v .env_dipsi. Preverite: DEEPSEEK_API_KEY=sk-...']);
    exit;
}

// ============================================================
// PREVERI VHOD
// ============================================================
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['napaka' => 'Samo POST.']);
    exit;
}

$telo = file_get_contents('php://input');
$podatki = json_decode($telo, true);

if (!isset($podatki['messages']) || !is_array($podatki['messages'])) {
    http_response_code(400);
    echo json_encode(['napaka' => 'Manjka polje messages.']);
    exit;
}

// ============================================================
// POSREDUJ NA DEEPSEEK
// ============================================================
$zahteva = [
    'model'       => $podatki['model'] ?? 'deepseek-chat',
    'messages'    => $podatki['messages'],
    'max_tokens'  => min((int)($podatki['max_tokens'] ?? 1000), 4000),
    'temperature' => (float)($podatki['temperature'] ?? 0.7),
];

if (!empty($podatki['system'])) {
    array_unshift($zahteva['messages'], [
        'role'    => 'system',
        'content' => $podatki['system'],
    ]);
}

$kontekst = stream_context_create([
    'http' => [
        'method'  => 'POST',
        'header'  => implode("\r\n", [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $api_kljuc,
        ]),
        'content' => json_encode($zahteva),
        'timeout' => 60,
    ]
]);

$odziv = @file_get_contents('https://api.deepseek.com/chat/completions', false, $kontekst);

if ($odziv === false) {
    http_response_code(502);
    echo json_encode(['napaka' => 'Napaka pri klicu DeepSeek API.']);
    exit;
}

$deepseek_odziv = json_decode($odziv, true);

// ============================================================
// PRETVORI V ANTHROPIC OBLIKO (da artefakt ne rabi sprememb)
// ============================================================
if (isset($deepseek_odziv['choices'][0]['message']['content'])) {
    $besedilo = $deepseek_odziv['choices'][0]['message']['content'];
    echo json_encode([
        'content' => [
            ['type' => 'text', 'text' => $besedilo]
        ]
    ]);
} else {
    // Vrni kar je prišlo (za debug)
    http_response_code(502);
    echo json_encode(['napaka' => 'Nepričakovan format od DeepSeek.', 'surovo' => $deepseek_odziv]);
}
