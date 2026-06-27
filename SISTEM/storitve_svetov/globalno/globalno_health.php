<?php
/**
 * ---------------------------------------------------------
 * POT: SISTEM/storitve_svetov/globalno/globalno_health.php
 * v111 (27.5.2026 17:00)
 * ---------------------------------------------------------
 * OPIS: Health check helperji
 * ---------------------------------------------------------
 *
 * ODVISNOSTI:
 * - pot.php
 *
 * UPORABA:
 * - SISTEM/administracija/diagnostika/health.php
 *
 * FUNKCIJE:
 * - globalno_health_preveri(), globalno_health_status()
 * - globalno_health_alarm(), globalno_health_povzetek()
 *
 * PREPOVEDI:
 * - Brez echo, print_r, var_dump
 *
 * STATUS: Stabilno
 *
 * ZGODOVINA:
 * - v111: FAZA 48 – implementacija
 *
 * ---------------------------------------------------------
 * AVTOR: AstraMentalica Mojster
 * ---------------------------------------------------------
 */

declare(strict_types=1);

$GLOBALS['GLOBALNO_HEALTH_STATUS'] = [];

function globalno_health_preveri(string $komponenta): array
{
$rezultat = [
    'komponenta' => $komponenta,
    'status' => 'uspeh',
    'sporocilo' => '',
    'podrobnosti' => [],
    'cas' => time()
];

try {
    switch ($komponenta) {
        case 'baza':
            // Preveri bazo
            if (function_exists('baza_beri')) {
                $test = baza_beri('sistem', 1);
                $rezultat['podrobnosti']['branje'] = 'ok';
            } else {
                throw new RuntimeException('Baza ni dostopna');
            }
            break;
            
        case 'cache':
            // Preveri cache
            if (function_exists('cache_preberi')) {
                $testKljuc = 'health_test_' . time();
                cache_shrani($testKljuc, 'test', 10);
                $prebrano = cache_preberi($testKljuc);
                if ($prebrano !== 'test') {
                    throw new RuntimeException('Cache ne deluje');
                }
                cache_zbrisi($testKljuc);
                $rezultat['podrobnosti'] = 'ok';
            } else {
                throw new RuntimeException('Cache ni na voljo');
            }
            break;
            
        case 'vrsta':
            // Preveri čakalno vrsto
            if (function_exists('queue_dodaj')) {
                $testId = queue_dodaj(['akcija' => 'health_test'], 'sprotno');
                $rezultat['podrobnosti']['dodajanje'] = 'ok';
            } else {
                throw new RuntimeException('Čakalna vrsta ne deluje');
            }
            break;
            
        case 'moduli':
            // Preveri module
            if (function_exists('modul_peskovnik_vsi')) {
                $moduli = modul_peskovnik_vsi();
                $rezultat['podrobnosti']['stevilo'] = count($moduli);
            } else {
                throw new RuntimeException('Modul sistem ne deluje');
            }
            break;
            
        default:
            throw new RuntimeException("Neznana komponenta: $komponenta");
    }
    
    $rezultat['status'] = 'uspeh';
    
} catch (Throwable $e) {
    $rezultat['status'] = 'napaka';
    $rezultat['sporocilo'] = $e->getMessage();
}

$GLOBALS['GLOBALNO_HEALTH_STATUS'][$komponenta] = $rezultat;

return $rezultat;
}

function globalno_health_status(): array
{
$status = [
    'sistem' => [
        'verzija' => SISTEM_VERZIJA,
        'okolje' => RAZVOJNI_NACIN ? 'razvoj' : 'produkcija',
        'php_verzija' => PHP_VERSION
    ],
    'komponente' => $GLOBALS['GLOBALNO_HEALTH_STATUS'],
    'skupni_status' => 'uspeh',
    'cas' => time()
];

foreach ($GLOBALS['GLOBALNO_HEALTH_STATUS'] as $komponenta) {
    if ($komponenta['status'] === 'napaka') {
        $status['skupni_status'] = 'napaka';
        break;
    }
}

return $status;
}

function globalno_health_alarm(string $komponenta, string $sporocilo, string $raven = 'opozorilo'): void
{
if (function_exists('dnevnik_' . $raven)) {
    call_user_func('dnevnik_' . $raven, "[Health] $komponenta: $sporocilo");
}

// Pošlji notifikacijo adminu
if (function_exists('async_poslji')) {
    async_poslji('notifikacija', [
        'uporabnik_id' => 1,
        'naslov' => "Health alarm: $komponenta",
        'sporocilo' => $sporocilo,
        'tip' => $raven
    ], 'visoka_prednost');
}
}

function globalno_health_povzetek(): array
{
$povzetek = [
    'skupaj_komponent' => count($GLOBALS['GLOBALNO_HEALTH_STATUS']),
    'delujocih' => 0,
    'ne_delujocih' => 0,
    'zadnje_preverjanje' => 0
];

foreach ($GLOBALS['GLOBALNO_HEALTH_STATUS'] as $komponenta) {
    if ($komponenta['status'] === 'uspeh') {
        $povzetek['delujocih']++;
    } else {
        $povzetek['ne_delujocih']++;
    }
    
    if ($komponenta['cas'] > $povzetek['zadnje_preverjanje']) {
        $povzetek['zadnje_preverjanje'] = $komponenta['cas'];
    }
}

return $povzetek;
}

function globalno_health_preveri_vse(): array
{
$komponente = ['baza', 'cache', 'vrsta', 'moduli'];
$rezultati = [];

foreach ($komponente as $komponenta) {
    $rezultati[$komponenta] = globalno_health_preveri($komponenta);
}

return $rezultati;
}