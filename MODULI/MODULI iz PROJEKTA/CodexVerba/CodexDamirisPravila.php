<?php
/**
 * Codex Damiris - Pravila in Konfiguracija
 * Lokacija: /var/www/html/codex-damiris/CodexDamirisPravila.php
 */

class CodexDamirisPravila {
    
    // UPORABNIŠKI NIVOJI
    const GOST = 0;
    const OSNOVNI = 1;
    const POTRJEN = 2;
    const NAPREDNI = 3;
    const VIP = 4;
    const ADMIN = 5;
    
    // NASTAVITVE APLIKACIJE
    public static function pridobiNastavitve() {
        return [
            'ime_aplikacije' => 'Codex Damiris',
            'verzija' => '2.0.0',
            'okolje' => 'razvoj',
            'url' => 'http://localhost/codex-damiris',
            'casovni_pas' => 'Europe/Ljubljana',
            
            // Baza podatkov
            'baza' => [
                'gostitelj' => 'localhost',
                'ime' => 'codex_damiris',
                'uporabnik' => 'root',
                'geslo' => '',
                'znakovni_niz' => 'utf8mb4'
            ],
            
            // Varnost
            'varnost' => [
                'hash_algoritmus' => PASSWORD_DEFAULT,
                'jwt_kljuc' => 'codex_damiris_skrivnost_2024',
                'csrf_ime' => 'codex_token'
            ],
            
            // Seja
            'seja' => [
                'trajanje' => 86400,
                'regeneracija' => 1800
            ]
        ];
    }
    
    // KATEGORIJE ZNANJA
    public static function pridobiKategorije() {
        return [
            'astrologija' => [
                'ime' => 'Astrologija',
                'opis' => 'Raziskovanje zvezd in planetov',
                'ikona' => 'zvezde',
                'barva' => '#667eea'
            ],
            'numerologija' => [
                'ime' => 'Numerologija', 
                'opis' => 'Moč števil in njihov pomen',
                'ikona' => 'kristal',
                'barva' => '#764ba2'
            ],
            'rune' => [
                'ime' => 'Rune',
                'opis' => 'Starodavni simboli in njihova moč',
                'ikona' => 'runes',
                'barva' => '#f093fb'
            ],
            'mantre' => [
                'ime' => 'Mantre',
                'opis' => 'Moč besed in vibracij',
                'ikona' => 'om',
                'barva' => '#f5576c'
            ],
            'ezoterika' => [
                'ime' => 'Ezoterika',
                'opis' => 'Skrito znanje in duhovne resnice',
                'ikona' => 'kristal_krogla',
                'barva' => '#4facfe'
            ],
            'simboli' => [
                'ime' => 'Simboli',
                'opis' => 'Jezik duše preko simbolov',
                'ikona' => 'oko',
                'barva' => '#43e97b'
            ]
        ];
    }
    
    // PRAVILA DOSTOPA
    public static function pridobiPravilaDostopa() {
        return [
            self::GOST => [
                'branje_osnovno' => true,
                'iskanje' => true,
                'dodajanje_vsebin' => false,
                'ai_dostop' => false
            ],
            self::OSNOVNI => [
                'branje_osnovno' => true,
                'iskanje' => true,
                'dodajanje_vsebin' => false,
                'ai_dostop' => false
            ],
            self::POTRJEN => [
                'branje_osnovno' => true,
                'iskanje' => true,
                'dodajanje_vsebin' => true,
                'ai_dostop' => true
            ],
            self::NAPREDNI => [
                'branje_vse' => true,
                'iskanje' => true,
                'dodajanje_vsebin' => true,
                'ai_dostop' => true,
                'pregled_statistik' => true
            ],
            self::VIP => [
                'branje_vse' => true,
                'iskanje' => true,
                'dodajanje_vsebin' => true,
                'ai_dostop' => true,
                'pregled_statistik' => true,
                'dostop_ekskluzivno' => true
            ],
            self::ADMIN => [
                'vse_dovoljeno' => true
            ]
        ];
    }
    
    /**
     * Preveri ali nivo ima dovoljenje
     */
    public static function imaDovoljenje($nivo, $dovoljenje) {
        $pravila = self::pridobiPravilaDostopa();
        
        if ($nivo === self::ADMIN) {
            return true;
        }
        
        return isset($pravila[$nivo][$dovoljenje]) && $pravila[$nivo][$dovoljenje];
    }
}
?>