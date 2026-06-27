<?php
class ObdelovalnikAI {
    private $deepseek_kljuc;
    private $gemini_kljuc;
    
    public function __construct() {
        $this->deepseek_kljuc = defined('DEEPSEEK_API_KLJUC') ? DEEPSEEK_API_KLJUC : '';
        $this->gemini_kljuc = defined('GEMINI_API_KLJUC') ? GEMINI_API_KLJUC : '';
    }
    
    /**
     * Pošlje zahtevo k DeepSeek API-ju
     */
    public function klepetDeepSeek($sporocilo, $model = 'deepseek-chat') {
        // Zabeleži AI dejavnost
        $this->zabeleziAIDejavnost('deepseek_poizvedba', $sporocilo);
        
        if (empty($this->deepseek_kljuc)) {
            return ['napaka' => 'DeepSeek API ključ ni nastavljen'];
        }
        
        $povezava = 'https://api.deepseek.com/v1/chat/completions';
        
        $podatki = [
            'model' => $model,
            'messages' => [
                ['role' => 'user', 'content' => $sporocilo]
            ],
            'max_tokens' => 2000,
            'temperature' => 0.7
        ];
        
        $moznosti = [
            'http' => [
                'header' => [
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . $this->deepseek_kljuc
                ],
                'method' => 'POST',
                'content' => json_encode($podatki)
            ]
        ];
        
        $kontekst = stream_context_create($moznosti);
        $odgovor = @file_get_contents($povezava, false, $kontekst);
        
        if ($odgovor === FALSE) {
            return ['napaka' => 'Napaka pri povezavi z DeepSeek API'];
        }
        
        $dekodiran_odgovor = json_decode($odgovor, true);
        
        if (isset($dekodiran_odgovor['choices'][0]['message']['content'])) {
            // Zabeleži uspešno poizvedbo
            $this->zabeleziAIDejavnost('deepseek_uspesno', $sporocilo, true);
            return ['uspesno' => true, 'odgovor' => $dekodiran_odgovor['choices'][0]['message']['content']];
        } else {
            // Zabeleži neuspešno poizvedbo
            $this->zabeleziAIDejavnost('deepseek_napaka', $sporocilo, false);
            return ['napaka' => 'Neveljaven odgovor od DeepSeek API'];
        }
    }
    
    /**
     * Pošlje zahtevo k Gemini API-ju
     */
    public function klepetGemini($sporocilo, $model = 'gemini-pro') {
        // Zabeleži AI dejavnost
        $this->zabeleziAIDejavnost('gemini_poizvedba', $sporocilo);
        
        if (empty($this->gemini_kljuc)) {
            return ['napaka' => 'Gemini API ključ ni nastavljen'];
        }
        
        $povezava = 'https://generativelanguage.googleapis.com/v1/models/' . $model . ':generateContent?key=' . $this->gemini_kljuc;
        
        $podatki = [
            'contents' => [
                'parts' => [
                    ['text' => $sporocilo]
                ]
            ]
        ];
        
        $moznosti = [
            'http' => [
                'header' => "Content-Type: application/json\r\n",
                'method' => 'POST',
                'content' => json_encode($podatki)
            ]
        ];
        
        $kontekst = stream_context_create($moznosti);
        $odgovor = @file_get_contents($povezava, false, $kontekst);
        
        if ($odgovor === FALSE) {
            return ['napaka' => 'Napaka pri povezavi z Gemini API'];
        }
        
        $dekodiran_odgovor = json_decode($odgovor, true);
        
        if (isset($dekodiran_odgovor['candidates'][0]['content']['parts'][0]['text'])) {
            // Zabeleži uspešno poizvedbo
            $this->zabeleziAIDejavnost('gemini_uspesno', $sporocilo, true);
            return ['uspesno' => true, 'odgovor' => $dekodiran_odgovor['candidates'][0]['content']['parts'][0]['text']];
        } else {
            // Zabeleži neuspešno poizvedbo
            $this->zabeleziAIDejavnost('gemini_napaka', $sporocilo, false);
            return ['napaka' => 'Neveljaven odgovor od Gemini API'];
        }
    }
    
    /**
     * Generira odgovor z najboljšim dostopnim AI modelom
     */
    public function generirajOdgovor($sporocilo) {
        // Zabeleži splošno AI dejavnost
        $this->zabeleziAIDejavnost('splosna_poizvedba', $sporocilo);
        
        // Najprej poskusi z DeepSeek
        $odgovor = $this->klepetDeepSeek($sporocilo);
        
        if (isset($odgovor['uspesno'])) {
            return $odgovor;
        }
        
        // Če DeepSeek ne deluje, poskusi z Gemini
        $odgovor = $this->klepetGemini($sporocilo);
        
        if (isset($odgovor['uspesno'])) {
            return $odgovor;
        }
        
        // Če noben API ne deluje, vrni napako
        $this->zabeleziAIDejavnost('vse_napake', $sporocilo, false);
        return ['napaka' => 'Vsi AI modeli so trenutno nedosegljivi'];
    }
    
    /**
     * Specializirana funkcija za duhovne odgovore
     */
    public function duhovniSvetovalec($vprasanje) {
        $kontekst = "Si duhovni svetovalec z imenom Astra. Odgovarjaj na področju duhovnosti, meditacije, 
        osebne rasti in vizionarstva. Bodi podporen, moder in navdihujoč. Uporabljaj slovenski jezik.";
        
        $popolno_vprasanje = $kontekst . "\n\nVprašanje: " . $vprasanje;
        
        // Zabeleži specializirano AI dejavnost
        $this->zabeleziAIDejavnost('duhovno_svetovanje', $vprasanje);
        
        return $this->generirajOdgovor($popolno_vprasanje);
    }
    
    /**
     * Specializirana funkcija za tarot interpretacije
     */
    public function tarotInterpretacija($karte) {
        $kontekst = "Si strokovnjak za tarot karte. Razlagi pomen kart in njihovo povezavo. 
        Bodi intuitiven, moderen in podporen. Uporabljaj slovenski jezik.";
        
        $vprasanje = $kontekst . "\n\nRazloži tarot karte: " . $karte;
        
        // Zabeleži specializirano AI dejavnost
        $this->zabeleziAIDejavnost('tarot_interpretacija', $karte);
        
        return $this->generirajOdgovor($vprasanje);
    }
    
    /**
     * Zabeleži AI dejavnost v dnevnik
     */
    private function zabeleziAIDejavnost($tip, $sporocilo, $uspesno = null) {
        // Preveri, ali funkcija za beleženje obstaja
        if (function_exists('zapisi_ai_dejavnost')) {
            $status = $uspesno !== null ? ($uspesno ? 'uspešno' : 'neuspešno') : 'poizvedba';
            $polno_sporocilo = $tip . ' (' . $status . '): ' . substr($sporocilo, 0, 200);
            
            zapisi_ai_dejavnost($tip, $polno_sporocilo, $_SESSION['uporabnik_id'] ?? null);
        }
    }
}
?>