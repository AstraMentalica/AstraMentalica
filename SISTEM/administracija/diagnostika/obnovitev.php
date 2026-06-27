<?php
/**
 * ---------------------------------------------------------
 * POT: SISTEM/administracija/diagnostika/obnovitev.php
 * v111 (27.5.2026 10:45)
 * ---------------------------------------------------------
 * OPIS: Recovery model – obnovitev sistema po napaki
 * ---------------------------------------------------------
 *
 * ODVISNOSTI:
 * - pot.php
 * - SISTEM/kernel/baze/upravljalec_baz.php
 *
 * UPORABA:
 * - API endpoint /api/health/recovery
 * - CLI php cli.php recovery
 *
 * FUNKCIJE:
 * - obnovitev_izvedi(), obnovitev_zadnja()
 * - obnovitev_zgodovina(), obnovitev_statistika()
 * - obnovitev_varnostna_kopija(), obnovitev_iz_varnostne_kopije()
 *
 * PREPOVEDI:
 * - Brez echo, print_r, var_dump
 *
 * STATUS: Stabilno
 *
 * ZGODOVINA:
 * - v111: FAZA 20 – implementacija
 *
 * ---------------------------------------------------------
 * AVTOR: AstraMentalica Mojster
 * ---------------------------------------------------------
 */

declare(strict_types=1);

$GLOBALS['OBNOVITEV_ZGODOVINA'] = [];

function obnovitev_zabelezi(string $tip, array $podatki): void
{
$zapis = [
    'tip' => $tip,
    'podatki' => $podatki,
    'cas' => time(),
    'uspeh' => $podatki['uspeh'] ?? false,
    'id' => uniqid('rec_', true)
];

$GLOBALS['OBNOVITEV_ZGODOVINA'][] = $zapis;

// Omeji zgodovino na 100 zapisov
if (count($GLOBALS['OBNOVITEV_ZGODOVINA']) > 100) {
    array_shift($GLOBALS['OBNOVITEV_ZGODOVINA']);
}

// Shrani v bazo
if (function_exists('baza_zapisi')) {
    baza_zapisi('obnovitev_zgodovina', $zapis);
}
}

function obnovitev_izvedi(array $opcije = []): array
{
$zacetek = microtime(true);
$rezultati = [];
$napake = [];

// 1. Obnovi cache
if (!isset($opcije['brez_cache']) || !$opcije['brez_cache']) {
    try {
        cache_pocisti();
        $rezultati['cache'] = 'obnovljen';
    } catch (Throwable $e) {
        $napake[] = 'Cache: ' . $e->getMessage();
        $rezultati['cache'] = 'napaka';
    }
}

// 2. Obnovi register modulov
if (!isset($opcije['brez_modulov']) || !$opcije['brez_modulov']) {
    try {
        moduli_sinhroniziraj_z_registrom();
        $rezultati['moduli'] = 'sinhronizirani';
    } catch (Throwable $e) {
        $napake[] = 'Moduli: ' . $e->getMessage();
        $rezultati['moduli'] = 'napaka';
    }
}

// 3. Počisti mrtve pakete v vrsti
if (!isset($opcije['brez_vrste']) || !$opcije['brez_vrste']) {
    try {
        $mrtveVrste = queue_mrtve_vrste();
        foreach ($mrtveVrste as $vrsta) {
            queue_mrtvo_pocisti($vrsta);
        }
        $rezultati['vrsta'] = 'pociscena';
    } catch (Throwable $e) {
        $napake[] = 'Vrsta: ' . $e->getMessage();
        $rezultati['vrsta'] = 'napaka';
    }
}

// 4. Obnovi idempotenco
if (!isset($opcije['brez_idempotence']) || !$opcije['brez_idempotence']) {
    try {
        if (function_exists('idempotenca_pocisti_stare')) {
            $stevilo = idempotenca_pocisti_stare();
            $rezultati['idempotenca'] = "pociscena ($stevilo)";
        }
    } catch (Throwable $e) {
        $napake[] = 'Idempotenca: ' . $e->getMessage();
        $rezultati['idempotenca'] = 'napaka';
    }
}

// 5. Ponastavi circuit breakerje
if (!isset($opcije['brez_circuit_breaker']) || !$opcije['brez_circuit_breaker']) {
    try {
        $GLOBALS['CIRCUIT_BREAKERJI'] = [];
        $rezultati['circuit_breaker'] = 'ponastavljen';
    } catch (Throwable $e) {
        $napake[] = 'Circuit breaker: ' . $e->getMessage();
        $rezultati['circuit_breaker'] = 'napaka';
    }
}

// 6. Ponastavi sledenje
if (!isset($opcije['brez_sledenja']) || !$opcije['brez_sledenja']) {
    try {
        sled_pocisti();
        $rezultati['sledenje'] = 'pocisceno';
    } catch (Throwable $e) {
        $napake[] = 'Sledenje: ' . $e->getMessage();
        $rezultati['sledenje'] = 'napaka';
    }
}

$trajanje = round((microtime(true) - $zacetek) * 1000, 2);
$uspeh = empty($napake);

$odziv = [
    'status' => $uspeh ? 'uspeh' : 'opozorilo',
    'status_koda' => $uspeh ? 200 : 207,
    'sporocilo' => $uspeh ? 'Obnovitev uspešna' : 'Obnovitev delno uspešna',
    'vsebina' => [
        'trajanje_ms' => $trajanje,
        'rezultati' => $rezultati,
        'napake' => $napake
    ]
];

obnovitev_zabelezi('izvedba', [
    'uspeh' => $uspeh,
    'trajanje_ms' => $trajanje,
    'rezultati' => $rezultati,
    'napake' => $napake,
    'opcije' => $opcije
]);

return $odziv;
}

function obnovitev_zadnja(): ?array
{
if (!empty($GLOBALS['OBNOVITEV_ZGODOVINA'])) {
    return end($GLOBALS['OBNOVITEV_ZGODOVINA']);
}

if (function_exists('baza_beri')) {
    $zadnja = baza_beri('obnovitev_zgodovina', [], ['cas' => 'DESC'], 1);
    return !empty($zadnja) ? $zadnja[0] : null;
}

return null;
}

function obnovitev_zgodovina(int $limit = 20): array
{
$zgodovina = $GLOBALS['OBNOVITEV_ZGODOVINA'];

if (function_exists('baza_beri') && count($zgodovina) < $limit) {
    $bazaZgodovina = baza_beri('obnovitev_zgodovina', [], ['cas' => 'DESC'], $limit);
    $zgodovina = array_merge($zgodovina, $bazaZgodovina);
    usort($zgodovina, fn($a, $b) => $b['cas'] <=> $a['cas']);
}

return array_slice($zgodovina, 0, $limit);
}

function obnovitev_statistika(): array
{
$vsaZgodovina = obnovitev_zgodovina(1000);
$uspesnih = 0;
$neuspesnih = 0;

foreach ($vsaZgodovina as $zapis) {
    if ($zapis['uspeh']) {
        $uspesnih++;
    } else {
        $neuspesnih++;
    }
}

return [
    'skupaj' => count($vsaZgodovina),
    'uspesnih' => $uspesnih,
    'neuspesnih' => $neuspesnih,
    'uspesnost' => count($vsaZgodovina) > 0 
        ? round($uspesnih / count($vsaZgodovina) * 100, 2) . '%' 
        : '0%',
    'zadnja_obnovitev' => !empty($vsaZgodovina) ? $vsaZgodovina[0]['cas'] : null
];
}

function obnovitev_varnostna_kopija(string $tip = 'baza'): array
{
$lokacija = POT_PODATKI_SKLADISCE . '/varnostne_kopije';
if (!is_dir($lokacija)) {
    mkdir($lokacija, 0755, true);
}

$imeDatoteke = $lokacija . '/backup_' . $tip . '_' . date('Ymd_His') . '.json';

try {
    if ($tip === 'baza') {
        $zbirke = ['globalno_nastavitve', 'globalno_navigacija', 'moduli', 'uporabniki', 'moduli_razglasi'];
        $podatkiZaBackup = [];
        
        foreach ($zbirke as $zbirka) {
            if (function_exists('baza_beri')) {
                $podatkiZaBackup[$zbirka] = baza_beri($zbirka);
            }
        }
        
        $podatkiZaBackup['_meta'] = [
            'verzija' => SISTEM_VERZIJA,
            'cas' => time(),
            'tip' => $tip
        ];
        
        file_put_contents($imeDatoteke, json_encode($podatkiZaBackup, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        
        obnovitev_zabelezi('varnostna_kopija', [
            'uspeh' => true,
            'tip' => $tip,
            'datoteka' => basename($imeDatoteke),
            'velikost' => filesize($imeDatoteke)
        ]);
        
        return [
            'status' => 'uspeh',
            'sporocilo' => 'Varnostna kopija ustvarjena',
            'vsebina' => [
                'datoteka' => basename($imeDatoteke),
                'velikost' => filesize($imeDatoteke),
                'cas' => time()
            ]
        ];
    }
    
    return [
        'status' => 'napaka',
        'sporocilo' => "Nepodprt tip varnostne kopije: $tip"
    ];
    
} catch (Throwable $e) {
    obnovitev_zabelezi('varnostna_kopija', [
        'uspeh' => false,
        'tip' => $tip,
        'napaka' => $e->getMessage()
    ]);
    
    return [
        'status' => 'napaka',
        'sporocilo' => 'Napaka pri ustvarjanju varnostne kopije: ' . $e->getMessage()
    ];
}
}

function obnovitev_iz_varnostne_kopije(string $datoteka): array
{
$lokacija = POT_PODATKI_SKLADISCE . '/varnostne_kopije';
$pot = $lokacija . '/' . $datoteka;

if (!file_exists($pot)) {
    return [
        'status' => 'napaka',
        'sporocilo' => 'Varnostna kopija ne obstaja: ' . $datoteka
    ];
}

$vsebina = file_get_contents($pot);
$podatki = json_decode($vsebina, true);

if ($podatki === null) {
    return [
        'status' => 'napaka',
        'sporocilo' => 'Varnostna kopija je poškodovana'
    ];
}

try {
    foreach ($podatki as $zbirka => $vrednosti) {
        if ($zbirka === '_meta') {
            continue;
        }
        
        // Počisti obstoječe podatke
        $obstojeci = baza_beri($zbirka);
        foreach ($obstojeci as $zapis) {
            baza_zbrisi($zbirka, $zapis['id']);
        }
        
        // Uvozi nove podatke
        foreach ($vrednosti as $zapis) {
            baza_zapisi($zbirka, $zapis);
        }
    }
    
    obnovitev_zabelezi('obnovitev_iz_kopije', [
        'uspeh' => true,
        'datoteka' => $datoteka,
        'meta' => $podatki['_meta'] ?? []
    ]);
    
    return [
        'status' => 'uspeh',
        'sporocilo' => 'Obnovitev iz varnostne kopije uspešna',
        'vsebina' => [
            'datoteka' => $datoteka,
            'meta' => $podatki['_meta'] ?? []
        ]
    ];
    
} catch (Throwable $e) {
    obnovitev_zabelezi('obnovitev_iz_kopije', [
        'uspeh' => false,
        'datoteka' => $datoteka,
        'napaka' => $e->getMessage()
    ]);
    
    return [
        'status' => 'napaka',
        'sporocilo' => 'Napaka pri obnovitvi: ' . $e->getMessage()
    ];
}
}

function obnovitev_varnostne_kopije_seznam(): array
{
$lokacija = POT_PODATKI_SKLADISCE . '/varnostne_kopije';
if (!is_dir($lokacija)) {
    return [];
}

$datoteke = glob($lokacija . '/backup_*.json');
$seznam = [];

foreach ($datoteke as $datoteka) {
    $stat = stat($datoteka);
    $seznam[] = [
        'ime' => basename($datoteka),
        'velikost' => $stat['size'],
        'ustvarjeno' => $stat['mtime'],
        'ustvarjeno_formatirano' => date('Y-m-d H:i:s', $stat['mtime'])
    ];
}

// Razvrsti po datumu (novejše prve)
usort($seznam, fn($a, $b) => $b['ustvarjeno'] <=> $a['ustvarjeno']);

return $seznam;
}

// API endpoint registracija
if (function_exists('api_dodaj_pot')) {
api_dodaj_pot('/api/health/recovery', function($zahteva) {
    $opcije = $zahteva['vsebina'] ?? [];
    return obnovitev_izvedi($opcije);
}, ['OBJAVA']);

api_dodaj_pot('/api/health/backup', function($zahteva) {
    $tip = $zahteva['parametri']['tip'] ?? 'baza';
    return obnovitev_varnostna_kopija($tip);
}, ['OBJAVA']);

api_dodaj_pot('/api/health/backups', function($zahteva) {
    return odziv_uspeh(obnovitev_varnostne_kopije_seznam(), 'Seznam varnostnih kopij');
}, ['DOBI']);
}

// CLI podpora
if (PHP_SAPI === 'cli') {
if (isset($argv[1]) && $argv[1] === 'recovery') {
    $rezultat = obnovitev_izvedi();
    echo json_encode($rezultat, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
}
if (isset($argv[1]) && $argv[1] === 'backup') {
    $tip = $argv[2] ?? 'baza';
    $rezultat = obnovitev_varnostna_kopija($tip);
    echo json_encode($rezultat, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
}
}