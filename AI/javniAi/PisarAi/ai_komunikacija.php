<?php
// ai_komunikacija.php
// Razred za komunikacijo z AI API-jem

class AIKomunikacija {
    private $api_kljuc;
    private $osnovni_url = "https://api.openai.com/v1/chat/completions";
    
    public function __construct($api_kljuc) {
        $this->api_kljuc = $api_kljuc;
    }
    
    // Generiraj idejo za clanek
    public function generirajIdejo($tema) {
        $sporocilo = "Generiraj 5 zanimivih idej za blog clanek na temo: " . $tema . ". Odgovori v slovenscini.";
        
        $odgovor = $this->posljiZahtevek($sporocilo);
        return $this->obdelajOdgovor($odgovor);
    }
    
    // Generiraj celoten clanek
    public function generirajClanek($naslov, $tema) {
        $sporocilo = "Napis blog clanek z naslovom: '" . $naslov . "' na temo: " . $tema . ". Clanek naj bo pisan v slovenscini, dolg vsaj 800 besed, strukturiran z uvodom, glavnim delom in zakljuckom. Vkljucuj podnaslove in specificne primere.";
        
        $odgovor = $this->posljiZahtevek($sporocilo);
        return $this->obdelajOdgovor($odgovor);
    }
    
    // Generiraj kratko raziskavo
    public function generirajRaziskavo($tema) {
        $sporocilo = "Naredi kratko raziskavo na temo: " . $tema . ". Poudari kljucne tocke, trende in priporocila. Odgovori v slovenscini, strukturiraj v poglavja.";
        
        $odgovor = $this->posljiZahtevek($sporocilo);
        return $this->obdelajOdgovor($odgovor);
    }
    
    // Poslje zahtevek k API-ju
    private function posljiZahtevek($sporocilo) {
        $podatki = [
            "model" => "gpt-3.5-turbo",
            "messages" => [
                [
                    "role" => "user",
                    "content" => $sporocilo
                ]
            ],
            "max_tokens" => 2000,
            "temperature" => 0.7
        ];
        
        $nastavitve = [
            'http' => [
                'header' => "Content-Type: application/json\r\n" .
                           "Authorization: Bearer " . $this->api_kljuc . "\r\n",
                'method' => 'POST',
                'content' => json_encode($podatki)
            ]
        ];
        
        $kontekst = stream_context_create($nastavitve);
        $odgovor = file_get_contents($this->osnovni_url, false, $kontekst);
        
        return json_decode($odgovor, true);
    }
    
    // Obdelaj odgovor od AI-ja
    private function obdelajOdgovor($odgovor) {
        if (isset($odgovor['choices'][0]['message']['content'])) {
            return $odgovor['choices'][0]['message']['content'];
        } else {
            return "Napaka pri generiranju vsebine: " . json_encode($odgovor);
        }
    }
}

// Razsiritev glavnega razreda z AI funkcionalnostjo
class BlogAvtomatizacija {
    // ... prejsnje funkcije ...
    
    private $ai_komunikacija;
    
    public function nastaviAIKomunikacijo($api_kljuc) {
        $this->ai_komunikacija = new AIKomunikacija($api_kljuc);
    }
    
    // Generiraj in shrani clanke
    public function generirajInShraniClanke() {
        $ideje = $this->ai_komunikacija->generirajIdejo($this->tema);
        
        // Shrani ideje v bazo
        $this->shraniIdeje($ideje);
        
        // Generiraj clanke za vsako idejo
        $ideje_array = explode("\n", $ideje);
        foreach ($ideje_array as $ideja) {
            if (strlen(trim($ideja)) > 10) {
                $clanek = $this->ai_komunikacija->generirajClanek(trim($ideja), $this->tema);
                $this->shraniClanek(trim($ideja), $clanek, $this->tema);
            }
        }
    }
    
    // Shrani clanek v bazo
    private function shraniClanek($naslov, $vsebina, $tema) {
        $poizvedba = $this->povezava_baze->prepare("INSERT INTO clanki (naslov, vsebina, tema) VALUES (?, ?, ?)");
        $poizvedba->execute([$naslov, $vsebina, $tema]);
        return $this->povezava_baze->lastInsertId();
    }
    
    // Shrani ideje v bazo
    private function shraniIdeje($ideje) {
        $poizvedba = $this->povezava_baze->prepare("INSERT INTO nastavitve_blog (naziv_nastavitve, vrednost) VALUES ('zadnje_ideje', ?)");
        $poizvedba->execute([$ideje]);
    }
}

// Uporaba
$avtomatizacija->nastaviAIKomunikacijo($api_kljuc);
?>