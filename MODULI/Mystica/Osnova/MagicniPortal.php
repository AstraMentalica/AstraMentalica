<?php
/**
 * MagicniPortal.php - Osnovni sistem magičnega portala
 */

class MagicniPortal {
    
    private $konfiguracija;
    private $trenutnoStanje;
    private $magicniElementi;
    
    public function __construct($konfiguracija) {
        $this->konfiguracija = $konfiguracija;
        $this->trenutnoStanje = 'inicializiran';
        $this->magicniElementi = $this->pripraviMagicneElemente();
    }
    
    public function vstopVPortal($uporabnik, $kljuc) {
        if (!$this->preveriMagicniKljuc($kljuc)) {
            return [
                'uspesno' => false,
                'sporocilo' => 'Nepravilen magični ključ',
                'dostop' => 'zavrnjen'
            ];
        }
        
        $dostop = $this->dolociDostop($uporabnik['stopnja']);
        
        return [
            'uspesno' => true,
            'sporocilo' => 'Dobrodošli v Aurora Mystica',
            'dostop' => $dostop,
            'magicni_elementi' => $this->magicniElementi,
            'trenutno_stanje' => $this->trenutnoStanje
        ];
    }
    
    private function preveriMagicniKljuc($kljuc) {
        $minDolzina = $this->konfiguracija['magicni_sistem']['magicni_kljuci']['min_dolzina'];
        return strlen($kljuc) >= $minDolzina;
    }
    
    private function dolociDostop($stopnja) {
        $dostopi = [
            'S0' => ['vsebine' => ['uvod'], 'dovoljenja' => ['ogled']],
            'S1' => ['vsebine' => ['uvod', 'osnovni_zapisi'], 'dovoljenja' => ['ogled', 'osnovne_prakse']],
            'S2' => ['vsebine' => ['vse_vsebine'], 'dovoljenja' => ['ogled', 'vse_prakse', 'sodelovanje']],
            'S3' => ['vsebine' => ['vse_vsebine', 'ekskluzivno'], 'dovoljenja' => ['vse_dovoljenja', 'povabilo_prijateljev']],
            'S4' => ['vsebine' => ['vse_vsebine', 'ekskluzivno', 'VIP'], 'dovoljenja' => ['vse_dovoljenja', 'administracija']],
            'S5' => ['vsebine' => ['vse_vsebine'], 'dovoljenja' => ['popoln_dostop']]
        ];
        
        return $dostopi[$stopnja] ?? $dostopi['S0'];
    }
    
    private function pripraviMagicneElemente() {
        return [
            'energije' => [
                'kvantna' => ['moc' => 95, 'opis' => 'Energija kvantne prepletenosti'],
                'astralna' => ['moc' => 88, 'opis' => 'Energija astralnih ravnin'],
                'etericna' => ['moc' => 92, 'opis' => 'Eterična življenjska energija']
            ],
            'simboli' => [
                'pentagram' => ['pomen' => 'Zaščita in ravnovesje'],
                'ankh' => ['pomen' => 'Večno življenje in duhovnost'],
                'vesica_piscis' => ['pomen' => 'Stvarjenje in enost']
            ],
            'portalni_mehanizmi' => [
                'vrtljiva_vrata' => ['hitrost' => 'spremenljiva', 'smer' => 'nepredvidljiva'],
                'dimenzijski_prehodi' => ['stabilnost' => 'visoka', 'tveganje' => 'zmerno'],
                'casovni_tuneli' => ['natančnost' => 'srednja', 'varnost' => 'visoka']
            ]
        ];
    }
    
    public function spremeniStanje($novoStanje) {
        $dovoljenaStanja = ['inicializiran', 'aktiven', 'vzdrževanje', 'sleep', 'magicni_način'];
        
        if (in_array($novoStanje, $dovoljenaStanja)) {
            $this->trenutnoStanje = $novoStanje;
            return true;
        }
        
        return false;
    }
    
    public function pridobiMagicneElemente() {
        return $this->magicniElementi;
    }
}
?>