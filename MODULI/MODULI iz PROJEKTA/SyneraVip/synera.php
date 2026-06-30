<?php
declare(strict_types=1);

/**
 * Synera.php - Moj glavni frontend controller + API
 * Obeznostna datoteka: (ime_modula).php
 */

class Synera {
    
    private static $instanca = null;
    private $jedro;
    private $zahteva;
    
    public static function pridobiInstanco(): self {
        if (self::$instanca === null) {
            self::$instanca = new self();
        }
        return self::$instanca;
    }
    
    private function __construct() {
        $this->inicializiraj();
    }
    
    private function inicializiraj(): void {
        $this->naloziSisteme();
        $this->zahteva = $this->analizirajZahtevo();
        
        if ($this->jeAPIzahteva()) {
            $this->nastaviAPIGlave();
        }
    }
    
    private function naloziSisteme(): void {
        $sistemi = [
            'SyneraJedro.php',
            'SyneraFunkcije.php', 
            'AI_Synera.php',
            'Elementi/Statistika.php'
        ];
        
        foreach ($sistemi as $sistem) {
            if (file_exists(__DIR__ . '/' . $sistem)) {
                require_once __DIR__ . '/' . $sistem;
            }
        }
    }
    
    private function analizirajZahtevo(): array {
        return [
            'metoda' => $_SERVER['REQUEST_METHOD'] ?? 'GET',
            'pot' => $_GET['akcija'] ?? 'prikaz',
            'podatki' => $this->pridobiVhodnePodatke(),
            'je_api' => isset($_GET['api']) || strpos($_SERVER['REQUEST_URI'] ?? '', '/api/') !== false
        ];
    }
    
    private function pridobiVhodnePodatke(): array {
        if ($this->zahteva['metoda'] === 'POST') {
            return $_POST;
        }
        
        if ($this->zahteva['metoda'] === 'GET') {
            return $_GET;
        }
        
        $jsonPodatki = json_decode(file_get_contents('php://input'), true);
        return is_array($jsonPodatki) ? $jsonPodatki : [];
    }
    
    private function nastaviAPIGlave(): void {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
    }
    
    public function obdelajZahtevo(): void {
        if ($this->jeAPIzahteva()) {
            $this->obdelajAPIzahtevo();
        } else {
            $this->obdelajPrikazZahtevo();
        }
    }
    
    private function obdelajAPIzahtevo(): void {
        $akcija = $this->zahteva['pot'];
        $podatki = $this->zahteva['podatki'];
        
        $odgovor = $this->izvediAPIAkcijo($akcija, $podatki);
        echo json_encode($odgovor, JSON_PRETTY_UNICODE | JSON_PRETTY_PRINT);
    }
    
    private function izvediAPIAkcijo(string $akcija, array $podatki): array {
        return match($akcija) {
            'komanda' => $this->obdelajKomando($podatki['komanda'] ?? ''),
            'stanje' => $this->pridobiStanjeSistemov(),
            'analiza' => $this->izvediAnalizoEnergije(),
            'generiraj_sigil' => $this->generirajSigil(),
            'statistika' => $this->pridobiStatistiko(),
            'element' => $this->pridobiElement($podatki['element'] ?? ''),
            default => ['napaka' => 'Neznana API akcija: ' . $akcija]
        };
    }
    
    private function obdelajKomando(string $komanda): array {
        if (!$this->jedro) {
            return ['uspeh' => false, 'napaka' => 'Jedro ni na voljo'];
        }
        
        return $this->jedro->izvediKomando($komanda);
    }
    
    private function pridobiStanjeSistemov(): array {
        if (!$this->jedro) {
            return ['napaka' => 'Jedro ni na voljo'];
        }
        
        return [
            'uspeh' => true,
            'stanje' => $this->jedro->pridobiStanje(),
            'cas' => date('Y-m-d H:i:s')
        ];
    }
    
    private function izvediAnalizoEnergije(): array {
        if (!class_exists('AI_Synera')) {
            return ['napaka' => 'AI sistem ni na voljo'];
        }
        
        return [
            'uspeh' => true,
            'analiza' => AI_Synera::analizirajEnergijo(),
            'timestamp' => date('Y-m-d H:i:s')
        ];
    }
    
    private function generirajSigil(): array {
        if (!$this->jedro) {
            return ['napaka' => 'Jedro ni na voljo'];
        }
        
        return $this->jedro->generirajSigil();
    }
    
    private function pridobiStatistiko(): array {
        if (!class_exists('SyneraFunkcije')) {
            return ['napaka' => 'Funkcije niso na voljo'];
        }
        
        return [
            'uspeh' => true,
            'statistika' => SyneraFunkcije::pridobiStatistiko(),
            'povezave' => SyneraFunkcije::preveriPovezave()
        ];
    }
    
    private function pridobiElement(string $element): array {
        $elementRazred = ucfirst($element);
        $elementDatoteka = __DIR__ . '/Elementi/' . $elementRazred . '.php';
        
        if (!file_exists($elementDatoteka)) {
            return ['napaka' => "Element $element ne obstaja"];
        }
        
        require_once $elementDatoteka;
        
        if (!class_exists($elementRazred) || !method_exists($elementRazred, 'pridobiPodatke')) {
            return ['napaka' => "Element $element ni pravilno definiran"];
        }
        
        return [
            'uspeh' => true,
            'element' => $element,
            'podatki' => call_user_func([$elementRazred, 'pridobiPodatke'])
        ];
    }
    
    private function obdelajPrikazZahtevo(): void {
        $akcija = $this->zahteva['pot'];
        
        switch ($akcija) {
            case 'komandna_plosca':
                $this->prikaziKomandnoPlosco();
                break;
            case 'statistika':
                $this->prikaziStatistiko();
                break;
            case 'elementi':
                $this->prikaziElemente();
                break;
            case 'nastavitve':
                $this->prikaziNastavitve();
                break;
            default:
                $this->prikaziDomov();
        }
    }
    
    private function prikaziDomov(): void {
        echo "<div class='synera-vsebina'>";
        echo "<h1>🔮 Dobrodošli v Syneri</h1>";
        echo "<p>Platforma za simbole, rune in mantre z AI podporo</p>";
        $this->prikaziKomandnoPlosco();
        
        if (class_exists('Statistika')) {
            echo Statistika::prikazi();
        }
        
        echo "</div>";
    }
    
    private function prikaziKomandnoPlosco(): void {
        echo "<div class='komandna-plosca'>";
        echo "<h2>🎯 Komandna Plošča</h2>";
        
        if ($this->jedro) {
            $stanje = $this->jedro->pridobiStanje();
            echo "<div class='stanje-sistemov'>";
            foreach ($stanje['sistemi'] as $sistem) {
                $status = $sistem['status'];
                $barva = strpos($status, '🟢') !== false ? 'zelen' : 'rumen';
                echo "<div class='sistem status-$barva'>";
                echo "<strong>{$sistem['ime']}</strong>: {$sistem['status']}";
                echo "</div>";
            }
            echo "</div>";
        }
        
        echo "<div class='komandne-moznosti'>";
        echo "<button onclick='izvediKomando(\"stanje\")' class='gumb'>📊 Stanje Sistemov</button>";
        echo "<button onclick='izvediKomando(\"analiza\")' class='gumb'>🔮 Analiza Energije</button>";
        echo "<button onclick='izvediKomando(\"sigil\")' class='gumb'>🛡️ Generiraj Sigil</button>";
        echo "<button onclick='izvediKomando(\"pomoc\")' class='gumb'>❓ Pomoč</button>";
        echo "</div>";
        echo "</div>";
    }
    
    private function prikaziStatistiko(): void {
        echo "<div class='statistika-stran'>";
        echo "<h2>📈 Statistika Sistemov</h2>";
        
        if (class_exists('Statistika')) {
            echo Statistika::prikazi();
        }
        
        echo "</div>";
    }
    
    private function prikaziElemente(): void {
        echo "<div class='elementi-stran'>";
        echo "<h2>🧩 Elementi Platforme</h2>";
        echo "<p>Razpoložljivi elementi za prikaz in uporabo:</p>";
        
        $elementi = ['Statistika'];
        foreach ($elementi as $element) {
            $datoteka = __DIR__ . '/Elementi/' . $element . '.php';
            if (file_exists($datoteka)) {
                echo "<div class='element-kljuc'>• $element</div>";
            }
        }
        
        echo "</div>";
    }
    
    private function prikaziNastavitve(): void {
        echo "<div class='nastavitve-stran'>";
        echo "<h2>⚙️ Nastavitve Sistemov</h2>";
        echo "<p>Konfiguracija platforme Synera</p>";
        echo "</div>";
    }
    
    private function jeAPIzahteva(): bool {
        return $this->zahteva['je_api'] || $this->zahteva['metoda'] === 'POST';
    }
}

// Avtomatski zagon controllerja
try {
    $synera = Synera::pridobiInstanco();
    $synera->obdelajZahtevo();
} catch (Exception $napaka) {
    header('Content-Type: application/json');
    echo json_encode([
        'uspeh' => false,
        'napaka' => 'Napaka pri zagonu: ' . $napaka->getMessage()
    ], JSON_PRETTY_UNICODE);
}
?>