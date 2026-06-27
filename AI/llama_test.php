<?php
declare(strict_types=1);
require_once __DIR__ . '/llama_helper.php';

header('Content-Type: text/plain; charset=utf-8');
echo "Olloama test\n";
try {
    $cfg = llama_config();
    $modelsUrl = rtrim($cfg['url'], '/') . '/api/models';
    $ch = curl_init($modelsUrl);
    curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER=>true, CURLOPT_HTTPHEADER=>['Content-Type: application/json'], CURLOPT_TIMEOUT=>10]);
    $mresp = curl_exec($ch);
    $merr = curl_error($ch);
    curl_close($ch);
    if ($merr) throw new RuntimeException('cURL: '.$merr);
    $md = json_decode($mresp, true);
    echo "Models endpoint response:\n" . ($mresp ?: 'no response') . "\n\n";

    $messages = [
        ['role'=>'system','content'=>'You are a compact test assistant. Answer briefly.'],
        ['role'=>'user','content'=>'Pozdravi me v slovenščini.']
    ];
    $resp = llama_chat($messages, null, 0.1, 256);
    echo "Chat response:\n" . $resp . "\n";
} catch (Throwable $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
