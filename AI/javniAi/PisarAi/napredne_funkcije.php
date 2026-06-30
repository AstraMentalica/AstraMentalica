<?php
// napredne_funkcije.php
// Napredne funkcije za izboljšavo sistema

class NapredneFunkcije {
    private $blog_sistem;
    
    public function __construct($blog_sistem) {
        $this->blog_sistem = $blog_sistem;
    }
    
    // Analiza uspešnosti člankov
    public function analizirajUspešnost() {
        $poizvedba = $this->blog_sistem->povezava_baze->query("
            SELECT tema, COUNT(*) as stevilo, 
                   AVG(LENGTH(vsebina)) as povprecna_dolzina,
                   MIN(datum_ustvarjen) as prvi_datum,
                   MAX(datum_ustvarjen) as zadnji_datum
            FROM clanki 
            GROUP BY tema
        ");
        
        $analiza = $poizvedba->fetchAll(PDO::FETCH_ASSOC);
        $this->shraniAnalizo($analiza);
        
        return $analiza;
    }
    
    // Generiraj mesečno poročilo
    public function generirajMesecnoPorocilo() {
        $mesec = date('Y-m');
        $sporocilo = "Mesečno poročilo za blog avtomatizacijo - " . $mesec . "\n\n";
        
        $analiza = $this->analizirajUspešnost();
        
        foreach ($analiza as $vrstica) {
            $sporocilo .= "Tema: " . $vrstica['tema'] . "\n";
            $sporocilo .= "Število člankov: " . $vrstica['stevilo'] . "\n";
            $sporocilo .= "Povprečna dolžina: " . round($vrstica['povprecna_dolzina']) . " znakov\n\n";
        }
        
        $this->shraniPorocilo($mesec, $sporocilo);
        return $sporocilo;
    }
    
    // Optimizacija vsebine s SEO
    public function optimizirajSEO($clanek_id) {
        $clanek = $this->pridobiClanek($clanek_id);
        
        if ($clanek) {
            $seo_naslov = $this->optimizirajNaslov($clanek['naslov']);
            $seo_vsebina = $this->optimizirajVsebino($clanek['vsebina']);
            
            $this->posodobiClanek($clanek_id, $seo_naslov, $seo_vsebina);
            
            return "SEO optimizacija zakljucena za clanek ID: " . $clanek_id;
        }
        
        return "Clanek ne obstaja";
    }
    
    // Optimiziraj naslov za SEO
    private function optimizirajNaslov($naslov) {
        $sporocilo = "Optimiziraj naslednji naslov blog clanka za SEO: '" . $naslov . "'. Naslov naj bo privlacen, vsebuje kljucne besede in ne daljsi od 60 znakov. Odgovori samo z optimiziranim naslovom v slovenscini.";
        
        return $this->blog_sistem->ai_komunikacija->posljiZahtevek($sporocilo);
    }
    
    // Optimiziraj vsebino za SEO
    private function optimizirajVsebino($vsebina) {
        $sporocilo = "Optimiziraj naslednjo vsebino blog clanka za SEO. Dodaj ustrezne podnaslove, kljucne besede in poskrbi za berljivost. Ohrani prvotni pomen in jezik (slovenscina). Vsebina: " . substr($vsebina, 0, 3000);
        
        return $this->blog_sistem->ai_komunikacija->posljiZahtevek($sporocilo);
    }
    
    // Pridobi clanek iz baze
    private function pridobiClanek($id) {
        $poizvedba = $this->blog_sistem->povezava_baze->prepare("SELECT * FROM clanki WHERE id = ?");
        $poizvedba->execute([$id]);
        return $poizvedba->fetch(PDO::FETCH_ASSOC);
    }
    
    // Posodobi clanek v bazi
    private function posodobiClanek($id, $naslov, $vsebina) {
        $poizvedba = $this->blog_sistem->povezava_baze->prepare("UPDATE clanki SET naslov = ?, vsebina = ? WHERE id = ?");
        $poizvedba->execute([$naslov, $vsebina, $id]);
    }
    
    // Shrani analizo v bazo
    private function shraniAnalizo($analiza) {
        $json_analiza = json_encode($analiza, JSON_UNESCAPED_UNICODE);
        $poizvedba = $this->blog_sistem->povezava_baze->prepare(
            "INSERT INTO nastavitve_blog (naziv_nastavitve, vrednost) VALUES ('analiza_uspesnosti', ?)"
        );
        $poizvedba->execute([$json_analiza]);
    }
    
    // Shrani poročilo v bazo
    private function shraniPorocilo($mesec, $porocilo) {
        $poizvedba = $this->blog_sistem->povezava_baze->prepare(
            "INSERT INTO nastavitve_blog (naziv_nastavitve, vrednost) VALUES ('porocilo_' . ?, ?)"
        );
        $poizvedba->execute([$mesec, $porocilo]);
    }
}

// Razširitev glavnega razreda
class BlogAvtomatizacija {
    // ... prejsnje funkcije ...
    
    private $napredne_funkcije;
    
    public function inicializirajNapredneFunkcije() {
        $this->napredne_funkcije = new NapredneFunkcije($this);
    }
    
    public function pridobiNapredneFunkcije() {
        return $this->napredne_funkcije;
    }
}

// Uporaba naprednih funkcij
$blog_sistem->inicializirajNapredneFunkcije();
$napredne = $blog_sistem->pridobiNapredneFunkcije();

// Generiraj mesečno poročilo
$porocilo = $napredne->generirajMesecnoPorocilo();
echo $porocilo;

// Optimiziraj SEO za zadnjih 5 člankov
for ($i = 1; $i <= 5; $i++) {
    $rezultat = $napredne->optimizirajSEO($i);
    echo $rezultat . "\n";
}
?>