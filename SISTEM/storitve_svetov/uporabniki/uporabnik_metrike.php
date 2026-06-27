<?php
/**
 * ---------------------------------------------------------
 * POT: SISTEM/storitve_svetov/uporabniki/uporabnik_metrike.php
 * v111 (27.5.2026 16:30)
 * ---------------------------------------------------------
 * OPIS: Metrike uporabnikov – beleženje aktivnosti
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
 * - uporabniki_metrike_zabelezi(), uporabniki_metrike_pridobi()
 * - uporabniki_metrike_statistika(), uporabniki_metrike_cisti()
 *
 * PREPOVEDI:
 * - Brez echo, print_r, var_dump
 *
 * STATUS: Stabilno
 *
 * ZGODOVINA:
 * - v111: FAZA 46 – implementacija
 *
 * ---------------------------------------------------------
 * AVTOR: AstraMentalica Mojster
 * ---------------------------------------------------------
 */

declare(strict_types=1);

function uporabniki_metrike_zabelezi(string $uporabnikId, string $tip, array $podatki = []): void
{
$metrika = [
    'id' => uniqid('um_', true),
    'uporabnik_id' => $uporabnikId,
    'tip' => $tip,
    'podatki' => $podatki,
    'cas' => time(),
    'ip' => varnost_pridobi_ip(),
    'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? ''
];

baza_zapisi('uporabniki_metrike', $metrika);

// Sproži dogodek za analitiko
dogodek_sprozi('uporabnik.metrika', $metrika);

// Shrani tudi v monitoring
if (function_exists('monitoring_metric_shrani')) {
    monitoring_metric_shrani("uporabnik.$tip", 1, ['uporabnik_id' => $uporabnikId]);
}
}

function uporabniki_metrike_pridobi(string $uporabnikId, string $tip = null, int $od = 0, int $do = 0, int $limit = 100): array
{
$vse = baza_beri('uporabniki_metrike');
$filtrirane = array_filter($vse, function($m) use ($uporabnikId, $tip, $od, $do) {
    if ($m['uporabnik_id'] !== $uporabnikId) return false;
    if ($tip !== null && $m['tip'] !== $tip) return false;
    if ($od > 0 && $m['cas'] < $od) return false;
    if ($do > 0 && $m['cas'] > $do) return false;
    return true;
});

// Razvrsti po času (novejše prve)
usort($filtrirane, function($a, $b) {
    return $b['cas'] <=> $a['cas'];
});

return array_slice($filtrirane, 0, $limit);
}

function uporabniki_metrike_statistika(string $uporabnikId, int $zadnjihDni = 30): array
{
$od = time() - ($zadnjihDni * 86400);
$vse = uporabniki_metrike_pridobi($uporabnikId, null, $od);

$stat = [
    'skupaj' => count($vse),
    'po_tipih' => [],
    'zadnjih_24h' => 0,
    'zadnjih_7dni' => 0
];

$zdaj = time();
$dan24h = $zdaj - 86400;
$dan7dni = $zdaj - 604800;

foreach ($vse as $m) {
    $tip = $m['tip'];
    $stat['po_tipih'][$tip] = ($stat['po_tipih'][$tip] ?? 0) + 1;
    
    if ($m['cas'] > $dan24h) $stat['zadnjih_24h']++;
    if ($m['cas'] > $dan7dni) $stat['zadnjih_7dni']++;
}

return $stat;
}

function uporabniki_metrike_cisti(int $starejseOd = 2592000): int
{
$vse = baza_beri('uporabniki_metrike');
$meja = time() - $starejseOd;
$izbrisanih = 0;

foreach ($vse as $m) {
    if ($m['cas'] < $meja) {
        baza_zbrisi('uporabniki_metrike', $m['id']);
        $izbrisanih++;
    }
}

return $izbrisanih;
}

function uporabniki_metrike_izvozi(string $uporabnikId): array
{
return uporabniki_metrike_pridobi($uporabnikId, null, 0, 0, 10000);
}
SISTEM/storitve_svetov/uporabniki/uporabniki_izvoz.php
php
<?php
/**
 * ---------------------------------------------------------
 * POT: SISTEM/storitve_svetov/uporabniki/uporabniki_izvoz.php
 * v111 (27.5.2026 16:30)
 * ---------------------------------------------------------
 * OPIS: Izvoz podatkov uporabnikov (GDPR)
 * ---------------------------------------------------------
 *
 * ODVISNOSTI:
 * - pot.php
 * - SISTEM/kernel/baze/upravljalec_baz.php
 *
 * UPORABA:
 * - UPORABNIKI/prikaz/uporabnik/profil.php
 *
 * FUNKCIJE:
 * - uporabniki_izvoz_ustvari(), uporabniki_izvoz_pridobi()
 * - uporabniki_izvoz_prenesi(), uporabniki_izvoz_zbrisi()
 *
 * PREPOVEDI:
 * - Brez echo, print_r, var_dump
 *
 * STATUS: Stabilno
 *
 * ZGODOVINA:
 * - v111: FAZA 46 – implementacija
 *
 * ---------------------------------------------------------
 * AVTOR: AstraMentalica Mojster
 * ---------------------------------------------------------
 */

declare(strict_types=1);

function uporabniki_izvoz_ustvari(string $uporabnikId): string
{
// Zberi vse podatke o uporabniku
$uporabnik = baza_beri_enega('uporabniki', $uporabnikId);
if ($uporabnik === null) {
    throw new RuntimeException("Uporabnik $uporabnikId ne obstaja");
}

// Odstrani občutljive podatke
unset($uporabnik['hash_gesla']);
unset($uporabnik['aktivacijski_zeton']);
unset($uporabnik['obnovitveni_zeton']);
unset($uporabnik['2fa_secret']);

$izvoz = [
    'uporabnik' => $uporabnik,
    'cas_izvoza' => time(),
    'verzija' => SISTEM_VERZIJA,
    'podatki' => []
];

// Zberi PASSPORT podatke
$passportPot = uporabnik_passport($uporabnikId);
if (is_dir($passportPot)) {
    $datoteke = glob($passportPot . '/*.json');
    foreach ($datoteke as $datoteka) {
        $ime = basename($datoteka, '.json');
        $vsebina = file_get_contents($datoteka);
        $izvoz['podatki'][$ime] = json_decode($vsebina, true) ?: [];
    }
}

// Zberi aktivnosti
$izvoz['podatki']['aktivnosti'] = uporabniki_aktivnosti_zadnje($uporabnikId, 1000);

// Zberi metrike
$izvoz['podatki']['metrike'] = uporabniki_metrike_izvozi($uporabnikId);

// Zberi notifikacije
$izvoz['podatki']['notifikacije'] = uporabniki_notifikacije_pridobi($uporabnikId, 1000);

// Shrani izvoz
$izvozId = uniqid('export_', true);
$pot = POT_PODATKI_SKLADISCE . '/izvozi/' . $izvozId . '.json';
$mapa = dirname($pot);
if (!is_dir($mapa)) {
    mkdir($mapa, 0755, true);
}

file_put_contents($pot, json_encode($izvoz, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

// Zabeleži v bazo
baza_zapisi('uporabniki_izvozi', [
    'id' => $izvozId,
    'uporabnik_id' => $uporabnikId,
    'ustvarjeno' => time(),
    'velikost' => filesize($pot)
]);

return $izvozId;
}

function uporabniki_izvoz_pridobi(string $uporabnikId): array
{
$vsi = baza_beri('uporabniki_izvozi');
return array_filter($vsi, fn($i) => $i['uporabnik_id'] === $uporabnikId);
}

function uporabniki_izvoz_prenesi(string $izvozId): ?array
{
$pot = POT_PODATKI_SKLADISCE . '/izvozi/' . $izvozId . '.json';
if (!file_exists($pot)) {
    return null;
}

$vsebina = file_get_contents($pot);
return json_decode($vsebina, true);
}

function uporabniki_izvoz_zbrisi(string $izvozId): bool
{
$pot = POT_PODATKI_SKLADISCE . '/izvozi/' . $izvozId . '.json';
if (file_exists($pot)) {
    unlink($pot);
}

// Odstrani iz baze
$vsi = baza_beri('uporabniki_izvozi');
foreach ($vsi as $zapis) {
    if ($zapis['id'] === $izvozId) {
        return baza_zbrisi('uporabniki_izvozi', $zapis['id']);
    }
}

return true;
}

function uporabniki_izvoz_pocisti(int $starejseOd = 604800): int
{
$vsi = baza_beri('uporabniki_izvozi');
$meja = time() - $starejseOd;
$izbrisanih = 0;

foreach ($vsi as $zapis) {
    if ($zapis['ustvarjeno'] < $meja) {
        uporabniki_izvoz_zbrisi($zapis['id']);
        $izbrisanih++;
    }
}

return $izbrisanih;
}