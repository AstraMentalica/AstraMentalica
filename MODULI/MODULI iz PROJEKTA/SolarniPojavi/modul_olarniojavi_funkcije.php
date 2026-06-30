<?php
/**
 * MODUL: SolarniPojavi
 * FUNKCIJE: modul_olarniojavi_funkcije.php
 * VERZIJA: 1.0.0 (24.6.2026)
 *
 * NAMEN:
 *     Sončevi pojavi, sončna energija in kozmični vpliv
 *
 * TIP: enciklopedija
 * KLJUČNE BESEDE: ["sonce","solar","energija","pojavi"]
 */

declare(strict_types=1);

class ModulOlarniojaviFunkcije {
    private array $podatki;
    
    public function __construct() {
        $this->podatki = [
            'ime' => 'SolarniPojavi',
            'id' => 'olarniojavi',
            'tip' => 'enciklopedija',
            'verzija' => '1.0.0',
            'opis' => 'Sončevi pojavi, sončna energija in kozmični vpliv',
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
            'naslov' => 'SolarniPojavi',
            'sporocilo' => 'Dobrodošli v modulu SolarniPojavi!',
            'tip' => 'enciklopedija',
            'opis' => 'Sončevi pojavi, sončna energija in kozmični vpliv',
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
            'ime' => 'SolarniPojavi',
            'id' => 'olarniojavi',
            'opis' => 'Sončevi pojavi, sončna energija in kozmični vpliv'
        ];
    }
}