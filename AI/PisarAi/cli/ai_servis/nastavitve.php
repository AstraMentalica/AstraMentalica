// ============================================================
// AI INTEGRACIJA - IZBERI ENO OPCIJO
// ============================================================

// OPCIJA 1: OpenAI (ChatGPT)
if (get_option('ai_provider') === 'openai') {
    function ai_generiraj($prompt, $options = []) {
        $apiKey = get_option('openai_api_key');
        if (!$apiKey) return null;
        
        $data = [
            'model' => 'gpt-3.5-turbo',
            'messages' => [['role' => 'user', 'content' => $prompt]],
            'max_tokens' => $options['max_tokens'] ?? 1500,
            'temperature' => 0.7
        ];
        
        $ch = curl_init('https://api.openai.com/v1/chat/completions');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $apiKey
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode === 200) {
            $result = json_decode($response, true);
            return $result['choices'][0]['message']['content'] ?? null;
        }
        
        error_log("OpenAI API error: $httpCode - $response");
        return null;
    }
    
    function ai_analiziraj($vsebina, $tip = 'splosno') {
        $prompt = "Analiziraj naslednjo vsebino:\n\n$vsebina\n\n";
        if ($tip === 'splosno') {
            $prompt .= "Naredi: 1) Povzetek (2-3 stavke) 2) Ključne točke 3) Priporočila";
        }
        return ai_generiraj($prompt, ['max_tokens' => 800]);
    }
}

// OPCIJA 2: Groq (hiter, free tier)
elseif (get_option('ai_provider') === 'groq') {
    function ai_generiraj($prompt, $options = []) {
        $apiKey = get_option('groq_api_key');
        if (!$apiKey) return null;
        
        $data = [
            'model' => 'llama3-8b-8192',
            'messages' => [['role' => 'user', 'content' => $prompt]],
            'max_tokens' => $options['max_tokens'] ?? 1500
        ];
        
        $ch = curl_init('https://api.groq.com/openai/v1/chat/completions');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $apiKey
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        
        $response = curl_exec($ch);
        curl_close($ch);
        
        $result = json_decode($response, true);
        return $result['choices'][0]['message']['content'] ?? null;
    }
    
    function ai_analiziraj($vsebina, $tip = 'splosno') {
        return ai_generiraj("Analiziraj: $vsebina", ['max_tokens' => 800]);
    }
}

// OPCIJA 3: Lokalni Ollama (brezplačno, zasebno)
elseif (get_option('ai_provider') === 'ollama') {
    function ai_generiraj($prompt, $options = []) {
        $data = [
            'model' => 'llama2',
            'prompt' => $prompt,
            'stream' => false,
            'options' => ['num_predict' => $options['max_tokens'] ?? 1500]
        ];
        
        $ch = curl_init('http://localhost:11434/api/generate');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        
        $response = curl_exec($ch);
        curl_close($ch);
        
        $result = json_decode($response, true);
        return $result['response'] ?? null;
    }
    
    function ai_analiziraj($vsebina, $tip = 'splosno') {
        return ai_generiraj("Analiziraj: $vsebina");
    }
}