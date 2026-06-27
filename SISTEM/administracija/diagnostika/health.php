<?php
/**
 * ---------------------------------------------------------
 * POT: SISTEM/administracija/diagnostika/health.php
 * v111 (27.5.2026 10:45)
 * ---------------------------------------------------------
 * OPIS: Health check endpoint – preverjanje stanja sistema
 * ---------------------------------------------------------
 *
 * ODVISNOSTI:
 * - pot.php
 * - SISTEM/kernel/baze/upravljalec_baz.php
 *
 * UPORABA:
 * - API endpoint /api/health
 *
 * FUNKCIJE:
 * - health_preveri_vse(), health_preveri_baze()
 * - health_preveri_cache(), health_preveri_vrsto()
 * - health_preveri_module(), health_preveri_sledenje()
 * - health_preveri_sistem(), health_preveri_povezave()
 *
 * PREPOVEDI:
 * - Brez echo, print_r, var_dump (razen API izhoda)
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

function health_preveri_baze(): array
{
$rezultat = [
    'status' => 'uspeh',
    'sporocilo' => 'Baze delujejo',
    'podrobnosti' => []
];

try {
    // Preveri ali lahko beremo iz baze
    $test = baza_beri('sistem');
    $rezultat['podrobnosti']['branje'] = 'ok';
    
    // Preveri ali lahko pišemo v bazo
    $testId = baza_zapisi('sistem', ['test' => 'health_check', 'cas' => time()]);
    if ($testId) {
        baza_zbrisi('sistem', $testId);
        $rezultat['podrobnosti']['pisanje'] = 'ok';
    }
    
    $rezultat['podrobnosti']['cas'] = time();
    
} catch (Throwable $e) {
    $rezultat['status'] = 'napaka';
    $rezultat['sporocilo'] = 'Napaka pri dostopu do baz: ' . $e->getMessage();
    $rezultat['podrobnosti']['napaka'] = $e->getMessage();
    $rezultat['podrobnosti']['izjema'] = get_class($e);
}

return $rezultat;
}

function health_preveri_cache(): array
{
$rezultat = [
    'status' => 'uspeh',
    'sporocilo' => 'Cache deluje',
    'podrobnosti' => []
];

try {
    $testKljuc = 'health_test_' . time();
    $testVrednost = ['test' => true, 'cas' => time()];
    
    // Shrani v cache
    $shranjeno = cache_shrani($testKljuc, $testVrednost, 60);
    if (!$shranjeno) {
        throw new Exception('Ne morem shraniti v cache');
    }
    $rezultat['podrobnosti']['shranjevanje'] = 'ok';
    
    // Preberi iz cache
    $prebrano = cache_preberi($testKljuc);
    if ($prebrano === null) {
        throw new Exception('Ne morem prebrati iz cache');
    }
    $rezultat['podrobnosti']['branje'] = 'ok';
    
    // Izbriši
    cache_zbrisi($testKljuc);
    $rezultat['podrobnosti']['brisanje'] = 'ok';
    
} catch (Throwable $e) {
    $rezultat['status'] = 'opozorilo';
    $rezultat['sporocilo'] = 'Cache ima težave: ' . $e->getMessage();
    $rezultat['podrobnosti']['napaka'] = $e->getMessage();
}

return $rezultat;
}

function health_preveri_vrsto(): array
{
$rezultat = [
    'status' => 'uspeh',
    'sporocilo' => 'Čakalna vrsta deluje',
    'podrobnosti' => []
];

try {
    // Preveri število paketov v različnih vrstah
    $vrste = ['sprotno', 'visoka_prednost', 'obicajna_prednost', 'nizka_prednost'];
    foreach ($vrste as $vrsta) {
        $stevilo = queue_stevilo($vrsta);
        $rezultat['podrobnosti'][$vrsta] = $stevilo;
    }
    
    // Preveri ali lahko dodamo paket
    $testId = queue_dodaj([
        'akcija' => 'health_check',
        'podatki' => ['test' => true, 'cas' => time()]
    ], 'sprotno');
    
    if ($testId) {
        $rezultat['podrobnosti']['dodajanje'] = 'ok';
        // Počisti testni paket
        $paket = queue_vzemi('sprotno');
        if ($paket && $paket['akcija'] === 'health_check') {
            queue_potrdi('sprotno', $paket['id']);
        }
    }
    
} catch (Throwable $e) {
    $rezultat['status'] = 'opozorilo';
    $rezultat['sporocilo'] = 'Čakalna vrsta ima težave: ' . $e->getMessage();
    $rezultat['podrobnosti']['napaka'] = $e->getMessage();
}

return $rezultat;
}

function health_preveri_module(): array
{
$rezultat = [
    'status' => 'uspeh',
    'sporocilo' => 'Moduli delujejo',
    'podrobnosti' => []
];

try {
    $vsiModuli = modul_peskovnik_vsi();
    $rezultat['podrobnosti']['skupaj'] = count($vsiModuli);
    
    $aktivni = 0;
    $poKategorijah = [];
    foreach ($vsiModuli as $ime => $modul) {
        if ($modul->jeAktiviran()) {
            $aktivni++;
        }
        $kategorija = $modul->kategorija();
        if (!isset($poKategorijah[$kategorija])) {
            $poKategorijah[$kategorija] = 0;
        }
        $poKategorijah[$kategorija]++;
    }
    $rezultat['podrobnosti']['aktivni'] = $aktivni;
    $rezultat['podrobnosti']['po_kategorijah'] = $poKategorijah;
    
} catch (Throwable $e) {
    $rezultat['status'] = 'opozorilo';
    $rezultat['sporocilo'] = 'Moduli imajo težave: ' . $e->getMessage();
    $rezultat['podrobnosti']['napaka'] = $e->getMessage();
}

return $rezultat;
}

function health_preveri_sledenje(): array
{
$rezultat = [
    'status' => 'uspeh',
    'sporocilo' => 'Sledenje deluje',
    'podrobnosti' => []
];

try {
    // Ustvari testno sled
    $testId = sled_zacni('health', 'test_check', ['test' => true]);
    if ($testId) {
        $rezultat['podrobnosti']['ustvarjanje'] = 'ok';
        sled_koncaj($testId, ['uspeh' => true]);
        $rezultat['podrobnosti']['zakljucek'] = 'ok';
    }
    
    // Preveri statistiko sledenja
    $statistika = sled_statistika();
    $rezultat['podrobnosti']['skupaj_sledi'] = $statistika['skupaj'] ?? 0;
    $rezultat['podrobnosti']['aktivnih_sledi'] = $statistika['aktivnih'] ?? 0;
    
} catch (Throwable $e) {
    $rezultat['status'] = 'opozorilo';
    $rezultat['sporocilo'] = 'Sledenje ima težave: ' . $e->getMessage();
    $rezultat['podrobnosti']['napaka'] = $e->getMessage();
}

return $rezultat;
}

function health_preveri_sistem(): array
{
$rezultat = [
    'status' => 'uspeh',
    'sporocilo' => 'Sistem deluje',
    'podrobnosti' => []
];

// Preveri kritične konstante
$konstante = ['ROOT', 'POT_KOREN', 'SISTEM_VERZIJA', 'IME_APLIKACIJE'];
foreach ($konstante as $konstanta) {
    if (!defined($konstanta)) {
        $rezultat['status'] = 'napaka';
        $rezultat['sporocilo'] = "Manjka kritična konstanta: $konstanta";
        return $rezultat;
    }
}

// Preveri ključne datoteke
$datoteke = [
    'pot.php' => ROOT . '/pot.php',
    'index.php' => ROOT . '/index.php',
    'adapter.php' => POT_ADAPTER . '/adapter.php'
];

foreach ($datoteke as $ime => $pot) {
    if (!file_exists($pot)) {
        $rezultat['status'] = 'napaka';
        $rezultat['sporocilo'] = "Manjka datoteka: $ime";
        return $rezultat;
    }
}

// Preveri ključne mape
$mape = [
    'PODATKI' => POT_PODATKI,
    'CACHE' => POT_PODATKI_CACHE,
    'LOG' => POT_PODATKI_LOG
];

foreach ($mape as $ime => $pot) {
    if (!is_dir($pot)) {
        $rezultat['status'] = 'napaka';
        $rezultat['sporocilo'] = "Manjka mapa: $ime";
        return $rezultat;
    }
    if (!is_writable($pot)) {
        $rezultat['status'] = 'opozorilo';
        $rezultat['sporocilo'] = "Mapa ni pisljiva: $ime";
    }
}

return $rezultat;
}

function health_preveri_povezave(): array
{
$rezultat = [
    'status' => 'uspeh',
    'sporocilo' => 'Povezave delujejo',
    'podrobnosti' => []
];

// Preveri povezavo z bazo (če je MySQL)
if (function_exists('mysqli_connect') && defined('DB_HOST')) {
    try {
        $mysqli = @new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($mysqli->connect_error) {
            $rezultat['status'] = 'opozorilo';
            $rezultat['sporocilo'] = 'Povezava z MySQL bazo ni uspela';
            $rezultat['podrobnosti']['mysql'] = $mysqli->connect_error;
        } else {
            $rezultat['podrobnosti']['mysql'] = 'ok';
            $mysqli->close();
        }
    } catch (Throwable $e) {
        $rezultat['podrobnosti']['mysql'] = $e->getMessage();
    }
}

return $rezultat;
}

function health_preveri_vse(): array
{
$zacetek = microtime(true);

$preveritve = [
    'sistem' => health_preveri_sistem(),
    'baze' => health_preveri_baze(),
    'cache' => health_preveri_cache(),
    'vrsta' => health_preveri_vrsto(),
    'moduli' => health_preveri_module(),
    'sledenje' => health_preveri_sledenje(),
    'povezave' => health_preveri_povezave()
];

// Določi skupni status
$skupniStatus = 'uspeh';
$napake = [];

foreach ($preveritve as $ime => $preveritev) {
    if ($preveritev['status'] === 'napaka') {
        $skupniStatus = 'napaka';
        $napake[] = $ime . ': ' . $preveritev['sporocilo'];
    } elseif ($preveritev['status'] === 'opozorilo' && $skupniStatus !== 'napaka') {
        $skupniStatus = 'opozorilo';
    }
}

$trajanje = round((microtime(true) - $zacetek) * 1000, 2);
$koda = $skupniStatus === 'napaka' ? 503 : ($skupniStatus === 'opozorilo' ? 200 : 200);

return [
    'status' => $skupniStatus,
    'status_koda' => $koda,
    'sporocilo' => $skupniStatus === 'napaka' ? implode('; ', $napake) : 'Health check zaključen',
    'vsebina' => [
        'preveritve' => $preveritve,
        'trajanje_ms' => $trajanje,
        'cas' => time(),
        'cas_formatiran' => date('Y-m-d H:i:s'),
        'verzija' => SISTEM_VERZIJA,
        'okolje' => RAZVOJNI_NACIN ? 'razvoj' : 'produkcija'
    ]
];
}

// API endpoint registracija
if (function_exists('api_dodaj_pot')) {
api_dodaj_pot('/api/health', function($zahteva) {
    return health_preveri_vse();
}, ['DOBI']);
}

// CLI podpora
if (PHP_SAPI === 'cli' && (!isset($argv[1]) || $argv[1] === 'health')) {
$rezultat = health_preveri_vse();
echo json_encode($rezultat, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
}