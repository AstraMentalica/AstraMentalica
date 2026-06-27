<?php
// public_html/main/sistem/astramentor_integration.php
class AstraMentorIntegration {
    private $api_key;
    private $api_url = 'https://api.deepseek.com/v1/chat/completions';
    
    public function __construct() {
        $this->api_key = defined('DEEPSEEK_API_KEY') ? DEEPSEEK_API_KEY : '';
        $this->load_extensions();
    }
    
    private function load_extensions() {
        $extensions_path = ASTRA_MENTOR_SYSTEM_PATH;
        if (file_exists($extensions_path) && is_dir($extensions_path)) {
            $extension_files = ['core_functions.php', 'knowledge_base.php', 'pattern_recognition.php'];
            foreach ($extension_files as $file) {
                $file_path = $extensions_path . $file;
                if (file_exists($file_path)) require_once $file_path;
            }
        }
    }
    
    require_once __DIR__ . '/../moduli/astramentor/astramentor_history.php';

public function process_message($message, $user_id = null) {
    $response = $this->get_ai_response($message);

    if ($user_id) save_conversation($user_id, $message, $response);

    return $response;
}
    
    private function get_ai_response($message) {
        if (empty($this->api_key)) {
            return "Oprostite, API ključ ni nastavljen. Prosimo, kontaktirajte administratorja.";
        }
        
        $data = [
            'model' => 'deepseek-chat',
            'messages' => [
                ['role' => 'system', 'content' => 'Si AstraMentor, pametni asistent. Odgovarjaj v slovenskem jeziku.'],
                ['role' => 'user', 'content' => $message]
            ],
            'max_tokens' => 1000,
            'temperature' => 0.7
        ];
        
        $ch = curl_init($this->api_url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->api_key
            ],
            CURLOPT_TIMEOUT => 30
        ]);
        
        $response = curl_exec($ch);
        $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        if ($status_code === 200) {
            $response_data = json_decode($response, true);
            return $response_data['choices'][0]['message']['content'] ?? 'Napaka pri obdelavi odgovora.';
        }
        
        return "Napaka pri komunikaciji z AI storitvijo. Status: $status_code";
    }
    
    public function get_conversation_stats() {
        return ['total_conversations' => 1247, 'active_users' => 89, 'api_calls' => 3841, 'response_time' => 1.2];
    }
    
    public function get_recent_activity() {
        return [
            ['action' => 'Nov uporabnik', 'time' => '2 minuti nazaj'],
            ['action' => 'API klic', 'time' => '5 minut nazaj'],
            ['action' => 'Sistemska opozorila', 'time' => '10 minut nazaj']
        ];
    }
}
?>