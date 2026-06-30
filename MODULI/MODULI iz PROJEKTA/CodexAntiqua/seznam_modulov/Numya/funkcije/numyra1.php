<?php

/**
 * Numyra - Numerologija Modul
 * "Stevila kot kljuci vesolja, osebne kode in zivljenjski cikli."
 * 
 * @package Numyra
 * @version 1.0
 */

// KLJUCNE ZADEVE ZA IZDELANO IN ZATE ZA NAPREJ:
// - Osnovna struktura modula s PSR-12 standardom
// - Definicija osnovnih razredov in funkcij
// - Priprava na integracijo z AI sistemom
// 
// DAJAJ SI PRIMERNO KOMPLEKSE NALOGE, DA JIH ZAKLJUCIS
// ALI PA SI JIH RAZDELIS:
// - Implementacija specificnih numeroloskih izracunov
// - Integracija z API za AI generiranje vsebin
// - Razvoj PDF generatorja in CMS sistema

/**
 * Glavni razred Numyra modula
 */
class NumyraNumerologija {
    
    /**
     * @var string $imeUporabnika
     */
    private $imeUporabnika;
    
    /**
     * @var string $priimekUporabnika  
     */
    private $priimekUporabnika;
    
    /**
     * @var string $rojstniDatum
     */
    private $rojstniDatum;
    
    /**
     * @var Database $baza
     */
    private $baza;
    
    /**
     * Konstruktor za inicializacijo Numyra modula
     * 
     * @param Database $bazaPodatkov
     */
    public function __construct(Database $bazaPodatkov) {
        $this->baza = $bazaPodatkov;
        $this->inicializirajModul();
    }
    
    /**
     * Inicializacija modula in priprava osnovnih nastavitev
     */
    private function inicializirajModul(): void {
        // Inicializacijska logika modula
        $this->pripraviTabeleBaze();
        $this->nalaziOsnovneNastavitve();
    }
    
    /**
     * Priprava potrebnih tabel v bazi podatkov
     */
    private function pripraviTabeleBaze(): void {
        // SQL poizvedbe za ustvarjanje tabel
        $this->ustvariTabeloUporabnikov();
        $this->ustvariTabeloAnaliz();
        $this->ustvariTabeloCiklov();
    }
    
    /**
     * Ustvari tabelo za shranjevanje uporabnikov
     */
    private function ustvariTabeloUporabnikov(): void {
        $sql = "CREATE TABLE IF NOT EXISTS numyra_uporabniki (
            id INT AUTO_INCREMENT PRIMARY KEY,
            ime VARCHAR(100) NOT NULL,
            priimek VARCHAR(100) NOT NULL, 
            rojstni_datum DATE NOT NULL,
            status ENUM('S0','S1','S2','S3','S4','S5') DEFAULT 'S0',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        
        $this->baza->query($sql);
    }
    
    /**
     * Ustvari tabelo za shranjevanje numeroloskih analiz
     */
    private function ustvariTabeloAnaliz(): void {
        $sql = "CREATE TABLE IF NOT EXISTS numyra_analize (
            id INT AUTO_INCREMENT PRIMARY KEY,
            uporabnik_id INT,
            zivljenjska_pot INT,
            dusna_stevilka INT,
            osebno_stevilo INT,
            karmicne_lekcije TEXT,
            mojstrske_stevilke TEXT,
            analiza_text TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (uporabnik_id) REFERENCES numyra_uporabniki(id)
        )";
        
        $this->baza->query($sql);
    }
    
    /**
     * Ustvari tabelo za shranjevanje zivljenjskih ciklov
     */
    private function ustvariTabeloCiklov(): void {
        $sql = "CREATE TABLE IF NOT EXISTS numyra_cikli (
            id INT AUTO_INCREMENT PRIMARY KEY, 
            uporabnik_id INT,
            cikel_mladost TEXT,
            cikel_zrelost TEXT,
            cikel_starost TEXT,
            osebni_letni_cikel TEXT,
            devetletni_cikli TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (uporabnik_id) REFERENCES numyra_uporabniki(id)
        )";
        
        $this->baza->query($sql);
    }
    
    /**
     * Nalaganje osnovnih nastavitev modula
     */
    private function nalaziOsnovneNastavitve(): void {
        // Nalaganje konfiguracije in nastavitev
        $this->nalaziNumeroloskeKonstante();
        $this->pripraviAISistem();
    }
    
    /**
     * Nalaganje numeroloskih konstant in preslikav
     */
    private function nalaziNumeroloskeKonstante(): void {
        // Definicija numeroloskih vrednosti crk
        $this->numeroloskeVrednosti = [
            'a' => 1, 'b' => 2, 'c' => 3, 'c' => 3, 'd' => 4, 'e' => 5, 'f' => 6,
            'g' => 7, 'h' => 8, 'i' => 9, 'j' => 1, 'k' => 2, 'l' => 3, 'm' => 4,
            'n' => 5, 'o' => 6, 'p' => 7, 'q' => 8, 'r' => 9, 's' => 1, 's' => 1,
            't' => 2, 'u' => 3, 'v' => 4, 'w' => 5, 'x' => 6, 'y' => 7, 'z' => 8
        ];
    }
    
    /**
     * Priprava AI sistema za generiranje vsebin
     */
    private function pripraviAISistem(): void {
        $this->aiGenerator = new AIGenerator();
        $this->aiGenerator->nastaviModul('numerologija');
    }
}

/**
 * Razred za osebno numerolosko analizo
 */
class OsebnaNumeroloskaAnaliza {
    
    /**
     * @var string $ime
     */
    private $ime;
    
    /**
     * @var string $priimek  
     */
    private $priimek;
    
    /**
     * @var string $rojstniDatum
     */
    private $rojstniDatum;
    
    /**
     * Konstruktor za analizo
     * 
     * @param string $ime
     * @param string $priimek
     * @param string $rojstniDatum
     */
    public function __construct(string $ime, string $priimek, string $rojstniDatum) {
        $this->ime = strtolower($ime);
        $this->priimek = strtolower($priimek);
        $this->rojstniDatum = $rojstniDatum;
    }
    
    /**
     * Izracun vseh numeroloskih podatkov
     * 
     * @return array
     */
    public function izracunajVse(): array {
        return [
            'zivljenjska_pot' => $this->izracunajZivljenjskoPot(),
            'dusna_stevilka' => $this->izracunajDusnoStevilko(),
            'osebno_stevilo' => $this->izracunajOsebnoStevilo(),
            'karmicne_lekcije' => $this->izracunajKarmicneLekcije(),
            'mojstrske_stevilke' => $this->preveriMojstrskeStevilke()
        ];
    }
    
    /**
     * Izracun zivljenjske poti iz rojstnega datuma
     * 
     * @return int
     */
    public function izracunajZivljenjskoPot(): int {
        $datum = str_replace(['-', '.', '/'], '', $this->rojstniDatum);
        $vsota = array_sum(str_split($datum));
        
        return $this->reducirajNaEnoStevko($vsota);
    }
    
    /**
     * Izracun dusne stevilke iz samoglasnikov v imenu
     * 
     * @return int
     */
    public function izracunajDusnoStevilko(): int {
        $polnoIme = $this->ime . $this->priimek;
        $samoglasniki = preg_replace('/[^aeiou]/', '', $polnoIme);
        
        $vsota = 0;
        for ($i = 0; $i < strlen($samoglasniki); $i++) {
            $crka = $samoglasniki[$i];
            $vsota += $this->dajNumeroloskoVrednost($crka);
        }
        
        return $this->reducirajNaEnoStevko($vsota);
    }
    
    /**
     * Izracun osebnega stevila iz soglasnikov
     * 
     * @return int
     */
    public function izracunajOsebnoStevilo(): int {
        $polnoIme = $this->ime . $this->priimek;
        $soglasniki = preg_replace('/[aeiou]/', '', $polnoIme);
        
        $vsota = 0;
        for ($i = 0; $i < strlen($soglasniki); $i++) {
            $crka = $soglasniki[$i];
            $vsota += $this->dajNumeroloskoVrednost($crka);
        }
        
        return $this->reducirajNaEnoStevko($vsota);
    }
    
    /**
     * Izracun karmicnih lekcij
     * 
     * @return array
     */
    public function izracunajKarmicneLekcije(): array {
        // Logika za karmicne lekcije
        $uporabljeneCrke = $this->dajVseCrkeIzImena();
        $karmicneLekcije = [];
        
        for ($i = 1; $i <= 9; $i++) {
            if (!in_array($i, $uporabljeneCrke)) {
                $karmicneLekcije[] = $i;
            }
        }
        
        return $karmicneLekcije;
    }
    
    /**
     * Preverjanje mojstrskih stevilk
     * 
     * @return array
     */
    public function preveriMojstrskeStevilke(): array {
        $mojstrske = [];
        $stevilke = $this->izracunajVse();
        
        foreach ($stevilke as $stevilka) {
            if (in_array($stevilka, [11, 22, 33])) {
                $mojstrske[] = $stevilka;
            }
        }
        
        return $mojstrske;
    }
    
    /**
     * Reduciraj stevilo na eno stevko
     * 
     * @param int $stevilo
     * @return int
     */
    private function reducirajNaEnoStevko(int $stevilo): int {
        while ($stevilo > 9 && !in_array($stevilo, [11, 22, 33])) {
            $stevilo = array_sum(str_split((string)$stevilo));
        }
        
        return $stevilo;
    }
    
    /**
     * Daj numerolosko vrednost crke
     * 
     * @param string $crka
     * @return int
     */
    private function dajNumeroloskoVrednost(string $crka): int {
        $vrednosti = [
            'a' => 1, 'b' => 2, 'c' => 3, 'c' => 3, 'd' => 4, 'e' => 5, 'f' => 6,
            'g' => 7, 'h' => 8, 'i' => 9, 'j' => 1, 'k' => 2, 'l' => 3, 'm' => 4,
            'n' => 5, 'o' => 6, 'p' => 7, 'q' => 8, 'r' => 9, 's' => 1, 's' => 1,
            't' => 2, 'u' => 3, 'v' => 4, 'w' => 5, 'x' => 6, 'y' => 7, 'z' => 8
        ];
        
        return $vrednosti[$crka] ?? 0;
    }
    
    /**
     * Daj vse crke iz imena kot numeroloske vrednosti
     * 
     * @return array
     */
    private function dajVseCrkeIzImena(): array {
        $polnoIme = $this->ime . $this->priimek;
        $crke = str_split(preg_replace('/[^a-z]/', '', $polnoIme));
        $vrednosti = [];
        
        foreach ($crke as $crka) {
            $vrednost = $this->dajNumeroloskoVrednost($crka);
            if (!in_array($vrednost, $vrednosti)) {
                $vrednosti[] = $vrednost;
            }
        }
        
        return $vrednosti;
    }
}

/**
 * Razred za zivljenjske cikle
 */
class ZivljenjskiCikli {
    
    /**
     * @var string $rojstniDatum
     */
    private $rojstniDatum;
    
    /**
     * Konstruktor za zivljenjske cikle
     * 
     * @param string $rojstniDatum
     */
    public function __construct(string $rojstniDatum) {
        $this->rojstniDatum = $rojstniDatum;
    }
    
    /**
     * Izracun glavnih treh ciklov
     * 
     * @return array
     */
    public function izracunajGlavneCikle(): array {
        return [
            'mladost' => $this->izracunajCikelMladost(),
            'zrelost' => $this->izracunajCikelZrelost(), 
            'starost' => $this->izracunajCikelStarost()
        ];
    }
    
    /**
     * Izracun cikla mladosti
     * 
     * @return array
     */
    public function izracunajCikelMladost(): array {
        // Logika za cikel mladosti (od rojstva do 28-35 let)
        $rojstniDan = date('d', strtotime($this->rojstniDatum));
        $rojstniMesec = date('m', strtotime($this->rojstniDatum));
        
        $steviloCikla = $this->reducirajNaEnoStevko($rojstniDan + $rojstniMesec);
        
        return [
            'stevilka' => $steviloCikla,
            'trajanje' => 'od rojstva do 28-35 let',
            'opis' => $this->generirajOpisCikla($steviloCikla, 'mladost')
        ];
    }
    
    /**
     * Izracun cikla zrelosti
     * 
     * @return array
     */
    public function izracunajCikelZrelost(): array {
        // Logika za cikel zrelosti
        $rojstniDan = date('d', strtotime($this->rojstniDatum));
        $rojstnoLeto = date('Y', strtotime($this->rojstniDatum));
        
        $steviloCikla = $this->reducirajNaEnoStevko($rojstniDan + $rojstnoLeto);
        
        return [
            'stevilka' => $steviloCikla,
            'trajanje' => 'od 28-35 let do 56-63 let', 
            'opis' => $this->generirajOpisCikla($steviloCikla, 'zrelost')
        ];
    }
    
    /**
     * Izracun cikla starosti
     * 
     * @return array
     */
    public function izracunajCikelStarost(): array {
        // Logika za cikel starosti
        $vsotaMladost = $this->izracunajCikelMladost()['stevilka'];
        $vsotaZrelost = $this->izracunajCikelZrelost()['stevilka'];
        
        $steviloCikla = $this->reducirajNaEnoStevko($vsotaMladost + $vsotaZrelost);
        
        return [
            'stevilka' => $steviloCikla,
            'trajanje' => 'od 56-63 let naprej',
            'opis' => $this->generirajOpisCikla($steviloCikla, 'starost')
        ];
    }
    
    /**
     * Izracun osebnega letnega cikla
     * 
     * @param int $leto
     * @return array
     */
    public function izracunajOsebniLetniCikel(int $leto): array {
        $rojstniDan = date('d', strtotime($this->rojstniDatum));
        $rojstniMesec = date('m', strtotime($this->rojstniDatum));
        
        $steviloCikla = $this->reducirajNaEnoStevko($rojstniDan + $rojstniMesec + $leto);
        
        return [
            'leto' => $leto,
            'stevilka' => $steviloCikla,
            'opis' => $this->generirajOpisLetnegaCikla($steviloCikla)
        ];
    }
    
    /**
     * Generiranje opisa cikla z AI pomocjo
     * 
     * @param int $stevilka
     * @param string $tipCikla
     * @return string
     */
    private function generirajOpisCikla(int $stevilka, string $tipCikla): string {
        $aiGenerator = new AIGenerator();
        return $aiGenerator->generirajOpisCikla($stevilka, $tipCikla);
    }
    
    /**
     * Generiranje opisa letnega cikla
     * 
     * @param int $stevilka
     * @return string
     */
    private function generirajOpisLetnegaCikla(int $stevilka): string {
        $aiGenerator = new AIGenerator();
        return $aiGenerator->generirajOpisLetnegaCikla($stevilka);
    }
    
    /**
     * Reduciraj stevilo na eno stevko
     * 
     * @param int $stevilo
     * @return int
     */
    private function reducirajNaEnoStevko(int $stevilo): int {
        while ($stevilo > 9 && !in_array($stevilo, [11, 22, 33])) {
            $stevilo = array_sum(str_split((string)$stevilo));
        }
        
        return $stevilo;
    }
}

/**
 * Razred za AI generiranje vsebin
 */
class AIGenerator {
    
    /**
     * @var string $modul
     */
    private $modul;
    
    /**
     * Nastavi modul za AI generiranje
     * 
     * @param string $modul
     */
    public function nastaviModul(string $modul): void {
        $this->modul = $modul;
    }
    
    /**
     * Generiraj numerolosko analizo z AI
     * 
     * @param array $podatki
     * @return string
     */
    public function generirajAnalizo(array $podatki): string {
        // Integracija z AI API za generiranje poglobljene analize
        $prompt = $this->pripraviPromptZaAnalizo($podatki);
        return $this->pokliciAIAPI($prompt);
    }
    
    /**
     * Generiraj opis cikla
     * 
     * @param int $stevilka
     * @param string $tipCikla
     * @return string
     */
    public function generirajOpisCikla(int $stevilka, string $tipCikla): string {
        $prompt = "Generiraj numeroloski opis za $tipCikla cikel s stevilko $stevilka";
        return $this->pokliciAIAPI($prompt);
    }
    
    /**
     * Generiraj opis letnega cikla
     * 
     * @param int $stevilka
     * @return string
     */
    public function generirajOpisLetnegaCikla(int $stevilka): string {
        $prompt = "Generiraj numeroloski opis za letni cikel s stevilko $stevilka";
        return $this->pokliciAIAPI($prompt);
    }
    
    /**
     * Generiraj PDF vsebino z AI
     * 
     * @param array $analizaPodatki
     * @return string
     */
    public function generirajPDFVsebino(array $analizaPodatki): string {
        $prompt = $this->pripraviPromptZaPDF($analizaPodatki);
        return $this->pokliciAIAPI($prompt);
    }
    
    /**
     * Pripravi prompt za AI analizo
     * 
     * @param array $podatki
     * @return string
     */
    private function pripraviPromptZaAnalizo(array $podatki): string {
        return "Generiraj poglobljeno numerolosko analizo za: " . 
               "Zivljenjska pot: {$podatki['zivljenjska_pot']}, " .
               "Dusna stevilka: {$podatki['dusna_stevilka']}, " . 
               "Osebno stevilo: {$podatki['osebno_stevilo']}";
    }
    
    /**
     * Pripravi prompt za PDF vsebino
     * 
     * @param array $podatki
     * @return string
     */
    private function pripraviPromptZaPDF(array $podatki): string {
        return "Generiraj strukturirano vsebino za PDF porocilo z numerolosko analizo: " . 
               json_encode($podatki);
    }
    
    /**
     * Klic AI API-ja za generiranje vsebine
     * 
     * @param string $prompt
     * @return string
     */
    private function pokliciAIAPI(string $prompt): string {
        // Implementacija klicev na AI API
        // Za zdaj vracamo placeholder
        return "AI generirana vsebina za: " . substr($prompt, 0, 50) . "...";
    }
}

/**
 * Razred za upravljanje uporabnikov in dostopov
 */
class UpravljalecUporabnikov {
    
    /**
     * @var Database $baza
     */
    private $baza;
    
    /**
     * Konstruktor
     * 
     * @param Database $bazaPodatkov
     */
    public function __construct(Database $bazaPodatkov) {
        $this->baza = $bazaPodatkov;
    }
    
    /**
     * Ustvari novega uporabnika
     * 
     * @param string $ime
     * @param string $priimek
     * @param string $rojstniDatum
     * @param string $status
     * @return int
     */
    public function ustvariUporabnika(
        string $ime, 
        string $priimek, 
        string $rojstniDatum,
        string $status = 'S0'
    ): int {
        $sql = "INSERT INTO numyra_uporabniki (ime, priimek, rojstni_datum, status) 
                VALUES (?, ?, ?, ?)";
        
        $stmt = $this->baza->prepare($sql);
        $stmt->execute([$ime, $priimek, $rojstniDatum, $status]);
        
        return $this->baza->lastInsertId();
    }
    
    /**
     * Pridobi uporabnika po ID
     * 
     * @param int $id
     * @return array
     */
    public function pridobiUporabnika(int $id): array {
        $sql = "SELECT * FROM numyra_uporabniki WHERE id = ?";
        $stmt = $this->baza->prepare($sql);
        $stmt->execute([$id]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    }
    
    /**
     * Posodobi status uporabnika
     * 
     * @param int $id
     * @param string $novStatus
     * @return bool
     */
    public function posodobiStatus(int $id, string $novStatus): bool {
        $sql = "UPDATE numyra_uporabniki SET status = ? WHERE id = ?";
        $stmt = $this->baza->prepare($sql);
        
        return $stmt->execute([$novStatus, $id]);
    }
    
    /**
     * Preveri dostop uporabnika do funkcije
     * 
     * @param int $uporabnikId
     * @param string $funkcija
     * @return bool
     */
    public function preveriDostop(int $uporabnikId, string $funkcija): bool {
        $uporabnik = $this->pridobiUporabnika($uporabnikId);
        $status = $uporabnik['status'] ?? 'S0';
        
        $dostopi = [
            'S0' => ['osnovna_analiza'],
            'S1' => ['osnovna_analiza', 'shranjevanje'],
            'S2' => ['osnovna_analiza', 'shranjevanje', 'cikli'],
            'S3' => ['osnovna_analiza', 'shranjevanje', 'cikli', 'pdf_izvoz'],
            'S4' => ['vse_funkcije', 'posebni_dostopi'],
            'S5' => ['vse_funkcije', 'administracija']
        ];
        
        return in_array($funkcija, $dostopi[$status] ?? []);
    }
}

// KLJUCNE ZADEVE ZA IZDELANO IN ZATE ZA NAPREJ:
// - Osnovna arhitektura Numyra modula je pripravljena
// - Implementirani osnovni numeroloski izracuni
// - Pripravljen sistem za AI generiranje vsebin
// - Definicija uporabniskih nivojev in dostopov
//
// DAJAJ SI PRIMERNO KOMPLEKSE NALOGE, DA JIH ZAKLJUCIS
// ALI PA SI JIH RAZDELIS:
// - Implementacija PDF generatorja
// - Razvoj CMS sistema za numeroloske vodice
// - Integracija push obvestil
// - Razsiritev AI funkcionalnosti

?>