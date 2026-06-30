<?php
// optimizacija_tokenov.php
// Optimizacija za zmanjšanje porabe tokenov in odpravljanje napak

class OptimizacijaTokenov {
    private $blog_sistem;
    private $max_tokens_na_zahtevek = 4000; // Nova varna meja
    
    public function __construct($blog_sistem) {
        $this->blog_sistem = $blog_sistem;
    }
    
    // Optimizirana generacija idej z manjšimi zahtevami
    public function optimizirajGeneriranjeIdej($tema) {
        $sporocilo = "Generiraj 3 konkretne ideje za blog na temo: " . $tema . ". Odgovori kratko, samo naslove idej, ločene z |. Slovenscina.";
        
        $odgovor = $this->posljiOptimiziranZahtevek($sporocilo, 500);
        return $this->razcleniIdeje($odgovor);
    }
    
    // Optimizirana generacija članka po delih
    public function generirajClanekPoDelih($naslov, $tema) {
        $celoten_clanek = "";
        
        // 1. Uvod
        $sporocilo_uvod = "Napiši uvod za blog članek z naslovom: '" . $naslov . "'. Tema: " . $tema . ". Naj bo kratek, privlačen, do 150 besed. Slovenscina.";
        $uvod = $this->posljiOptimiziranZahtevek($sporocilo_uvod, 800);
        $celoten_clanek .= $uvod . "\n\n";
        
        // 2. Glavni del - po podpoglavjih
        $podpoglavja = ["Ključne točke", "Primeri iz prakse", "Koristi in izzivi"];
        
        foreach ($podpoglavja as $podpoglavje) {
            $sporocilo_del = "Napiši poglavje '" . $podpoglavje . "' za članek '" . $naslov . "'. Tema: " . $tema . ". Naj bo do 200 besed, konkretno. Slovenscina.";
            $del = $this->posljiOptimiziranZahtevek($sporocilo_del, 1000);
            $celoten_clanek .= "## " . $podpoglavje . "\n" . $del . "\n\n";
        }
        
        // 3. Zaključek
        $sporocilo_zakljucek = "Napiši zaključek za članek '" . $naslov . "'. Povzemi glavne točke in daj klic k akciji. Do 150 besed. Slovenscina.";
        $zakljucek = $this->posljiOptimiziranZahtevek($sporocilo_zakljucek, 800);
        $celoten_clanek .= $zakljucek;
        
        return $celoten_clanek;
    }
    
    // Optimizirana raziskava
    public function generirajOptimiziranoRaziskavo($tema) {
        $sporocilo = "Naredi kratko analizo teme: " . $tema . ". Poudari 3-4 ključne točke. Naj bo zgoščeno, do 400 besed. Slovenscina.";
        
        return $this->posljiOptimiziranZahtevek($sporocilo, 1500);
    }
    
    // Optimiziran zahtevek z nadzorom tokenov
    private function posljiOptimiziranZahtevek($sporocilo, $max_tokens = null) {
        if ($max_tokens === null) {
            $max_tokens = $this->max_tokens_na_zahtevek;
        }
        
        $podatki = [
            "model" => "gpt-3.5-turbo",
            "messages" => [
                [
                    "role" => "user",
                    "content" => $sporocilo
                ]
            ],
            "max_tokens" => min($max_tokens, $this->max_tokens_na_zahtevek),
            "temperature" => 0.7
        ];
        
        $this->zabeleziPoraboTokenov($sporocilo, $max_tokens);
        
        try {
            $odgovor = $this->blog_sistem->ai_komunikacija->posljiVarenZahtevek(
                json_encode($podatki),
                $this->blog_sistem->pridobiVarnostniSistem()
            );
            
            $dekodiran_odgovor = json_decode($odgovor, true);
            
            if (isset($dekodiran_odgovor['choices'][0]['message']['content'])) {
                return $dekodiran_odgovor['choices'][0]['message']['content'];
            } else {
                throw new Exception("Napaka v odgovoru AI: " . json_encode($dekodiran_odgovor));
            }
            
        } catch (Exception $e) {
            $this->obdelajNapakoTokenov($e, $sporocilo);
            return "Napaka pri generiranju vsebine: " . $e->getMessage();
        }
    }
    
    // Razčleni ideje iz odgovora
    private function razcleniIdeje($odgovor) {
        $ideje = explode("|", $odgovor);
        $ciscene_ideje = [];
        
        foreach ($ideje as $ideja) {
            $ideja = trim($ideja);
            if (strlen($ideja) > 10 && !empty($ideja)) {
                $ciscene_ideje[] = $ideja;
            }
        }
        
        return array_slice($ciscene_ideje, 0, 3); // Največ 3 ideje
    }
    
    // Zabeleži porabo tokenov
    private function zabeleziPoraboTokenov($sporocilo, $max_tokens) {
        $dolzina_sporocila = strlen($sporocilo);
        $ocenjeni_tokens = intval($dolzina_sporocila / 4); // Groba ocena
        
        $podatki = [
            'sporocilo' => substr($sporocilo, 0, 100) . "...",
            'dolzina_sporocila' => $dolzina_sporocila,
            'ocenjeni_tokens' => $ocenjeni_tokens,
            'max_tokens' => $max_tokens,
            'datum' => date('Y-m-d H:i:s')
        ];
        
        $this->shraniPoraboTokenov($podatki);
    }
    
    // Shrani podatke o porabi tokenov
    private function shraniPoraboTokenov($podatki) {
        try {
            $poizvedba = $this->blog_sistem->povezava_baze->prepare(
                "INSERT INTO nastavitve_blog (naziv_nastavitve, vrednost) VALUES ('poraba_tokenov', ?)"
            );
            $poizvedba->execute([json_encode($podatki)]);
        } catch (Exception $e) {
            // Prepreči neskončno zanko napak
            error_log("Napaka pri shranjevanju porabe tokenov: " . $e->getMessage());
        }
    }
    
    // Obdelaj napako s tokeni
    private function obdelajNapakoTokenov($napaka, $sporocilo) {
        $sporocilo_napake = "TOKEN NAPAKA: " . $napaka->getMessage() . " | Sporocilo: " . substr($sporocilo, 0, 200);
        
        // Zmanjšaj max_tokens za naslednje zahtevke
        $this->max_tokens_na_zahtevek = max(1000, $this->max_tokens_na_zahtevek - 500);
        
        $this->blog_sistem->pridobiVarnostniSistem()->obdelajNapako(
            new Exception($sporocilo_napake),
            "Optimizacija tokenov"
        );
    }
    
    // Pridobi statistiko porabe
    public function pridobiStatistikoPorabe() {
        $poizvedba = $this->blog_sistem->povezava_baze->query(
            "SELECT COUNT(*) as skupno_zahtevkov, 
                    AVG(JSON_EXTRACT(vrednost, '$.ocenjeni_tokens')) as povprecni_tokens
             FROM nastavitve_blog 
             WHERE naziv_nastavitve = 'poraba_tokenov'"
        );
        
        return $poizvedba->fetch(PDO::FETCH_ASSOC);
    }
}

// Nadgradnja AI komunikacije za OpenRouter
class AIKomunikacija {
    private $api_kljuc;
    private $osnovni_url = "https://openrouter.ai/api/v1/chat/completions";
    private $model = "openai/gpt-3.5-turbo"; // Privzeti model za OpenRouter
    
    public function __construct($api_kljuc) {
        $this->api_kljuc = $api_kljuc;
    }
    
    // Posodobljen zahtevek za OpenRouter
    public function posljiZahtevek($sporocilo, $max_tokens = 2000) {
        $podatki = [
            "model" => $this->model,
            "messages" => [
                [
                    "role" => "user",
                    "content" => $sporocilo
                ]
            ],
            "max_tokens" => $max_tokens,
            "temperature" => 0.7
        ];
        
        $nastavitve = [
            'http' => [
                'header' => "Content-Type: application/json\r\n" .
                           "Authorization: Bearer " . $this->api_kljuc . "\r\n" .
                           "HTTP-Referer: https://yourdomain.com\r\n" . // Zahtevano za OpenRouter
                           "X-Title: Blog Avtomatizacija\r\n",
                'method' => 'POST',
                'content' => json_encode($podatki),
                'timeout' => 30
            ]
        ];
        
        try {
            $kontekst = stream_context_create($nastavitve);
            $odgovor = @file_get_contents($this->osnovni_url, false, $kontekst);
            
            if ($odgovor === FALSE) {
                throw new Exception("Napaka pri povezavi z API");
            }
            
            return $odgovor;
            
        } catch (Exception $e) {
            throw new Exception("Napaka API: " . $e->getMessage());
        }
    }
    
    // Testna funkcija za preverjanje API-ja
    public function testirajPovezavo() {
        $test_sporocilo = "Odgovori samo z 'OK'. Test povezave.";
        
        try {
            $odgovor = $this->posljiZahtevek($test_sporocilo, 10);
            $dekodiran = json_decode($odgovor, true);
            
            if (isset($dekodiran['choices'][0]['message']['content'])) {
                return "Povezava uspesna: " . $dekodiran['choices'][0]['message']['content'];
            } else {
                return "Napaka v odgovoru: " . json_encode($dekodiran);
            }
        } catch (Exception $e) {
            return "Napaka pri testu: " . $e->getMessage();
        }
    }
}

// Integracija v glavni sistem
class BlogAvtomatizacija {
    // ... prejsnje funkcije ...
    
    private $optimizacija_tokenov;
    
    public function inicializirajOptimizacijoTokenov() {
        $this->optimizacija_tokenov = new OptimizacijaTokenov($this);
    }
    
    public function pridobiOptimizacijoTokenov() {
        return $this->optimizacija_tokenov;
    }
    
    // Optimizirana generacija člankov
    public function optimiziranoGenerirajClanke() {
        $this->zapisVLog("Zacetek optimizirane generacije clankov");
        
        $ideje = $this->optimizacija_tokenov->optimizirajGeneriranjeIdej($this->tema);
        
        $this->zapisVLog("Generirane ideje: " . count($ideje));
        
        foreach ($ideje as $ideja) {
            $this->zapisVLog("Generiranje clanka za: " . $ideja);
            
            $clanek = $this->optimizacija_tokenov->generirajClanekPoDelih($ideja, $this->tema);
            $this->shraniClanek($ideja, $clanek, $this->tema);
            
            // Počakaj med zahtevki
            sleep(2);
        }
        
        $this->zapisVLog("Optimizirana generacija zakljucena");
    }
    
    private function zapisVLog($sporocilo) {
        file_put_contents("optimizacija_log.txt", date('Y-m-d H:i:s') . " - " . $sporocilo . "\n", FILE_APPEND);
    }
}
?>