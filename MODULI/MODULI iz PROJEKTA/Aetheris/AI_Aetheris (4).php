<?php
/**
 * AI AETHERIS - UMETNA INTELIGENCA ZA FORUM
 */

class AI_Aetheris {
    private $model;
    private $zgodovina;
    private $kontekst;
    
    public function __construct($model = 'aetheris-osnovni') {
        $this->model = $model;
        $this->zgodovina = [];
        $this->kontekst = $this->inicializirajKontekst();
    }
    
    /**
     * Inicializiraj kontekst za AI
     */
    private function inicializirajKontekst() {
        return [
            'ezoterika' => [
                'opis' => 'Notranja znanja, simbolika, iniciacije',
                'kljucne_besede' => ['mir', 'dusha', 'vizija', 'symbol', 'notranji']
            ],
            'eterika' => [
                'opis' => 'Subtilna telesa, cakre, energijsko ravnovesje', 
                'kljucne_besede' => ['energija', 'cakra', 'aura', 'polje', 'vibracija']
            ],
            'magija' => [
                'opis' => 'Rituali, volja, crta med svetovi',
                'kljucne_besede' => ['magija', 'ritual', 'volja', 'moc', 'skrivnost']
            ],
            'hermetika' => [
                'opis' => 'Makrokozmos = mikrokozmos, simboli in principi',
                'kljucne_besede' => ['hermetika', 'princip', 'kozmos', 'zakon', 'enost']
            ]
        ];
    }
    
    /**
     * Obdelaj uporabniško vprašanje
     */
    public function obdelajVprasanje($vprasanje, $uporabnik) {
        // Posodobi statistiko
        AetherisJedro::posodobiStatistikoVprasanj();
        
        // Sanitiziraj vnos
        $vprasanje = aetherisSanitizirajVnos($vprasanje);
        
        // Analiziraj vsebino
        $analiza = $this->analizirajVsebino($vprasanje);
        
        // Generiraj odgovor
        $odgovor = $this->generirajOdgovor($vprasanje, $analiza, $uporabnik);
        
        // Shrani v zgodovino
        $this->shraniVZgodovino($vprasanje, $odgovor, $uporabnik, $analiza);
        
        // Posodobi statistiko
        AetherisJedro::posodobiStatistikoOdgovorov();
        
        return $odgovor;
    }
    
    /**
     * Analiziraj vsebino vprašanja
     */
    private function analizirajVsebino($vprasanje) {
        $vprasanje = strtolower($vprasanje);
        $dolzina = strlen($vprasanje);
        
        $zaznane_teme = [];
        $kompleksnost = 'osnovna';
        
        foreach ($this->kontekst as $tema => $podatki) {
            foreach ($podatki['kljucne_besede'] as $beseda) {
                if (strpos($vprasanje, $beseda) !== false) {
                    $zaznane_teme[] = $tema;
                    break;
                }
            }
        }
        
        if (count($zaznane_teme) > 1) {
            $kompleksnost = 'visoka';
        } elseif (count($zaznane_teme) === 1) {
            $kompleksnost = 'srednja';
        }
        
        return [
            'zaznane_teme' => array_unique($zaznane_teme),
            'dolzina_vprasanja' => $dolzina,
            'kompleksnost' => $kompleksnost,
            'stevilo_kljucnih_besed' => count($zaznane_teme)
        ];
    }
    
    /**
     * Generiraj odgovor na podlagi analize
     */
    private function generirajOdgovor($vprasanje, $analiza, $uporabnik) {
        $raven = $this->pridobiRavenUporabnika($uporabnik);
        $teme = $analiza['zaznane_teme'];
        
        // Pridobi ustrezne odgovore glede na temo
        $mozni_odgovori = $this->pridobiOdgovoreZaTemo($teme, $raven);
        
        if (empty($mozni_odgovori)) {
            $mozni_odgovori = $this->pridobiSplosneOdgovore($raven);
        }
        
        $izbran_odgovor = $mozni_odgovori[array_rand($mozni_odgovori)];
        
        return $this->formatirajOdgovor($izbran_odgovor, $raven, $teme);
    }
    
    /**
     * Pridobi odgovore za specifično temo
     */
    private function pridobiOdgovoreZaTemo($teme, $raven) {
        $baza_odgovorov = [
            'ezoterika' => [
                "Notranji mir je potovanje, ki se zacne v srcu...",
                "V tišini najdemo odgovore, ki jih iscemo...",
                "Simboli so mostovi do globljih resnic...",
                "Duhovna pot zahteva potrpljenje in vdanost..."
            ],
            'eterika' => [
                "Energijski tokovi tečejo skozi vse življenje...",
                "Uravnoteženje caker je ključ do vitalnosti...",
                "Aura se odziva na naše misli in čustva...",
                "Vibracije vplivajo na našo resničnost..."
            ],
            'magija' => [
                "Prava magija je umetnost spreminjanja zavesti...",
                "Rituali so jezik, s katerim govorimo z vesoljem...",
                "Volja je orodje za oblikovanje resničnosti...",
                "Skrivnosti so vrata do novega razumevanja..."
            ]
        ];
        
        $odgovori = [];
        foreach ($teme as $tema) {
            if (isset($baza_odgovorov[$tema])) {
                // Glede na raven uporabnika prilagodi odgovore
                if ($raven >= 2) {
                    $odgovori = array_merge($odgovori, $baza_odgovorov[$tema]);
                } else {
                    // Za nižje ravni vrni samo prva dva odgovora
                    $odgovori = array_merge($odgovori, array_slice($baza_odgovorov[$tema], 0, 2));
                }
            }
        }
        
        return $odgovori;
    }
    
    /**
     * Pridobi splošne odgovore
     */
    private function pridobiSplosneOdgovori($raven) {
        $splosni_odgovori = [
            "Zanimivo vprašanje... Energije se odzivajo na tvojo pozornost...",
            "V iskanju odgovorov najdeš tudi del sebe...",
            "Pot do resnice vodi skozi tišino in opazovanje...",
            "Vsako vprašanje nosi seme odgovora v sebi..."
        ];
        
        if ($raven >= 1) {
            $splosni_odgovori[] = "Kot registrirani uporabnik imaš dostop do globljih vpogledov...";
        }
        
        if ($raven >= 2) {
            $splosni_odgovori[] = "Tvoja napredna raven omogoča bolj kompleksne odgovore...";
        }
        
        return $splosni_odgovori;
    }
    
    /**
     * Formatiraj končni odgovor
     */
    private function formatirajOdgovor($odgovor, $raven, $teme) {
        $ikona = "💫";
        $model_info = "AI:{$this->model}";
        $raven_info = "Raven:{$raven}";
        
        if (!empty($teme)) {
            $teme_info = "Teme:" . implode(',', $teme);
            return "{$ikona} {$odgovor} [{$model_info}|{$raven_info}|{$teme_info}]";
        }
        
        return "{$ikona} {$odgovor} [{$model_info}|{$raven_info}]";
    }
    
    /**
     * Pridobi raven uporabnika
     */
    private function pridobiRavenUporabnika($uporabnik) {
        $uporabnik_podatki = AetherisJedro::pridobiUporabnika($uporabnik);
        return $uporabnik_podatki['raven_dostopa'] ?? 0;
    }
    
    /**
     * Shrani interakcijo v zgodovino
     */
    private function shraniVZgodovino($vprasanje, $odgovor, $uporabnik, $analiza) {
        $this->zgodovina[] = [
            'vprasanje' => $vprasanje,
            'odgovor' => $odgovor,
            'uporabnik' => $uporabnik,
            'analiza' => $analiza,
            'cas' => aetherisTrenutniCas(),
            'model' => $this->model
        ];
        
        // Ohrani samo zadnjih 100 vnosov
        if (count($this->zgodovina) > 100) {
            array_shift($this->zgodovina);
        }
    }
    
    /**
     * Pridobi zgodovino interakcij
     */
    public function pridobiZgodovino($stevilo = 10) {
        return array_slice($this->zgodovina, -$stevilo);
    }
    
    /**
     * Pridobi statistiko AI
     */
    public function pridobiStatistiko() {
        return [
            'stevilo_interakcij' => count($this->zgodovina),
            'zadnja_interakcija' => empty($this->zgodovina) ? null : end($this->zgodovina)['cas'],
            'uporabljeni_model' => $this->model,
            'velikost_zgodovine' => count($this->zgodovina)
        ];
    }
}
?>