<?php
/**
 * MODUL: Sonaris
 * FUNKCIJE: modul_onaris_funkcije.php
 * VERZIJA: 1.0.0 (24.6.2026)
 *
 * NAMEN:
 *     Zvočna pokrajina, terapevtski zvoki in vodna harmonija
 *
 * TIP: zbiralec
 * KLJUČNE BESEDE: ["zvok","terapija","harmonija","frekvence"]
 */

declare(strict_types=1);

class ModulOnarisFunkcije {
    private array $podatki;
    
    public function __construct() {
        $this->podatki = [
            'ime' => 'Sonaris',
            'id' => 'onaris',
            'tip' => 'zbiralec',
            'verzija' => '1.0.0',
            'opis' => 'Zvočna pokrajina, terapevtski zvoki in vodna harmonija',
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
            'naslov' => 'Sonaris',
            'sporocilo' => 'Dobrodošli v modulu Sonaris!',
            'tip' => 'zbiralec',
            'opis' => 'Zvočna pokrajina, terapevtski zvoki in vodna harmonija',
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
            'ime' => 'Sonaris',
            'id' => 'onaris',
            'opis' => 'Zvočna pokrajina, terapevtski zvoki in vodna harmonija'
        ];
    }
}