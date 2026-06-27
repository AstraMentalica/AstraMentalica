<?php
/**
 * AETHERIS JEDRO - GLAVNI RAZRED APLIKACIJE
 * Statično jedro sistema
 */

class AetherisJedro {
    public static $uporabniki;
    public static $tematskiSklopi;
    public static $teme;
    public static $komentarji;
    public static $statistike;
    
    /**
     * Inicializira celoten sistem
     */
    public static function inicializiraj() {
        self::$uporabniki = [];
        self::$tematskiSklopi = [];
        self::$teme = [];
        self::$komentarji = [];
        self::$statistike = [
            'stevilo_vprasanj' => 0,
            'stevilo_odgovorov' => 0,
            'stevilo_tem' => 0,
            'stevilo_komentarjev' => 0,
            'zadnja_aktivnost' => date('Y-m-d H:i:s')
        ];
        
        self::ustvariTestnePodatke();
    }
    
    /**
     * Ustvari testne podatke za demonstracijo
     */
    private static function ustvariTestnePodatke() {
        // Uporabniki sistema
        self::$uporabniki = [
            'gost' => [
                'id' => 0,
                'ime' => 'Gost',
                'eposta' => 'gost@aetheris.si',
                'raven_dostopa' => 0,
                'datum_registracije' => '2024-01-01'
            ],
            'registriran' => [
                'id' => 1,
                'ime' => 'Janez Novak',
                'eposta' => 'janez@aetheris.si',
                'raven_dostopa' => 1,
                'datum_registracije' => '2024-01-15'
            ],
            'napredni' => [
                'id' => 2,
                'ime' => 'Marija Horvat',
                'eposta' => 'marija@aetheris.si',
                'raven_dostopa' => 2,
                'datum_registracije' => '2024-01-20'
            ],
            'upravitelj' => [
                'id' => 3,
                'ime' => 'Admin Aetheris',
                'eposta' => 'admin@aetheris.si',
                'raven_dostopa' => 3,
                'datum_registracije' => '2024-01-10'
            ]
        ];
        
        // Tematski sklopi foruma
        self::$tematskiSklopi = [
            1 => [
                'id' => 1,
                'naslov' => 'Ezoterika',
                'opis' => 'Notranja znanja, simbolika in iniciacije',
                'ikona' => '🔮',
                'raven_dostopa' => 0,
                'ustvarjeno' => '2024-01-01'
            ],
            2 => [
                'id' => 2,
                'naslov' => 'Eterika',
                'opis' => 'Subtilna telesa, cakre in energijsko ravnovesje',
                'ikona' => '✨',
                'raven_dostopa' => 1,
                'ustvarjeno' => '2024-01-01'
            ],
            3 => [
                'id' => 3,
                'naslov' => 'Magija',
                'opis' => 'Rituali, volja in crta med svetovi',
                'ikona' => '⚡',
                'raven_dostopa' => 2,
                'ustvarjeno' => '2024-01-01'
            ],
            4 => [
                'id' => 4,
                'naslov' => 'Skrita Soba',
                'opis' => 'Ekskluzivne razprave za izbrane clanek',
                'ikona' => '🔒',
                'raven_dostopa' => 2,
                'ustvarjeno' => '2024-01-01'
            ]
        ];
        
        // Primer teme za demonstracijo
        self::$teme = [
            1 => [
                'id' => 1,
                'naslov' => 'Kako najti notranji mir v hecticnem svetu?',
                'vsebina' => 'Razpravljajmo o metodah za doseganje notranjega miru v vsakdanjem zivljenju...',
                'avtor' => 'registriran',
                'sklop_id' => 1,
                'datum_ustvarjanja' => '2024-01-15 10:30:00',
                'stevilo_ogledov' => 42,
                'zaklenjena' => false
            ]
        ];
        
        // Posodobi statistiko
        self::$statistike['stevilo_tem'] = count(self::$teme);
    }
    
    /**
     * Pridobi vse uporabnike sistema
     */
    public static function pridobiVseUporabnike() {
        return self::$uporabniki;
    }
    
    /**
     * Pridobi uporabnika po ID-ju
     */
    public static function pridobiUporabnika($uporabnikId) {
        return self::$uporabniki[$uporabnikId] ?? null;
    }
    
    /**
     * Pridobi vse tematske sklope
     */
    public static function pridobiVseTematskeSklope() {
        return self::$tematskiSklopi;
    }
    
    /**
     * Pridobi tematski sklop po ID-ju
     */
    public static function pridobiTematskiSklop($sklopId) {
        return self::$tematskiSklopi[$sklopId] ?? null;
    }
    
    /**
     * Pridobi vse teme (opcijsko filtrirano po sklopu)
     */
    public static function pridobiVseTeme($sklopId = null) {
        if ($sklopId === null) {
            return self::$teme;
        }
        
        return array_filter(self::$teme, function($tema) use ($sklopId) {
            return $tema['sklop_id'] == $sklopId;
        });
    }
    
    /**
     * Pridobi temo po ID-ju
     */
    public static function pridobiTemo($temaId) {
        return self::$teme[$temaId] ?? null;
    }
    
    /**
     * Pridobi statistiko sistema
     */
    public static function pridobiStatistiko() {
        self::$statistike['zadnja_aktivnost'] = date('Y-m-d H:i:s');
        return self::$statistike;
    }
    
    /**
     * Preveri dostop uporabnika do sklopa
     */
    public static function preveriDostop($uporabnikId, $sklopId) {
        $uporabnik = self::pridobiUporabnika($uporabnikId);
        $sklop = self::pridobiTematskiSklop($sklopId);
        
        if (!$uporabnik || !$sklop) {
            return false;
        }
        
        return $uporabnik['raven_dostopa'] >= $sklop['raven_dostopa'];
    }
    
    /**
     * Ustvari novo temo
     */
    public static function ustvariTemo($naslov, $vsebina, $avtor, $sklopId) {
        $novId = count(self::$teme) + 1;
        
        self::$teme[$novId] = [
            'id' => $novId,
            'naslov' => $naslov,
            'vsebina' => $vsebina,
            'avtor' => $avtor,
            'sklop_id' => $sklopId,
            'datum_ustvarjanja' => date('Y-m-d H:i:s'),
            'stevilo_ogledov' => 0,
            'zaklenjena' => false
        ];
        
        self::$statistike['stevilo_tem']++;
        self::$statistike['zadnja_aktivnost'] = date('Y-m-d H:i:s');
        
        return $novId;
    }
    
    /**
     * Posodobi statistiko vprasanj
     */
    public static function posodobiStatistikoVprasanj() {
        self::$statistike['stevilo_vprasanj']++;
        self::$statistike['zadnja_aktivnost'] = date('Y-m-d H:i:s');
    }
    
    /**
     * Posodobi statistiko odgovorov
     */
    public static function posodobiStatistikoOdgovorov() {
        self::$statistike['stevilo_odgovorov']++;
        self::$statistike['zadnja_aktivnost'] = date('Y-m-d H:i:s');
    }
}

// Inicializiraj jedro ob vključitvi
AetherisJedro::inicializiraj();
?>