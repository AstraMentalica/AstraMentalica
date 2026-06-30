<?php
/**
 * povezavaAi.PHP - Glavni razred za komunikacijo z AI
 * @author Orakleum
 * @datum 2024-01-15
 */

require_once POT_AI . 'astraMentor/pomocnikAi.php';

class PovezavaAI {
    private $kljuc_deepseek;
    private $kljuc_gemini;
    
    public function __construct() {
        $this->kljuc_deepseek = KLJUC_DEEPSEEK;
        $this->kljuc_gemini = KLJUC_GEMINI;
    }
    
    public function posljiSporocilo($sporocilo, $tip = 'splosno') {
        // Preveri varnost vnosa
        $sporocilo = ocistiVnos($sporocilo);
        
        switch ($tip) {
            case 'duhovno':
                return $this->duhovnoSvetovanje($sporocilo);
            case 'tarot':
                return $this->tarotInterpretacija($sporocilo);
            default:
                return $this->splosnoVprasanje($sporocilo);
        }
    }
    
    private function splosnoVprasanje($sporocilo) {
        $url = 'https://api.deepseek.com/v1/chat/completions';
        
        $podatki = [
            'model' => 'deepseek-chat',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'Si AstraMentor, pametni asistent. Odgovarjaj v slovenskem jeziku.'
                ],
                [
                    'role' => 'user', 
                    'content' => $sporocilo
                ]
            ],
            'max_tokens' => 1000,
            'temperature' => 0.7
        ];
        
        $odgovor = $this->posljiApiZahtevo($url, $podatki, $this->kljuc_deepseek);
        return $odgovor['choices'][0]['message']['content'] ?? 'Napaka pri obdelavi.';
    }
    
    private function posljiApiZahtevo($url, $podatki, $kljuc) {
        $curl = curl_init();
        
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($podatki),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $kljuc
            ],
            CURLOPT_TIMEOUT => 30
        ]);
        
        $odgovor = curl_exec($curl);
        curl_close($curl);
        
        return json_decode($odgovor, true);
    }
}
?>