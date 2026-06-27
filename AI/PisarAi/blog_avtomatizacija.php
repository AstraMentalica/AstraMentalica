<?php
// blog_avtomatizacija.php
// Glavna datoteka za avtomatiziran pisanje bloga z AI

class BlogAvtomatizacija {
    private $api_kljuc;
    private $tema;
    private $stevilo_clankov;
    private $povezava_baze;
    
    public function __construct($api_kljuc, $tema, $stevilo_clankov = 1) {
        $this->api_kljuc = $api_kljuc;
        $this->tema = $tema;
        $this->stevilo_clankov = $stevilo_clankov;
        $this->povezava_baze = null;
    }
    
    // Povezava z bazo podatkov
    public function poveziZBazo($gostitelj, $uporabnik, $geslo, $baza) {
        try {
            $this->povezava_baze = new PDO("mysql:host=$gostitelj;dbname=$baza", $uporabnik, $geslo);
            $this->povezava_baze->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return true;
        } catch(PDOException $e) {
            echo "Napaka pri povezavi: " . $e->getMessage();
            return false;
        }
    }
    
    // Ustvari tabelo za clanke ce ne obstaja
    public function ustvariTabeloClankov() {
        $poizvedba = "CREATE TABLE IF NOT EXISTS clanki (
            id INT AUTO_INCREMENT PRIMARY KEY,
            naslov VARCHAR(255) NOT NULL,
            vsebina TEXT NOT NULL,
            datum_ustvarjen DATETIME DEFAULT CURRENT_TIMESTAMP,
            status ENUM('osnutek', 'objavljen') DEFAULT 'osnutek',
            kljucne_besede TEXT,
            tema VARCHAR(100)
        )";
        
        $this->povezava_baze->exec($poizvedba);
    }
}

// Konfiguracija
$api_kljuc = "tvoj_api_kljuc_tukaj"; // Zamenjaj s pravim API kljucem
$tema = "Tehnologija in umetna inteligenca"; // Prva tema za raziskavo
$avtomatizacija = new BlogAvtomatizacija($api_kljuc, $tema, 5);

// Povezava z bazo (prilagodi podatke)
if ($avtomatizacija->poveziZBazo("localhost", "uporabnik", "geslo", "blog_baza")) {
    $avtomatizacija->ustvariTabeloClankov();
    echo "Baza pripravljena za delo.\n";
}
?>