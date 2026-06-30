<?php
/**
 * Orakleum Modul - Pravila in Omejitve
 * Datoteka: modul_oracle_pravila.php
 * Namen: Definiranje pravil, omejitev in validacij za Orakleum modul
 */

// Preveri direktni dostop
si_preveri_direktni_dostop();

/**
 * Pravila za Orakleum modul
 */
class OrakleumPravila {
    
    /**
     * Preveri ali je uporabnik upravičen do dostopa
     */
    public static function preveriDostop($uporabnik_id = null, $tip_zahteve = 'vlecenje') {
        $pravila = self::getPravilaDostopa();
        
        // Preveri splošna pravila
        if (!$pravila['splosno']['dovoljen_dostop']) {
            return [
                'uspeh' => false,
                'razlog' => 'Dostop do modula je trenutno onemogočen',
                'koda' => 'ACCESS_DENIED'
            ];
        }
        
        // Preveri tip zahteve
        if (!isset($pravila['tipi_zahtev'][$tip_zahteve])) {
            return [
                'uspeh' => false,
                'razlog' => 'Nepodprt tip zahteve',
                'koda' => 'UNSUPPORTED_REQUEST_TYPE'
            ];
        }
        
        $tip_pravila = $pravila['tipi_zahtev'][$tip_zahteve];
        
        // Preveri zahtevo po prijavi
        if ($tip_pravila['zahteva_prijavo'] && !$uporabnik_id) {
            return [
                'uspeh' => false,
                'razlog' => 'Ta zahteva zahteva prijavo',
                'koda' => 'LOGIN_REQUIRED'
            ];
        }
        
        // Preveri omejitve za anonimne uporabnike
        if (!$uporabnik_id && $tip_pravila['omejitve_anonimni']['max_na_dan'] > 0) {
            $trenutna_uporaba = self::preveriDnevnoOmejitev($_SERVER['REMOTE_ADDR'], $tip_zahteve);
            if ($trenutna_uporaba >= $tip_pravila['omejitve_anonimni']['max_na_dan']) {
                return [
                    'uspeh' => false,
                    'razlog' => 'Presegli ste dnevno omejitev za anonimne uporabnike',
                    'koda' => 'DAILY_LIMIT_EXCEEDED_ANONYMOUS'
                ];
            }
        }
        
        // Preveri omejitve za prijavljene uporabnike
        if ($uporabnik_id && $tip_pravila['omejitve_prijavljeni']['max_na_dan'] > 0) {
            $trenutna_uporaba = self::preveriDnevnoOmejitev($uporabnik_id, $tip_zahteve, true);
            if ($trenutna_uporaba >= $tip_pravila['omejitve_prijavljeni']['max_na_dan']) {
                return [
                    'uspeh' => false,
                    'razlog' => 'Presegli ste dnevno omejitev',
                    'koda' => 'DAILY_LIMIT_EXCEEDED_USER'
                ];
            }
        }
        
        return [
            'uspeh' => true,
            'sporocilo' => 'Dostop dovoljen'
        ];
    }
    
    /**
     * Preveri omejitve po času
     */
    public static function preveriCasovneOmejitve($tip_zahteve = 'vlecenje') {
        $pravila = self::getPravilaDostopa();
        $casovne_omejitve = $pravila['casovne_omejitve'];
        
        $trenutni_cas = new DateTime();
        $trenutna_ura = (int)$trenutni_cas->format('G');
        $trenutni_dan = (int)$trenutni_cas->format('N'); // 1 = ponedeljek, 7 = nedelja
        
        // Preveri časovne okna
        if (isset($casovne_omejitve['aktivne_ure'])) {
            $aktivne_ure = $casovne_omejitve['aktivne_ure'];
            if ($trenutna_ura < $aktivne_ure['od'] || $trenutna_ura >= $aktivne_ure['do']) {
                return [
                    'uspeh' => false,
                    'razlog' => "Modul je aktiven samo med {$aktivne_ure['od']}:00 in {$aktivne_ure['do']}:00",
                    'koda' => 'TIME_RESTRICTION'
                ];
            }
        }
        
        // Preveri prepovedane dni
        if (isset($casovne_omejitve['prepovedani_dnevi']) && 
            in_array($trenutni_dan, $casovne_omejitve['prepovedani_dnevi'])) {
            return [
                'uspeh' => false,
                'razlog' => 'Modul ni na voljo na ta dan',
                'koda' => 'DAY_RESTRICTION'
            ];
        }
        
        return [
            'uspeh' => true,
            'sporocilo' => 'Časovne omejitve ustrezne'
        ];
    }
    
    /**
     * Preveri vsebnost vprašanja
     */
    public static function preveriVprasanj($vprasanje, $tip_vlecenja = 'navadno') {
        $pravila = self::getPravilaVprasanj();
        
        // Osnovne validacije
        if (empty(trim($vprasanje)) && $tip_vlecenja === 'interpretacija') {
            return [
                'uspeh' => false,
                'razlog' => 'Vprašanje je obvezno za interpretacijo',
                'koda' => 'QUESTION_REQUIRED'
            ];
        }
        
        // Dolžina
        $dolzina = strlen($vprasanje);
        if ($dolzina > $pravila['maksimalna_dolzina']) {
            return [
                'uspeh' => false,
                'razlog' => "Vprašanje je predolgo (maksimum {$pravila['maksimalna_dolzina']} znakov)",
                'koda' => 'QUESTION_TOO_LONG'
            ];
        }
        
        if ($dolzina < $pravila['minimalna_dolzina']) {
            return [
                'uspeh' => false,
                'razlog' => "Vprašanje je prekratko (minimum {$pravila['minimalna_dolzina']} znakov)",
                'koda' => 'QUESTION_TOO_SHORT'
            ];
        }
        
        // Prepovedane besede
        $prepovedane_besede = $pravila['prepovedane_besede'];
        foreach ($prepovedane_besede as $beseda) {
            if (stripos($vprasanje, $beseda) !== false) {
                return [
                    'uspeh' => false,
                    'razlog' => 'Vprašanje vsebuje prepovedane izraze',
                    'koda' => 'FORBIDDEN_CONTENT'
                ];
            }
        }
        
        // SQL Injection preventiva
        if (self::preveriSqlInjection($vprasanje)) {
            return [
                'uspeh' => false,
                'razlog' => 'Vprašanje vsebuje sumljive znake',
                'koda' => 'SUSPICIOUS_INPUT'
            ];
        }
        
        return [
            'uspeh' => true,
            'sporocilo' => 'Vprašanje je veljavno'
        ];
    }
    
    /**
     * Preveri veljavnost karte
     */
    public static function preveriKarto($karta_id) {
        $kartice_datoteka = dirname(__FILE__) . '/modul_oracle_jsonbaza.php';
        
        if (!file_exists($kartice_datoteka)) {
            return [
                'uspeh' => false,
                'razlog' => 'Kartice baza ni na voljo',
                'koda' => 'DATABASE_UNAVAILABLE'
            ];
        }
        
        $json_baza = require $kartice_datoteka;
        
        if (!isset($json_baza['kartice'][$karta_id])) {
            return [
                'uspeh' => false,
                'razlog' => 'Karta ne obstaja',
                'koda' => 'CARD_NOT_FOUND'
            ];
        }
        
        $karta = $json_baza['kartice'][$karta_id];
        
        // Preveri ali je karta aktivna
        if (isset($karta['aktivna']) && !$karta['aktivna']) {
            return [
                'uspeh' => false,
                'razlog' => 'Karta trenutno ni na voljo',
                'koda' => 'CARD_INACTIVE'
            ];
        }
        
        return [
            'uspeh' => true,
            'karta' => $karta,
            'sporocilo' => 'Karta je veljavna'
        ];
    }
    
    /**
     * Preveri rate limiting
     */
    public static function preveriRateLimit($identifikator, $tip = 'vlecenje') {
        $pravila = self::getPravilaDostopa();
        $rate_limit = $pravila['rate_limiting'][$tip] ?? 10; // privzeto 10 zahtev
        
        // Preveri koliko zahtev je bilo v zadnji minuti
        $trenutni_cas = time();
        $zadnja_minuta = $trenutni_cas - 60;
        
        // Tu bi implementiral dejansko preverjanje baze
        // Zaenkrat simuliram
        $zahtevi_v_minuti = 0; // placeholder
        
        if ($zahtevi_v_minuti >= $rate_limit) {
            return [
                'uspeh' => false,
                'razlog' => "Presegli ste limit zahtev ({$rate_limit} na minuto)",
                'koda' => 'RATE_LIMIT_EXCEEDED'
            ];
        }
        
        return [
            'uspeh' => true,
            'trenutni_count' => $zahtevi_v_minuti,
            'limit' => $rate_limit
        ];
    }
    
    /**
     * Vrni pravila dostopa
     */
    private static function getPravilaDostopa() {
        return [
            'splosno' => [
                'dovoljen_dostop' => true,
                'vzdrzevanje' => false,
                'debug_mode' => false
            ],
            'tipi_zahtev' => [
                'vlecenje' => [
                    'zahteva_prijavo' => false,
                    'omejitve_anonimni' => ['max_na_dan' => 20, 'max_na_uro' => 5],
                    'omejitve_prijavljeni' => ['max_na_dan' => 100, 'max_na_uro' => 20]
                ],
                'interpretacija' => [
                    'zahteva_prijavo' => false,
                    'omejitve_anonimni' => ['max_na_dan' => 10, 'max_na_uro' => 3],
                    'omejitve_prijavljeni' => ['max_na_dan' => 50, 'max_na_uro' => 10]
                ],
                'orakelj' => [
                    'zahteva_prijavo' => false,
                    'omejitve_anonimni' => ['max_na_dan' => 5, 'max_na_uro' => 2],
                    'omejitve_prijavljeni' => ['max_na_dan' => 25, 'max_na_uro' => 5]
                ]
            ],
            'casovne_omejitve' => [
                'aktivne_ure' => ['od' => 6, 'do' => 23], // 6:00 - 23:00
                'prepovedani_dnevi' => [], // prazno = vsi dnevi dovoljeni
                'vzdrzevalne_ure' => ['od' => 2, 'do' => 4] // 02:00 - 04:00
            ],
            'rate_limiting' => [
                'vlecenje' => 10,
                'interpretacija' => 5,
                'orakelj' => 3
            ]
        ];
    }
    
    /**
     * Vrni pravila za vprašanja
     */
    private static function getPravilaVprasanj() {
        return [
            'minimalna_dolzina' => 3,
            'maksimalna_dolzina' => 500,
            'prepovedane_besede' => [
                'spam', 'reklama', 'virus', 'hack', 'crack',
                'podkupnina', 'tatvina', 'umori', 'nasilje'
            ],
            'obvezne_besede' => [], // ni obveznih besed
            'dovoljeni_znaki' => 'a-zA-ZčšžČŠŽ0-9\s\.\,\!\?\-\(\)\[\]\{\}',
            'prepovedani_simboli' => ['<', '>', '&lt;', '&gt;', '<?', '?>', '<script', '</script>']
        ];
    }
    
    /**
     * Preveri dnevno omejitev
     */
    private static function preveriDnevnoOmejitev($identifikator, $tip_zahteve, $prijavljen = false) {
        // To bi bilo implementirano z dejansko bazo
        // Zaenkrat vračam 0
        return 0;
    }
    
    /**
     * Preveri SQL injection
     */
    private static function preveriSqlInjection($vnos) {
        $sumljivi_vzorec = [
            '/union\s+select/i',
            '/drop\s+table/i',
            '/delete\s+from/i',
            '/insert\s+into/i',
            '/update\s+set/i',
            '/\'\s*or\s*\'1\'\s*=\s*\'1/i',
            '/\-\-/i',
            '/\/\*/i',
            '/\*\//i'
        ];
        
        foreach ($sumljivi_vzorec as $vzorec) {
            if (preg_match($vzorec, $vnos)) {
                return true;
            }
        }
        
        return false;
    }
}

/**
 * Pomožne funkcije za pravila
 */

/**
 * Sanitiziraj vnos uporabnika
 */
function oracle_sanitizirajVnos($vnos) {
    // Odstrani HTML oznake
    $vnos = strip_tags($vnos);
    
    // Odstrani posebne znake
    $vnos = htmlspecialchars($vnos, ENT_QUOTES, 'UTF-8');
    
    // Odstrani preveč presledkov
    $vnos = preg_replace('/\s+/', ' ', $vnos);
    
    return trim($vnos);
}

/**
 * Validiraj email naslov (če potrebno)
 */
function oracle_validirajEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Preveri IP naslov (za anonimne uporabnike)
 */
function oracle_pridobiClientIP() {
    $ip_keys = ['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'];
    
    foreach ($ip_keys as $key) {
        if (array_key_exists($key, $_SERVER) === true) {
            foreach (array_map('trim', explode(',', $_SERVER[$key])) as $ip) {
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                    return $ip;
                }
            }
        }
    }
    
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}

?>