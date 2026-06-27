<?php
/**
 * Codex Damiris - Glavni Kontroler
 * Lokacija: /var/www/html/codex-damiris/CodexKontroler.php
 */

class CodexKontroler {
    private $jedro;
    private $storitve;
    private $ai;
    
    public function __construct() {
        $this->jedro = new CodexJedro();
        $this->storitve = new CodexStoritve($this->jedro);
        $this->ai = new AI_Codex($this->jedro);
    }
    
    /**
     * Obdelaj zahtevo
     */
    public function obdelajZahtevo() {
        $akcija = $_GET['akcija'] ?? $_POST['akcija'] ?? 'domov';
        
        // Preveri CSRF za POST zahteve
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            CodexFunkcije::preveriCSRF();
        }
        
        switch ($akcija) {
            case 'domov':
                $this->prikaziDomov();
                break;
                
            case 'prijava':
                $this->obdelajPrijavo();
                break;
                
            case 'registracija':
                $this->obdelajRegistracijo();
                break;
                
            case 'odjava':
                $this->obdelajOdjavo();
                break;
                
            case 'iskanje':
                $this->obdelajIskanje();
                break;
                
            case 'dodaj_vsebino':
                $this->obdelajDodajanjeVsebine();
                break;
                
            case 'ai_chat':
                $this->obdelajAIChat();
                break;
                
            case 'aktivacija':
                $this->obdelajAktivacijo();
                break;
                
            default:
                $this->prikaziDomov();
        }
    }
    
    /**
     * Prikaži domačo stran
     */
    private function prikaziDomov() {
        $priljubljene = $this->jedro->pridobiPriljubljene(6);
        $vsebine = $this->jedro->pridobiVsebine(null, 12);
        $kategorije = CodexPravila::pridobiKategorije();
        
        include 'codex.php';
    }
    
    /**
     * Obdelaj prijavo
     */
    private function obdelajPrijavo() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $geslo = $_POST['geslo'] ?? '';
            
            $rezultat = $this->storitve->prijaviUporabnika($email, $geslo);
            
            if ($rezultat['uspeh']) {
                CodexFunkcije::prikaziSporocilo('success', 'Uspešna prijava!');
                CodexFunkcije::preusmeri('/codex-damiris/');
            } else {
                CodexFunkcije::prikaziSporocilo('error', $rezultat['napaka']);
                $this->prikaziDomov();
            }
        }
    }
    
    /**
     * Obdelaj registracijo
     */
    private function obdelajRegistracijo() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $geslo = $_POST['geslo'] ?? '';
            $vzdevek = $_POST['vzdevek'] ?? '';
            $telefon = $_POST['telefon'] ?? '';
            
            $rezultat = $this->storitve->registrirajUporabnika($email, $geslo, $vzdevek, $telefon);
            
            if ($rezultat['uspeh']) {
                CodexFunkcije::prikaziSporocilo('success', $rezultat['sporocilo']);
                CodexFunkcije::preusmeri('/codex-damiris/');
            } else {
                CodexFunkcije::prikaziSporocilo('error', $rezultat['napaka']);
                $this->prikaziDomov();
            }
        }
    }
    
    /**
     * Obdelaj odjavo
     */
    private function obdelajOdjavo() {
        session_destroy();
        CodexFunkcije::prikaziSporocilo('success', 'Uspešna odjava');
        CodexFunkcije::preusmeri('/codex-damiris/');
    }
    
    /**
     * Obdelaj iskanje
     */
    private function obdelajIskanje() {
        $poizvedba = $_GET['q'] ?? '';
        $kategorija = $_GET['kategorija'] ?? null;
        
        if (!empty($poizvedba)) {
            $rezultati = $this->jedro->iskanje($poizvedba, $kategorija);
            $kategorije = CodexPravila::pridobiKategorije();
            
            include 'codex.php';
        } else {
            $this->prikaziDomov();
        }
    }
    
    /**
     * Obdelaj dodajanje vsebine
     */
    private function obdelajDodajanjeVsebine() {
        if (!$this->jedro->jePrijavljen()) {
            CodexFunkcije::prikaziSporocilo('error', 'Za dodajanje vsebin se morate prijaviti');
            CodexFunkcije::preusmeri('/codex-damiris/');
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $naslov = $_POST['naslov'] ?? '';
            $vsebina = $_POST['vsebina'] ?? '';
            $kategorija = $_POST['kategorija'] ?? '';
            $avtorId = $this->jedro->pridobiTrenutnegaUporabnika()['id'];
            
            $rezultat = $this->storitve->dodajVsebino($naslov, $vsebina, $kategorija, $avtorId);
            
            if ($rezultat['uspeh']) {
                CodexFunkcije::prikaziSporocilo('success', $rezultat['sporocilo']);
            } else {
                CodexFunkcije::prikaziSporocilo('error', $rezultat['napaka']);
            }
            
            CodexFunkcije::preusmeri('/codex-damiris/');
        }
    }
    
    /**
     * Obdelaj AI chat
     */
    private function obdelajAIChat() {
        if (!$this->jedro->jePrijavljen()) {
            http_response_code(401);
            echo json_encode(['uspeh' => false, 'napaka' => 'Ni prijave']);
            return;
        }
        
        $sporocilo = $_POST['sporocilo'] ?? '';
        
        if (empty($sporocilo)) {
            http_response_code(400);
            echo json_encode(['uspeh' => false, 'napaka' => 'Prazno sporočilo']);
            return;
        }
        
        header('Content-Type: application/json');
        $rezultat = $this->ai->chat($sporocilo);
        echo json_encode($rezultat);
    }
    
    /**
     * Obdelaj aktivacijo
     */
    private function obdelajAktivacijo() {
        $koda = $_GET['koda'] ?? '';
        
        if (empty($koda)) {
            CodexFunkcije::prikaziSporocilo('error', 'Manjkajoča aktivacijska koda');
            CodexFunkcije::preusmeri('/codex-damiris/');
            return;
        }
        
        $rezultat = $this->storitve->aktivirajRacun($koda);
        
        if ($rezultat['uspeh']) {
            CodexFunkcije::prikaziSporocilo('success', 'Račun uspešno aktiviran! Lahko se prijavite.');
        } else {
            CodexFunkcije::prikaziSporocilo('error', $rezultat['napaka']);
        }
        
        CodexFunkcije::preusmeri('/codex-damiris/');
    }
}
?>