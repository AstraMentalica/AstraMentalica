<?php
/**
 * MODUL: Stelar
 * FUNKCIJE: modul_telar_funkcije.php
 * VERZIJA: 1.0.0 (24.6.2026)
 *
 * NAMEN: Zvezdna energija in astralna potovanja
 * TIP: enciklopedija
 * KLJUČNE BESEDE: ["zvezde","astral","energija","potovanja"]
 */

declare(strict_types=1);

class ModulTelarFunkcije {
    private array $podatki;
    
    public function __construct() {
        $this->podatki = [
            'ime' => 'Stelar',
            'id' => 'telar',
            'tip' => 'enciklopedija',
            'verzija' => '1.0.0',
            'opis' => 'Zvezdna energija in astralna potovanja',
            'aktiviran' => true
        ];
    }
    
    public function pridobiInformacije(): array { return $this->podatki; }
    
    public function pridobiDomov(): array {
        return [
            'naslov' => 'Stelar',
            'sporocilo' => 'Dobrodošli v modulu Stelar!',
            'tip' => 'enciklopedija',
            'opis' => 'Zvezdna energija in astralna potovanja',
            'status' => 'pripravljen'
        ];
    }
    
    public function izvediAkcijo(string $akcija, array $parametri = []): array {
        return match($akcija) {
            'info' => $this->pridobiInformacije(),
            'domov' => $this->pridobiDomov(),
            default => ['napaka' => "Neznana akcija: $akcija"]
        };
    }
    
    public static function pridobiStatic(): array {
        return ['ime' => 'Stelar', 'id' => 'telar', 'opis' => 'Zvezdna energija in astralna potovanja'];
    }
}