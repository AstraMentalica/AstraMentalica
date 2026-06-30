<?php
/**
 * Codex Damiris - Glavno Jedro Aplikacije (Statično)
 * Lokacija: /var/www/html/codex-damiris/CodexDamirisJedro.php
 */

class CodexDamirisJedro {
    
    // UPORABNIŠKI NIVOJI
    const GOST = 0;
    const OSNOVNI = 1;
    const POTRJEN = 2;
    const NAPREDNI = 3;
    const VIP = 4;
    const ADMIN = 5;
    
    // NASTAVITVE APLIKACIJE
    private static $nastavitve = [
        'ime_aplikacije' => 'Codex Damiris',
        'verzija' => '2.0.0',
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
            'trajanje' => 86400, // 24 ure
            'regeneracija' => 1800 // 30 minut
        ]
    ];
    
    // KATEGORIJE ZNANJA
    private static $kategorije = [
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
    
    // PRAVILA DOSTOPA
    private static $pravila_dostopa = [
        0 => [ // GOST
            'branje_osnovno' => true,
            'iskanje' => true,
            'dodajanje_vsebin' => false,
            'ai_dostop' => false
        ],
        1 => [ // OSNOVNI
            'branje_osnovno' => true,
            'iskanje' => true,
            'dodajanje_vsebin' => false,
            'ai_dostop' => false
        ],
        2 => [ // POTRJEN
            'branje_osnovno' => true,
            'iskanje' => true,
            'dodajanje_vsebin' => true,
            'ai_dostop' => true
        ],
        3 => [ // NAPREDNI
            'branje_vse' => true,
            'iskanje' => true,
            'dodajanje_vsebin' => true,
            'ai_dostop' => true,
            'pregled_statistik' => true
        ],
        4 => [ // VIP
            'branje_vse' => true,
            'iskanje' => true,
            'dodajanje_vsebin' => true,
            'ai_dostop' => true,
            'pregled_statistik' => true,
            'dostop_ekskluzivno' => true
        ],
        5 => [ // ADMIN
            'vse_dovoljeno' => true
        ]
    ];
    
    /**
     * Pridobi nastavitve aplikacije
     */
    public static function pridobiNastavitve() {
        return self::$nastavitve;
    }
    
    /**
     * Pridobi vse kategorije
     */
    public static function pridobiKategorije() {
        return self::$kategorije;
    }
    
    /**
     * Pridobi kategorijo po ključu
     */
    public static function pridobiKategorijo($kljuc) {
        return self::$kategorije[$kljuc] ?? null;
    }
    
    /**
     * Pridobi pravila dostopa
     */
    public static function pridobiPravilaDostopa() {
        return self::$pravila_dostopa;
    }
    
    /**
     * Preveri ali nivo ima dovoljenje
     */
    public static function imaDovoljenje($nivo, $dovoljenje) {
        if ($nivo === self::ADMIN) {
            return true;
        }
        
        return isset(self::$pravila_dostopa[$nivo][$dovoljenje]) && 
               self::$pravila_dostopa[$nivo][$dovoljenje];
    }
    
    /**
     * Pridobi DSN za povezavo z bazo
     */
    public static function pridobiBazaDsn() {
        $baza = self::$nastavitve['baza'];
        return "mysql:host={$baza['gostitelj']};dbname={$baza['ime']};charset={$baza['znakovni_niz']}";
    }
    
    /**
     * Pridobi PDO nastavitve
     */
    public static function pridobiBazaNastavitve() {
        return [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ];
    }
}
?>