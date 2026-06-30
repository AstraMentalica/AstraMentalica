<?php
/**
 * Codex Damiris - AI Sistem
 * Lokacija: /var/www/html/codex-damiris/AI_CodexDamiris.php
 */

class AI_CodexDamiris {
    private $pdo;
    private $apiKljuc;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->apiKljuc = getenv('CODEX_AI_API_KLJUC') ?: 'tvoj-ai-api-kljuc';
    }
    
    /**
     * Generiraj vsebino s pomočjo AI
     */
    public function generirajVsebino($tema, $stil = "mistichno", $uporabnikNivo) {
        if (!CodexDamirisJedro::imaDovoljenje($uporabnikNivo, 'ai_dostop')) {
            return ['uspeh' => false, 'napaka' => 'Nimate dostopa do AI funkcij'];
        }
        
        $prompt = $this->pripraviPrompt($tema, $stil);
        $odgovor = $this->kliciAI($prompt);
        
        return [
            'uspeh' => true,
            'vsebina' => $odgovor
        ];
    }
    
    /**
     * Analiziraj vsebino z AI
     */
    public function analizirajVsebino($vsebina, $uporabnikNivo) {
        if (!CodexDamirisJedro::imaDovoljenje($uporabnikNivo, 'ai_dostop')) {
            return ['uspeh' => false, 'napaka' => 'Nimate dostopa do AI funkcij'];
        }
        
        $prompt = "
        Kot strokovnjak za ezoteriko analiziraj to vsebino:
        
        {$vsebina}
        
        Prosim za analizo v naslednjih področjih:
        - Tematska ustreznost
        - Duhovna globina  
        - Priporočila za izboljšave
        - Povezane teme
        
        Odgovori v JSON formatu.
        ";
        
        $analiza = $this->kliciAI($prompt);
        
        return [
            'uspeh' => true,
            'analiza' => $analiza
        ];
    }
    
    /**
     * Chat s Codex AI
     */
    public function chat($sporocilo, $uporabnikNivo) {
        if (!CodexDamirisJedro::imaDovoljenje($uporabnikNivo, 'ai_dostop')) {
            return ['uspeh' => false, 'napaka' => 'Nimate dostopa do AI funkcij'];
        }
        
        $zgodovina = $this->pridobiZgodovinoPogovora();
        
        $prompt = "
        Si Codex AI - pametni asistent za ezoteriko in duhovno modrost.
        Odgovarjaj v slovenskem jeziku, bodi podporen in informativen.
        
        Zgodovina pogovora:
        {$zgodovina}
        
        Novo sporočilo: {$sporocilo}
        ";
        
        $odgovor = $this->kliciAI($prompt);
        
        $this->shraniVZgodovino($sporocilo, $odgovor);
        
        return [
            'uspeh' => true,
            'odgovor' => $odgovor
        ];
    }
    
    /**
     * Priporoči vsebine
     */
    public function priporociVsebine($uporabnikId) {
        $zgodovina = $this->pridobiZgodovinoPogovora();
        $zaznamki = $this->pridobiZaznamkeUporabnika($uporabnikId);
        
        $prompt = "
        Na podlagi uporabnikove zgodovine in zaznamkov priporoči relevantne vsebine:
        
        Zgodovina: {$zgodovina}
        Zaznamki: {$zaznamki}
        
        Priporoči 5 vsebin iz Codex Damiris v JSON formatu.
        ";
        
        $priporocila = $this->kliciAI($prompt);
        
        return [
            'uspeh' => true,
            'priporocila' => $priporocila
        ];
    }
    
    /**
     * Kliči AI API
     */
    private function kliciAI($prompt) {
        // V razvojnem okolju vrnemo testne podatke
        if ($_SERVER['HTTP_HOST'] == 'localhost') {
            return $this->simulirajAI($prompt);
        }
        
        $podatki = [
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'Si strokovnjak za ezoteriko. Odgovarjaš v slovenskem jeziku.'
                ],
                [
                    'role' => 'user', 
                    'content' => $prompt
                ]
            ],
            'max_tokens' => 1000,
            'temperature' => 0.7
        ];
        
        $moznosti = [
            'http' => [
                'method' => 'POST',
                'header' => [
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . $this->apiKljuc
                ],
                'content' => json_encode($podatki)
            ]
        ];
        
        $kontekst = stream_context_create($moznosti);
        $odgovor = @file_get_contents('https://api.openai.com/v1/chat/completions', false, $kontekst);
        
        if ($odgovor === false) {
            throw new Exception('Napaka pri komunikaciji z AI');
        }
        
        $podatkiOdgovora = json_decode($odgovor, true);
        return $podatkiOdgovora['choices'][0]['message']['content'] ?? 'Napaka pri AI odgovoru';
    }
    
    /**
     * Pripravi prompt za generiranje
     */
    private function pripraviPrompt($tema, $stil) {
        return "
        Ustvari ezoterično vsebino na temo: {$tema}
        Stil: {$stil}
        
        Vsebina naj vključuje:
        - Duhovno poglobitev
        - Praktične vpoglede
        - Starodavno modrost
        - Navdihujoč zaključek
        
        Bodi avtentičen in globok.
        ";
    }
    
    /**
     * Simuliraj AI za razvoj
     */
    private function simulirajAI($prompt) {
        if (strpos($prompt, 'generiraj') !== false) {
            return json_encode([
                'naslov' => 'Simulirana AI Vsebina',
                'vsebina' => "To je simulirana AI vsebina za razvojne namene.\n\nTo je globoka ezoterična vsebina, ki razkriva skrite resnice o vesolju in človeški duši.",
                'kljucne_besede' => ['simulacija', 'razvoj', 'ezoterika']
            ]);
        }
        
        return "Simuliran AI odgovor za razvoj. V produkciji bi bil to pravi odgovor.";
    }
    
    private function pridobiZgodovinoPogovora() {
        return $_SESSION['codex_ai_zgodovina'] ?? '';
    }
    
    private function shraniVZgodovino($vprasanje, $odgovor) {
        $_SESSION['codex_ai_zgodovina'] .= "U: {$vprasanje}\nA: {$odgovor}\n\n";
    }
    
    private function pridobiZaznamkeUporabnika($uporabnikId) {
        $stmt = $this->pdo->prepare(
            "SELECT v.naslov FROM zaznamki z 
             JOIN vsebine v ON z.vsebina_id = v.id 
             WHERE z.uporabnik_id = ? 
             ORDER BY z.cas DESC LIMIT 10"
        );
        $stmt->execute([$uporabnikId]);
        $zaznamki = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        return implode(', ', $zaznamki);
    }
}
?>