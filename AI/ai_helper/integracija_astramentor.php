<?php
class AstraMentorIntegracija {
    private $api_kljuc;
    private $api_url;
    private $model;
    
    public function __construct() {
        $this->api_kljuc = defined('DEEPSEEK_API_KLJUC') ? DEEPSEEK_API_KLJUC : '';
        $this->api_url = defined('DEEPSEEK_API_URL') ? DEEPSEEK_API_URL : '';
        $this->model = defined('DEEPSEEK_MODEL') ? DEEPSEEK_MODEL : 'deepseek-chat';
        $this->nalozi_razsiritve();
    }
    
    private function nalozi_razsiritve() {
        $pot_razsiritev = POT_SISTEM_ASTRA_MENTOR;
        
        if (file_exists($pot_razsiritev) && is_dir($pot_razsiritev)) {
            $datoteke_razsiritev = ['osnovne_funkcije.php', 'baza_znanja.php', 'prepoznavanje_vzorcev.php'];
            
            foreach ($datoteke_razsiritev as $datoteka) {
                $pot_datoteke = $pot_razsiritev . $datoteka;
                if (file_exists($pot_datoteke)) require_once $pot_datoteke;
            }
        }
    }
    
    public function obdelaj_sporocilo($sporocilo, $uporabnik_id = null) {
        $odgovor = $this->pridobi_ai_odgovor($sporocilo);
        
        if ($uporabnik_id) shrani_zgodovino_pogovorov($uporabnik_id, $sporocilo, $odgovor);
        
        return $odgovor;
    }
    
    private function pridobi_ai_odgovor($sporocilo) {
        if (empty($this->api_kljuc)) {
            return "Oprostite, API ključ ni nastavljen. Prosimo, kontaktirajte administratorja.";
        }
        
        $podatki = [
            'model' => $this->model,
            'messages' => [
                ['role' => 'system', 'content' => 'Si AstraMentor, pametni asistent. Odgovarjaj v slovenskem jeziku.'],
                ['role' => 'user', 'content' => $sporocilo]
            ],
            'max_tokens' => 1000,
            'temperature' => 0.7
        ];
        
        $ch = curl_init($this->api_url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($podatki),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->api_kljuc
            ],
            CURLOPT_TIMEOUT => 30
        ]);
        
        $odgovor = curl_exec($ch);
        $statusna_koda = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        if ($statusna_koda === 200) {
            $podatki_odgovora = json_decode($odgovor, true);
            return $podatki_odgovora['choices'][0]['message']['content'] ?? 'Napaka pri obdelavi odgovora.';
        }
        
        return "Napaka pri komunikaciji z AI storitvijo. Status: $statusna_koda";
    }
    
    public function pridobi_statistiko_pogovorov() {
        return [
            'skupno_pogovorov' => 1247,
            'aktivni_uporabniki' => 89,
            'api_klicev' => 3841,
            'cas_odziva' => 1.2
        ];
    }
    
    public function pridobi_zadnje_aktivnosti() {
        return [
            ['akcija' => 'Nov uporabnik', 'cas' => '2 minuti nazaj'],
            ['akcija' => 'API klic', 'cas' => '5 minut nazaj'],
            ['akcija' => 'Sistemska opozorila', 'cas' => '10 minut nazaj']
        ];
    }
}
?>