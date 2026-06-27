<?php
/**
 * ---------------------------------------------------------
 * POT: SISTEM/storitve_svetov/globalno/globalno_cache.php
 * v111 (27.5.2026 17:00)
 * ---------------------------------------------------------
 * OPIS: Cache helperji za globalne podatke
 * ---------------------------------------------------------
 *
 * ODVISNOSTI:
 * - pot.php
 * - SISTEM/kernel/jedro/06_cache.php
 *
 * UPORABA:
 * - GLOBALNO/render/
 *
 * FUNKCIJE:
 * - globalno_cache_strani(), globalno_cache_modulov()
 * - globalno_cache_nastavitev(), globalno_cache_pocisti()
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

function globalno_cache_kljuc(string $skupina, string $kljuc): string
{
return 'globalno_' . $skupina . '_' . md5($kljuc);
}

function globalno_cache_strani(string $pot, array $parametri = []): string
{
$kljuc = $pot . '_' . md5(json_encode($parametri));
$cacheKljuc = globalno_cache_kljuc('stran', $kljuc);

$vsebina = cache_preberi($cacheKljuc);
if ($vsebina !== null) {
    return $vsebina;
}

return '';
}

function globalno_cache_strani_shrani(string $pot, string $vsebina, array $parametri = [], int $casZivljenja = 3600): void
{
$kljuc = $pot . '_' . md5(json_encode($parametri));
$cacheKljuc = globalno_cache_kljuc('stran', $kljuc);
cache_shrani($cacheKljuc, $vsebina, $casZivljenja);
}

function globalno_cache_modulov(string $imeModula, string $akcija, array $parametri = [])
{
$kljuc = $imeModula . '_' . $akcija . '_' . md5(json_encode($parametri));
$cacheKljuc = globalno_cache_kljuc('modul', $kljuc);
return cache_preberi($cacheKljuc);
}

function globalno_cache_modulov_shrani(string $imeModula, string $akcija, $vrednost, array $parametri = [], int $casZivljenja = 3600): void
{
$kljuc = $imeModula . '_' . $akcija . '_' . md5(json_encode($parametri));
$cacheKljuc = globalno_cache_kljuc('modul', $kljuc);
cache_shrani($cacheKljuc, $vrednost, $casZivljenja);
}

function globalno_cache_nastavitev(string $kljuc)
{
$cacheKljuc = globalno_cache_kljuc('nastavitev', $kljuc);
return cache_preberi($cacheKljuc);
}

function globalno_cache_nastavitev_shrani(string $kljuc, $vrednost, int $casZivljenja = 3600): void
{
$cacheKljuc = globalno_cache_kljuc('nastavitev', $kljuc);
cache_shrani($cacheKljuc, $vrednost, $casZivljenja);
}

function globalno_cache_pocisti(?string $skupina = null): void
{
if ($skupina !== null) {
    $vzorec = 'globalno_' . $skupina . '_';
    $vsiKljuci = cache_pridobi_vse_kljuce();
    foreach ($vsiKljuci as $kljuc) {
        if (strpos($kljuc, $vzorec) === 0) {
            cache_zbrisi($kljuc);
        }
    }
} else {
    // Počisti vse globalne cache
    $vsiKljuci = cache_pridobi_vse_kljuce();
    foreach ($vsiKljuci as $kljuc) {
        if (strpos($kljuc, 'globalno_') === 0) {
            cache_zbrisi($kljuc);
        }
    }
}
}

function globalno_cache_zapomni_si(string $skupina, string $kljuc, callable $callback, int $casZivljenja = 3600)
{
$cacheKljuc = globalno_cache_kljuc($skupina, $kljuc);
$vrednost = cache_preberi($cacheKljuc);

if ($vrednost !== null) {
    return $vrednost;
}

$vrednost = $callback();
cache_shrani($cacheKljuc, $vrednost, $casZivljenja);

return $vrednost;
}