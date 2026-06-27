<?php
/**
 * ---------------------------------------------------------
 * POT: SISTEM/storitve_svetov/globalno/globalno_varnost.php
 * v111 (27.5.2026 17:00)
 * ---------------------------------------------------------
 * OPIS: Globalne varnostne funkcije
 * ---------------------------------------------------------
 *
 * ODVISNOSTI:
 * - pot.php
 *
 * UPORABA:
 * - SISTEM/kernel/varnost/
 *
 * FUNKCIJE:
 * - globalno_varnost_xss(), globalno_varnost_sql()
 * - globalno_varnost_csrf(), globalno_varnost_headers()
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

function globalno_varnost_xss(string $vhod, bool $ohraniHtml = false): string
{
if ($ohraniHtml) {
    // Ohrani osnovne HTML tage
    return strip_tags($vhod, '<p><br><b><i><u><em><strong><a><img><ul><ol><li><h1><h2><h3><h4><h5><h6>');
}

return htmlspecialchars($vhod, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

function globalno_varnost_xss_array(array $vhod, bool $rekurzivno = true): array
{
$rezultat = [];
foreach ($vhod as $kljuc => $vrednost) {
    if (is_array($vrednost) && $rekurzivno) {
        $rezultat[$kljuc] = globalno_varnost_xss_array($vrednost, true);
    } elseif (is_string($vrednost)) {
        $rezultat[$kljuc] = globalno_varnost_xss($vrednost);
    } else {
        $rezultat[$kljuc] = $vrednost;
    }
}
return $rezultat;
}

function globalno_varnost_sql(string $vhod): string
{
// Escape za SQL (v produkciji uporabi PBO pripravljene stavke)
return addslashes($vhod);
}

function globalno_varnost_csrf_generiraj(): string
{
if (function_exists('csrf_generiraj_token')) {
    return csrf_generiraj_token();
}
return bin2hex(random_bytes(32));
}

function globalno_varnost_csrf_preveri(?string $token = null): bool
{
if (function_exists('csrf_preveri_token')) {
    return csrf_preveri_token($token);
}

if ($token === null) {
    $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
}

$prcakovani = $_SESSION['_csrf_token'] ?? '';
return hash_equals($prcakovani, $token);
}

function globalno_varnost_headers(): void
{
if (headers_sent()) {
    return;
}

header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');

if (!RAZVOJNI_NACIN) {
    header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval'; style-src 'self' 'unsafe-inline'");
}
}

function globalno_varnost_rate_limit(string $kljuc, int $omejitev = 60, int $casOkna = 60): bool
{
if (function_exists('omejitve_preveri')) {
    return omejitve_preveri($kljuc, $omejitev, $casOkna);
}

// Preprosta implementacija
$pot = sys_get_temp_dir() . '/rate_limit_' . md5($kljuc) . '.json';
$zdaj = time();

if (file_exists($pot)) {
    $podatki = json_decode(file_get_contents($pot), true);
    $podatki['zahteve'] = array_filter($podatki['zahteve'] ?? [], fn($t) => $t > $zdaj - $casOkna);
    
    if (count($podatki['zahteve']) >= $omejitev) {
        return false;
    }
    
    $podatki['zahteve'][] = $zdaj;
} else {
    $podatki = ['zahteve' => [$zdaj]];
}

file_put_contents($pot, json_encode($podatki));
return true;
}

function globalno_varnost_random_niz(int $dolzina = 32): string
{
return bin2hex(random_bytes($dolzina / 2));
}