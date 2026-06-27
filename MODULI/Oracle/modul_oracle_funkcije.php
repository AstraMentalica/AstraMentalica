<?php
/**
 * Orakleum Modul - Dodatne Funkcije
 * Datoteka: modul_oracle_funkcije.php
 * Namen: Dodatne funkcije in pomožne metode za Orakleum
 */

// Preveri direktni dostop
si_preveri_direktni_dostop();

/**
 * Funkcije za delo z kartami
 */

/**
 * Preveri ali karta obstaja v bazi
 */
function oracle_preveri_karto($karta_id) {
    $json_baza = require __DIR__ . '/modul_oracle_jsonbaza.php';
    return isset($json_baza['kartice'][$karta_id]);
}

/**
 * Pridobi podrobne informacije o karti
 */
function oracle_pridobi_podatke_karte($karta_id) {
    $json_baza = require __DIR__ . '/modul_oracle_jsonbaza.php';
    
    if (!isset($json_baza['kartice'][$karta_id])) {
        return false;
    }
    
    $karta = $json_baza['kartice'][$karta_id];
    
    // Dodaj dodatne podatke
    $karta['frekvenca'] = oracle_izracunaj_frekvenco($karta_id);
    $karta['energija'] = oracle_doloc_energijo($karta_id);
    $karta['barva'] = oracle_pridobi_barvo($karta_id);
    $karta['element'] = oracle_pridobi_element($karta_id);
    
    return $karta;
}

/**
 * Vleci karto naključno iz mesta
 */
function oracle_vleci_karto_iz_mesta($mesto) {
    $json_baza = require __DIR__ . '/modul_oracle_jsonbaza.php';
    $kartice_mesta = $json_baza['mesta'][$mesto]['kartice'] ?? [];
    
    if (empty($kartice_mesta)) {
        // Če mesto nima določenih kart, vzemi naključno
        $vse_kartice = array_keys($json_baza['kartice']);
        $karta_id = $vse_kartice[array_rand($vse_kartice)];
    } else {
        $karta_id = $kartice_mesta[array_rand($kartice_mesta)];
    }
    
    return oracle_pridobi_podatke_karte($karta_id);
}

/**
 * Interpretiraj karto glede na pozicijo
 */
function oracle_interpretiraj_pozicijo($karta_id, $pozicija, $vprasanje = '') {
    $karta = oracle_pridobi_podatke_karte($karta_id);
    if (!$karta) {
        return false;
    }
    
    $interpretacija = [
        'karta' => $karta,
        'pozicija' => $pozicija,
        'vprasanje' => $vprasanje,
        'glavna_interpretacija' => oracle_generiraj_interpretacijo($karta, $pozicija, $vprasanje),
        'energija_pozicije' => oracle_pridobi_energijo_pozicije($pozicija),
        'casovna_komponenta' => oracle_doloc_casovno_komponento($pozicija)
    ];
    
    return $interpretacija;
}

/**
 * Funkcije za orakleje
 */

/**
 * Ustvari orakel z več kartami
 */
function oracle_ustvari_orakel($tip_oraklja, $stevilo_kart, $vprasanje = '') {
    $json_baza = require __DIR__ . '/modul_oracle_jsonbaza.php';
    
    // Pridobi ustrezne pozicije za tip oraklja
    $pozicije = oracle_pridobi_pozicije_za_orakel($tip_oraklja, $stevilo_kart);
    
    $orakel = [
        'tip' => $tip_oraklja,
        'stevilo_kart' => $stevilo_kart,
        'pozicije' => $pozicije,
        'kartice' => [],
        'vprasanje' => $vprasanje,
        'cas_ustvaritve' => date('Y-m-d H:i:s'),
        'id_oraklja' => uniqid('orakel_')
    ];
    
    // Vleci kartice za vsako pozicijo
    foreach ($pozicije as $pozicija) {
        $karta = oracle_vleci_karto_iz_mesta($pozicija);
        if ($karta) {
            $orakel['kartice'][] = [
                'pozicija' => $pozicija,
                'karta' => $karta,
                'interpretacija' => oracle_interpretiraj_pozicijo($karta['id'], $pozicija, $vprasanje)
            ];
        }
    }
    
    // Dodaj skupno interpretacijo
    $orakel['skupna_interpretacija'] = oracle_interpretiraj_orakel($orakel['kartice']);
    
    return $orakel;
}

/**
 * Interpretiraj celoten orakel
 */
function oracle_interpretiraj_orakel($kartice_oraklja) {
    $interpretacija = [
        'glavna_tema' => oracle_doloc_glavno_temo($kartice_oraklja),
        'energija_skupine' => oracle_izracunaj_energijo_skupine($kartice_oraklja),
        'skupno_sporocilo' => oracle_generiraj_sporocilo($kartice_oraklja),
        'priporocila' => oracle_generiraj_priporocila($kartice_oraklja),
        'opozorila' => oracle_generiraj_opozorila($kartice_oraklja),
        'frekvenca' => oracle_izracunaj_frekvenco_oraklja($kartice_oraklja)
    ];
    
    return $interpretacija;
}

/**
 * Funkcije za statistike in sledenje
 */

/**
 * Shrani vlecenje kartice v statistike
 */
function oracle_shrani_vlecenje($karta_id, $uporabnik_id, $tip_vlecenja, $vprasanje = '') {
    $json_baza = require __DIR__ . '/modul_oracle_jsonbaza.php';
    
    $vlecenje = [
        'karta_id' => $karta_id,
        'uporabnik_id' => $uporabnik_id,
        'tip_vlecenja' => $tip_vlecenja,
        'vprasanje' => $vprasanje,
        'cas_vlecenja' => date('Y-m-d H:i:s'),
        'ip_naslov' => $_SERVER['REMOTE_ADDR'] ?? 'neznan',
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'neznan'
    ];
    
    // Dodaj v statistike
    $json_baza['statistike']['vlecenja'][] = $vlecenje;
    $json_baza['statistike']['skupno_vlecenj']++;
    
    // Posodobi najbolj vlečeno karto
    if (!isset($json_baza['statistike']['karta_stevilo'][$karta_id])) {
        $json_baza['statistike']['karta_stevilo'][$karta_id] = 0;
    }
    $json_baza['statistike']['karta_stevilo'][$karta_id]++;
    
    // Shrani nazaj v datoteko
    file_put_contents(__DIR__ . '/modul_oracle_jsonbaza.php', 
        "<?php\nreturn " . var_export($json_baza, true) . ";\n");
    
    return true;
}

/**
 * Pridobi statistike uporabe
 */
function oracle_pridobi_statistike() {
    $json_baza = require __DIR__ . '/modul_oracle_jsonbaza.php';
    
    $statistike = [
        'skupno_vlecenj' => $json_baza['statistike']['skupno_vlecenj'] ?? 0,
        'najbolj_vlecena_karta' => null,
        'najmanj_vlecena_karta' => null,
        'povprecno_vprasanje' => '',
        'aktivni_dnevi' => [],
        'top_ure' => []
    ];
    
    if (isset($json_baza['statistike']['karta_stevilo']) && 
        !empty($json_baza['statistike']['karta_stevilo'])) {
        
        arsort($json_baza['statistike']['karta_stevilo']);
        $statistike['najbolj_vlecena_karta'] = array_key_first($json_baza['statistike']['karta_stevilo']);
        
        asort($json_baza['statistike']['karta_stevilo']);
        $statistike['najmanj_vlecena_karta'] = array_key_first($json_baza['statistike']['karta_stevilo']);
    }
    
    return $statistike;
}

/**
 * Pomožne funkcije
 */

/**
 * Izračunaj frekvenco kartice
 */
function oracle_izracunaj_frekvenco($karta_id) {
    $json_baza = require __DIR__ . '/modul_oracle_jsonbaza.php';
    $stevilo = $json_baza['statistike']['karta_stevilo'][$karta_id] ?? 0;
    $skupno = $json_baza['statistike']['skupno_vlecenj'] ?? 1;
    
    return round(($stevilo / $skupno) * 100, 2);
}

/**
 * Določi energijo kartice
 */
function oracle_doloc_energijo($karta_id) {
    $json_baza = require __DIR__ . '/modul_oracle_jsonbaza.php';
    $karta = $json_baza['kartice'][$karta_id] ?? [];
    
    return $karta['energija'] ?? 'nevtralna';
}

/**
 * Pridobi barvo kartice
 */
function oracle_pridobi_barvo($karta_id) {
    $json_baza = require __DIR__ . '/modul_oracle_jsonbaza.php';
    $karta = $json_baza['kartice'][$karta_id] ?? [];
    
    return $karta['barva'] ?? '#8B4513';
}

/**
 * Pridobi element kartice
 */
function oracle_pridobi_element($karta_id) {
    $json_baza = require __DIR__ . '/modul_oracle_jsonbaza.php';
    $karta = $json_baza['kartice'][$karta_id] ?? [];
    
    return $karta['element'] ?? 'zrak';
}

/**
 * Generiraj interpretacijo
 */
function oracle_generiraj_interpretacijo($karta, $pozicija, $vprasanje) {
    $base_interpretacija = $karta['interpretacije'][$pozicija] ?? $karta['opis'];
    
    if (!empty($vprasanje)) {
        $base_interpretacija .= " Vaše vprašanje: '" . $vprasanje . "'";
    }
    
    return $base_interpretacija;
}

/**
 * Pridobi energijo pozicije
 */
function oracle_pridobi_energijo_pozicije($pozicija) {
    $json_baza = require __DIR__ . '/modul_oracle_jsonbaza.php';
    return $json_baza['pozicije'][$pozicija]['energija'] ?? 'nevtralna';
}

/**
 * Določi časovno komponento
 */
function oracle_doloc_casovno_komponento($pozicija) {
    $casovne_komponente = [
        'preteklost' => 'minulost',
        'sedanjost' => 'sedanjost',
        'prihodnost' => 'prihodnost',
        'notranjost' => 'večen trenutek',
        'zunanjost' => 'zunanji vplivi',
        'pot' => 'prihodnja pot'
    ];
    
    return $casovne_komponente[$pozicija] ?? 'nedoločen';
}

/**
 * Pridobi pozicije za orakel
 */
function oracle_pridobi_pozicije_za_orakel($tip_oraklja, $stevilo_kart) {
    $orakel_pozicije = [
        'tri_karte' => ['preteklost', 'sedanjost', 'prihodnost'],
        'sest_kart' => ['notranjost', 'zunanjost', 'preteklost', 'sedanjost', 'pot', 'prihodnost'],
        'en_karta' => ['nakljucno'],
        'ljubezen' => ['ti', 'partner', 'relacija', 'pot'],
        'kariera' => ['sedanjost', 'izziv', 'priložnost', 'pot']
    ];
    
    return $orakel_pozicije[$tip_oraklja] ?? array_slice(['nakljucno'], 0, $stevilo_kart);
}

/**
 * Določi glavno temo oraklja
 */
function oracle_doloc_glavno_temo($kartice_oraklja) {
    $teme = [];
    
    foreach ($kartice_oraklja as $kartica_info) {
        $karta = $kartica_info['karta'];
        $tema = $karta['tema'] ?? 'splošno';
        $teme[] = $tema;
    }
    
    // Najbolj pogosta tema
    $tema_stevilo = array_count_values($teme);
    arsort($tema_stevilo);
    
    return array_key_first($tema_stevilo);
}

/**
 * Izračunaj energijo skupine
 */
function oracle_izracunaj_energijo_skupine($kartice_oraklja) {
    $energija = 0;
    $stevilo = count($kartice_oraklja);
    
    foreach ($kartice_oraklja as $kartica_info) {
        $karta = $kartica_info['karta'];
        $karta_energija = $karta['energija_vrednost'] ?? 0;
        $energija += $karta_energija;
    }
    
    return $stevilo > 0 ? round($energija / $stevilo, 2) : 0;
}

/**
 * Generiraj sporočilo
 */
function oracle_generiraj_sporocilo($kartice_oraklja) {
    $sporocila = [
        'Karte govorijo o pomembnih spremembah v vašem življenju.',
        'Energija kart kaže na novo smer in priložnosti.',
        'Spoštovanje narave kart vas bo privedlo k modrosti.',
        'Karte pozivajo k notranji povezanosti in razumevanju.'
    ];
    
    return $sporocila[array_rand($sporocila)];
}

/**
 * Generiraj priporocila
 */
function oracle_generiraj_priporocila($kartice_oraklja) {
    return [
        'Bodite odprti za nove priložnosti',
        'Zaupajte svojim instinktom',
        'Prevzemite odgovornost za svoje odločitve',
        'Bodite potrpežljivi s procesom'
    ];
}

/**
 * Generiraj opozorila
 */
function oracle_generiraj_opozorila($kartice_oraklja) {
    return [
        'Ne sprejemajte prenagljenih odločitev',
        'Previdno z materialnimi zadevami',
        'Poslušajte svoj notranji glas',
        'Ne pozabite na svojo intuicijo'
    ];
}

/**
 * Izračunaj frekvenco oraklja
 */
function oracle_izracunaj_frekvenco_oraklja($kartice_oraklja) {
    $frekvence = [];
    
    foreach ($kartice_oraklja as $kartica_info) {
        $frekvence[] = oracle_izracunaj_frekvenco($kartica_info['karta']['id']);
    }
    
    return array_sum($frekvence) / count($frekvence);
}

?>