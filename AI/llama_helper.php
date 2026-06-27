<?php
declare(strict_types=1);

// Helper za komunikacijo z lokalnim Ollama / Llama 3.2 HTTP endpointom.
// Nastavitve: OLLAMA_URL, OLLAMA_CHAT_PATH, OLLAMA_API_KEY (neobvezno)

if (!defined('AI_VSTOP')) {
    $v = __DIR__ . '/varnost.php';
    if (file_exists($v)) require_once $v;
}

function llama_config(): array {
    $url = getenv('OLLAMA_URL') ?: 'http://localhost:11434';
    $path = getenv('OLLAMA_CHAT_PATH') ?: '/api/chat';
    $key = getenv('OLLAMA_API_KEY') ?: '';
    $envModel = getenv('OLLAMA_MODEL') ?: '';
    $modelsEnv = getenv('OLLAMA_MODELS') ?: ''; // comma-separated list
    $selectedFile = __DIR__ . '/selected_model.txt';
    $selectedModel = '';
    if (file_exists($selectedFile)) {
        $selectedModel = trim(file_get_contents($selectedFile));
    }
    $model = $selectedModel ?: $envModel;
    return ['url'=>$url, 'path'=>$path, 'key'=>$key, 'model'=>$model, 'models_env'=>$modelsEnv, 'selected_file'=>$selectedFile];
}

function llama_set_selected_model(string $model): bool {
    $file = __DIR__ . '/selected_model.txt';
    $res = @file_put_contents($file, $model);
    return $res !== false;
}

function llama_chat(array $messages, ?string $model = null, float $temperature = 0.2, int $max_tokens = 1024): string {
    $cfg = llama_config();
    $url = rtrim($cfg['url'], '/') . $cfg['path'];
    $useModel = $model ?? ($cfg['model'] ?: 'llama-3.2');

    $payload = json_encode([
        'model' => $useModel,
        'messages' => $messages,
        'temperature' => $temperature,
        'max_tokens' => $max_tokens
    ]);

    $headers = ['Content-Type: application/json'];
    if (!empty($cfg['key'])) $headers[] = 'Authorization: Bearer ' . $cfg['key'];

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $payload,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_TIMEOUT => 120,
    ]);
    $resp = curl_exec($ch);
    $err = curl_error($ch);
    curl_close($ch);
    if ($err) throw new RuntimeException('cURL error: ' . $err);
    $data = json_decode($resp, true);
    if (!$data) return trim((string)$resp);

    // Podpora več različnim formatom odgovora
    if (isset($data['choices'][0]['message']['content'])) {
        return trim($data['choices'][0]['message']['content']);
    }
    if (isset($data['choices'][0]['text'])) {
        return trim($data['choices'][0]['text']);
    }
    if (isset($data['response'])) return trim((string)$data['response']);
    if (isset($data['result'])) return trim((string)$data['result']);

    return trim((string)$resp);
}
