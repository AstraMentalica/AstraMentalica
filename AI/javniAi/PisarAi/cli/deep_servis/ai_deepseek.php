<?php
/**
 * DeepSeek AI Servis - Multi-API, Multi-Agent, z zgodovino
 */

class DeepSeekAI {
    private $db;
    private $sessionId;
    
    public function __construct() {
        $this->db = astra_db_povezava();
        $this->sessionId = session_id() . '_' . date('Ymd');
    }
    
    /**
     * Generiraj odgovor z DeepSeek API
     */
    public function generiraj($prompt, $agentId = 'bloger', $apiKeyId = null, $options = []) {
        // Pridobi agenta
        $agent = $this->getAgent($agentId);
        if (!$agent) {
            return ['error' => 'Agent ne obstaja'];
        }
        
        // Pridobi API ključ
        $apiKey = $this->getApiKey($apiKeyId ?? $agent['default_api_key_id']);
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
            
            $zgodovina[] = [
                'agent' => $trenutniAgent,
                'ime_agenta' => $result['agent'],
                'odgovor' => $result['content']
            ];
            
            $zadnjiOdgovor = $result['content'];
            $trenutniAgent = ($trenutniAgent === $agent1Id) ? $agent2Id : $agent1Id;
        }
        
        return [
            'success' => true,
            'zgodovina' => $zgodovina,
            'tema' => $tema
        ];
    }
    
    /**
     * Skupinsko reševanje problema (več agentov hkrati)
     */
    public function skupinskoResevanje($problem, $agenti = ['bloger', 'analitik', 'kreativec'], $apiKeyId = null) {
        $rezultati = [];
        
        foreach ($agenti as $agentId) {
            $result = $this->generiraj($problem, $agentId, $apiKeyId);
            if ($result['success']) {
                $rezultati[] = [
                    'agent' => $agentId,
                    'ime' => $result['agent'],
                    'odgovor' => $result['content']
                ];
            }
        }
        
        // Če imamo vsaj 2 odgovora, naj jih analitik združi
        if (count($rezultati) >= 2) {
            $zdruzenPrompt = "Združi naslednje odgovore v enoten, koherenten zaključek:\n\n";
            foreach ($rezultati as $r) {
                $zdruzenPrompt .= "=== {$r['ime']} ===\n{$r['odgovor']}\n\n";
            }
            
            $zdruzen = $this->generiraj($zdruzenPrompt, 'analitik', $apiKeyId, ['temperature' => 0.5]);
            if ($zdruzen['success']) {
                $rezultati['zdruzeno'] = $zdruzen['content'];
            }
        }
        
        return $rezultati;
    }
    
    /**
     * Pridobi zgodovino pogovorov
     */
    public function getHistory($sessionId = null, $agentId = null, $limit = 50) {
        $sessionId = $sessionId ?? $this->sessionId;
        $sql = "SELECT * FROM ai_zgodovina WHERE session_id = ?";
        $params = [$sessionId];
        
        if ($agentId) {
            $sql .= " AND agent_id = ?";
            $params[] = $agentId;
        }
        
        $sql .= " ORDER BY cas DESC LIMIT ?";
        $params[] = $limit;
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return array_reverse($results);
    }
    
    /**
     * Pridobi vse seje (pogovore)
     */
    public function getAllSessions() {
        $sql = "SELECT DISTINCT session_id, MIN(cas) as prvic, MAX(cas) as zadnjic, COUNT(*) as sporocil 
                FROM ai_zgodovina 
                GROUP BY session_id 
                ORDER BY zadnjic DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Izbriši zgodovino seje
     */
    public function deleteSession($sessionId) {
        $stmt = $this->db->prepare("DELETE FROM ai_zgodovina WHERE session_id = ?");
        return $stmt->execute([$sessionId]);
    }
    
    /**
     * Pridobi statistiko API ključev
     */
    public function getApiStats() {
        $sql = "SELECT * FROM ai_api_kljuci ORDER BY id";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Dodaj nov API ključ
     */
    public function addApiKey($ime, $apiKey, $model = 'deepseek-chat') {
        $stmt = $this->db->prepare("INSERT INTO ai_api_kljuci (ime, api_key, model) VALUES (?, ?, ?)");
        return $stmt->execute([$ime, $apiKey, $model]);
    }
    
    /**
     * Posodobi API ključ
     */
    public function updateApiKey($id, $data) {
        $set = [];
        $params = [];
        foreach ($data as $key => $value) {
            $set[] = "$key = ?";
            $params[] = $value;
        }
        $params[] = $id;
        $sql = "UPDATE ai_api_kljuci SET " . implode(', ', $set) . " WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }
    
    // ==================== PRIVATE METODE ====================
    
    private function getAgent($agentId) {
        $stmt = $this->db->prepare("SELECT * FROM ai_agenti WHERE id = ? AND aktivno = 1");
        $stmt->execute([$agentId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    private function getApiKey($id) {
        $stmt = $this->db->prepare("SELECT * FROM ai_api_kljuci WHERE id = ? AND aktivno = 1");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    private function saveMessage($sessionId, $agentId, $apiKeyId, $role, $content, $tokens = 0, $parentId = null) {
        $stmt = $this->db->prepare("
            INSERT INTO ai_zgodovina (session_id, agent_id, api_key_id, vloga, vsebina, odgovor_na, tokens_uporabljeni, cas) 
            VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
        ");
        return $stmt->execute([$sessionId, $agentId, $apiKeyId, $role, $content, $parentId, $tokens]);
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
        
        return ['error' => "API napaka: $httpCode - $response"];
    }
    
    private function updateApiUsage($apiKeyId, $tokens) {
        $stmt = $this->db->prepare("
            UPDATE ai_api_kljuci 
            SET porabljeno_danes = porabljeno_danes + ?, 
                zadnja_uporaba = NOW() 
            WHERE id = ?
        ");
        return $stmt->execute([$tokens, $apiKeyId]);
    }
}
?>