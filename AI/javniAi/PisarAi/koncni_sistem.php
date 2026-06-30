<?php
// koncni_sistem.php
// Končna integracija vseh komponent

class KoncniBlogSistem {
    private $blog_sistem;
    private $konfiguracija;
    
    public function __construct($konfiguracija) {
        $this->konfiguracija = $konfiguracija;
        $this->inicializirajSistem();
    }
    
    // Inicializiraj celoten sistem
    private function inicializirajSistem() {
        $this->blog_sistem = new BlogAvtomatizacija(
            $this->konfiguracija['api_kljuc'],
            $this->konfiguracija['zacetna_tema'],
            $this->konfiguracija['stevilo_clankov']
        );
        
        // Povezava z bazo
        if ($this->blog_sistem->poveziZBazo(
            $this->konfiguracija['baza_gostitelj'],
            $this->konfiguracija['baza_uporabnik'],
            $this->konfiguracija['baza_geslo'],
            $this->konfiguracija['baza_ime']
        )) {
            // Inicializiraj vse komponente
            $this->blog_sistem->nastaviAIKomunikacijo($this->konfiguracija['api_kljuc']);
            $this->blog_sistem->inicializirajVarnostniSistem();
            $this->blog_sistem->inicializirajOptimizacijoTokenov();
            $this->blog_sistem->inicializirajNapredneFunkcije();
            
            echo "Sistem uspesno inicializiran.\n";
        } else {
            throw new Exception("Napaka pri inicializaciji baze podatkov");
        }
    }
    
    // Zaženi celoten avtomatizacijski cikel
    public function zazeniAvtomatizacijo() {
        echo "Zaganjam avtomatizacijo bloga...\n";
        
        try {
            // 1. Preveri varnost
            $varnost = $this->blog_sistem->pridobiVarnostniSistem();
            $varnost->preveriAPIKljuc($this->konfiguracija['api_kljuc']);
            $varnost->preveriOmejitevZahtevkov();
            
            // 2. Generiraj optimizirane članke
            $this->blog_sistem->optimiziranoGenerirajClanke();
            
            // 3. Generiraj raziskavo
            $optimizacija = $this->blog_sistem->pridobiOptimizacijoTokenov();
            $raziskava = $optimizacija->generirajOptimiziranoRaziskavo($this->blog_sistem->tema);
            
            // 4. Shrani rezultate
            $this->shraniRezultate($raziskava);
            
            // 5. Generiraj poročilo
            $napredne = $this->blog_sistem->pridobiNapredneFunkcije();
            $porocilo = $napredne->generirajMesecnoPorocilo();
            
            echo "Avtomatizacija uspesno zakljucena.\n";
            return $porocilo;
            
        } catch (Exception $e) {
            $varnost->obdelajNapako($e, "Končna avtomatizacija");
            throw $e;
        }
    }
    
    // Pridobi statistiko sistema
    public function pridobiStatistiko() {
        $statistika = [];
        
        // Statistika člankov
        $poizvedba = $this->blog_sistem->povezava_baze->query(
            "SELECT COUNT(*) as skupno_clankov, 
                    COUNT(DISTINCT tema) as razlicne_teme,
                    AVG(LENGTH(vsebina)) as povprecna_dolzina
             FROM clanki"
        );
        $statistika['clanki'] = $poizvedba->fetch(PDO::FETCH_ASSOC);
        
        // Statistika tokenov
        $optimizacija = $this->blog_sistem->pridobiOptimizacijoTokenov();
        $statistika['tokeni'] = $optimizacija->pridobiStatistikoPorabe();
        
        // Statistika napak
        $poizvedba = $this->blog_sistem->povezava_baze->query(
            "SELECT COUNT(*) as skupno_napak 
             FROM nastavitve_blog 
             WHERE naziv_nastavitve = 'napaka'"
        );
        $statistika['napake'] = $poizvedba->fetch(PDO::FETCH_ASSOC);
        
        return $statistika;
    }
    
    // Shrani rezultate
    private function shraniRezultate($raziskava) {
        $poizvedba = $this->blog_sistem->povezava_baze->prepare(
            "INSERT INTO nastavitve_blog (naziv_nastavitve, vrednost) VALUES ('zadnja_raziskava', ?)"
        );
        $poizvedba->execute([$raziskava]);
    }
}

// Konfiguracija za produkcijo
$konfiguracija = [
    'api_kljuc' => 'tvoj_openrouter_api_kljuc',
    'zacetna_tema' => 'Tehnologija in umetna inteligenca',
    'stevilo_clankov' => 2,
    'baza_gostitelj' => 'localhost',
    'baza_uporabnik' => 'blog_uporabnik',
    'baza_geslo' => 'varno_geslo',
    'baza_ime' => 'avtomatiziran_blog'
];

// Uporaba
try {
    $koncni_sistem = new KoncniBlogSistem($konfiguracija);
    $porocilo = $koncni_sistem->zazeniAvtomatizacijo();
    
    echo "\n=== POROČILO ===\n";
    echo $porocilo;
    
    echo "\n=== STATISTIKA ===\n";
    $statistika = $koncni_sistem->pridobiStatistiko();
    print_r($statistika);
    
} catch (Exception $e) {
    echo "Napaka v sistemu: " . $e->getMessage() . "\n";
}
?>