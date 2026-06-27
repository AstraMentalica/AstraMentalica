php
<?php
/**
 * ---------------------------------------------------------
 * POT: SISTEM/administracija/diagnostika/monitoring.php
 * v111 (27.5.2026 10:45)
 * ---------------------------------------------------------
 * OPIS: Monitoring – zbiranje metrik in statistike
 * ---------------------------------------------------------
 *
 * ODVISNOSTI:
 * - pot.php
 *
 * UPORABA:
 * - SISTEM/administracija/diagnostika/health.php
 *
 * FUNKCIJE:
 * - monitoring_metric_shrani(), monitoring_metric_pridobi()
 * - monitoring_statistika(), monitoring_metric_povprecje()
 * - monitoring_sistem_metrike(), monitoring_metric_cisti()
 * - monitoring_alarm_preveri(), monitoring_alarm_dodaj()
 *
 * PREPOVEDI:
 * - Brez echo, print_r, var_dump
 *
 * STATUS: Stabilno
 *
 * ZGODOVINA:
 * - v111: FAZA 20 – razširjena implementacija
 *
 * ---------------------------------------------------------
 * AVTOR: AstraMentalica Mojster
 * ---------------------------------------------------------
 */

declare(strict_types=1);

$GLOBALS['MONITORING_METRIKE'] = [];
$GLOBALS['MONITORING_ALARMI'] = [];

function monitoring_metric_shrani(string $ime, $vrednost, array $oznake = []): void
{
$cas = time();
$metrika = [
    'id' => uniqid('met_', true),
    'ime' => $ime,
    'vrednost' => $vrednost,
    'oznake' => $oznake,
    'cas' => $cas,
    'cas_formatiran' => date('Y-m-d H:i:s', $cas)
];

$GLOBALS['MONITORING_METRIKE'][] = $metrika;

// Shrani v bazo za trajnost
if (function_exists('baza_zapisi')) {
    baza_zapisi('monitoring_metrike', $metrika);
}

// Omeji pomnilnik
if (count($GLOBALS['MONITORING_METRIKE']) > 10000) {
    array_shift($GLOBALS['MONITORING_METRIKE']);
}

// Preveri alarme
monitoring_alarm_preveri($ime, $vrednost, $oznake);
}

function monitoring_metric_pridobi(string $ime, int $casOd = 0, int $casDo = 0, ?string $oznaka = null): array
{
$rezultat = [];

foreach ($GLOBALS['MONITORING_METRIKE'] as $metrika) {
    if ($metrika['ime'] !== $ime) {
        continue;
    }
    
    if ($casOd > 0 && $metrika['cas'] < $casOd) {
        continue;
    }
    
    if ($casDo > 0 && $metrika['cas'] > $casDo) {
        continue;
    }
    
    if ($oznaka !== null && !in_array($oznaka, $metrika['oznake'])) {
        continue;
    }
    
    $rezultat[] = $metrika;
}

return $rezultat;
}

function monitoring_statistika(string $ime, int $casOd = 0, int $casDo = 0, ?string $oznaka = null): array
{
$metrike = monitoring_metric_pridobi($ime, $casOd, $casDo, $oznaka);

if (empty($metrike)) {
    return [
        'ime' => $ime,
        'stevilo' => 0,
        'min' => null,
        'max' => null,
        'povprecje' => null,
        'zadnja' => null,
        'prva' => null
    ];
}

$vrednosti = array_column($metrike, 'vrednost');
$numericVrednosti = array_filter($vrednosti, 'is_numeric');

return [
    'ime' => $ime,
    'stevilo' => count($metrike),
    'min' => !empty($numericVrednosti) ? min($numericVrednosti) : null,
    'max' => !empty($numericVrednosti) ? max($numericVrednosti) : null,
    'povprecje' => !empty($numericVrednosti) ? round(array_sum($numericVrednosti) / count($numericVrednosti), 2) : null,
    'zadnja' => end($metrike),
    'prva' => reset($metrike)
];
}

function monitoring_metric_povprecje(string $ime, int $casOd = 0, int $casDo = 0): ?float
{
$stat = monitoring_statistika($ime, $casOd, $casDo);
return $stat['povprecje'];
}

function monitoring_metric_cisti(int $starejseOd = 86400): int
{
$izbrisanih = 0;
$meja = time() - $starejseOd;

// Počisti pomnilnik
$noveMetrike = [];
foreach ($GLOBALS['MONITORING_METRIKE'] as $metrika) {
    if ($metrika['cas'] > $meja) {
        $noveMetrike[] = $metrika;
    } else {
        $izbrisanih++;
    }
}
$GLOBALS['MONITORING_METRIKE'] = $noveMetrike;

// Počisti bazo
if (function_exists('baza_beri')) {
    $stareMetrike = baza_beri('monitoring_metrike');
    foreach ($stareMetrike as $metrika) {
        if ($metrika['cas'] < $meja) {
            baza_zbrisi('monitoring_metrike', $metrika['id']);
            $izbrisanih++;
        }
    }
}

return $izbrisanih;
}

function monitoring_sistem_metrike(): void
{
// Poraba pomnilnika
monitoring_metric_shrani('sistem.pomnilnik.mb', round(memory_get_usage() / 1024 / 1024, 2), [
    'tip' => 'trenutni'
]);
monitoring_metric_shrani('sistem.pomnilnik.vrhunec.mb', round(memory_get_peak_usage() / 1024 / 1024, 2), [
    'tip' => 'vrhunec'
]);

// Število zahtev
if (function_exists('pogon') && pogon()->runtime()->tece()) {
    $stat = pogon()->runtime()->statistika();
    monitoring_metric_shrani('sistem.stevilo_zahtev', $stat['stevilo_zahtev'] ?? 0, [
        'tip' => 'skupaj'
    ]);
    monitoring_metric_shrani('sistem.stevilo_napak', $stat['stevilo_napak'] ?? 0, [
        'tip' => 'skupaj'
    ]);
}

// Število aktivnih modulov
if (function_exists('modul_peskovnik_vsi')) {
    $moduli = modul_peskovnik_vsi();
    $aktivni = 0;
    foreach ($moduli as $modul) {
        if ($modul->jeAktiviran()) {
            $aktivni++;
        }
    }
    monitoring_metric_shrani('sistem.aktivni_moduli', $aktivni);
}

// Število paketov v vrsti
if (function_exists('queue_stevilo')) {
    $vrste = ['sprotno', 'visoka_prednost', 'obicajna_prednost', 'nizka_prednost'];
    foreach ($vrste as $vrsta) {
        monitoring_metric_shrani("sistem.vrsta.$vrsta", queue_stevilo($vrsta));
    }
}

// Obremenitev sistema (če je CLI)
if (function_exists('sys_getloadavg')) {
    $load = sys_getloadavg();
    monitoring_metric_shrani('sistem.obremenitev.1min', $load[0] ?? 0);
    monitoring_metric_shrani('sistem.obremenitev.5min', $load[1] ?? 0);
    monitoring_metric_shrani('sistem.obremenitev.15min', $load[2] ?? 0);
}

// Čas izvajanja
if (defined('ADAPTER_ZACETEK')) {
    $trajanje = round((microtime(true) - ADAPTER_ZACETEK) * 1000, 2);
    monitoring_metric_shrani('sistem.trajanje_zahteve.ms', $trajanje);
}
}

function monitoring_alarm_dodaj(string $ime, string $pogoj, $meja, callable $akcija): void
{
$GLOBALS['MONITORING_ALARMI'][$ime] = [
    'ime' => $ime,
    'pogoj' => $pogoj,
    'meja' => $meja,
    'akcija' => $akcija,
    'zadnji_sprozen' => null,
    'stevilo_sprozenih' => 0
];
}

function monitoring_alarm_preveri(string $ime, $vrednost, array $oznake = []): void
{
foreach ($GLOBALS['MONITORING_ALARMI'] as $alarmIme => $alarm) {
    if ($alarmIme !== $ime && strpos($ime, $alarmIme) !== 0) {
        continue;
    }
    
    $sprozi = false;
    
    switch ($alarm['pogoj']) {
        case '>':
            $sprozi = $vrednost > $alarm['meja'];
            break;
        case '<':
            $sprozi = $vrednost < $alarm['meja'];
            break;
        case '>=':
            $sprozi = $vrednost >= $alarm['meja'];
            break;
        case '<=':
            $sprozi = $vrednost <= $alarm['meja'];
            break;
        case '==':
            $sprozi = $vrednost == $alarm['meja'];
            break;
    }
    
    if ($sprozi) {
        $alarm['zadnji_sprozen'] = time();
        $alarm['stevilo_sprozenih']++;
        $GLOBALS['MONITORING_ALARMI'][$alarmIme] = $alarm;
        
        try {
            $alarm['akcija']($ime, $vrednost, $oznake);
        } catch (Throwable $e) {
            if (function_exists('dnevnik_napaka')) {
                dnevnik_napaka("Napaka pri alarmu '$alarmIme': " . $e->getMessage());
            }
        }
    }
}
}

function monitoring_alarmi(): array
{
return $GLOBALS['MONITORING_ALARMI'];
}

// Registracija privzetih alarmov
function monitoring_registriraj_privzete_alarme(): void
{
monitoring_alarm_dodaj('sistem.pomnilnik.mb', '>', 200, function($ime, $vrednost, $oznake) {
    if (function_exists('dnevnik_opozorilo')) {
        dnevnik_opozorilo("Visoka poraba pomnilnika: {$vrednost}MB", ['ime' => $ime, 'vrednost' => $vrednost]);
    }
    if (function_exists('async_poslji')) {
        async_poslji('notifikacija', [
            'uporabnik_id' => 1,
            'sporocilo' => "Opozorilo: Visoka poraba pomnilnika ({$vrednost}MB)",
            'tip' => 'opozorilo'
        ], 'visoka_prednost');
    }
});

monitoring_alarm_dodaj('sistem.stevilo_napak', '>', 100, function($ime, $vrednost, $oznake) {
    if (function_exists('dnevnik_opozorilo')) {
        dnevnik_opozorilo("Visoko število napak: {$vrednost}", ['ime' => $ime, 'vrednost' => $vrednost]);
    }
});
}

// Registracija privzetih alarmov ob zagonu
if (function_exists('dogodek_poslusaj')) {
dogodek_poslusaj('sistem.zagon', 'monitoring_registriraj_privzete_alarme', 50);
}