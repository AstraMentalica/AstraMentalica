<?php
/**
 * UpravljanjeUporabnikov.php - Sistem za upravljanje uporabnikov
 */

class UpravljanjeUporabnikov {
    
    private $konfiguracija;
    private $uporabniki;
    private $aktivneSeje;
    
    public function __construct($konfiguracija) {
        $this->konfiguracija = $konfiguracija;
        $this->uporabniki = $this->naloziTestneUporabnike();
        $this->aktivneSeje = [];
    }
    
    public function prijaviUporabnika($uporabniskoIme, $geslo) {
        foreach ($this->uporabniki as $uporabnik) {
            if ($uporabnik['uporabnisko_ime'] === $uporabniskoIme) {
                if ($this->preveriGeslo($geslo, $uporabnik['geslo'])) {
                    $sejaToken = $this->ustvariSejoToken($uporabnik);
                    return [
                        'uspesno' => true,
                        'uporabnik' => $uporabnik,
                        'seja_token' => $sejaToken,
                        'sporocilo' => 'Uspešna prijava'
                    ];
                }
            }
        }
        
        return [
            'uspesno' => false,
            'sporocilo' => 'Nepravilno uporabniško ime ali geslo'
        ];
    }
    
    public function pridobiUporabnika($id) {
        foreach ($this->uporabniki as $uporabnik) {
            if ($uporabnik['id'] == $id) {
                return $uporabnik;
            }
        }
        return null;
    }
    
    public function nadgradiStopnjo($uporabnikId, $novaStopnja) {
        foreach ($this->uporabniki as &$uporabnik) {
            if ($uporabnik['id'] == $uporabnikId) {
                $stareStopnje = ['S0', 'S1', 'S2', 'S3', 'S4', 'S5'];
                $trenutniIndex = array_search($uporabnik['stopnja'], $stareStopnje);
                $noviIndex = array_search($novaStopnja, $stareStopnje);
                
                if ($noviIndex > $trenutniIndex) {
                    $uporabnik['stopnja'] = $novaStopnja;
                    $uporabnik['zadnja_nadgradnja'] = date('Y-m-d H:i:s');
                    
                    return [
                        'uspesno' => true,
                        'stara_stopnja' => $stareStopnje[$trenutniIndex],
                        'nova_stopnja' => $novaStopnja,
                        'sporocilo' => 'Stopnja uspešno nadgrajena'
                    ];
                }
            }
        }
        
        return [
            'uspesno' => false,
            'sporocilo' => 'Nadgradnja ni mogoča'
        ];
    }
    
    public function ustvariUporabnika($podatki) {
        $novId = count($this->uporabniki) + 1;
        $novUporabnik = [
            'id' => $novId,
            'uporabnisko_ime' => $podatki['uporabnisko_ime'],
            'geslo' => password_hash($podatki['geslo'], PASSWORD_DEFAULT),
            'stopnja' => 'S0',
            'email' => $podatki['email'],
            'datum_registracije' => date('Y-m-d H:i:s'),
            'zadnja_prijava' => null,
            'magicne_tocke' => 0
        ];
        
        $this->uporabniki[] = $novUporabnik;
        
        return [
            'uspesno' => true,
            'uporabnik' => $novUporabnik,
            'sporocilo' => 'Uporabnik uspešno ustvarjen'
        ];
    }
    
    private function naloziTestneUporabnike() {
        return [
            [
                'id' => 1,
                'uporabnisko_ime' => 'magicni_iskanec',
                'geslo' => password_hash('geslo123', PASSWORD_DEFAULT),
                'stopnja' => 'S2',
                'email' => 'iskanec@aurora.si',
                'datum_registracije' => '2024-01-15 10:00:00',
                'zadnja_prijava' => '2024-01-20 14:30:00',
                'magicne_tocke' => 150
            ],
            [
                'id' => 2,
                'uporabnisko_ime' => 'skrivnostni_mojster',
                'geslo' => password_hash('mojster456', PASSWORD_DEFAULT),
                'stopnja' => 'S4',
                'email' => 'mojster@aurora.si',
                'datum_registracije' => '2024-01-10 09:15:00',
                'zadnja_prijava' => '2024-01-20 16:45:00',
                'magicne_tocke' => 450
            ]
        ];
    }
    
    private function preveriGeslo($vnesenoGeslo, $shranjenoGeslo) {
        return password_verify($vnesenoGeslo, $shranjenoGeslo);
    }
    
    private function ustvariSejoToken($uporabnik) {
        $token = bin2hex(random_bytes(32));
        $this->aktivneSeje[$token] = [
            'uporabnik_id' => $uporabnik['id'],
            'ustvarjen' => time(),
            'poteče' => time() + 3600 // 1 ura
        ];
        return $token;
    }
    
    public function preveriSejoToken($token) {
        if (!isset($this->aktivneSeje[$token])) {
            return false;
        }
        
        $seja = $this->aktivneSeje[$token];
        if (time() > $seja['poteče']) {
            unset($this->aktivneSeje[$token]);
            return false;
        }
        
        return $this->pridobiUporabnika($seja['uporabnik_id']);
    }
}
?>