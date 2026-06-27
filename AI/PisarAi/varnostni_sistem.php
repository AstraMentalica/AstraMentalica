<?php
// varnostni_sistem.php
// Varnostni mehanizmi in obdelava napak

class VarnostniSistem {
    private $blog_sistem;
    private $max_dolzina_clanka = 10000;
    private $max_zahtevkov_na_dan = 50;
    
    public function __construct($blog_sistem) {
        $this->blog_sistem = $blog_sistem;
    }
    
    // Preveri veljavnost API ključa
    public function preveriAPIKljuc($api_kljuc) {
        if (empty($api_kljuc) || strlen($api_kljuc) < 20) {
            throw new Exception("Neveljaven API kljuc");
        }
        
        // Preveri format API ključa (osnovno preverjanje)
        if (!preg_match('/^sk-[a-zA-Z0-9]+$/', $api_kljuc)) {
            throw new Exception("Neveljaven format API kljuca");
        }
        
        return true;
    }
    
    // Preveri število zahtevkov
    public function preveriOmejitevZahtevkov() {
        $danes = date('Y-m-d');
        $poizvedba = $this->blog_sistem->povezava_baze->prepare(
            "SELECT COUNT(*) as stevilo FROM nastavitve_blog WHERE naziv_nastavitve LIKE 'zahtevek_%' AND DATE(datum_spremembe) = ?"
        );
        $poizvedba->execute([$danes]);
        $rezultat = $poizvedba->fetch(PDO::FETCH_ASSOC);
        
        if ($rezultat['stevilo'] >= $this->max_zahtevkov_na_dan) {
            throw new Exception("Dnevna omejitev zahtevkov je dosezena");
        }
        
        // Zabeleži zahtevek
        $this->zabeleziZahtevek();
        
        return true;
    }
    
    // Sanitiziraj vnos za bazo
    public function sanitizirajVnos($vnos) {
        if (is_array($vnos)) {
            return array_map([$this, 'sanitizirajVnos'], $vnos);
        }
        
        // Odstrani potencialno nevorne znake
        $vnos = htmlspecialchars($vnos, ENT_QUOTES, 'UTF-8');
        $vnos = strip_tags($vnos);
        
        // Preveri dolžino
        if (strlen($vnos) > $this->max_dolzina_clanka) {
            throw new Exception("Vnos je predolg");
        }
        
        return $vnos;
    }
    
    // Validiraj temo
    public function validirajTemo($tema) {
        $tema = trim($tema);
        
        if (empty($tema) || strlen($tema) < 5) {
            throw new Exception("Tema mora biti vsaj 5 znakov dolga");
        }
        
        if (strlen($tema) > 200) {
            throw new Exception("Tema je predolga");
        }
        
        // Preveri za nevrane znake
        if (preg_match('/[<>{}]/', $tema)) {
            throw new Exception("Tema vsebuje neveljavne znake");
        }
        
        return $tema;
    }
    
    // Obdelaj napako in zabeleži
    public function obdelajNapako($napaka, $kontekst = '') {
        $sporocilo = "Napaka: " . $napaka->getMessage() . " | Kontekst: " . $kontekst . " | Datum: " . date('Y-m-d H:i:s');
        
        // Zabeleži v log
        $this->zapisVLogNapak($sporocilo);
        
        // Shrani v bazo
        $this->shraniNapako($sporocilo);
        
        // Pošlji obvestilo (v produkciji bi poslali email)
        $this->posljiObvestilo($sporocilo);
    }
    
    // Zabeleži zahtevek
    private function zabeleziZahtevek() {
        $poizvedba = $this->blog_sistem->povezava_baze->prepare(
            "INSERT INTO nastavitve_blog (naziv_nastavitve, vrednost) VALUES ('zahtevek_' . ?, '1')"
        );
        $poizvedba->execute([date('Y-m-d_H-i-s')]);
    }
    
    // Zapis v log napak
    private function zapisVLogNapak($sporocilo) {
        $log_datoteka = "napake_log.txt";
        file_put_contents($log_datoteka, $sporocilo . "\n", FILE_APPEND);
    }
    
    // Shrani napako v bazo
    private function shraniNapako($sporocilo) {
        try {
            $poizvedba = $this->blog_sistem->povezava_baze->prepare(
                "INSERT INTO nastavitve_blog (naziv_nastavitve, vrednost) VALUES ('napaka', ?)"
            );
            $poizvedba->execute([$sporocilo]);
        } catch (Exception $e) {
            // Če tudi to ne deluje, zapiši v datoteko
            error_log("Kriticna napaka: " . $e->getMessage());
        }
    }
    
    // Pošlji obvestilo (simulacija)
    private function posljiObvestilo($sporocilo) {
        // V produkciji bi tu poslali email ali drugo obvestilo
        file_put_contents("obvestila.txt", $sporocilo . "\n", FILE_APPEND);
    }
}

// Nadgradnja AI komunikacije z varnostjo
class AIKomunikacija {
    // ... prejsnje funkcije ...
    
    public function posljiVarenZahtevek($sporocilo, $varnostni_sistem) {
        try {
            // Preveri omejitve
            $varnostni_sistem->preveriOmejitevZahtevkov();
            
            // Sanitiziraj sporočilo
            $sporocilo = $varnostni_sistem->sanitizirajVnos($sporocilo);
            
            // Pošlji zahtevek
            return $this->posljiZahtevek($sporocilo);
            
        } catch (Exception $e) {
            $varnostni_sistem->obdelajNapako($e, "AI zahtevek");
            throw $e;
        }
    }
}

// Končna integracija v glavni sistem
class BlogAvtomatizacija {
    // ... prejsnje funkcije ...
    
    private $varnostni_sistem;
    
    public function inicializirajVarnostniSistem() {
        $this->varnostni_sistem = new VarnostniSistem($this);
    }
    
    public function pridobiVarnostniSistem() {
        return $this->varnostni_sistem;
    }
    
    // Varna generacija člankov
    public function varnoGenerirajClanke() {
        try {
            $this->varnostni_sistem->preveriAPIKljuc($this->api_kljuc);
            return $this->generirajInShraniClanke();
        } catch (Exception $e) {
            $this->varnostni_sistem->obdelajNapako($e, "Generiranje clankov");
            return false;
        }
    }
}

// Uporaba varnostnega sistema
$blog_sistem->inicializirajVarnostniSistem();
$varnost = $blog_sistem->pridobiVarnostniSistem();

try {
    $varnost->preveriAPIKljuc($api_kljuc);
    $blog_sistem->varnoGenerirajClanke();
} catch (Exception $e) {
    echo "Varnostna napaka: " . $e->getMessage();
}
?>