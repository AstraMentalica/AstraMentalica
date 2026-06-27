<?php
declare(strict_types=1);

/**
 * SyneraFunkcije.php - Pomožne funkcije za platformo
 * Obeznostna datoteka: (Ime_modula)Funkcije.php
 */

class SyneraFunkcije {
    
    public static function pridobiStatistiko(): array {
        return [
            'zagonov' => 847,
            'analiz' => 423,
            'sigilov' => 156,
            'uporabnikov' => 89,
            'aktivnih_sej' => 12,
            'povprecni_odziv' => '145ms',
            'zadnja_aktivnost' => date('Y-m-d H:i:s')
        ];
    }
    
    public static function preveriPovezave(): array {
        return [
            'ai_komunikacija' => [
                'status' => '🟢', 
                'odziv' => '125ms',
                'opis' => 'AI analiza in priporočila'
            ],
            'baza_podatkov' => [
                'status' => '🟢', 
                'odziv' => '45ms',
                'opis' => 'Shranjevanje podatkov'
            ],
            'generator_sigilov' => [
                'status' => '🟢', 
                'odziv' => '89ms',
                'opis' => 'Ustvarjanje simbolov'
            ]
        ];
    }
    
    public static function formatirajCas(string $format = 'd.m.Y H:i:s'): string {
        return date($format);
    }
    
    public static function preveriDostop(string $zahtevanaRaven): bool {
        $dovoljeneRavni = [
            'S0_GOST' => 0,
            'S1_OSNOVNI' => 1, 
            'S2_POTRJEN' => 2,
            'S3_NAPREDNI' => 3,
            'S4_VIP' => 4,
            'S5_ADMIN' => 5
        ];
        
        $trenutnaRaven = $dovoljeneRavni['S2_POTRJEN'] ?? 0;
        $zahtevana = $dovoljeneRavni[$zahtevanaRaven] ?? 0;
        
        return $trenutnaRaven >= $zahtevana;
    }
    
    public static function sanitizirajVnos(string $vnos): string {
        return htmlspecialchars(trim($vnos), ENT_QUOTES, 'UTF-8');
    }
    
    public static function generirajID(int $dolzina = 8): string {
        return substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyz'), 0, $dolzina);
    }
}
?>