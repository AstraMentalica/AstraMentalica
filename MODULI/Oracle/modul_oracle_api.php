<?php
/**
 * Orakleum API Endpoint
 * Datoteka: modul_oracle_api.php
 * Namen: RESTful API za zunanji dostop do Orakleum funkcionalnosti
 */

// Nastavi headerje za API
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Preveri OPTIONS zahtevo (CORS preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Preveri direktni dostop in varnost
si_preveri_direktni_dostop();

/**
 * Orakleum API Handler
 */
class OrakleumAPI {
    
    private $modul;
    private $pravila;
    
    public function __construct() {
        // Vključi potrebne datoteke
        require_once __DIR__ . '/modul_oracle.php';
        require_once __DIR__ . '/modul_oracle_pravila.php';
        require_once __DIR__ . '/modul_oracle_funkcije.php';
        
        $this->modul = new ModulOrakleum();
        $this->pravila = new OrakleumPravila();
    }
    
    /**
     * Obdela API zahtevo
     */
    public function obdelajZahtevo() {
        try {
            // Preveri metodo
            $metoda = $_SERVER['REQUEST_METHOD'];
            $putanja = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            $deli = explode('/', trim($putanja, '/'));
            
            // Odstrani 'api' iz poti
            $deli = array_filter($deli, function($del) { return $del !== 'api'; });
            $deli = array_values($deli);
            
            // Preveri format odgovora
            $format = $_GET['format'] ?? 'json';
            
            // Usmeri zahtevo
            $rezultat = $this->usmeriZahtevo($metoda, $deli, $_GET, $_POST);
            
            // Vrni odgovor
            $this->vrniOdgovor($rezultat, $format);
            
        } catch (Exception $e) {
            $this->vrniNapako(500, 'API_ERROR', $e->getMessage());
        }
    }
    
    /**
     * Usmeri zahtevo na ustrezno metodo
     */
    private function usmeriZahtevo($metoda, $deli, $get, $post) {
        $endpoint = $deli[0] ?? '';
        
        switch ($endpoint) {
            case 'kartice':
                return $this->handleKartice($metoda, $deli, $get, $post);
                
            case 'vleci':
                return $this->handleVleci($metoda, $deli, $get, $post);
                
            case 'interpretiraj':
                return $this->handleInterpretiraj($metoda, $deli, $get, $post);
                
            case 'orakelj':
                return $this->handleOrakelj($metoda, $deli, $get, $post);
                
            case 'statistike':
                return $this->handleStatistike($metoda, $deli, $get, $post);
                
            case 'pozicije':
                return $this->handlePozicije($metoda, $deli, $get, $post);
                
            case 'health':
                return $this->handleHealthCheck();
                
            default:
                return $this->vrniNapako(404, 'NOT_FOUND', 'Endpoint ne obstaja');
        }
    }
    
    /**
     * Endpoint za kartice
     */
    private function handleKartice($metoda, $deli, $get, $post) {
        if ($metoda !== 'GET') {
            return $this->vrniNapako(405, 'METHOD_NOT_ALLOWED', 'Samo GET metoda je dovoljena');
        }
        
        // Pridobi vse kartice ali specifično karto
        if (isset($deli[1])) {
            $karta_id = $deli[1];
            $karta = oracle_pridobi_podatke_karte($karta_id);
            
            if (!$karta) {
                return $this->vrniNapako(404, 'CARD_NOT_FOUND', 'Karta ne obstaja');
            }
            
            return [
                'uspeh' => true,
                'podatki' => $karta
            ];
        } else {
            // Vrni vse kartice
            $kartice = [];
            $json_baza = require __DIR__ . '/modul_oracle_jsonbaza.php';
            
            foreach ($json_baza['kartice'] as $id => $karta) {
                $kartice[] = [
                    'id' => $id,
                    'ime' => $karta['ime'],
                    'simbol' => $karta['simbol'],
                    'opis' => $karta['kratek_opis'],
                    'element' => $karta['element']
                ];
            }
            
            return [
                'uspeh' => true,
                'podatki' => [
                    'kartice' => $kartice,
                    'skupaj' => count($kartice)
                ]
            ];
        }
    }
    
    /**
     * Endpoint za vlečenje kart
     */
    private function handleVleci($metoda, $deli, $get, $post) {
        if ($metoda !== 'POST') {
            return $this->vrniNapako(405, 'METHOD_NOT_ALLOWED', 'Samo POST metoda je dovoljena');
        }
        
        // Preveri dostop
        $dostop = $this->pravila::preveriDostop(null, 'vlecenje');
        if (!$dostop['uspeh']) {
            return $this->vrniNapako(403, 'ACCESS_DENIED', $dostop['razlog']);
        }
        
        // Preveri rate limit
        $ip = oracle_pridobiClientIP();
        $rate_limit = $this->pravila::preveriRateLimit($ip, 'vlecenje');
        if (!$rate_limit['uspeh']) {
            return $this->vrniNapako(429, 'RATE_LIMIT_EXCEEDED', $rate_limit['razlog']);
        }
        
        $pozicija = $post['pozicija'] ?? 'nakljucno';
        $vprasanje = $post['vprasanje'] ?? '';
        
        // Validiraj vprašanje
        $validacija = $this->pravila::preveriVprasanj($vprasanje, 'navadno');
        if (!$validacija['uspeh']) {
            return $this->vrniNapako(400, 'INVALID_QUESTION', $validacija['razlog']);
        }
        
        // Vleci karto
        $rezultat = $this->modul->obdelajZahtevek('vleci_karto', [
            'pozicija' => $pozicija,
            'vprasanje' => oracle_sanitizirajVnos($vprasanje)
        ]);
        
        if ($rezultat['uspeh']) {
            // Shrani statistiko
            oracle_shrani_vlecenje(
                $rezultat['karta']['id'], 
                'api_user', 
                'vleci_karto',
                $vprasanje
            );
        }
        
        return $rezultat;
    }
    
    /**
     * Endpoint za interpretacijo
     */
    private function handleInterpretiraj($metoda, $deli, $get, $post) {
        if ($metoda !== 'POST') {
            return $this->vrniNapako(405, 'METHOD_NOT_ALLOWED', 'Samo POST metoda je dovoljena');
        }
        
        // Preveri dostop
        $dostop = $this->pravila::preveriDostop(null, 'interpretacija');
        if (!$dostop['uspeh']) {
            return $this->vrniNapako(403, 'ACCESS_DENIED', $dostop['razlog']);
        }
        
        $karta_id = $post['karta_id'] ?? '';
        $vprasanje = $post['vprasanje'] ?? '';
        
        if (empty($karta_id)) {
            return $this->vrniNapako(400, 'MISSING_CARD_ID', 'karta_id je obvezen parameter');
        }
        
        // Preveri karto
        $preverka = $this->pravila::preveriKarto($karta_id);
        if (!$preverka['uspeh']) {
            return $this->vrniNapako(404, 'CARD_NOT_FOUND', $preverka['razlog']);
        }
        
        // Interpretiraj
        $rezultat = $this->modul->obdelajZahtevek('interpretiraj', [
            'karta_id' => $karta_id,
            'vprasanje' => oracle_sanitizirajVnos($vprasanje)
        ]);
        
        return $rezultat;
    }
    
    /**
     * Endpoint za orakleje
     */
    private function handleOrakelj($metoda, $deli, $get, $post) {
        if ($metoda !== 'POST') {
            return $this->vrniNapako(405, 'METHOD_NOT_ALLOWED', 'Samo POST metoda je dovoljena');
        }
        
        // Preveri dostop
        $dostop = $this->pravila::preveriDostop(null, 'orakelj');
        if (!$dostop['uspeh']) {
            return $this->vrniNapako(403, 'ACCESS_DENIED', $dostop['razlog']);
        }
        
        $tip_oraklja = $post['tip'] ?? 'tri_karte';
        $stevilo_kart = $post['stevilo'] ?? 3;
        $vprasanje = $post['vprasanje'] ?? '';
        
        // Ustvari orakelj
        $rezultat = $this->modul->obdelajZahtevek('odpri_orakel', [
            'tip' => $tip_oraklja,
            'stevilo' => $stevilo_kart,
            'vprasanje' => oracle_sanitizirajVnos($vprasanje)
        ]);
        
        return $rezultat;
    }
    
    /**
     * Endpoint za statistike
     */
    private function handleStatistike($metoda, $deli, $get, $post) {
        if ($metoda !== 'GET') {
            return $this->vrniNapako(405, 'METHOD_NOT_ALLOWED', 'Samo GET metoda je dovoljena');
        }
        
        $tip_statistike = $deli[1] ?? 'splosno';
        
        switch ($tip_statistike) {
            case 'splosno':
                $statistike = oracle_pridobi_statistike();
                break;
                
            case 'kartice':
                $statistike = $this->getKartaStatistike();
                break;
                
            case 'orakelji':
                $statistike = $this->getOrakeljStatistike();
                break;
                
            default:
                return $this->vrniNapako(404, 'INVALID_STATISTICS_TYPE', 'Neznan tip statistike');
        }
        
        return [
            'uspeh' => true,
            'podatki' => $statistike
        ];
    }
    
    /**
     * Endpoint za pozicije
     */
    private function handlePozicije($metoda, $deli, $get, $post) {
        if ($metoda !== 'GET') {
            return $this->vrniNapako(405, 'METHOD_NOT_ALLOWED', 'Samo GET metoda je dovoljena');
        }
        
        $json_baza = require __DIR__ . '/modul_oracle_jsonbaza.php';
        
        return [
            'uspeh' => true,
            'podatki' => [
                'pozicije' => $json_baza['pozicije'],
                'mesta' => $json_baza['mesta'],
                'orakelji' => $json_baza['orakelji']
            ]
        ];
    }
    
    /**
     * Health check endpoint
     */
    private function handleHealthCheck() {
        return [
            'uspeh' => true,
            'status' => 'OK',
            'verzija' => '1.0.0',
            'cas' => date('Y-m-d H:i:s'),
            'test' => [
                'kartice' => true,
                'baza' => true,
                'pravila' => true
            ]
        ];
    }
    
    /**
     * Vrni uspešen odgovor
     */
    private function vrniOdgovor($podatki, $format = 'json') {
        http_response_code(200);
        
        if ($format === 'json') {
            echo json_encode($podatki, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        } else {
            echo "Format {$format} ni podprt";
        }
    }
    
    /**
     * Vrni napako
     */
    private function vrniNapako($status_koda, $koda, $sporocilo) {
        http_response_code($status_koda);
        
        $odgovor = [
            'uspeh' => false,
            'napaka' => [
                'koda' => $koda,
                'sporocilo' => $sporocilo,
                'cas' => date('Y-m-d H:i:s')
            ]
        ];
        
        echo json_encode($odgovor, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
    
    /**
     * Pridobi statistike kart
     */
    private function getKartaStatistike() {
        $json_baza = require __DIR__ . '/modul_oracle_jsonbaza.php';
        $karta_stevilo = $json_baza['statistike']['karta_stevilo'] ?? [];
        
        arsort($karta_stevilo);
        
        return [
            'najbolj_vlecena' => array_key_first($karta_stevilo),
            'najmanj_vlecena' => array_key_last($karta_stevilo),
            'razporeditev' => $karta_stevilo,
            'skupno_vlecenj' => array_sum($karta_stevilo)
        ];
    }
    
    /**
     * Pridobi statistike orakljev
     */
    private function getOrakeljStatistike() {
        // Placeholder - implementiral bi dejansko logiko
        return [
            'tipi_orakljev' => [
                'tri_karte' => 150,
                'sest_kart' => 89,
                'ljubezen' => 67,
                'kariera' => 45
            ],
            'najbolj_priljubljen' => 'tri_karte'
        ];
    }
}

// Inicializiraj API
$api = new OrakleumAPI();
$api->obdelajZahtevo();

?>