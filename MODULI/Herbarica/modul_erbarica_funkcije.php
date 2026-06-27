<?php
/**
 * MODUL: Herbarica
 * FUNKCIJE: modul_erbarica_funkcije.php
 * VERZIJA: 1.0.0 (24.6.2026)
 *
 * NAMEN:
 *     Zeliščna enciklopedija za naravno zdravljenje
 *
 * TIP: enciklopedija
 * KLJUČNE BESEDE: ["zelišča","rastline","zdravljenje","naravno"]
 */

declare(strict_types=1);

class ModulErbaricaFunkcije {
    private array $podatki;
    
    public function __construct() {
        $this->podatki = [
            'ime' => 'Herbarica',
            'id' => 'erbarica',
            'tip' => 'enciklopedija',
            'verzija' => '1.0.0',
            'opis' => 'Zeliščna enciklopedija za naravno zdravljenje',
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
            'naslov' => 'Herbarica',
            'sporocilo' => 'Dobrodošli v modulu Herbarica!',
            'tip' => 'enciklopedija',
            'opis' => 'Zeliščna enciklopedija za naravno zdravljenje',
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
            'ime' => 'Herbarica',
            'id' => 'erbarica',
            'opis' => 'Zeliščna enciklopedija za naravno zdravljenje'
        ];
    }
}