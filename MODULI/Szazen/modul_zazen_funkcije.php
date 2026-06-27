<?php
/**
 * MODUL: Szazen
 * FUNKCIJE: modul_zazen_funkcije.php
 * VERZIJA: 1.0.0 (24.6.2026)
 *
 * NAMEN: Zazen - zen meditacija v sedenju
 * TIP: praksa
 * KLJUČNE BESEDE: ["zazen","zen","meditacija","sedenje"]
 */

declare(strict_types=1);

class ModulZazenFunkcije {
    private array $podatki;
    
    public function __construct() {
        $this->podatki = [
            'ime' => 'Szazen',
            'id' => 'zazen',
            'tip' => 'praksa',
            'verzija' => '1.0.0',
            'opis' => 'Zazen - zen meditacija v sedenju',
            'aktiviran' => true
        ];
    }
    
    public function pridobiInformacije(): array { return $this->podatki; }
    
    public function pridobiDomov(): array {
        return [
            'naslov' => 'Szazen',
            'sporocilo' => 'Dobrodošli v modulu Szazen!',
            'tip' => 'praksa',
            'opis' => 'Zazen - zen meditacija v sedenju',
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
        return ['ime' => 'Szazen', 'id' => 'zazen', 'opis' => 'Zazen - zen meditacija v sedenju'];
    }
}