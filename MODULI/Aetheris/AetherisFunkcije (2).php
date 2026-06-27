<?php
/**
 * AETHERIS FUNKCIJE - POMOŽNE FUNKCIJE SISTEMA
 */

/**
 * Formatiraj podatke za lepši izpis
 */
function aetherisFormatirajIzpis($podatki) {
    if (is_array($podatki)) {
        return json_encode($podatki, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
    return htmlspecialchars($podatki, ENT_QUOTES, 'UTF-8');
}

/**
 * Preveri veljavnost UI ključa
 */
function aetherisPreveriUIKljuc($kljuc) {
    return !empty($kljuc) && strlen($kljuc) >= 8 && strpos($kljuc, 'aetheris') !== false;
}

/**
 * Sanitiziraj uporabniški vnos
 */
function aetherisSanitizirajVnos($vnos) {
    if (is_array($vnos)) {
        return array_map('aetherisSanitizirajVnos', $vnos);
    }
    return htmlspecialchars(trim($vnos), ENT_QUOTES, 'UTF-8');
}

/**
 * Generiraj napako v standardnem formatu
 */
function aetherisGenerirajNapako($sporocilo) {
    return [
        'status' => 'napaka',
        'sporocilo' => $sporocilo,
        'cas' => date('Y-m-d H:i:s')
    ];
}

/**
 * Generiraj uspešni odgovor v standardnem formatu
 */
function aetherisGenerirajUspeh($sporocilo, $podatki = []) {
    return array_merge([
        'status' => 'uspeh',
        'sporocilo' => $sporocilo,
        'cas' => date('Y-m-d H:i:s')
    ], $podatki);
}

/**
 * Preveri dostop uporabnika do akcije
 */
function aetherisPreveriDostop($uporabnik, $zahtevanaRaven) {
    $uporabnikPodatki = AetherisJedro::pridobiUporabnika($uporabnik);
    
    if (!$uporabnikPodatki) {
        return false;
    }
    
    return $uporabnikPodatki['raven_dostopa'] >= $zahtevanaRaven;
}

/**
 * Logiraj dejavnost v sistem
 */
function aetherisLogirajDejavnost($dejavnost, $uporabnik = 'sistem') {
    $datum = date('Y-m-d H:i:s');
    $vnos = "[$datum] [$uporabnik] $dejavnost\n";
    
    // V praksi bi logirali v datoteko ali bazo
    error_log($vnos);
}

/**
 * Validiraj podatke za novo temo
 */
function aetherisValidirajTemo($naslov, $vsebina) {
    $napake = [];
    
    if (empty($naslov) || strlen($naslov) < 5) {
        $napake[] = 'Naslov mora vsebovati vsaj 5 znakov';
    }
    
    if (empty($vsebina) || strlen($vsebina) < 10) {
        $napake[] = 'Vsebina mora vsebovati vsaj 10 znakov';
    }
    
    if (strlen($naslov) > 200) {
        $napake[] = 'Naslov je predolg (max 200 znakov)';
    }
    
    return $napake;
}

/**
 * Pridobi trenutni čas v formatu za bazo
 */
function aetherisTrenutniCas() {
    return date('Y-m-d H:i:s');
}

/**
 * Formatiraj čas za prikaz uporabniku
 */
function aetherisFormatirajCas($cas) {
    $date = new DateTime($cas);
    return $date->format('d.m.Y H:i');
}
?>