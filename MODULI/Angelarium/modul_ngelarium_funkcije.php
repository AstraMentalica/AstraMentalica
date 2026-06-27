<?php
/**
 * MODUL: Angelarium
 * FUNKCIJE: modul_ngelarium_funkcije.php
 * VERZIJA: 1.0.0 (24.6.2026)
 *
 * NAMEN:
 *     Nebeška hierarhija angelov in arhangelov
 *
 * TIP: enciklopedija
 * KLJUČNE BESEDE: ["angeli","arhangeli","nebo","hierarhija"]
 */

declare(strict_types=1);

class ModulNgelariumFunkcije {
    private array $podatki;
    
    public function __construct() {
        $this->podatki = [
            'ime' => 'Angelarium',
            'id' => 'ngelarium',
            'tip' => 'enciklopedija',
            'verzija' => '1.0.0',
            'opis' => 'Nebeška hierarhija angelov in arhangelov',
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
            'naslov' => 'Angelarium',
            'sporocilo' => 'Dobrodošli v modulu Angelarium!',
            'tip' => 'enciklopedija',
            'opis' => 'Nebeška hierarhija angelov in arhangelov',
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
            'ime' => 'Angelarium',
            'id' => 'ngelarium',
            'opis' => 'Nebeška hierarhija angelov in arhangelov'
        ];
    }
}