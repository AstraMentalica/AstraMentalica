<?php
/**
 * ---------------------------------------------------------
 * POT: SISTEM/administracija/avtomatika/razpored.php
 * v111 (27.5.2026 07:30)
 * ---------------------------------------------------------
 * OPIS: Razporejevalnik opravil (scheduler)
 * ---------------------------------------------------------
 *
 * ODVISNOSTI:
 * - pot.php
 *
 * UPORABA:
 * - SISTEM/administracija/avtomatika/obdelava.php
 *
 * FUNKCIJE:
 * - razpored_dodaj(), razpored_izvedi(), razpored_vsi()
 * - razpored_odstrani(), razpored_statistika()
 *
 * PREPOVEDI:
 * - Brez echo, print_r, var_dump
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

$GLOBALS['RAZPORED_OPRAVIL'] = [];
$GLOBALS['RAZPORED_ZGODOVINA'] = [];

function razpored_parsiraj(string $izraz): array
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

function razpored_ujema(string $vzorec, int $vrednost): bool
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

function razpored_dodaj(string $ime, string $izraz, callable $opravilo, array $parametri = []): void
{
$GLOBALS['RAZPORED_OPRAVIL'][$ime] = [
    'ime' => $ime,
    'izraz' => $izraz,
    'opravilo' => $opravilo,
    'parametri' => $parametri,
    'zadnji_zagon' => null,
    'zadnji_uspeh' => null
];
}

function razpored_odstrani(string $ime): bool
{
if (isset($GLOBALS['RAZPORED_OPRAVIL'][$ime])) {
    unset($GLOBALS['RAZPORED_OPRAVIL'][$ime]);
    return true;
}
return false;
}

function razpored_izvedi(int $zdaj = null): array
{
if ($zdaj === null) {
    $zdaj = time();
}

$podatki = getdate($zdaj);
$minuta = $podatki['minutes'];
$ura = $podatki['hours'];
$dan = $podatki['mday'];
$mesec = $podatki['mon'];
$teden = $podatki['wday'];

$izvedena = [];

foreach ($GLOBALS['RAZPORED_OPRAVIL'] as $ime => &$opravilo) {
    $vzorec = razpored_parsiraj($opravilo['izraz']);
    if (empty($vzorec)) {
        continue;
    }
    
    $zaIzvest = razpored_ujema($vzorec['minuta'], $minuta) &&
                razpored_ujema($vzorec['ura'], $ura) &&
                razpored_ujema($vzorec['dan'], $dan) &&
                razpored_ujema($vzorec['mesec'], $mesec) &&
                razpored_ujema($vzorec['teden'], $teden);
    
    if ($zaIzvest) {
        $zadnjiZagon = $opravilo['zadnji_zagon'] ?? 0;
        if ($zdaj - $zadnjiZagon >= 60) {
            $start = microtime(true);
            try {
                $callback = $opravilo['opravilo'];
                $callback($opravilo['parametri']);
                $uspeh = true;
                $opravilo['zadnji_uspeh'] = $zdaj;
            } catch (Throwable $e) {
                $uspeh = false;
                error_log("[RAZPORED] Napaka pri opravilu '$ime': " . $e->getMessage());
            }
            
            $trajanje = round((microtime(true) - $start) * 1000, 2);
            $opravilo['zadnji_zagon'] = $zdaj;
            $izvedena[] = $ime;
            
            // Zabeleži zgodovino
            $GLOBALS['RAZPORED_ZGODOVINA'][] = [
                'ime' => $ime,
                'cas' => $zdaj,
                'uspeh' => $uspeh,
                'trajanje_ms' => $trajanje
            ];
            
            if (count($GLOBALS['RAZPORED_ZGODOVINA']) > 500) {
                array_shift($GLOBALS['RAZPORED_ZGODOVINA']);
            }
        }
    }
}

return $izvedena;
}

function razpored_vsi(): array
{
return $GLOBALS['RAZPORED_OPRAVIL'];
}

function razpored_statistika(): array
{
$stat = [
    'skupaj' => count($GLOBALS['RAZPORED_OPRAVIL']),
    'zadnji_zagon' => null,
    'zadnji_uspeh' => null,
    'zgodovina' => array_slice($GLOBALS['RAZPORED_ZGODOVINA'], -20)
];

foreach ($GLOBALS['RAZPORED_OPRAVIL'] as $ime => $opravilo) {
    if ($opravilo['zadnji_zagon'] && ($stat['zadnji_zagon'] === null || $opravilo['zadnji_zagon'] > $stat['zadnji_zagon'])) {
        $stat['zadnji_zagon'] = $opravilo['zadnji_zagon'];
    }
    if ($opravilo['zadnji_uspeh'] && ($stat['zadnji_uspeh'] === null || $opravilo['zadnji_uspeh'] > $stat['zadnji_uspeh'])) {
        $stat['zadnji_uspeh'] = $opravilo['zadnji_uspeh'];
    }
}

return $stat;
}

// Registracija privzetih razporejenih opravil
function razpored_registriraj_privzeta(): void
{
razpored_dodaj('dnevna_analiza', '0 0 * * *', function() {
    if (function_exists('cleanup_izvedi')) {
        $rezultat = cleanup_izvedi(['brez_cache' => true]);
        dnevnik_info("Dnevna analiza: " . $rezultat['sporocilo']);
    }
});

razpored_dodaj('tedenska_varnostna_kopija', '0 0 * * 0', function() {
    if (function_exists('opravilo_varnostna_kopija')) {
        opravilo_varnostna_kopija(['tip' => 'baza']);
    }
});
}