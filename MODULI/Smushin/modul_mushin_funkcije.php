<?php
/**
 * MODUL: Smushin
 * FUNKCIJE: modul_mushin_funkcije.php
 * VERZIJA: 1.0.0 (24.6.2026)
 *
 * NAMEN: Mushin - stanje brez uma v zen praksi
 * TIP: praksa
 * KLJUČNE BESEDE: ["mushin","zen","um","praznina"]
 */

declare(strict_types=1);

class ModulMushinFunkcije {
    private array $podatki;
    
    public function __construct() {
        $this->podatki = [
            'ime' => 'Smushin',
            'id' => 'mushin',
            'tip' => 'praksa',
            'verzija' => '1.0.0',
            'opis' => 'Mushin - stanje brez uma v zen praksi',
            'aktiviran' => true
        ];
    }
    
    public function pridobiInformacije(): array { return $this->podatki; }
    
    public function pridobiDomov(): array {
        return [
            'naslov' => 'Smushin',
            'sporocilo' => 'Dobrodošli v modulu Smushin!',
            'tip' => 'praksa',
            'opis' => 'Mushin - stanje brez uma v zen praksi',
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
        return ['ime' => 'Smushin', 'id' => 'mushin', 'opis' => 'Mushin - stanje brez uma v zen praksi'];
    }
}