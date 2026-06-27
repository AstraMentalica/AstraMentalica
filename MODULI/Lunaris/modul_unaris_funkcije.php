<?php
/**
 * MODUL: Lunaris
 * FUNKCIJE: modul_unaris_funkcije.php
 * VERZIJA: 1.0.0 (24.6.2026)
 *
 * NAMEN:
 *     Lunarni cikli, faze lune in njihov vpliv na čustva in energijo
 *
 * TIP: enciklopedija
 * KLJUČNE BESEDE: ["luna","cikli","faze","plima"]
 */

declare(strict_types=1);

class ModulUnarisFunkcije {
    private array $podatki;
    
    public function __construct() {
        $this->podatki = [
            'ime' => 'Lunaris',
            'id' => 'unaris',
            'tip' => 'enciklopedija',
            'verzija' => '1.0.0',
            'opis' => 'Lunarni cikli, faze lune in njihov vpliv na čustva in energijo',
            'aktiviran' => true
        ];
    }
    
    /**
     * Pridobi osnovne informacije o modulu
     */
    public function pridobiInformacije(): array {
        return $this->podatki;
    }
    
    /**
     * Pridobi vsebino za domov stran
     */
    public function pridobiDomov(): array {
        return [
            'naslov' => 'Lunaris',
            'sporocilo' => 'Dobrodošli v modulu Lunaris!',
            'tip' => 'enciklopedija',
            'opis' => 'Lunarni cikli, faze lune in njihov vpliv na čustva in energijo',
            'status' => 'pripravljen'
        ];
    }
    
    /**
     * Izvedi akcijo modula
     */
    public function izvediAkcijo(string $akcija, array $parametri = []): array {
        return match($akcija) {
            'info' => $this->pridobiInformacije(),
            'domov' => $this->pridobiDomov(),
            default => ['napaka' => "Neznana akcija: $akcija"]
        };
    }
    
    /**
     * Pridobi statične podatke modula
     */
    public static function pridobiStatic(): array {
        return [
            'ime' => 'Lunaris',
            'id' => 'unaris',
            'opis' => 'Lunarni cikli, faze lune in njihov vpliv na čustva in energijo'
        ];
    }
}