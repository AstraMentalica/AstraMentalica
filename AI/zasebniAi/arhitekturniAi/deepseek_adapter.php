<?php
declare(strict_types=1);

// Adapter: če je DEEPSEEK_API_KEY nastavljen, kliče DeepSeek API,
// sicer preusmeri klic na lokalni Ollama (llama_helper.php).

if (!defined('AI_VSTOP')) {
    $v = __DIR__ . '/../../varnost.php';
    if (file_exists($v)) require_once $v;
}

if (!defined('DEEPSEEK_API_URL')) define('DEEPSEEK_API_URL', 'https://api.deepseek.com/v1/chat/completions');
if (!defined('DEEPSEEK_MODEL')) define('DEEPSEEK_MODEL', 'deepseek-chat');

function deepseek_adapter_klic(string $sistemsko, string $sporocilo, float $temp = 0.3): string {
    // Allow forcing local Ollama via env var (useful if credits expired)
    $forceLocal = false;
    $env = getenv('FORCE_LOCAL_OLLAMA');
    if ($env !== false) {
        $env = strtolower(trim($env));
        $forceLocal = in_array($env, ['1','true','yes'], true);
    }

    // 1) Če imamo ključ za DeepSeek in ni prisiljen lokalni Ollama, uporabimo njihov API
    if (!$forceLocal && defined('DEEPSEEK_API_KEY') && DEEPSEEK_API_KEY) {
        $payload = json_encode([
            'model' => DEEPSEEK_MODEL,
            'temperature' => $temp,
            'messages' => [
                ['role' => 'system', 'content' => $sistemsko],
                ['role' => 'user', 'content' => $sporocilo]
            ]
        ]);
        $ch = curl_init(DEEPSEEK_API_URL);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . DEEPSEEK_API_KEY
            ],
            CURLOPT_TIMEOUT => 120
        ]);
        $odgovor = curl_exec($ch);
        $napaka = curl_error($ch);
        curl_close($ch);
        if ($napaka) throw new RuntimeException("DeepSeek cURL: $napaka");
        $data = json_decode($odgovor, true);
        if (!isset($data['choices'][0]['message']['content'])) {
            throw new RuntimeException("DeepSeek API: " . ($data['error']['message'] ?? $odgovor));
        }
        return trim($data['choices'][0]['message']['content']);
    }

    // 2) Fallback: uporabimo lokalni Ollama/Llama preko llama_helper.php
    $llamaFile = __DIR__ . '/../../llama_helper.php';
    if (!file_exists($llamaFile)) {
        // Poskusi vključi iz AI/ poti
        $llamaFile = __DIR__ . '/../../llama_helper.php';
    }
    if (!file_exists($llamaFile)) throw new RuntimeException('Niti DeepSeek ključa niti lokalnega llama_helper.php ni najdeno.');

    require_once $llamaFile;

    $messages = [
        ['role' => 'system', 'content' => $sistemsko],
        ['role' => 'user', 'content' => $sporocilo]
    ];

    // Uporabi privzeti model iz env, če je nastavljen
    $cfg = function_exists('llama_config') ? llama_config() : [];
    $model = $cfg['model'] ?? null;

    return llama_chat($messages, $model, $temp, 2048);
}
