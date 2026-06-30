<?php
// cron_sistem.php
// Sistem za avtomatizirano izvajanje preko cron joba

class CronSistem {
    private $blog_sistem;
    private $log_datoteka = "cron_log.txt";
    
    public function __construct($blog_sistem) {
        $this->blog_sistem = $blog_sistem;
    }
    
    // Glavna funkcija za izvajanje
    public function izvediAvtomatizacijo() {
        $this->zapisVLog("Zacetek avtomatizacije: " . date('Y-m-d H:i:s'));
        
        // 1. Preveri zadnjo temo
        $nova_tema = $this->dolociNaslednjoTemo();
        
        // 2. Generiraj raziskavo
        $raziskava = $this->blog_sistem->generirajRaziskavo($nova_tema);
        $this->shraniRaziskavo($nova_tema, $raziskava);
        
        // 3. Generiraj clanke
        $this->blog_sistem->generirajInShraniClanke();
        
        // 4. Posodobi temo
        $this->posodobiTemo($nova_tema);
        
        $this->zapisVLog("Konec avtomatizacije: " . date('Y-m-d H:i:s'));
    }
    
    // Doloci naslednjo temo za raziskavo
    private function dolociNaslednjoTemo() {
        $teme = [
            "Prihodnost umetne inteligence v vsakdanjem zivljenju",
            "Vpliv AI na digitalni marketing",
            "Eticna vprasanja umetne inteligence",
            "AI v zdravstvu: revolucionarne spremembe",
            "Strojevo ucenje in podatkovna analitika",
            "Robotika in avtomatizacija v industriji",
            "AI in kreativne industrije",
            "Varnost in zasebnost v dobi AI",
            "Prihodnost dela z umetno inteligenco",
            "AI v izobrazevanju: prilagodljivo ucenje"
        ];
        
        // Preberi zadnjo uporabljeno temo
        $zadnja_tema = $this->preberiZadnjoTemo();
        
        // Najdi naslednjo temo
        $index = array_search($zadnja_tema, $teme);
        $naslednji_index = ($index === false) ? 0 : ($index + 1) % count($teme);
        
        return $teme[$naslednji_index];
    }
    
    // Preberi zadnjo temo iz baze
    private function preberiZadnjoTemo() {
        $poizvedba = $this->blog_sistem->povezava_baze->prepare(
            "SELECT vrednost FROM nastavitve_blog WHERE naziv_nastavitve = 'zadnja_raziskana_tema' ORDER BY id DESC LIMIT 1"
        );
        $poizvedba->execute();
        $rezultat = $poizvedba->fetch(PDO::FETCH_ASSOC);
        
        return $rezultat ? $rezultat['vrednost'] : "";
    }
    
    // Shrani raziskavo v bazo
    private function shraniRaziskavo($tema, $vsebina) {
        $poizvedba = $this->blog_sistem->povezava_baze->prepare(
            "INSERT INTO nastavitve_blog (naziv_nastavitve, vrednost) VALUES ('raziskava_' . ?, ?)"
        );
        $poizvedba->execute([date('Y-m-d'), $vsebina]);
    }
    
    // Posodobi temo v bazi
    private function posodobiTemo($tema) {
        $poizvedba = $this->blog_sistem->povezava_baze->prepare(
            "INSERT INTO nastavitve_blog (naziv_nastavitve, vrednost) VALUES ('zadnja_raziskana_tema', ?)"
        );
        $poizvedba->execute([$tema]);
    }
    
    // Zapis v log datoteko
    private function zapisVLog($sporocilo) {
        file_put_contents($this->log_datoteka, $sporocilo . "\n", FILE_APPEND);
    }
}

// Cron izvajalec
class CronIzvajalec {
    public static function zaženi() {
        // Inicializacija sistema
        $api_kljuc = "tvoj_api_kljuc_tukaj";
        $tema = "Začetna tema";
        $blog_sistem = new BlogAvtomatizacija($api_kljuc, $tema);
        
        // Povezava z bazo
        if ($blog_sistem->poveziZBazo("localhost", "uporabnik", "geslo", "blog_baza")) {
            $blog_sistem->nastaviAIKomunikacijo($api_kljuc);
            
            // Zagon cron sistema
            $cron = new CronSistem($blog_sistem);
            $cron->izvediAvtomatizacijo();
            
            echo "Avtomatizacija uspesno izvedena.\n";
        } else {
            echo "Napaka pri povezavi z bazo.\n";
        }
    }
}

// Zaženi cron (ta del se kliče preko cron joba)
// CronIzvajalec::zaženi();
?>