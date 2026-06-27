<?php
/**
 * MODUL: Swabi
 * FUNKCIJE: modul_wabi_funkcije.php
 * VERZIJA: 1.0.0 (24.6.2026)
 *
 * NAMEN: Wabi-sabi - lepota nepopolnosti
 * TIP: praksa
 * KLJUČNE BESEDE: ["wabi","sabi","japonsko","estetika"]
 */

declare(strict_types=1);

class ModulWabiFunkcije {
    private array $podatki;
    
    public function __construct() {
        $this->podatki = [
            'ime' => 'Swabi',
            'id' => 'wabi',
            'tip' => 'praksa',
            'verzija' => '1.0.0',
            'opis' => 'Wabi-sabi - lepota nepopolnosti',
            'aktiviran' => true
        ];
    }
    
    public function pridobiInformacije(): array { return $this->podatki; }
    
    public function pridobiDomov(): array {
        return [
            'naslov' => 'Swabi',
            'sporocilo' => 'Dobrodošli v modulu Swabi!',
            'tip' => 'praksa',
            'opis' => 'Wabi-sabi - lepota nepopolnosti',
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
        return ['ime' => 'Swabi', 'id' => 'wabi', 'opis' => 'Wabi-sabi - lepota nepopolnosti'];
    }
}