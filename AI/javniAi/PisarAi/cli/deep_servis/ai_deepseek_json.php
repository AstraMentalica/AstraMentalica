<?php
/**
 * DeepSeek AI Servis - JSON shranjevanje (brez baze!)
 * Vse shranjeno v /podatki/ai/ mapo
 */

class DeepSeekAI_JSON {
    private $dataPath;
    private $sessionId;
    
    public function __construct() {
        $this->dataPath = PODATKI_PATH . '/ai';
        $this->sessionId = session_id() . '_' . date('Ymd');
        $this->ensureDirectories();
    }
    
    /**
     * Ustvari potrebne mape
     */
    private function ensureDirectories() {
        $dirs = [
            $this->dataPath,
            $this->dataPath . '/zgodovina',
            $this->dataPath . '/agenti',
            $this->dataPath . '/api_kljuci',
            $this->dataPath . '/pogovori'
        ];
        foreach ($dirs as $dir) {
            if (!is_dir($dir)) mkdir($dir, 0755, true);
        }
    }
    
    /**
     * Generiraj odgovor z DeepSeek API
     */
    public function generiraj($prompt, $agentId = 'bloger', $apiKeyId = null, $options = []) {
        // Pridobi agenta
        $agent = $this->getAgent($agentId);
        if (!$agent) {
            return ['error' => "Agent '$agentId' ne obstaja"];
        }
        
        // Pridobi API ključ
        $apiKey = $this->getApiKey($apiKeyId);
        if (!$apiKey) {
            return ['error' => 'API ključ ni na voljo'];
        }
        
        // Pripravi sporočila
        $messages = [];
        
        // System prompt
        if (!empty($agent['system_prompt'])) {
            $messages[] = ['role' => 'system', 'content' => $agent['system_prompt']];
        }
        
        // Zgodovina (zadnjih 10 sporočil)
        $history = $this->getHistory($this->sessionId, $agentId, 10);
        foreach ($history as $h) {
            $messages[] = ['role' => $h['vloga'], 'content' => $h['vsebina']];
        }
        
        // Trenutni prompt
        $messages[] = ['role' => 'user', 'content' => $prompt];
        
        // Shrani uporabniško sporočilo
        $this->saveMessage($this->sessionId, $agentId, $apiKey['id'], 'user', $prompt);
        
        // API klic
        $result = $this->callDeepSeekAPI($apiKey, $messages, [
            'temperature' => $options['temperature'] ?? $agent['temperatura'],
            'max_tokens' => $options['max_tokens'] ?? $agent['max_tokens']
        ]);
        
        if (isset($result['error'])) {
            return $result;
        }
        
        // Shrani asistentov odgovor
        $this->saveMessage($this->sessionId, $agentId, $apiKey['id'], 'assistant', $result['content'], $result['tokens']);
        
        // Posodobi porabo API ključa
        $this->updateApiUsage($apiKey['id'], $result['tokens']);
        
        return [
            'success' => true,
            'content' => $result['content'],
            'agent' => $agent['ime'],
            'api_key' => $apiKey['ime'],
            'tokens' => $result['tokens']
        ];
    }
    
    /**
     * Pogovor med dvema agentoma
     */
    public function pogovorMedAgenti($agent1Id, $agent2Id, $tema, $steviloIzmjen = 3, $apiKeyId = null) {
        $zgodovina = [];
        $trenutniAgent = $agent1Id;
        $zadnjiOdgovor = $tema;
        
        // Ustvari unikatni ID za ta pogovor
        $pogovorId = date('Ymd_His') . '_' . uniqid();
        
        for ($i = 0; $i < $steviloIzmjen; $i++) {
            $result = $this->generiraj(
                $zadnjiOdgovor, 
                $trenutniAgent, 
                $apiKeyId,
                ['temperature' => 0.8]
            );
            
            if (!$result['success']) {
                return ['error' => $result['error'], 'zgodovina' => $zgodovina];
            }
            
            $izmenjava = [
                'stevilka' => $i + 1,
                'agent' => $trenutniAgent,
                'ime_agenta' => $result['agent'],
                'odgovor' => $result['content'],
                'cas' => date('Y-m-d H:i:s')
            ];
            $zgodovina[] = $izmenjava;
            
            $zadnjiOdgovor = $result['content'];
            $trenutniAgent = ($trenutniAgent === $agent1Id) ? $agent2Id : $agent1Id;
        }
        
        // Shrani celoten pogovor v JSON
        $pogovorData = [
            'id' => $pogovorId,
            'tema' => $tema,
            'agenti' => [$agent1Id, $agent2Id],
            'stevilo_izmjen' => $steviloIzmjen,
            'zgodovina' => $zgodovina,
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        file_put_contents($this->dataPath . "/pogovori/{$pogovorId}.json", json_encode($pogovorData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        
        return [
            'success' => true,
            'zgodovina' => $zgodovina,
            'tema' => $tema,
            'pogovor_id' => $pogovorId
        ];
    }
    
    /**
     * Skupinsko reševanje problema
     */
    public function skupinskoResevanje($problem, $agenti = ['bloger', 'analitik', 'kreativec'], $apiKeyId = null) {
        $rezultati = [];
        
        foreach ($agenti as $agentId) {
            $result = $this->generiraj($problem, $agentId, $apiKeyId);
            if ($result['success']) {
                $rezultati[] = [
                    'agent' => $agentId,
                    'ime' => $result['agent'],
                    'odgovor' => $result['content'],
                    'cas' => date('Y-m-d H:i:s')
                ];
            }
        }
        
        // Če imamo vsaj 2 odgovora, naj jih analitik združi
        if (count($rezultati) >= 2) {
            $zdruzenPrompt = "Združi naslednje odgovore v enoten, koherenten zaključek. 
            Upoštevaj vse perspektive in naredi sintetiziran odgovor:\n\n";
            foreach ($rezultati as $r) {
                $zdruzenPrompt .= "=== {$r['ime']} ===\n{$r['odgovor']}\n\n";
            }
            
            $zdruzen = $this->generiraj($zdruzenPrompt, 'analitik', $apiKeyId, ['temperature' => 0.5]);
            if ($zdruzen['success']) {
                $rezultati['zdruzeno'] = $zdruzen['content'];
            }
        }
        
        // Shrani skupinsko rešitev
        $skupinskoId = date('Ymd_His') . '_group_' . uniqid();
        $skupinskoData = [
            'id' => $skupinskoId,
            'problem' => $problem,
            'agenti' => $agenti,
            'rezultati' => $rezultati,
            'created_at' => date('Y-m-d H:i:s')
        ];
        file_put_contents($this->dataPath . "/pogovori/{$skupinskoId}.json", json_encode($skupinskoData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        
        return $rezultati;
    }
    
    /**
     * Pridobi zgodovino pogovorov
     */
    public function getHistory($sessionId = null, $agentId = null, $limit = 50) {
        $sessionId = $sessionId ?? $this->sessionId;
        $file = $this->dataPath . "/zgodovina/{$sessionId}.json";
        
        if (!file_exists($file)) {
            return [];
        }
        
        $data = json_decode(file_get_contents($file), true);
        if (!$data) return [];
        
        // Filtriraj po agentu
        if ($agentId) {
            $data = array_filter($data, function($msg) use ($agentId) {
                return $msg['agent_id'] === $agentId;
            });
        }
        
        // Vrni zadnjih $limit sporočil
        $data = array_slice($data, -$limit);
        return array_reverse($data);
    }
    
    /**
     * Pridobi vse seje (pogovore)
     */
    public function getAllSessions() {
        $files = glob($this->dataPath . "/zgodovina/*.json");
        $sessions = [];
        
        foreach ($files as $file) {
            $sessionId = basename($file, '.json');
            $data = json_decode(file_get_contents($file), true);
            if ($data) {
                $sessions[] = [
                    'session_id' => $sessionId,
                    'prvic' => $data[0]['cas'] ?? 'N/A',
                    'zadnjic' => end($data)['cas'] ?? 'N/A',
                    'sporocil' => count($data)
                ];
            }
        }
        
        // Sortiraj po zadnjem času
        usort($sessions, function($a, $b) {
            return strtotime($b['zadnjic']) - strtotime($a['zadnjic']);
        });
        
        return $sessions;
    }
    
    /**
     * Pridobi shranjene pogovore med agenti
     */
    public function getSavedConversations() {
        $files = glob($this->dataPath . "/pogovori/*.json");
        $conversations = [];
        
        foreach ($files as $file) {
            $data = json_decode(file_get_contents($file), true);
            if ($data) {
                $conversations[] = $data;
            }
        }
        
        // Sortiraj po času
        usort($conversations, function($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });
        
        return $conversations;
    }
    
    /**
     * Izbriši zgodovino seje
     */
    public function deleteSession($sessionId) {
        $file = $this->dataPath . "/zgodovina/{$sessionId}.json";
        if (file_exists($file)) {
            return unlink($file);
        }
        return false;
    }
    
    /**
     * Izbriši pogovor
     */
    public function deleteConversation($conversationId) {
        $file = $this->dataPath . "/pogovori/{$conversationId}.json";
        if (file_exists($file)) {
            return unlink($file);
        }
        return false;
    }
    
    /**
     * Izvozi vse podatke v en JSON
     */
    public function exportAllData() {
        $export = [
            'export_date' => date('Y-m-d H:i:s'),
            'api_keys' => $this->getAllApiKeys(),
            'agents' => $this->getAllAgents(),
            'sessions' => [],
            'conversations' => $this->getSavedConversations()
        ];
        
        // Dodaj vse seje
        $sessions = $this->getAllSessions();
        foreach ($sessions as $session) {
            $export['sessions'][$session['session_id']] = $this->getHistory($session['session_id']);
        }
        
        $filename = $this->dataPath . "/export_" . date('Ymd_His') . ".json";
        file_put_contents($filename, json_encode($export, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        
        return $filename;
    }
    
    /**
     * Pridobi statistiko API ključev
     */
    public function getApiStats() {
        return $this->getAllApiKeys();
    }
    
    /**
     * Dodaj nov API ključ
     */
    public function addApiKey($ime, $apiKey, $model = 'deepseek-chat') {
        $keys = $this->getAllApiKeys();
        $newId = count($keys) + 1;
        
        $keys[] = [
            'id' => $newId,
            'ime' => $ime,
            'api_key' => $apiKey,
            'bazni_url' => 'https://api.deepseek.com',
            'model' => $model,
            'aktivno' => true,
            'porabljeno_danes' => 0,
            'zadnja_uporaba' => null,
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        file_put_contents($this->dataPath . "/api_kljuci/keys.json", json_encode($keys, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        return true;
    }
    
    /**
     * Posodobi API ključ
     */
    public function updateApiKey($id, $data) {
        $keys = $this->getAllApiKeys();
        foreach ($keys as &$key) {
            if ($key['id'] == $id) {
                foreach ($data as $k => $v) {
                    $key[$k] = $v;
                }
                break;
            }
        }
        file_put_contents($this->dataPath . "/api_kljuci/keys.json", json_encode($keys, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        return true;
    }
    
    // ==================== PRIVATE METODE ====================
    
    private function getAgent($agentId) {
        $agents = $this->getAllAgents();
        return $agents[$agentId] ?? null;
    }
    
    private function getAllAgents() {
        $file = $this->dataPath . "/agenti/agenti.json";
        if (!file_exists($file)) {
            $this->createDefaultAgents();
        }
        return json_decode(file_get_contents($file), true);
    }
    
    private function createDefaultAgents() {
        $agents = [
            'bloger' => [
                'id' => 'bloger',
                'ime' => '📝 Blog pisec',
                'opis' => 'Specializiran za pisanje blog člankov',
                'system_prompt' => 'Ti si profesionalni blog pisec. Pišeš privlačne, informativne in SEO-optimizirane članke v slovenskem jeziku.',
                'temperatura' => 0.8,
                'max_tokens' => 2500,
                'aktivno' => true
            ],
            'analitik' => [
                'id' => 'analitik',
                'ime' => '🔍 Analitik',
                'opis' => 'Analizira vsebino in podatke',
                'system_prompt' => 'Ti si analitik. Analiziraš podano vsebino, iščeš vzorce, povzemaš in podaš priporočila.',
                'temperatura' => 0.5,
                'max_tokens' => 1500,
                'aktivno' => true
            ],
            'kreativec' => [
                'id' => 'kreativec',
                'ime' => '🎨 Kreativec',
                'opis' => 'Ustvarjalno pisanje in ideje',
                'system_prompt' => 'Ti si kreativni pisec. Ustvarjaš zanimive zgodbe, metafore in kreativne vsebine.',
                'temperatura' => 0.9,
                'max_tokens' => 2000,
                'aktivno' => true
            ],
            'urejevalec' => [
                'id' => 'urejevalec',
                'ime' => '✏️ Urejevalec',
                'opis' => 'Lektorira in izboljšuje besedila',
                'system_prompt' => 'Ti si profesionalni lektor. Popravljaš slovnico, slog in berljivost besedil.',
                'temperatura' => 0.3,
                'max_tokens' => 1000,
                'aktivno' => true
            ]
        ];
        file_put_contents($this->dataPath . "/agenti/agenti.json", json_encode($agents, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }
    
    private function getApiKey($id = null) {
        $keys = $this->getAllApiKeys();
        if ($id) {
            foreach ($keys as $key) {
                if ($key['id'] == $id && $key['aktivno']) {
                    return $key;
                }
            }
        }
        // Vrni prvi aktivni ključ
        foreach ($keys as $key) {
            if ($key['aktivno']) {
                return $key;
            }
        }
        return null;
    }
    
    private function getAllApiKeys() {
        $file = $this->dataPath . "/api_kljuci/keys.json";
        if (!file_exists($file)) {
            // Ustvari primer ključa
            $defaultKeys = [
                [
                    'id' => 1,
                    'ime' => 'DeepSeek Glavni',
                    'api_key' => 'your_deepseek_api_key_here',
                    'bazni_url' => 'https://api.deepseek.com',
                    'model' => 'deepseek-chat',
                    'aktivno' => true,
                    'porabljeno_danes' => 0,
                    'zadnja_uporaba' => null,
                    'created_at' => date('Y-m-d H:i:s')
                ]
            ];
            file_put_contents($file, json_encode($defaultKeys, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            return $defaultKeys;
        }
        return json_decode(file_get_contents($file), true);
    }
    
    private function saveMessage($sessionId, $agentId, $apiKeyId, $role, $content, $tokens = 0) {
        $file = $this->dataPath . "/zgodovina/{$sessionId}.json";
        
        $history = [];
        if (file_exists($file)) {
            $history = json_decode(file_get_contents($file), true);
        }
        
        $history[] = [
            'id' => uniqid(),
            'session_id' => $sessionId,
            'agent_id' => $agentId,
            'api_key_id' => $apiKeyId,
            'vloga' => $role,
            'vsebina' => $content,
            'tokens_uporabljeni' => $tokens,
            'cas' => date('Y-m-d H:i:s')
        ];
        
        // Ohrani samo zadnjih 500 sporočil na sejo
        if (count($history) > 500) {
            $history = array_slice($history, -500);
        }
        
        file_put_contents($file, json_encode($history, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        return true;
    }
    
    private function callDeepSeekAPI($apiKey, $messages, $options) {
        $data = [
            'model' => $apiKey['model'],
            'messages' => $messages,
            'temperature' => $options['temperature'] ?? 0.7,
            'max_tokens' => $options['max_tokens'] ?? 2000,
            'stream' => false
        ];
        
        $ch = curl_init($apiKey['bazni_url'] . '/v1/chat/completions');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $apiKey['api_key']
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode === 200) {
            $result = json_decode($response, true);
            return [
                'content' => $result['choices'][0]['message']['content'],
                'tokens' => $result['usage']['total_tokens'] ?? 0
            ];
        }
        
        return ['error' => "API napaka: $httpCode - " . substr($response, 0, 200)];
    }
    
    private function updateApiUsage($apiKeyId, $tokens) {
        $keys = $this->getAllApiKeys();
        foreach ($keys as &$key) {
            if ($key['id'] == $apiKeyId) {
                $key['porabljeno_danes'] += $tokens;
                $key['zadnja_uporaba'] = date('Y-m-d H:i:s');
                break;
            }
        }
        file_put_contents($this->dataPath . "/api_kljuci/keys.json", json_encode($keys, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }
}
?>