<?php
/**
 * StopnjeDostopa.php - Upravljanje stopenj dostopa uporabnikov
 */

class StopnjeDostopa {
    
    private $stopnje;
    
    public function __construct() {
        $this->stopnje = $this->pripraviStopnjeDostopa();
    }
    
    public function pridobiStopnjo($stopnjaKoda) {
        return $this->stopnje[$stopnjaKoda] ?? $this->stopnje['S0'];
    }
    
    public function pridobiVseStopnje() {
        return $this->stopnje;
    }
    
    public function preveriDovoljenje($stopnjaKoda, $dovoljenje) {
        $stopnja = $this->pridobiStopnjo($stopnjaKoda);
        return in_array($dovoljenje, $stopnja['dovoljenja']);
    }
    
    public function lahkoNadgradi($trenutnaStopnja, $zeljenaStopnja) {
        $zaporedje = ['S0', 'S1', 'S2', 'S3', 'S4', 'S5'];
        $trenutniIndex = array_search($trenutnaStopnja, $zaporedje);
        $zeljeniIndex = array_search($zeljenaStopnja, $zaporedje);
        
        return $zeljeniIndex !== false && $trenutniIndex !== false && $zeljeniIndex > $trenutniIndex;
    }
    
    private function pripraviStopnjeDostopa() {
        return [
            'S0' => [
                'ime' => 'Gost',
                'opis' => 'Anonimni obiskovalec',
                'dovoljenja' => ['ogled_uvoda', 'brskanje_osnovno'],
                'omejitve' => ['brez_shranjevanja', 'brez_interakcije'],
                'magicne_znacilnosti' => ['bezen_vpogled']
            ],
            'S1' => [
                'ime' => 'Registriran Uporabnik',
                'opis' => 'Osnovni registrirani uporabnik',
                'dovoljenja' => ['ogled_vsebine', 'osnovne_interakcije', 'shranjevanje_nastavitev'],
                'omejitve' => ['omejen_dostop', 'brez_ekskluzivnih_vsebin'],
                'magicne_znacilnosti' => ['dostop_do_zapisov', 'osnovne_prakse']
            ],
            'S2' => [
                'ime' => 'Potrjen Uporabnik',
                'opis' => 'Potrjen član skupnosti',
                'dovoljenja' => ['dostop_do_vseh_vsebin', 'sodelovanje', 'komentiranje', 'ocene'],
                'omejitve' => ['brez_administracije', 'brez_VIP_vsebin'],
                'magicne_znacilnosti' => ['napredne_prakse', 'dostop_do_ritualov']
            ],
            'S3' => [
                'ime' => 'Napredni Uporabnik',
                'opis' => 'Član z naprednimi pravicami',
                'dovoljenja' => ['ekskluzivne_vsebine', 'povabilo_prijateljev', 'ustvarjanje_vsebin'],
                'omejitve' => ['brez_sistemskih_nastavitev'],
                'magicne_znacilnosti' => ['dostop_do_skrivnosti', 'posebni_rituali']
            ],
            'S4' => [
                'ime' => 'VIP Član',
                'opis' => 'Posebni dostopi in privilegiji',
                'dovoljenja' => ['VIP_vsebine', 'prednostni_dostop', 'osebna_podpora'],
                'omejitve' => ['brez_admin_pravic'],
                'magicne_znacilnosti' => ['redke_magije', 'osebni_mojster']
            ],
            'S5' => [
                'ime' => 'Administrator',
                'opis' => 'Popoln dostop in upravljanje',
                'dovoljenja' => ['vse_operacije', 'upravljanje_sistema', 'nadgradnje'],
                'omejitve' => ['brez_omejitev'],
                'magicne_znacilnosti' => ['popolna_moc', 'upravljanje_portala']
            ]
        ];
    }
    
    public function pridobiZahteveZaNadgradnjo($trenutnaStopnja) {
        $zahteve = [
            'S0_S1' => ['registracija' => true, 'verifikacija' => true],
            'S1_S2' => ['magicne_tocke' => 100, 'aktivnost' => '7_dni', 'opravljeni_izzivi' => 3],
            'S2_S3' => ['magicne_tocke' => 300, 'aktivnost' => '30_dni', 'prispevki' => 10],
            'S3_S4' => ['magicne_tocke' => 700, 'predlagan' => true, 'posebna_iniciacija' => true],
            'S4_S5' => ['skrivno_znanje' => true, 'odobritev' => 'sistema']
        ];
        
        $kljuc = $trenutnaStopnja . '_' . $this->pridobiNaslednjoStopnjo($trenutnaStopnja);
        return $zahteve[$kljuc] ?? [];
    }
    
    private function pridobiNaslednjoStopnjo($trenutnaStopnja) {
        $zaporedje = ['S0', 'S1', 'S2', 'S3', 'S4', 'S5'];
        $trenutniIndex = array_search($trenutnaStopnja, $zaporedje);
        
        if ($trenutniIndex !== false && isset($zaporedje[$trenutniIndex + 1])) {
            return $zaporedje[$trenutniIndex + 1];
        }
        
        return null;
    }
}
?>