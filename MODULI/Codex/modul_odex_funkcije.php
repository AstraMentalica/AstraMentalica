<?php
/**
 * MODUL: Codex
 * FUNKCIJE: modul_odex_funkcije.php
 * VERZIJA: 1.0.0 (24.6.2026)
 *
 * NAMEN: Splošna zbirka modrosti in znanja
 * TIP: knjiga
 * KLJUČNE BESEDE: ["koda","modrost","znanje","zbirka"]
 */

declare(strict_types=1);

class ModulOdexFunkcije {
    private array $podatki;
    
    public function __construct() {
        $this->podatki = [
            'ime' => 'Codex',
            'id' => 'odex',
            'tip' => 'knjiga',
            'verzija' => '1.0.0',
            'opis' => 'Splošna zbirka modrosti in znanja',
            'aktiviran' => true
        ];
    }
    
    public function pridobiInformacije(): array { return $this->podatki; }
    
    public function pridobiDomov(): array {
        return [
            'naslov' => 'Codex',
            'sporocilo' => 'Dobrodošli v modulu Codex!',
            'tip' => 'knjiga',
            'opis' => 'Splošna zbirka modrosti in znanja',
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
        return ['ime' => 'Codex', 'id' => 'odex', 'opis' => 'Splošna zbirka modrosti in znanja'];
    }
}