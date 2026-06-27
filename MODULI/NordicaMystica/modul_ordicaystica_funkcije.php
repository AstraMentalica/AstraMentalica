<?php
/**
 * MODUL: NordicaMystica
 * FUNKCIJE: modul_ordicaystica_funkcije.php
 * VERZIJA: 1.0.0 (24.6.2026)
 *
 * NAMEN: Nordijska mitologija in magija
 * TIP: enciklopedija
 * KLJUČNE BESEDE: ["nordijsko","mitologija","vikingi","magija"]
 */

declare(strict_types=1);

class ModulOrdicaysticaFunkcije {
    private array $podatki;
    
    public function __construct() {
        $this->podatki = [
            'ime' => 'NordicaMystica',
            'id' => 'ordicaystica',
            'tip' => 'enciklopedija',
            'verzija' => '1.0.0',
            'opis' => 'Nordijska mitologija in magija',
            'aktiviran' => true
        ];
    }
    
    public function pridobiInformacije(): array { return $this->podatki; }
    
    public function pridobiDomov(): array {
        return [
            'naslov' => 'NordicaMystica',
            'sporocilo' => 'Dobrodošli v modulu NordicaMystica!',
            'tip' => 'enciklopedija',
            'opis' => 'Nordijska mitologija in magija',
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
        return ['ime' => 'NordicaMystica', 'id' => 'ordicaystica', 'opis' => 'Nordijska mitologija in magija'];
    }
}