<?php
/**
 * ---------------------------------------------------------
 * POT: SISTEM/administracija/avtomatika/cron.php
 * v111 (27.5.2026 07:30)
 * ---------------------------------------------------------
 * OPIS: Cron jobovi – časovno načrtovane naloge
 * ---------------------------------------------------------
 *
 * ODVISNOSTI:
 * - pot.php
 *
 * UPORABA:
 * - cli.php (php cli.php cron)
 *
 * FUNKCIJE:
 * - cron_registriraj(), cron_parsiraj(), cron_ujema()
 * - cron_je_za_izvest(), cron_izvedi_dozorele()
 * - cron_zagon(), cron_statistika()
 *
 * PREPOVEDI:
 * - Brez echo, print_r, var_dump (razen CLI izpisa)
 *
 * STATUS: Stabilno
 *
 * ZGODOVINA:
 * - v111: FAZA 8 – implementacija
 *
 * ---------------------------------------------------------
 * AVTOR: AstraMentalica Mojster
 * ---------------------------------------------------------
 */

declare(strict_types=1);

$GLOBALS['CRON_JOBOVI'] = [];
$GLOBALS['CRON_ZGODOVINA'] = [];

function cron_parsiraj(string $izraz): array
{
$deli = explode(' ', $izraz);
if (count($deli) !== 5) {
    return [];
}

return [
    'minuta' => $deli[0],
    'ura' => $deli[1],
    'dan' => $deli[2],
    'mesec' => $deli[3],
    'teden' => $deli[4]
];
}

function cron_ujema(string $vzorec, int $vrednost): bool
{
if ($vzorec === '*') {
    return true;
}

if (strpos($vzorec, '/') !== false) {
    $del = explode('/', $vzorec);
    $delitelj = (int)$del[1];
    return $vrednost % $delitelj === 0;
}

if (strpos($vzorec, '-') !== false) {
    $meje = explode('-', $vzorec);
    return $vrednost >= (int)$meje[0] && $vrednost <= (int)$meje[1];
}

if (strpos($vzorec, ',') !== false) {
    $vrednosti = explode(',', $vzorec);
    return in_array($vrednost, array_map('intval', $vrednosti));
}

return (int)$vzorec === $vrednost;
}

function cron_je_za_izvest(array $job, int $cas): bool
{
$podatki = getdate($cas);

$minuta = $podatki['minutes'];
$ura = $podatki['hours'];
$dan = $podatki['mday'];
$mesec = $podatki['mon'];
$teden = $podatki['wday'];

$vzorec = cron_parsiraj($job['izraz']);
if (empty($vzorec)) {
    return false;
}

return cron_ujema($vzorec['minuta'], $minuta) &&
       cron_ujema($vzorec['ura'], $ura) &&
       cron_ujema($vzorec['dan'], $dan) &&
       cron_ujema($vzorec['mesec'], $mesec) &&
       cron_ujema($vzorec['teden'], $teden);
}

function cron_registriraj(string $ime, string $izraz, callable $callback, array $parametri = []): void
{
$GLOBALS['CRON_JOBOVI'][$ime] = [
    'ime' => $ime,
    'izraz' => $izraz,
    'callback' => $callback,
    'parametri' => $parametri,
    'zadnji_zagon' => null,
    'zadnji_uspeh' => null
];
}

function cron_izvedi_dozorele(int $zdaj = null): array
{
if ($zdaj === null) {
    $zdaj = time();
}

$izvedeni = [];

foreach ($GLOBALS['CRON_JOBOVI'] as $ime => &$job) {
    if (cron_je_za_izvest($job, $zdaj)) {
        $zadnjiZagon = $job['zadnji_zagon'] ?? 0;
        
        // Prepreči večkratno izvedbo v isti minuti
        if ($zdaj - $zadnjiZagon >= 60) {
            $start = microtime(true);
            try {
                $callback = $job['callback'];
                $callback($job['parametri']);
                $uspeh = true;
            } catch (Throwable $e) {
                $uspeh = false;
                error_log("[CRON] Napaka pri izvajanju joba '$ime': " . $e->getMessage());
            }
            
            $trajanje = round((microtime(true) - $start) * 1000, 2);
            $job['zadnji_zagon'] = $zdaj;
            if ($uspeh) {
                $job['zadnji_uspeh'] = $zdaj;
            }
            
            $izvedeni[] = $ime;
            
            // Zabeleži zgodovino
            $GLOBALS['CRON_ZGODOVINA'][] = [
                'ime' => $ime,
                'cas' => $zdaj,
                'uspeh' => $uspeh,
                'trajanje_ms' => $trajanje
            ];
            
            // Omeji zgodovino na 1000 zapisov
            if (count($GLOBALS['CRON_ZGODOVINA']) > 1000) {
                array_shift($GLOBALS['CRON_ZGODOVINA']);
            }
        }
    }
}

return $izvedeni;
}

function cron_statistika(): array
{
$stat = [
    'skupaj_jobov' => count($GLOBALS['CRON_JOBOVI']),
    'zadnji_zagon' => null,
    'zadnji_uspeh' => null,
    'zgodovina' => array_slice($GLOBALS['CRON_ZGODOVINA'], -20)
];

foreach ($GLOBALS['CRON_JOBOVI'] as $ime => $job) {
    if ($job['zadnji_zagon'] && ($stat['zadnji_zagon'] === null || $job['zadnji_zagon'] > $stat['zadnji_zagon'])) {
        $stat['zadnji_zagon'] = $job['zadnji_zagon'];
    }
    if ($job['zadnji_uspeh'] && ($stat['zadnji_uspeh'] === null || $job['zadnji_uspeh'] > $stat['zadnji_uspeh'])) {
        $stat['zadnji_uspeh'] = $job['zadnji_uspeh'];
    }
}

return $stat;
}

function cron_zagon(): void
{
// Registriraj privzete cron jobove
cron_registriraj('cleanup_cache', '0 */6 * * *', function() {
    if (function_exists('cleanup_cache')) {
        $stevilo = cleanup_cache();
        dnevnik_info("Očiščenih $stevilo cache datotek");
    }
});

cron_registriraj('cleanup_logs', '0 0 * * 0', function() {
    if (function_exists('cleanup_logs')) {
        $stevilo = cleanup_logs();
        dnevnik_info("Očiščenih $stevilo log datotek");
    }
});

cron_registriraj('cleanup_temp', '0 */12 * * *', function() {
    if (function_exists('cleanup_temp')) {
        $stevilo = cleanup_temp();
        dnevnik_info("Očiščenih $stevilo temp datotek");
    }
});

cron_registriraj('sync_modules', '*/30 * * * *', function() {
    if (function_exists('moduli_sinhroniziraj_z_registrom')) {
        moduli_sinhroniziraj_z_registrom();
        dnevnik_info("Moduli sinhronizirani z registrom");
    }
});

cron_registriraj('queue_worker', '* * * * *', function() {
    if (function_exists('vrsta_odprava_obdelaj')) {
        $stevilo = vrsta_odprava_obdelaj('obicajna_prednost', 10);
        if ($stevilo > 0) {
            dnevnik_info("Obdelanih $stevilo paketov iz vrste");
        }
    }
}, ['vrsta' => 'obicajna_prednost']);

cron_registriraj('health_check', '*/5 * * * *', function() {
    if (function_exists('health_preveri_vse')) {
        $rezultat = health_preveri_vse();
        if ($rezultat['status'] !== 'uspeh') {
            dnevnik_opozorilo("Health check opozorilo: " . $rezultat['sporocilo']);
        }
    }
});

// Izvedi dozorele jobove
$izvedeni = cron_izvedi_dozorele();

if (PHP_SAPI === 'cli' && !empty($izvedeni)) {
    echo "Izvedeni cron jobovi: " . implode(', ', $izvedeni) . "\n";
}
}