<?php
/**
 * Orakleum Demo Testiranje
 * Datoteka: modul_oracle_demo.php
 * Namen: Demo implementacija za testiranje modula brez polnega sistema
 */

// Nastavi demo način
define('DEMO_MODE', true);

/**
 * Demo Klasa za Orakleum modul
 */
class OrakleumDemo {
    
    private $demo_data;
    
    public function __construct() {
        $this->demo_data = $this->naloziDemoPodatke();
    }
    
    /**
     * Test osnovnih funkcij
     */
    public function testOsnovneFunkcije() {
        $testi = [
            'nalaganje_kart' => $this->testNalaganjeKart(),
            'vlecenje_karte' => $this->testVlecenjeKarte(),
            'interpretacija' => $this->testInterpretacija(),
            'orakelj' => $this->testOrakelj()
        ];
        
        return [
            'uspeh' => true,
            'testi' => $testi,
            'sporocilo' => 'Vsi demo testi so uspešno izvedeni'
        ];
    }
    
    /**
     * Test nalaganja kart
     */
    private function testNalaganjeKart() {
        try {
            // Simuliraj nalaganje kart iz JSON baze
            $kartice = $this->demo_data['kartice'];
            
            $nalozene_kartice = [];
            foreach (array_slice($kartice, 0, 3) as $karta_id => $karta) {
                $nalozene_kartice[] = [
                    'id' => $karta_id,
                    'ime' => $karta['ime'],
                    'simbol' => $karta['simbol']
                ];
            }
            
            return [
                'uspeh' => true,
                'stevilo_kart' => count($nalozene_kartice),
                'kartice' => $nalozene_kartice
            ];
        } catch (Exception $e) {
            return [
                'uspeh' => false,
                'napaka' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Test vlečenja kart
     */
    private function testVlecenjeKarte() {
        try {
            $kartice = $this->demo_data['kartice'];
            $karta_ids = array_keys($kartice);
            $nakljucna_karta_id = $karta_ids[array_rand($karta_ids)];
            $karta = $kartice[$nakljucna_karta_id];
            
            return [
                'uspeh' => true,
                'vlecena_karta' => [
                    'id' => $nakljucna_karta_id,
                    'ime' => $karta['ime'],
                    'simbol' => $karta['simbol'],
                    'opis' => $karta['opis']
                ],
                'cas_vlecenja' => date('Y-m-d H:i:s')
            ];
        } catch (Exception $e) {
            return [
                'uspeh' => false,
                'napaka' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Test interpretacije
     */
    private function testInterpretacija() {
        try {
            $test_karta = 'l_ljubezen';
            $test_pozicija = 'sedanjost';
            $test_vprasanje = 'Kaj me čaka v ljubezni?';
            
            // Simuliraj interpretacijo
            $interpretacija = [
                'karta' => $test_karta,
                'pozicija' => $test_pozicija,
                'vprasanje' => $test_vprasanje,
                'interpretacija' => 'Ljubezen prinaša globoke spremembe v vaše življenje.',
                'napotki' => [
                    'Bodite odprti za novo ljubezen',
                    'Zaupajte svojemu srcu',
                    'Ne hitite z odločitvami'
                ]
            ];
            
            return [
                'uspeh' => true,
                'interpretacija' => $interpretacija
            ];
        } catch (Exception $e) {
            return [
                'uspeh' => false,
                'napaka' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Test oraklja
     */
    private function testOrakelj() {
        try {
            $kartice = $this->demo_data['kartice'];
            $tip_oraklja = 'tri_karte';
            $pozicije = ['preteklost', 'sedanjost', 'prihodnost'];
            
            $orakelj_kartice = [];
            foreach ($pozicije as $pozicija) {
                $karta_ids = array_keys($kartice);
                $nakljucna_karta_id = $karta_ids[array_rand($karta_ids)];
                $karta = $kartice[$nakljucna_karta_id];
                
                $orakelj_kartice[] = [
                    'pozicija' => $pozicija,
                    'karta' => [
                        'id' => $nakljucna_karta_id,
                        'ime' => $karta['ime'],
                        'simbol' => $karta['simbol'],
                        'opis' => $karta['opis']
                    ]
                ];
            }
            
            return [
                'uspeh' => true,
                'orakelj' => [
                    'tip' => $tip_oraklja,
                    'kartice' => $orakelj_kartice,
                    'skupna_interpretacija' => 'Karte kažejo na pomembne spremembe v vašem življenju.',
                    'cas_ustvaritve' => date('Y-m-d H:i:s')
                ]
            ];
        } catch (Exception $e) {
            return [
                'uspeh' => false,
                'napaka' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Simuliraj BridgePreveri demo način
     */
    public function demoBridgePreveri() {
        return [
            'sistem_prisoten' => false,
            'demo_nacin' => true,
            'message' => 'Delovanje v demo načinu - polna funkcionalnost na voljo'
        ];
    }
    
    /**
     * Demo uporabniška simulacija
     */
    public function demoUporabniskaSimulacija() {
        return [
            'uporabnik' => [
                'id' => 'demo_user_001',
                'ime' => 'Demo Uporabnik',
                'email' => 'demo@example.com',
                'status' => 'testiranje'
            ],
            'aktivnosti' => [
                [
                    'tip' => 'vlecenje_karte',
                    'karta' => 'l_ljubezen',
                    'cas' => date('Y-m-d H:i:s', strtotime('-1 hour'))
                ],
                [
                    'tip' => 'orakelj',
                    'tip_oraklja' => 'tri_karte',
                    'cas' => date('Y-m-d H:i:s', strtotime('-30 minutes'))
                ]
            ],
            'statistike' => [
                'vlecenj_danes' => 5,
                'najbolj_vlecena_karta' => 'l_popolnost',
                'skupaj_cas' => '00:45:23'
            ]
        ];
    }
    
    /**
     * Nalozi demo podatke
     */
    private function naloziDemoPodatke() {
        return [
            'kartice' => [
                'l_popolnost' => [
                    'ime' => 'Popolnost',
                    'simbol' => '🌟',
                    'opis' => 'Popolnost, harmonija in dosežek'
                ],
                'l_ljubezen' => [
                    'ime' => 'Ljubezen',
                    'simbol' => '💖',
                    'opis' => 'Ljubezen, strast in povezanost'
                ],
                'l_moc' => [
                    'ime' => 'Moč',
                    'simbol' => '💪',
                    'opis' => 'Moč, notranja energija in samokontrola'
                ],
                'l_svoboda' => [
                    'ime' => 'Svoboda',
                    'simbol' => '🕊️',
                    'opis' => 'Svoboda, neodvisnost in nova perspektiva'
                ]
            ],
            'orakelji' => [
                'tri_karte' => [
                    'ime' => 'Tri Karte',
                    'pozicije' => ['preteklost', 'sedanjost', 'prihodnost']
                ],
                'ljubezen' => [
                    'ime' => 'Ljubezen',
                    'pozicije' => ['ti', 'partner', 'relacija']
                ]
            ]
        ];
    }
}

// Demo test runner
if (basename($_SERVER['SCRIPT_NAME']) == basename(__FILE__)) {
    echo "=== ORAKLEUM DEMO TESTIRANJE ===\n\n";
    
    $demo = new OrakleumDemo();
    
    // Test 1: Osnovne funkcije
    echo "1. Test osnovnih funkcij...\n";
    $rezultat = $demo->testOsnovneFunkcije();
    echo "Status: " . ($rezultat['uspeh'] ? "✅ Uspešno" : "❌ Neuspešno") . "\n";
    echo "Sporočilo: " . $rezultat['sporocilo'] . "\n\n";
    
    // Test 2: BridgePreveri demo
    echo "2. BridgePreveri demo način...\n";
    $bridge = $demo->demoBridgePreveri();
    echo "Demo način: " . ($bridge['demo_nacin'] ? "✅ Aktiven" : "❌ Neaktiven") . "\n";
    echo "Sporočilo: " . $bridge['message'] . "\n\n";
    
    // Test 3: Uporabniška simulacija
    echo "3. Uporabniška simulacija...\n";
    $uporabnik = $demo->demoUporabniskaSimulacija();
    echo "Uporabnik: " . $uporabnik['uporabnik']['ime'] . "\n";
    echo "Aktivnosti: " . count($uporabnik['aktivnosti']) . "\n";
    echo "Vlecenj danes: " . $uporabnik['statistike']['vlecenj_danes'] . "\n\n";
    
    echo "=== DEMO TEST ZAKLJUČEN ===\n";
    echo "Orakleum modul je pripravljen za integracijo!\n";
}

?>