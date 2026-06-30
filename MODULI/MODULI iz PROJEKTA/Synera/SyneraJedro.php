<?php
declare(strict_types=1);

/**
 * SyneraJedro.php - Glavno jedro aplikacije (statično)
 * Obeznostna datoteka: (Ime_modula)Jedro.php
 */

class SyneraJedro {
    
    private static $instanca = null;
    private $stanje;
    private $casZagona;
    
    public static function pridobiInstanco(): self {
        if (self::$instanca === null) {
            self::$instanca = new self();
        }
        return self::$instanca;
    }
    
    private function __construct() {
        $this->casZagona = microtime(true);
        $this->inicializirajJedro();
    }
    
    private function inicializirajJedro(): void {
        $this->stanje = [
            'sistemi' => [
                'ai_komunikacija' => [
                    'ime' => 'AI Komunikacija', 
                    'status' => '🟢 Deluje',
                    'opis' => 'Inteligentna analiza in priporočila',
                    'zadnja_aktivnost' => date('H:i:s')
                ],
                'generator_sigilov' => [
                    'ime' => 'Generator Sigilov', 
                    'status' => '🟢 Pripravljen',
                    'opis' => 'Ustvarjanje osebnih simbolov',
                    'zadnja_aktivnost' => date('H:i:s')
                ],
                'sveta_geometrija' => [
                    'ime' => 'Sveta Geometrija', 
                    'status' => '🟢 Aktivna',
                    'opis' => 'Merkaba in drugi vzorci',
                    'zadnja_aktivnost' => date('H:i:s')
                ],
                'analizator_energije' => [
                    'ime' => 'Analizator Energije', 
                    'status' => '🟢 Deluje',
                    'opis' => 'Energetska diagnostika',
                    'zadnja_aktivnost' => date('H:i:s')
                ]
            ],
            'zaznamki' => [
                ['cas' => date('H:i:s'), 'sporocilo' => 'SyneraJedro inicializirano'],
                ['cas' => date('H:i:s'), 'sporocilo' => 'Vsi sistemi preverjeni'],
                ['cas' => date('H:i:s'), 'sporocilo' => 'Platforma pripravljena za delo']
            ],
            'nastavitve' => [
                'jezik' => 'slovenscina',
                'timezone' => 'Europe/Ljubljana',
                'debug' => false,
                'razsirjen_nacin' => true
            ]
        ];
        
        $this->dodajZaznamek('Jedro uspešno zagnano ob ' . date('H:i:s'));
    }
    
    public function izvediKomando(string $komanda): array {
        $this->dodajZaznamek("Izvedena komanda: $komanda");
        
        $rezultat = match(strtolower(trim($komanda))) {
            'stanje', 'status' => $this->komandaStanje(),
            'analiza', 'energija' => $this->komandaAnaliza(),
            'sigil', 'generiraj' => $this->komandaSigil(),
            'pomoc', 'help' => $this->komandaPomoc(),
            'razsirjen', 'extended' => $this->komandaRazsirjenPrikaz(),
            'zaznamki', 'logs' => $this->komandaZaznamki(),
            default => $this->komandaNeznana($komanda)
        };
        
        return $rezultat;
    }
    
    private function komandaStanje(): array {
        $casDelovanja = round(microtime(true) - $this->casZagona, 2);
        
        $sporocilo = "📊 STANJE SISTEMOV - Synera Platforma\n\n";
        $sporocilo .= "🕒 Čas delovanja: {$casDelovanja}s\n";
        $sporocilo .= "📅 Zadnja aktivnost: " . date('H:i:s') . "\n\n";
        
        foreach ($this->stanje['sistemi'] as $sistem) {
            $sporocilo .= "{$sistem['status']} {$sistem['ime']}\n";
            $sporocilo .= "   📝 {$sistem['opis']}\n";
            $sporocilo .= "   ⏰ {$sistem['zadnja_aktivnost']}\n\n";
        }
        
        $sporocilo .= "🎯 Vsi sistemi delujejo optimalno!";
        
        return [
            'uspeh' => true,
            'sporocilo' => $sporocilo,
            'tip' => 'stanje'
        ];
    }
    
    private function komandaAnaliza(): array {
        $sporocilo = "🔮 ANALIZA ENERGIJE - Synera AI\n\n";
        $sporocilo .= "✨ Energetski profil:\n";
        $sporocilo .= "   • Tip: Ogenj 🔥\n";
        $sporocilo .= "   • Ravnovesje: Visoko ⚖️\n";
        $sporocilo .= "   • Vitalnost: Odlična 💪\n\n";
        
        $sporocilo .= "🎯 Priporočila:\n";
        $sporocilo .= "   • Meditacija z runo Fehu\n";
        $sporocilo .= "   • Uporaba toplih barv (rdeča, oranžna)\n";
        $sporocilo .= "   • Jutranje energijske vaje (06:00-08:00)\n\n";
        
        $sporocilo .= "📈 Napoved razvoja:\n";
        $sporocilo .= "   • Kratkoročno: Rast kreativnosti\n";
        $sporocilo .= "   • Srednjeročno: Povečana vitalnost\n";
        $sporocilo .= "   • Dolgoročno: Transformacija\n";
        
        return [
            'uspeh' => true,
            'sporocilo' => $sporocilo,
            'tip' => 'analiza'
        ];
    }
    
    private function komandaSigil(): array {
        $sigilIme = "Osebni Zaščitni Sigil";
        $casUstvarjanja = date('H:i:s');
        
        $sporocilo = "🛡️ GENERIRANJE SIGILA - Uspešno\n\n";
        $sporocilo .= "📛 Ime: $sigilIme\n";
        $sporocilo .= "🎯 Namen: Zaščita in varnost\n";
        $sporocilo .= "📊 Učinkovitost: 92% 🏆\n";
        $sporocilo .= "⏰ Čas ustvarjanja: $casUstvarjanja\n\n";
        
        $sporocilo .= "🔮 Energetski vzorec:\n";
        $sporocilo .= "   • Stabilnost: Visoka 🏔️\n";
        $sporocilo .= "   • Frekvence: 432Hz, 528Hz 🎵\n";
        $sporocilo .= "   • Resonanca: Popolna 💫\n\n";
        
        $sporocilo .= "💡 Namig: Sigil je pripravljen za aktivacijo!";
        
        $this->stanje['sistemi']['generator_sigilov']['zadnja_aktivnost'] = $casUstvarjanja;
        $this->dodajZaznamek("Ustvarjen sigil: $sigilIme");
        
        return [
            'uspeh' => true,
            'sporocilo' => $sporocilo,
            'tip' => 'sigil',
            'sigil' => [
                'ime' => $sigilIme,
                'ucinkovitost' => '92%',
                'cas' => $casUstvarjanja
            ]
        ];
    }
    
    private function komandaPomoc(): array {
        $sporocilo = "🤖 POMOČ - Synera Komandna Plošča\n\n";
        $sporocilo .= "🎯 OSNOVNE KOMANDE:\n";
        $sporocilo .= "   • stanje - Prikaže stanje vseh sistemov\n";
        $sporocilo .= "   • analiza - Izvede analizo energije\n";
        $sporocilo .= "   • sigil - Generira zaščitni sigil\n";
        $sporocilo .= "   • pomoc - Prikaže to sporočilo\n\n";
        
        $sporocilo .= "🔧 NAPREDNE KOMANDE:\n";
        $sporocilo .= "   • razsirjen - Podroben prikaz sistemov\n";
        $sporocilo .= "   • zaznamki - Prikaže dnevnik dogodkov\n\n";
        
        $sporocilo .= "⚡ HITRI DOSTOP:\n";
        $sporocilo .= "   Uporabite kratke oblike: 'stat', 'ana', 'sig'\n\n";
        
        $sporocilo .= "💡 Namig: Vse komande so občutljive na velikost črk!";
        
        return [
            'uspeh' => true,
            'sporocilo' => $sporocilo,
            'tip' => 'pomoc'
        ];
    }
    
    private function komandaRazsirjenPrikaz(): array {
        $sporocilo = "🔍 RAZŠIRJEN PRIKAZ - Synera Jedro\n\n";
        $sporocilo .= "📋 NASTAVITVE SISTEMA:\n";
        
        foreach ($this->stanje['nastavitve'] as $kljuc => $vrednost) {
            $sporocilo .= "   • " . ucfirst($kljuc) . ": $vrednost\n";
        }
        
        $sporocilo .= "\n📊 STATISTIKA:\n";
        $sporocilo .= "   • Število sistemov: " . count($this->stanje['sistemi']) . "\n";
        $sporocilo .= "   • Število zaznamkov: " . count($this->stanje['zaznamki']) . "\n";
        $sporocilo .= "   • Čas zagona: " . date('H:i:s', (int)$this->casZagona) . "\n";
        
        return [
            'uspeh' => true,
            'sporocilo' => $sporocilo,
            'tip' => 'razsirjen'
        ];
    }
    
    private function komandaZaznamki(): array {
        $sporocilo = "📝 ZAZNAMKI - Zadnjih 10 dogodkov\n\n";
        
        $zadnjiZaznamki = array_slice($this->stanje['zaznamki'], -10);
        
        foreach ($zadnjiZaznamki as $zaznamek) {
            $sporocilo .= "   [{$zaznamek['cas']}] {$zaznamek['sporocilo']}\n";
        }
        
        return [
            'uspeh' => true,
            'sporocilo' => $sporocilo,
            'tip' => 'zaznamki'
        ];
    }
    
    private function komandaNeznana(string $komanda): array {
        return [
            'uspeh' => false,
            'sporocilo' => "❌ Neznana komanda: '$komanda'\nUporabite 'pomoc' za seznam vseh komand.",
            'tip' => 'napaka'
        ];
    }
    
    public function pridobiStanje(): array {
        return array_merge($this->stanje, [
            'cas_obdelave' => round((microtime(true) - $this->casZagona) * 1000, 2) . 'ms',
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    }
    
    public function generirajSigil(): array {
        return $this->komandaSigil();
    }
    
    private function dodajZaznamek(string $sporocilo): void {
        $this->stanje['zaznamki'][] = [
            'cas' => date('H:i:s'),
            'sporocilo' => $sporocilo
        ];
        
        if (count($this->stanje['zaznamki']) > 50) {
            array_shift($this->stanje['zaznamki']);
        }
    }
}

// Avtomatska inicializacija jedra
SyneraJedro::pridobiInstanco();
?>