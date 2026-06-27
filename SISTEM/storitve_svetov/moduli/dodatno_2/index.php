<?php
/**
 * MODUL_MYSTICA - Glavni vhodni point sistema
 * Datoteka: /Modul_Mystica/index.php
 */

class ModulMystica {
    private $konfiguracija;
    private $stanjeSistema;
    private $uporabniskiToken;
    private $komunikacijskiSistem;
    
    public function __construct() {
        $this->inicializirajSistem();
        $this->naloziKonfiguracijo();
        $this->pripraviKomunikacijo();
    }
    
    private function inicializirajSistem() {
        $this->stanjeSistema = [
            'naziv' => 'Modul Mystica',
            'verzija' => '1.0',
            'stanje' => 'inicializiran',
            'zadnja_aktivnost' => date('Y-m-d H:i:s'),
            'magicna_moc' => 100
        ];
    }
    
    private function naloziKonfiguracijo() {
        $this->konfiguracija = [
            'nastavitve_portala' => [
                'naziv' => 'Aurora Mystica Portal',
                'opis' => 'Skrivni portal za magicne izkusnje',
                'jezik' => 'slovenscina',
                'casovni_pas' => 'Europe/Ljubljana',
                'debug_mode' => true
            ],
            'uporabniske_stopnje' => ['S0', 'S1', 'S2', 'S3', 'S4', 'S5'],
            'varnostne_nastavitve' => [
                'max_prijav' => 3,
                'dolzina_sea' => 3600,
                'token_veljavnost' => 7200
            ]
        ];
    }
    
    private function pripraviKomunikacijo() {
        $this->komunikacijskiSistem = [
            'tipi_sporocil' => [
                'magicni_dogodki',
                'uporabniske_aktivnosti', 
                'sistemska_obvestila',
                'cron_naloge'
            ],
            'stanje' => 'pripravljen',
            'zadnja_komunikacija' => time()
        ];
    }
    
    public function obdelajVhod($vhodniPodatki) {
        $token = $vhodniPodatki['token'] ?? '';
        $ukaz = $vhodniPodatki['ukaz'] ?? '';
        $podatki = $vhodniPodatki['podatki'] ?? [];
        
        // Preveri veljavnost tokena
        if (!$this->preveriToken($token)) {
            return $this->vrniNapako('Neveljaven ali manjkajoc token');
        }
        
        // Obdelaj ukaz
        switch ($ukaz) {
            case 'prijava':
                return $this->obdelajPrijavo($podatki);
                
            case 'magicni_dogodek':
                return $this->zazeniMagicniDogodek($podatki);
                
            case 'pridobi_stanje':
                return $this->pridobiStanjeSistema();
                
            case 'nastavi_cron':
                return $this->nastaviCronNalogo($podatki);
                
            case 'komunikacija':
                return $this->obdelajKomunikacijo($podatki);
                
            default:
                return $this->vrniNapako('Neznan ukaz: ' . $ukaz);
        }
    }
    
    private function preveriToken($token) {
        if (empty($token)) {
            return false;
        }
        
        // Preveri format tokena (vsaj 10 znakov)
        if (strlen($token) < 10) {
            return false;
        }
        
        $this->uporabniskiToken = $token;
        return true;
    }
    
    private function obdelajPrijavo($podatki) {
        $uporabniskoIme = $podatki['uporabnisko_ime'] ?? '';
        $geslo = $podatki['geslo'] ?? '';
        
        if (empty($uporabniskoIme) || empty($geslo)) {
            return $this->vrniNapako('Manjkajo prijavni podatki');
        }
        
        // Simulacija uspesne prijave
        $uspesnaPrijava = (strlen($uporabniskoIme) >= 3 && strlen($geslo) >= 6);
        
        if ($uspesnaPrijava) {
            $novToken = $this->generirajNovToken();
            
            return [
                'uspeh' => true,
                'sporocilo' => 'Uspešna prijava',
                'token' => $novToken,
                'uporabnik' => [
                    'id' => rand(1000, 9999),
                    'uporabnisko_ime' => $uporabniskoIme,
                    'stopnja' => 'S' . rand(1, 3),
                    'magicne_tocke' => rand(50, 500)
                ],
                'stanje_portala' => $this->pridobiMagicnoStanje()
            ];
        } else {
            return $this->vrniNapako('Neuspesna prijava');
        }
    }
    
    private function zazeniMagicniDogodek($podatki) {
        $tipDogodka = $podatki['tip'] ?? 'nakljucni';
        $lokacija = $podatki['lokacija'] ?? 'osrednja_dvorana';
        
        $dogodek = $this->generirajMagicniDogodek($tipDogodka, $lokacija);
        
        return [
            'uspeh' => true,
            'sporocilo' => 'Magicni dogodek aktiviran',
            'dogodek' => $dogodek,
            'vpliv_na_magijo' => $this->izracunajVplivNaMagijo($dogodek)
        ];
    }
    
    private function generirajMagicniDogodek($tip, $lokacija) {
        $mozniDogodki = [
            'nakljucni' => [
                'neocakovano_znanje' => [
                    'opis' => 'Odkril si skrito resnico o sebi',
                    'moc' => rand(20, 80),
                    'trajanje' => rand(300, 1800)
                ],
                'srecanje_mojstra' => [
                    'opis' => 'Mojster te je povabil na dodatno ucenje',
                    'moc' => rand(40, 90),
                    'trajanje' => rand(600, 3600)
                ]
            ],
            'ritual' => [
                'klic_elementov' => [
                    'opis' => 'Uspešno priklical si sile narave',
                    'moc' => rand(60, 95),
                    'trajanje' => rand(900, 7200)
                ]
            ]
        ];
        
        $izbraniTip = $mozniDogodki[$tip] ?? $mozniDogodki['nakljucni'];
        $kljucDogodka = array_rand($izbraniTip);
        $dogodek = $izbraniTip[$kljucDogodka];
        
        return [
            'id' => uniqid('dogodek_'),
            'tip' => $tip,
            'lokacija' => $lokacija,
            'naziv' => $kljucDogodka,
            'opis' => $dogodek['opis'],
            'magicna_moc' => $dogodek['moc'],
            'trajanje' => $dogodek['trajanje'],
            'cas_aktivacije' => time(),
            'cas_poteka' => time() + $dogodek['trajanje']
        ];
    }
    
    private function izracunajVplivNaMagijo($dogodek) {
        $osnovniVpliv = $dogodek['magicna_moc'] / 10;
        $dodatniBonus = ($dogodek['trajanje'] > 1800) ? 5 : 0;
        
        return min(100, $osnovniVpliv + $dodatniBonus);
    }
    
    private function pridobiStanjeSistema() {
        $this->stanjeSistema['zadnja_aktivnost'] = date('Y-m-d H:i:s');
        $this->stanjeSistema['magicna_moc'] = max(10, $this->stanjeSistema['magicna_moc'] - rand(1, 5));
        
        return [
            'uspeh' => true,
            'stanje' => $this->stanjeSistema,
            'konfiguracija' => $this->konfiguracija,
            'komunikacija' => $this->komunikacijskiSistem
        ];
    }
    
    private function nastaviCronNalogo($podatki) {
        $tipNaloge = $podatki['tip'] ?? 'redna';
        $interval = $podatki['interval'] ?? 3600;
        $ukaz = $podatki['ukaz'] ?? '';
        
        if (empty($ukaz)) {
            return $this->vrniNapako('Manjka ukaz za cron nalogo');
        }
        
        $cronNaloga = [
            'id' => uniqid('cron_'),
            'tip' => $tipNaloge,
            'ukaz' => $ukaz,
            'interval' => $interval,
            'naslednje_izvajanje' => time() + $interval,
            'zadnje_izvajanje' => null,
            'stanje' => 'aktivna'
        ];
        
        return [
            'uspeh' => true,
            'sporocilo' => 'Cron naloga uspešno nastavljena',
            'cron_naloga' => $cronNaloga
        ];
    }
    
    private function obdelajKomunikacijo($podatki) {
        $sporocilo = $podatki['sporocilo'] ?? '';
        $prejemnik = $podatki['prejemnik'] ?? 'sistem';
        
        if (empty($sporocilo)) {
            return $this->vrniNapako('Prazno sporocilo');
        }
        
        $this->komunikacijskiSistem['zadnja_komunikacija'] = time();
        
        return [
            'uspeh' => true,
            'sporocilo' => 'Sporocilo prejeto',
            'odgovor' => $this->generirajOdgovor($sporocilo),
            'prejemnik' => $prejemnik,
            'casovni_zig' => date('Y-m-d H:i:s')
        ];
    }
    
    private function generirajOdgovor($sporocilo) {
        $sporocilo = strtolower($sporocilo);
        
        if (strpos($sporocilo, 'stanje') !== false) {
            return 'Sistem deluje normalno. Magicna moc: ' . $this->stanjeSistema['magicna_moc'] . '%';
        } elseif (strpos($sporocilo, 'magic') !== false) {
            return 'Magicni tokovi so stabilni. Portal je odprt za izkusnje.';
        } elseif (strpos($sporocilo, 'help') !== false || strpos($sporocilo, 'pomoc') !== false) {
            return 'Dostopni ukazi: prijava, magicni_dogodek, pridobi_stanje, nastavi_cron, komunikacija';
        } else {
            return 'Razumem vase sporocilo. Kako vam lahko pomagam pri magicnih izkusnjah?';
        }
    }
    
    private function generirajNovToken() {
        return 'token_' . bin2hex(random_bytes(16)) . '_' . time();
    }
    
    private function pridobiMagicnoStanje() {
        return [
            'portal_odprt' => true,
            'magicni_tokovi' => 'aktivni',
            'energija' => rand(70, 100) . '%',
            'povezava_vesolje' => 'stabilna',
            'dostopne_sobe' => ['glavna_dvorana', 'knjiznica', 'vrt_alhemije']
        ];
    }
    
    private function vrniNapako($sporocilo) {
        return [
            'uspeh' => false,
            'napaka' => $sporocilo,
            'cas' => date('Y-m-d H:i:s')
        ];
    }
    
    public function __destruct() {
        // Shrani stanje ob uničenju
        $this->stanjeSistema['stanje'] = 'zakljucen';
    }
}

// Glavna izvajalna koda
header('Content-Type: application/json; charset=utf-8');

try {
    $modulMystica = new ModulMystica();
    
    // Preveri ali je zahteva POST z JSON podatki
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $vhodniPodatki = json_decode(file_get_contents('php://input'), true) ?? [];
        $odgovor = $modulMystica->obdelajVhod($vhodniPodatki);
    } else {
        // GET zahteva - vrni osnovne informacije
        $odgovor = [
            'uspeh' => true,
            'sporocilo' => 'Modul Mystica je aktiven',
            'dostopne_metode' => 'POST z JSON podatki',
            'dostopni_ukazi' => [
                'prijava', 'magicni_dogodek', 'pridobi_stanje', 'nastavi_cron', 'komunikacija'
            ]
        ];
    }
    
    echo json_encode($odgovor, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'uspeh' => false,
        'napaka' => 'Sistemska napaka: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>