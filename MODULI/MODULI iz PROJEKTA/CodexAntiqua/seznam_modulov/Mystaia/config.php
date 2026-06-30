<?php
/**
 * Mystaia - Konfiguracija modula
 * Nastavitve specifične za e-terično trgovino
 * Modularno in varno za AstraPortal
 */

// Prepreči neposreden dostop in naloži glavni config
if (!defined('PORTAL_ACCESS')) {
    define('PORTAL_ACCESS', true);
    if (file_exists(dirname(__DIR__, 2) . '/config.php')) {
        require_once dirname(__DIR__, 2) . '/config.php';
    }
}

// Modul-specifične nastavitve
define('MYSTAIA_VERZIJA', '1.0.0');
define('MYSTAIA_DATA_DIR', get_data_path('mystaia') ?? dirname(__DIR__, 2) . '/datoteke/mystaia');
define('MYSTAIA_URL', get_module_url('mystaia') ?? '/moduli/mystaia');
define('MYSTAIA_ADMIN_URL', MYSTAIA_URL . '/admin.php');
// ============================================================================
// Osnovne poti in URL-ji
// ============================================================================

// ============================================================================
// Nastavitve trgovine
// ============================================================================
$mystaia_nastavitve = [
    'davcna_stopnja' => 0.22,
    'prevzemni_mestni' => [
        'ljubljana' => 'Ljubljana - Tržaška cesta 25',
        'maribor' => 'Maribor - Ulica talcev 15',
        'koper' => 'Koper - Pristaniška ulica 8'
    ],
    'dostava' => [
        'postna' => ['cena' => 3.50, 'rok' => '3-5 delovnih dni'],
        'hitra' => ['cena' => 6.90, 'rok' => '1-2 delovna dneva'],
        'express' => ['cena' => 12.90, 'rok' => 'do 24 ur']
    ],
    'placila' => [
        'kartica' => 'Kreditna kartica',
        'paypal' => 'PayPal',
        'ob-prevzemu' => 'Po povzetju',
        'bitcoin' => 'Bitcoin'
    ]
];

// ============================================================================
// Funkcije za jezik
// ============================================================================
function mystaia_nalozi_jezik($jezik = 'sl_SI') {
    $jezikovna_datoteka = MYSTAIA_DATA_DIR . "/jezik/{$jezik}.php";
    if (file_exists($jezikovna_datoteka)) {
        return require $jezikovna_datoteka;
    }
    return require MYSTAIA_DATA_DIR . "/jezik/sl_SI.php";
}

// ============================================================================
// Funkcije za izdelke in naročila
// ============================================================================
function mystaia_pridobi_izdelke($kategorija = null) {
    $datoteka = MYSTAIA_DATA_DIR . '/izdelki.json';
    $izdelki = json_decode(file_get_contents($datoteka), true) ?: [];

    if ($kategorija) {
        $izdelki = array_filter($izdelki, function($izdelek) use ($kategorija) {
            return $izdelek['kategorija'] === $kategorija;
        });
    }

    return $izdelki;
}

function mystaia_pridobi_izdelek($id) {
    $izdelki = mystaia_pridobi_izdelke();
    foreach ($izdelki as $izdelek) {
        if ($izdelek['id'] == $id) {
            return $izdelek;
        }
    }
    return null;
}

function mystaia_shrani_narocilo($podatki) {
    $datoteka = MYSTAIA_DATA_DIR . '/narocila.json';
    $trenutna_narocila = json_decode(file_get_contents($datoteka), true) ?: [];

    $novo_narocilo = [
        'id' => uniqid('narocilo_'),
        'cas' => date('Y-m-d H:i:s'),
        'podatki' => $podatki,
        'status' => 'v_obdelavi'
    ];

    $trenutna_narocila[] = $novo_narocilo;
    file_put_contents($datoteka, json_encode($trenutna_narocila, JSON_PRETTY_PRINT));

    return $novo_narocilo['id'];
}

function mystaia_izracunaj_skupno_ceno($kosarica) {
    global $mystaia_nastavitve;
    $skupna_cena = 0;

    foreach ($kosarica as $izdelek) {
        $izdelek_podatki = mystaia_pridobi_izdelek($izdelek['id']);
        if ($izdelek_podatki) {
            $skupna_cena += $izdelek_podatki['cena'] * $izdelek['kolicina'];
        }
    }

    return $skupna_cena;
}

// ============================================================================
// Funkcija za varno zapisovanje JSON datotek
// ============================================================================
function mystaia_write_json($file, $data) {
    $path = MYSTAIA_DATA_DIR . '/' . $file;
    return file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT));
}

// ============================================================================
// Inicializacija modula (če je potrebno)
// ============================================================================
if (!isset($_SESSION)) {
    session_start();
}

?>