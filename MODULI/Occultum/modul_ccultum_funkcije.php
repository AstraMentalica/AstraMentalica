<?php
/**
 * MODUL: Occultum
 * FUNKCIJE: modul_ccultum_funkcije.php
 * VERZIJA: 1.0.0 (24.6.2026)
 *
 * NAMEN: Okultne znanosti in skrivne prakse
 * TIP: enciklopedija
 * KLJUČNE BESEDE: ["okultno","znanost","prakse","skrivno"]
 */

declare(strict_types=1);

class ModulCcultumFunkcije {
    private array $podatki;
    
    public function __construct() {
        $this->podatki = [
            'ime' => 'Occultum',
            'id' => 'ccultum',
            'tip' => 'enciklopedija',
            'verzija' => '1.0.0',
            'opis' => 'Okultne znanosti in skrivne prakse',
            'aktiviran' => true
        ];
    }
    
    public function pridobiInformacije(): array { return $this->podatki; }
    
    public function pridobiDomov(): array {
        return [
            'naslov' => 'Occultum',
            'sporocilo' => 'Dobrodošli v modulu Occultum!',
            'tip' => 'enciklopedija',
            'opis' => 'Okultne znanosti in skrivne prakse',
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
        return ['ime' => 'Occultum', 'id' => 'ccultum', 'opis' => 'Okultne znanosti in skrivne prakse'];
    }
}