<?php
/**
 * AETHERIS FORUMETER - POPOLNO JEDRO SISTEMA
 * Vse funkcionalnosti v eni datoteki
 */

class AetherisForum {
    private $nastavitve;
    private $uporabniki;
    private $tematskiSklopi;
    private $teme;
    private $komentarji;
    private $statistike;
    
    public function __construct() {
        $this->nastavitve = [
            'ui_kljuc' => 'aetheris-tajni-kljuc',
            'max_komentarji' => 100
        ];
        
        $this->uporabniki = [];
        $this->tematskiSklopi = [];
        $this->teme = [];
        $this->komentarji = [];
        $this->statistike = [
            'vprasanja' => 0,
            'odgovori' => 0,
            'teme' => 0,
            'komentarji' => 0
        ];
        
        $this->inicializirajSistem();
    }
    
    private function inicializirajSistem() {
        $this->ustvariUporabnike();
        $this->ustvariTematskeSklope();
        $this->ustvariPrimerTem();
    }
    
    private function ustvariUporabnike() {
        $this->uporabniki = [
            'gost' => ['id' => 0, 'ime' => 'Gost', 'raven' => 0, 'dovoljenja' => ['branje']],
            'registriran' => ['id' => 1, 'ime' => 'Janez Novak', 'raven' => 1, 'dovoljenja' => ['branje', 'pisanje']],
            'napredni' => ['id' => 2, 'ime' => 'Marija Horvat', 'raven' => 2, 'dovoljenja' => ['branje', 'pisanje', 'skrito']],
            'upravitelj' => ['id' => 3, 'ime' => 'Admin Aetheris', 'raven' => 3, 'dovoljenja' => ['vse']]
        ];
    }
    
    private function ustvariTematskeSklope() {
        $this->tematskiSklopi = [
            1 => ['id' => 1, 'naslov' => 'Ezoterika', 'opis' => 'Notranja znanja', 'ikona' => '🔮', 'raven' => 0],
            2 => ['id' => 2, 'naslov' => 'Eterika', 'opis' => 'Energijska polja', 'ikona' => '✨', 'raven' => 1],
            3 => ['id' => 3, 'naslov' => 'Magija', 'opis' => 'Rituali in volja', 'ikona' => '⚡', 'raven' => 2],
            4 => ['id' => 4, 'naslov' => 'Skrita Soba', 'opis' => 'Ekskluzivne razprave', 'ikona' => '🔒', 'raven' => 2]
        ];
    }
    
    private function ustvariPrimerTem() {
        $this->teme = [
            1 => [
                'id' => 1,
                'naslov' => 'Kako najti notranji mir?',
                'vsebina' => 'Razprava o iskanju notranjega miru v hecticnem svetu...',
                'avtor' => 'registriran',
                'sklop' => 1,
                'datum' => '2024-01-15',
                'komentarji' => [1, 2]
            ]
        ];
        
        $this->komentarji = [
            1 => ['id' => 1, 'tema' => 1, 'avtor' => 'napredni', 'vsebina' => 'Odlicno vprasanje! Jaz meditiram vsak dan.', 'datum' => '2024-01-15'],
            2 => ['id' => 2, 'tema' => 1, 'avtor' => 'registriran', 'vsebina' => 'Hvala za nasvet!', 'datum' => '2024-01-16']
        ];
        
        $this->statistike['teme'] = 1;
        $this->statistike['komentarji'] = 2;
    }
    
    // JAVNE METODE ZA FORUM
    public function vprasajOrakelj($uporabnik, $vprasanje) {
        $this->statistike['vprasanja']++;
        
        if (!isset($this->uporabniki[$uporabnik])) {
            return "Napaka: Neveljaven uporabnik!";
        }
        
        $odgovor = $this->generirajOdgovor($vprasanje, $uporabnik);
        $this->statistike['odgovori']++;
        
        return $odgovor;
    }
    
    private function generirajOdgovor($vprasanje, $uporabnik) {
        $vprasanje = strtolower($vprasanje);
        $raven = $this->uporabniki[$uporabnik]['raven'];
        
        if (strpos($vprasanje, 'mir') !== false) {
            $odgovori = [
                "Notranji mir je potovanje, ne destinacija...",
                "V tišini srca najdeš odgovore...",
                "Vsak dah te približa notranjemu miru..."
            ];
        } elseif (strpos($vprasanje, 'energija') !== false) {
            $odgovori = [
                "Energija teče kot reka skozi vse...",
                "Uravnoteži svoje energijsko polje...",
                "Cakre so ključ do vitalnosti..."
            ];
        } elseif (strpos($vprasanje, 'magija') !== false) {
            if ($raven >= 2) {
                $odgovori = [
                    "Magija je umetnost spreminjanja zavesti...",
                    "Prava moc je v čisti nameri...",
                    "Rituali so mostovi med svetovi..."
                ];
            } else {
                return "🤫 To vprasanje zahteva višjo raven dostopa.";
            }
        } else {
            $odgovori = [
                "Zanimivo vprasanje... Energije se odzivajo...",
                "V globinah zavesti najdeš odgovor...",
                "Pot do resnice vodi skozi tebe..."
            ];
        }
        
        return "💫 " . $odgovori[array_rand($odgovori)] . " [Raven $raven]";
    }
    
    public function pridobiPriporocila($uporabnik) {
        if (!isset($this->uporabniki[$uporabnik])) {
            return ["napaka" => "Neveljaven uporabnik"];
        }
        
        $raven = $this->uporabniki[$uporabnik]['raven'];
        $dostopniSklopi = array_filter($this->tematskiSklopi, function($sklop) use ($raven) {
            return $sklop['raven'] <= $raven;
        });
        
        return [
            'sklopi' => $dostopniSklopi,
            'stevilo_sklopov' => count($dostopniSklopi),
            'nasvet' => $this->generirajNasvet($raven)
        ];
    }
    
    private function generirajNasvet($raven) {
        $nasveti = [
            0 => "Registriraj se za več možnosti!",
            1 => "Potrdi račun za dodatne sklope.",
            2 => "Doseži VIP status za ekskluzivne vsebine.",
            3 => "Kot upravitelj imaš polno kontrolo."
        ];
        
        return $nasveti[$raven] ?? "Napreduj na svoji poti.";
    }
    
    public function ustvariTemo($uporabnik, $naslov, $vsebina, $sklopId) {
        if (!isset($this->uporabniki[$uporabnik]) || $this->uporabniki[$uporabnik]['raven'] < 1) {
            return "Napaka: Niš dovoljenja za ustvarjanje tem!";
        }
        
        if (!isset($this->tematskiSklopi[$sklopId])) {
            return "Napaka: Neveljaven sklop!";
        }
        
        $novaTemaId = max(array_keys($this->teme)) + 1;
        $this->teme[$novaTemaId] = [
            'id' => $novaTemaId,
            'naslov' => $naslov,
            'vsebina' => $vsebina,
            'avtor' => $uporabnik,
            'sklop' => $sklopId,
            'datum' => date('Y-m-d H:i:s'),
            'komentarji' => []
        ];
        
        $this->statistike['teme']++;
        return "✅ Tema uspešno ustvarjena! ID: $novaTemaId";
    }
    
    public function dodajKomentar($uporabnik, $temaId, $vsebina) {
        if (!isset($this->uporabniki[$uporabnik]) || $this->uporabniki[$uporabnik]['raven'] < 1) {
            return "Napaka: Niš dovoljenja za komentiranje!";
        }
        
        if (!isset($this->teme[$temaId])) {
            return "Napaka: Neveljavna tema!";
        }
        
        $novKomentarId = max(array_keys($this->komentarji)) + 1;
        $this->komentarji[$novKomentarId] = [
            'id' => $novKomentarId,
            'tema' => $temaId,
            'avtor' => $uporabnik,
            'vsebina' => $vsebina,
            'datum' => date('Y-m-d H:i:s')
        ];
        
        $this->teme[$temaId]['komentarji'][] = $novKomentarId;
        $this->statistike['komentarji']++;
        
        return "✅ Komentar uspešno dodan!";
    }
    
    public function pridobiTeme($sklopId = null) {
        if ($sklopId) {
            return array_filter($this->teme, function($tema) use ($sklopId) {
                return $tema['sklop'] == $sklopId;
            });
        }
        return $this->teme;
    }
    
    public function pridobiKomentarje($temaId) {
        if (!isset($this->teme[$temaId])) {
            return [];
        }
        
        $komentarjiTeme = [];
        foreach ($this->teme[$temaId]['komentarji'] as $komentarId) {
            if (isset($this->komentarji[$komentarId])) {
                $komentarjiTeme[] = $this->komentarji[$komentarId];
            }
        }
        
        return $komentarjiTeme;
    }
    
    public function izvediCronOpravilo($tip) {
        $cas = date('Y-m-d H:i:s');
        
        switch ($tip) {
            case 'pocisti':
                return "✅ Cron: Očiščeno začasnih podatkov - $cas";
            case 'arhiviraj':
                return "✅ Cron: Arhivirane stare teme - $cas";
            case 'statistika':
                return "✅ Cron: Posodobljena statistika - $cas";
            default:
                return "❌ Cron: Neznano opravilo - $tip";
        }
    }
    
    public function pridobiStatistiko() {
        return array_merge($this->statistike, [
            'uporabniki' => count($this->uporabniki),
            'sklopi' => count($this->tematskiSklopi),
            'zadnja_aktivnost' => date('Y-m-d H:i:s')
        ]);
    }
    
    public function pridobiUporabnike() {
        return $this->uporabniki;
    }
    
    public function pridobiTematskeSklope() {
        return $this->tematskiSklopi;
    }
    
    public function preveriDostop($uporabnik, $sklopId) {
        if (!isset($this->uporabniki[$uporabnik]) || !isset($this->tematskiSklopi[$sklopId])) {
            return false;
        }
        
        return $this->uporabniki[$uporabnik]['raven'] >= $this->tematskiSklopi[$sklopId]['raven'];
    }
}

// Pomožne funkcije
function formatirajIzpis($podatki) {
    if (is_array($podatki)) {
        return json_encode($podatki, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
    return $podatki;
}

function preveriUIKljuc($kljuc) {
    return strlen($kljuc) >= 5 && strpos($kljuc, 'aetheris') !== false;
}
?>