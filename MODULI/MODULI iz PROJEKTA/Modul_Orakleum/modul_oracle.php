<?php
/**
 * Modul: Orakleum - Tarot & Orakelji
 * Datoteka: modul_oracle.php
 * Namen: Glavna datoteka modula z osnovnimi funkcijami
 * Verzija: 1.0.0
 * Avtor: MiniMax Agent
 */

// Preveri direktni dostop
si_preveri_direktni_dostop();

/**
 * Class ModulOrakleum
 * Glavna klasa za Orakleum modul
 */
class ModulOrakleum {
    
    private $loader;
    private $cache;
    private $db;
    private $config;
    
    public function __construct() {
        $this->loader = ModularniLoader::pridobiInstanco();
        $this->cache = Predpomnilnik::pridobiInstanco();
        $this->db = new BazaFunkcije();
        $this->config = $this->naloziNastavitve();
    }
    
    /**
     * Pridobi osnovne podatke modula
     */
    public function pridobiOsnovnePodatke() {
        return [
            'ime' => 'Orakleum',
            'razlicica' => '1.0.0',
            'opis' => 'Tarot & Orakelji - Mystic Cards sistem',
            'status' => 'aktiven',
            'zadnja_posodobitev' => date('Y-m-d H:i:s'),
            'statistike' => $this->pridobiStatistike(),
            'komponente' => $this->naloziKomponente()
        ];
    }
    
    /**
     * Pridobi vsebino modula za prikaz
     */
    public function pridobiVsebino($zahteva = []) {
        $vsebina = [
            'naslov' => '🎭 Orakleum - Mistične Karte',
            'podnaslov' => 'Tarot & Orakelji sistem',
            'kartice' => $this->pridobiVseKartice(),
            'interpretacije' => $this->pridobiInterpretacije(),
            'napotki' => $this->pridobiNapotke()
        ];
        
        return $vsebina;
    }
    
    /**
     * Obdela zahtevek za akcijo
     */
    public function obdelajZahtevek($akcija, $parametri = []) {
        try {
            switch ($akcija) {
                case 'vleci_karto':
                    return $this->vleciKarto($parametri);
                case 'interpretiraj':
                    return $this->interpretirajKarto($parametri);
                case 'odpri_orakel':
                    return $this.odpriOrakel($parametri);
                case 'shrani_interpretacijo':
                    return $this->shraniInterpretacijo($parametri);
                default:
                    return ['uspeh' => false, 'napaka' => 'Neznana akcija'];
            }
        } catch (Exception $e) {
            $this->loader->zabeleziNapako('Orakleum: ' . $e->getMessage());
            return ['uspeh' => false, 'napaka' => 'Napaka pri obdelavi zahtevka'];
        }
    }
    
    /**
     * Vleci karto iz mesta
     */
    private function vleciKarto($parametri) {
        $pozicija = $parametri['pozicija'] ?? 'nakljucno';
        $mesta = $this->pridobiMesta();
        
        if ($pozicija === 'nakljucno') {
            $karta = $mesta[array_rand($mesta)];
        } else {
            $karta = $mesta[$pozicija] ?? $mesta[array_rand($mesta)];
        }
        
        $karta['vlecenje_cas'] = date('Y-m-d H:i:s');
        $karta['id'] = uniqid('karta_');
        
        return [
            'uspeh' => true,
            'karta' => $karta,
            'sporocilo' => 'Karta je bila vlečena uspešno'
        ];
    }
    
    /**
     * Interpretiraj karto
     */
    private function interpretirajKarto($parametri) {
        $karta_id = $parametri['karta_id'];
        $vprasanje = $parametri['vprasanje'] ?? '';
        $uporabnik_id = $parametri['uporabnik_id'] ?? 'gost';
        
        // Pridobi interpretacijo iz baze
        $interpretacija = $this->db->pridobiInterpretacijoKarte($karta_id);
        
        if (!$interpretacija) {
            // Generiraj novo interpretacijo
            $interpretacija = $this->generirajInterpretacijo($karta_id, $vprasanje);
            $this->db->shraniInterpretacijo($karta_id, $interpretacija);
        }
        
        return [
            'uspeh' => true,
            'interpretacija' => $interpretacija,
            'dodatni_napotki' => $this->pridobiDodatneNapotke($karta_id)
        ];
    }
    
    /**
     * Odpri orakel
     */
    private function odpriOrakel($parametri) {
        $tip_oraklja = $parametri['tip'] ?? 'tarot';
        $stevilo_kart = $parametri['stevilo'] ?? 3;
        
        $orakel = [
            'tip' => $tip_oraklja,
            'kartice' => [],
            'interpretacija' => [],
            'cas' => date('Y-m-d H:i:s')
        ];
        
        for ($i = 0; $i < $stevilo_kart; $i++) {
            $karta = $this->vleciKarto(['pozicija' => $i]);
            $orakel['kartice'][] = $karta['karta'];
        }
        
        return [
            'uspeh' => true,
            'orakel' => $orakel,
            'celotna_interpretacija' => $this->interpretirajOrakel($orakel['kartice'])
        ];
    }
    
    /**
     * Pridobi mesta kart
     */
    private function pridobiMesta() {
        return [
            [
                'ime' => 'Preteklost',
                'pozicija' => 0,
                'opis' => 'Kaj je bilo',
                'barva' => '#8B4513'
            ],
            [
                'ime' => 'Sedanjost', 
                'pozicija' => 1,
                'opis' => 'Kar je',
                'barva' => '#228B22'
            ],
            [
                'ime' => 'Prihodnost',
                'pozicija' => 2,
                'opis' => 'Kar bo',
                'barva' => '#4169E1'
            ],
            [
                'ime' => 'Notranjost',
                'pozicija'  => 3,
                'opis' => 'Tvoj notranji svet',
                'barva' => '#9932CC'
            ],
            [
                'ime' => 'Zunanjost',
                'pozicija' => 4,
                'opis' => 'Zunanji vplivi',
                'barva' => '#FF6347'
            ],
            [
                'ime' => 'Pot',
                'pozicija' => 5,
                'opis' => 'Priporočena pot',
                'barva' => '#FFD700'
            ]
        ];
    }
    
    /**
     * Pridobi vse kartice
     */
    private function pridobiVseKartice() {
        $cache_key = 'orakleum_kartice_v3';
        $kartice = $this->cache->preberi($cache_key);
        
        if (!$kartice) {
            $kartice = [
                [
                    'id' => 'l popol',
                    'ime' => 'Popolnost',
                    'simbol' => '🌟',
                    'opis' => 'Popolnost in harmonija',
                    'element' => 'zrak',
                    'znamenje' => 'vsem',
                    'interpretacija' => [
                        'v_preteklosti' => 'Uspešno zaključena faza',
                        'v_sedanjosti' => ' trenutno doseženo ravnovesje',
                        'v_prihodnosti' => 'priložnost za popolnost'
                    ]
                ],
                [
                    'id' => 'l ljubezen',
                    'ime' => 'Ljubezen',
                    'simbol' => '💖',
                    'opis' => 'Ljubezen in strast',
                    'element' => 'voda',
                    'znamenje' => 'ljubezen',
                    'interpretacija' => [
                        'v_preteklosti' => 'ljubezenska izkušnja',
                        'v_sedanjosti' => 'trenutna ljubezenska situacija',
                        'v_prihodnosti' => 'ljubezenska priložnost'
                    ]
                ]
            ];
            
            $this->cache->shrani($cache_key, $kartice, 3600);
        }
        
        return $kartice;
    }
    
    /**
     * Pridobi statistike
     */
    private function pridobiStatistike() {
        return [
            'skupaj_vlecenj' => $this->db->pridobiStevilo('vlecenja_kart'),
            'najbolj_vlečena_karta' => $this->db->pridobiNajboljVlečeno('karta_id'),
            'aktivni_uporabniki' => $this->db->pridobiAktivneUporabnike(30),
            'povprecna_interpretacija' => $this->db->povprecnaInterpretacija()
        ];
    }
    
    /**
     * Naloži nastavitve modula
     */
    private function naloziNastavitve() {
        $nastavitve_datoteka = dirname(__FILE__) . '/modul_oracle_nastavitve.php';
        if (file_exists($nastavitve_datoteka)) {
            return require $nastavitve_datoteka;
        }
        
        return [
            'debug' => false,
            'cache_enabled' => true,
            'max_interpretacij' => 100,
            ' permitido_nakljucno_vlecenje' => true,
            'tip_vlecenja' => 'z_navratom'
        ];
    }
    
    /**
     * Naloži komponente
     */
    private function naloziKomponente() {
        $komponente_datoteka = dirname(__FILE__) . '/Elementi/komponente.php';
        if (file_exists($komponente_datoteka)) {
            return require $komponente_datoteka;
        }
        
        return [
            'kartice' => true,
            'interpretacije' => true,
            'orakelji' => true,
            'statistike' => true,
            'uporabniki' => false
        ];
    }
    
    /**
     * Dodatni metod za generiranje interpretacije
     */
    private function generirajInterpretacijo($karta_id, $vprasanje) {
        // Logika za generiranje interpretacije
        $osnovna_interpretacija = "Karta " . $karta_id . " kaže na pomembne spremembe.";
        
        if (!empty($vprasanje)) {
            $osnovna_interpretacija .= " Glede na vaše vprašanje: '" . $vprasanje . "'";
        }
        
        return [
            'osnovna' => $osnovna_interpretacija,
            'podrobna' => $this->generirajPodrobnoInterpretacijo($karta_id),
            'napotki' => $this->generirajNapotke($karta_id),
            'cas_generiranja' => date('Y-m-d H:i:s')
        ];
    }
    
    /**
     * Generiraj podrobno interpretacijo
     */
    private function generirajPodrobnoInterpretacijo($karta_id) {
        return "Podrobna interpretacija kartice " . $karta_id . ".";
    }
    
    /**
     * Generiraj napotke
     */
    private function generirajNapotke($karta_id) {
        return [
            'preberi_vprasaj' => "Preberite karto še enkrat z novim vprašanjem",
            'deluj_v_dobri_vere' => "Akcija naj bo v dobri veri in ljubezni",
            'cas_za_razmislek' => "Vzemite si čas za razmislek pred odločitvijo"
        ];
    }
    
    /**
     * Pridobi dodatne napotke
     */
    private function pridobiDodatneNapotke($karta_id) {
        return [
            'energija' => 'pozitivna',
            'priporocena_barva' => '#FFD700',
            'priporocena_ura' => 'jutri zjutraj',
            'priporocena_smer' => 'sever'
        ];
    }
    
    /**
     * Interpretiraj orakel
     */
    private function interpretirajOrakel($kartice) {
        $interpretacija = [
            'skupaj_pomens' => "Karte skupaj prikazujejo celovito sliko.",
            'vodic_za_akcijo' => "Sledite nasvetom kart za najboljše rezultate.",
            'frekvenca' => 'visoka'
        ];
        
        return $interpretacija;
    }
}

// PHP test del - če se izvaja direktno
if (basename($_SERVER['SCRIPT_NAME']) == basename(__FILE__)) {
    // Demo test
    $modul = new ModulOrakleum();
    echo "=== TEST ORACLE MODULA ===\n";
    print_r($modul->pridobiOsnovnePodatke());
    echo "\n" . $modul->obdelajZahtevek('vleci_karto', [])['sporocilo'] . "\n";
}

?>