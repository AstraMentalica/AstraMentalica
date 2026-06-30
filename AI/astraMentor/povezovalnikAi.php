<?php
// =============================================================================
// AstraMentalica - AI Povezava Modul
// =============================================================================
// Ta datoteka skrbi za komunikacijo z GPT/AI API-ji (DeepSeek, Gemini itd.)
// Nikoli ne zapisuj API ključev direktno v kodo, uporabi .env datoteko
// ============================================================================

// Naloži .env, če še ni naložen
if (!function_exists('nalozi_env')) {
    require_once POT_KERNEL . 'zaganjalnik.php';
}

// ============================================================================
// Funkcija za preverjanje ključev za AI modul
// ============================================================================
// $modul : ime modula ('gpt', 'deepseek', 'gemini')
// Vrne true, če je ključ prisoten, sicer ustavi izvajanje
function preveriAIKljuc($modul) {
    $kljuci = [
        'gpt' => 'OPENAI_API_KEY',
        'deepseek' => 'DEEPSEEK_API_KEY',
        'gemini' => 'GEMINI_API_KEY'
    ];

    if (!isset($kljuci[$modul])) return false;

    $kljuc = $kljuci[$modul];
    if (!isset($_ENV[$kljuc]) && !defined($kljuc)) {
        die("❌ AI modul '$modul' zahteva ključ '$kljuc' v .env ali kot konstanto.");
    }
    return true;
}

// Preveri, če so osnovni ključi definirani
if (!defined('DEEPSEEK_API_KEY') || !defined('GEMINI_API_KEY')) {
    die("❌ API ključi niso nastavljeni v .env!");
}

// ============================================================================
// Pošlji zahtevo na GPT / DeepSeek API
// ============================================================================
// $prompt : besedilo ali vprašanje
// $model  : model, npr. DEEPSEEK_MODEL
// Vrne array z rezultati ali napako
function posljiNaGPT($prompt, $model = DEEPSEEK_MODEL) {
    $apiKey = DEEPSEEK_API_KEY;

    $url = 'https://api.deepseek.com/v1/prediction';
    $data = json_encode([
        'model' => $model,
        'input' => $prompt
    ]);

    $opts = [
        "http" => [
            "header"  => "Content-type: application/json\r\n" .
                         "Authorization: Bearer $apiKey\r\n",
            "method"  => "POST",
            "content" => $data,
            "timeout" => 30
        ]
    ];

    $context  = stream_context_create($opts);
    $result = file_get_contents($url, false, $context);

    if ($result === false) {
        return ['napaka' => true, 'sporocilo' => 'Neuspešna povezava do GPT API.'];
    }

    $decoded = json_decode($result, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        return ['napaka' => true, 'sporocilo' => 'Napaka pri dekodiranju JSON.'];
    }

    return $decoded;
}

// ============================================================================
// Primer uporabe
// ============================================================================
// $rezultat = posljiNaGPT("Pozdravljen, GPT!");
// print_r($rezultat);
?>