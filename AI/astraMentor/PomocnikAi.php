<?php
//PomocnikAi.php
class DeepSeekHelper {
    const API_URL = 'https://api.deepseek.com/v1/chat/completions';
    const API_KEY = 'your_deepseek_api_key'; // Better to store in config
    
    public static function generateOutline($topic) {
        $prompt = "Create a detailed outline for a blog post about: $topic";
        
        $data = [
            'model' => 'deepseek-chat',
            'messages' => [
                ['role' => 'user', 'content' => $prompt]
            ],
            'max_tokens' => 1000
        ];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::API_URL);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . self::API_KEY,
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $response = curl_exec($ch);
        curl_close($ch);
        
        $result = json_decode($response, true);
        return $result['choices'][0]['message']['content'];
    }
}

class GoogleGeminiHelper {
    const API_URL = 'https://generativelanguage.googleapis.com/v1/models/gemini-pro:generateContent';
    const API_KEY = 'your_google_api_key';
    
    public static function writeArticle($outline) {
        $prompt = "Write a detailed blog post based on the following outline:\n\n$outline";
        
        $data = [
            'contents' => [
                'parts' => [
                    ['text' => $prompt]
                ]
            ]
        ];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::API_URL . '?key=' . self::API_KEY);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $response = curl_exec($ch);
        curl_close($ch);
        
        $result = json_decode($response, true);
        return $result['candidates'][0]['content']['parts'][0]['text'];
    }
}
