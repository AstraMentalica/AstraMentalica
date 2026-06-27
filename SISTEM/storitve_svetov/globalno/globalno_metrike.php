<?php
/**
 * ---------------------------------------------------------
 * POT: SISTEM/storitve_svetov/globalno/globalno_metrike.php
 * v111 (27.5.2026 17:00)
 * ---------------------------------------------------------
 * OPIS: Metrike sistema – zbiranje globalnih statistik
 * ---------------------------------------------------------
 *
 * ODVISNOSTI:
 * - pot.php
 * - SISTEM/kernel/baze/upravljalec_baz.php
 *
 * UPORABA:
 * - SISTEM/administracija/diagnostika/monitoring.php
 *
 * FUNKCIJE:
 * - globalno_metrike_zabelezi(), globalno_metrike_pridobi()
 * - globalno_metrike_statistika(), globalno_metrike_cisti()
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

function globalno_metrike_zabelezi(string $tip, $vrednost, array $oznake = []): void
{
$metrika = [
    'id' => uniqid('gm_', true),
    'tip' => $tip,
    'vrednost' => $vrednost,
    'oznake' => $oznake,
    'cas' => time()
];

baza_zapisi('globalno_metrike', $metrika);

// Shrani tudi v monitoring
if (function_exists('monitoring_metric_shrani')) {
    monitoring_metric_shrani($tip, $vrednost, $oznake);
}
}

function globalno_metrike_pridobi(string $tip, int $od = 0, int $do = 0, int $limit = 100): array
{
$vse = baza_beri('globalno_metrike');
$filtrirane = array_filter($vse, function($m) use ($tip, $od, $do) {
    if ($m['tip'] !== $tip) return false;
    if ($od > 0 && $m['cas'] < $od) return false;
    if ($do > 0 && $m['cas'] > $do) return false;
    return true;
});

// Razvrsti po času (novejše prve)
usort($filtrirane, fn($a, $b) => $b['cas'] <=> $a['cas']);

return array_slice($filtrirane, 0, $limit);
}

function globalno_metrike_statistika(string $tip, int $zadnjihDni = 30): array
{
$od = time() - ($zadnjihDni * 86400);
$vse = globalno_metrike_pridobi($tip, $od);

$vrednosti = array_column($vse, 'vrednost');
$numericVrednosti = array_filter($vrednosti, 'is_numeric');

$stat = [
    'skupaj' => count($vse),
    'zadnja' => !empty($vse) ? $vse[0] : null,
    'prva' => !empty($vse) ? end($vse) : null
];

if (!empty($numericVrednosti)) {
    $stat['min'] = min($numericVrednosti);
    $stat['max'] = max($numericVrednosti);
    $stat['povprecje'] = round(array_sum($numericVrednosti) / count($numericVrednosti), 2);
    $stat['vsota'] = array_sum($numericVrednosti);
}

// Po dnevih
$poDnevih = [];
foreach ($vse as $m) {
    $dan = date('Y-m-d', $m['cas']);
    $poDnevih[$dan] = ($poDnevih[$dan] ?? 0) + 1;
}
$stat['po_dnevih'] = $poDnevih;

return $stat;
}

function globalno_metrike_cisti(int $starejseOd = 2592000): int
{
$vse = baza_beri('globalno_metrike');
$meja = time() - $starejseOd;
$izbrisanih = 0;

foreach ($vse as $m) {
    if ($m['cas'] < $meja) {
        baza_zbrisi('globalno_metrike', $m['id']);
        $izbrisanih++;
    }
}

return $izbrisanih;
}

function globalno_metrike_sistem(): void
{
// Zberi sistemske metrike
globalno_metrike_zabelezi('sistem.zahteve', 1);
globalno_metrike_zabelezi('sistem.pomnilnik.mb', round(memory_get_usage() / 1024 / 1024, 2));
globalno_metrike_zabelezi('sistem.pomnilnik.vrhunec.mb', round(memory_get_peak_usage() / 1024 / 1024, 2));

if (function_exists('sys_getloadavg')) {
    $load = sys_getloadavg();
    globalno_metrike_zabelezi('sistem.obremenitev.1min', $load[0]);
    globalno_metrike_zabelezi('sistem.obremenitev.5min', $load[1]);
    globalno_metrike_zabelezi('sistem.obremenitev.15min', $load[2]);
}

// Število aktivnih uporabnikov (približno)
$aktivni = seja_pridobi('aktivni_uporabniki', 0);
globalno_metrike_zabelezi('sistem.aktivni_uporabniki', $aktivni);
}

function globalno_metrike_izvozi(string $tip = null): array
{
if ($tip !== null) {
    return globalno_metrike_pridobi($tip, 0, 0, 10000);
}

$vse = baza_beri('globalno_metrike');
usort($vse, fn($a, $b) => $b['cas'] <=> $a['cas']);
return $vse;
}