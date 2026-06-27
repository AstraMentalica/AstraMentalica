<?php
/**
 * ---------------------------------------------------------
 * POT: SISTEM/storitve_svetov/globalno/rbac.php
 * v111 (27.5.2026 06:00)
 * ---------------------------------------------------------
 * OPIS: Pravila dostopa (RBAC) – FAZA 3 DOPOLNITVE
 * ---------------------------------------------------------
 *
 * ODVISNOSTI:
 * - pot.php
 * - SISTEM/kernel/jedro/05_pravice.php
 * - SISTEM/kernel/jedro/04_seja.php
 *
 * UPORABA:
 * - GLOBALNO/render/ (za prikaz)
 * - SISTEM/kernel/middleware/
 *
 * FUNKCIJE:
 * - RBAC_vloga_ime(), RBAC_preveri_pot(), RBAC_dovoljene_poti()
 * - RBAC_preveri_dovoljenje(), RBAC_dovoljenja_za_modul()
 * - RBAC_moduli_po_vlogi(), RBAC_hierarhija()
 * - RBAC_naslednja_vloga(), RBAC_zahteva_vlogo()
 * - RBAC_preveri_api(), RBAC_dovoljeni_moduli()
 *
 * PREPOVEDI:
 * - Brez echo, print_r, var_dump
 * - Brez die(), exit()
 *
 * STATUS: Stabilno
 *
 * ZGODOVINA:
 * - v111: FAZA 2b – osnovna implementacija
 * - v111: FAZA 3 – dodane razširjene funkcije
 *
 * ---------------------------------------------------------
 * AVTOR: AstraMentalica Mojster
 * ---------------------------------------------------------
 */

declare(strict_types=1);

// ==================== OSNOVNE FUNKCIJE (FAZA 2b) ====================

function RBAC_vloga_ime(int $vloga): string
{
return match($vloga) {
    VLOGA_GOST => 'Gost',
    VLOGA_S0 => 'S0 – Začetnik',
    VLOGA_S1 => 'S1 – Učenec',
    VLOGA_S2 => 'S2 – Raziskovalec',
    VLOGA_S3 => 'S3 – Mojster',
    VLOGA_S4 => 'S4 – Veliki Mojster',
    VLOGA_S5 => 'S5 – Arhitekt',
    VLOGA_ADMIN => 'Administrator',
    default => 'Neznano'
};
}

function RBAC_preveri_pot(string $pot, int $vloga): bool
{
// Javne poti
$javnePoti = ['domov', 'prijava', 'registracija', 'obnovi_geslo'];
if (in_array($pot, $javnePoti)) {
    return true;
}

// Uporabniške poti
$uporabniskePoti = ['profil', 'nastavitve', 'zgodovina', 'dnevnik', 'sanje', 'meditacije'];
if (in_array($pot, $uporabniskePoti)) {
    return $vloga >= VLOGA_S0;
}

// Admin poti
$adminPoti = ['admin', 'astra', 'upravljanje', 'monitoring', 'dnevniki'];
if (in_array($pot, $adminPoti)) {
    return $vloga >= VLOGA_ADMIN;
}

// Modul poti
if (strpos($pot, 'modul/') === 0) {
    return $vloga >= VLOGA_S0;
}

// API poti
if (strpos($pot, 'api/') === 0) {
    return $vloga >= VLOGA_GOST;
}

return true;
}

function RBAC_dovoljene_poti(int $vloga): array
{
$vsePoti = [
    'domov' => VLOGA_GOST,
    'prijava' => VLOGA_GOST,
    'registracija' => VLOGA_GOST,
    'obnovi_geslo' => VLOGA_GOST,
    'profil' => VLOGA_S0,
    'nastavitve' => VLOGA_S0,
    'zgodovina' => VLOGA_S0,
    'dnevnik' => VLOGA_S0,
    'sanje' => VLOGA_S0,
    'meditacije' => VLOGA_S0,
    'moduli' => VLOGA_S1,
    'astro' => VLOGA_S2,
    'tarot' => VLOGA_S3,
    'admin' => VLOGA_ADMIN,
    'astra' => VLOGA_S5,
    'monitoring' => VLOGA_ADMIN,
    'dnevniki' => VLOGA_ADMIN
];

$dovoljene = [];
foreach ($vsePoti as $pot => $zahtevanaVloga) {
    if ($vloga >= $zahtevanaVloga) {
        $dovoljene[] = $pot;
    }
}

return $dovoljene;
}

function RBAC_preveri_dovoljenje(string $dovoljenje, ?int $vloga = null): bool
{
if ($vloga === null) {
    $vloga = seja_pridobi_vlogo();
}

return pravice_ima_dovoljenje($dovoljenje, $vloga);
}

function RBAC_dovoljenja_za_modul(string $imeModula, ?int $vloga = null): array
{
if ($vloga === null) {
    $vloga = seja_pridobi_vlogo();
}

$modul = moduli_nalozi($imeModula);
if ($modul === null) {
    return [];
}

$zahtevanaDovoljenja = $modul['razglas']['dovoljenja'] ?? [];
$dovoljena = [];

foreach ($zahtevanaDovoljenja as $dovoljenje => $potrebnaVloga) {
    if ($vloga >= $potrebnaVloga) {
        $dovoljena[] = $dovoljenje;
    }
}

return $dovoljena;
}

function RBAC_moduli_po_vlogi(int $vloga): array
{
$vsiModuli = baza_beri('moduli');
$dovoljeni = [];

foreach ($vsiModuli as $modul) {
    if ($modul['aktiviran'] && $vloga >= ($modul['vloga_min'] ?? VLOGA_GOST)) {
        $dovoljeni[] = $modul;
    }
}

return $dovoljeni;
}

// ==================== FAZA 3 – DODATNE FUNKCIJE ====================

function RBAC_hierarhija(): array
{
return [
    VLOGA_GOST => 'Osnovni dostop',
    VLOGA_S0 => 'Registriran uporabnik',
    VLOGA_S1 => 'Dostop do osnovnih modulov',
    VLOGA_S2 => 'Dostop do naprednih modulov',
    VLOGA_S3 => 'Dostop do ekskluzivnih modulov',
    VLOGA_S4 => 'Dostop do premium modulov',
    VLOGA_S5 => 'Dostop do vseh modulov',
    VLOGA_ADMIN => 'Poln dostop'
];
}

function RBAC_naslednja_vloga(int $trenutnaVloga): ?int
{
$stopnje = [VLOGA_GOST, VLOGA_S0, VLOGA_S1, VLOGA_S2, VLOGA_S3, VLOGA_S4, VLOGA_S5, VLOGA_ADMIN];
$trenutniIndeks = array_search($trenutnaVloga, $stopnje);

if ($trenutniIndeks !== false && $trenutniIndeks + 1 < count($stopnje)) {
    return $stopnje[$trenutniIndeks + 1];
}

return null;
}

function RBAC_prejsnja_vloga(int $trenutnaVloga): ?int
{
$stopnje = [VLOGA_GOST, VLOGA_S0, VLOGA_S1, VLOGA_S2, VLOGA_S3, VLOGA_S4, VLOGA_S5, VLOGA_ADMIN];
$trenutniIndeks = array_search($trenutnaVloga, $stopnje);

if ($trenutniIndeks !== false && $trenutniIndeks - 1 >= 0) {
    return $stopnje[$trenutniIndeks - 1];
}

return null;
}

function RBAC_zahteva_vlogo(string $pot, array $pravilaPoti = []): int
{
$privzetaPravila = [
    '/admin/*' => VLOGA_ADMIN,
    '/astra/*' => VLOGA_S5,
    '/moduli/*' => VLOGA_S0,
    '/profil/*' => VLOGA_S0,
    '/api/*' => VLOGA_GOST,
    '/api/admin/*' => VLOGA_ADMIN,
    '/upravljanje/*' => VLOGA_ADMIN,
    '/monitoring/*' => VLOGA_ADMIN
];

$vsaPravila = array_merge($privzetaPravila, $pravilaPoti);

foreach ($vsaPravila as $vzorec => $zahtevanaVloga) {
    $vzorecRegex = str_replace('*', '.*', $vzorec);
    if (preg_match('#^' . $vzorecRegex . '$#', $pot)) {
        return $zahtevanaVloga;
    }
}

return VLOGA_GOST;
}

function RBAC_preveri_api(string $pot, string $metoda, int $vloga): bool
{
// API pravila
$apiPravila = [
    'GET' => [
        '/api/v1/*' => VLOGA_GOST,
        '/api/v1/user/*' => VLOGA_S0,
        '/api/v1/admin/*' => VLOGA_ADMIN
    ],
    'POST' => [
        '/api/v1/*' => VLOGA_S0,
        '/api/v1/admin/*' => VLOGA_ADMIN
    ],
    'PUT' => [
        '/api/v1/*' => VLOGA_S0,
        '/api/v1/admin/*' => VLOGA_ADMIN
    ],
    'DELETE' => [
        '/api/v1/*' => VLOGA_ADMIN
    ]
];

$pravilaZaMetodo = $apiPravila[$metoda] ?? $apiPravila['GET'];

foreach ($pravilaZaMetodo as $vzorec => $zahtevanaVloga) {
    $vzorecRegex = str_replace('*', '.*', $vzorec);
    if (preg_match('#^' . $vzorecRegex . '$#', $pot)) {
        return $vloga >= $zahtevanaVloga;
    }
}

return $vloga >= VLOGA_GOST;
}

function RBAC_dovoljeni_moduli(int $vloga): array
{
$vsiModuli = array_keys($GLOBALS['REGISTER_MODULOV'] ?? []);
$dovoljeni = [];

foreach ($vsiModuli as $modulIme) {
    $minVloga = $GLOBALS['REGISTER_MODULOV'][$modulIme]['vloga_min'] ?? VLOGA_GOST;
    if ($vloga >= $minVloga) {
        $dovoljeni[] = $modulIme;
    }
}

return $dovoljeni;
}

function RBAC_opis_vloge(int $vloga): array
{
$opisi = [
    VLOGA_GOST => [
        'ime' => 'Gost',
        'opis' => 'Neprijavljen uporabnik z osnovnim dostopom',
        'barva' => 'gray',
        'ikon' => '👤'
    ],
    VLOGA_S0 => [
        'ime' => 'Začetnik',
        'opis' => 'Prijavljen uporabnik z osnovnimi funkcijami',
        'barva' => 'green',
        'ikon' => '🌱'
    ],
    VLOGA_S1 => [
        'ime' => 'Učenec',
        'opis' => 'Dostop do osnovnih modulov in vsebin',
        'barva' => 'blue',
        'ikon' => '📚'
    ],
    VLOGA_S2 => [
        'ime' => 'Raziskovalec',
        'opis' => 'Dostop do naprednih orodij in analiz',
        'barva' => 'indigo',
        'ikon' => '🔬'
    ],
    VLOGA_S3 => [
        'ime' => 'Mojster',
        'opis' => 'Dostop do ekskluzivnih modulov',
        'barva' => 'purple',
        'ikon' => '🏆'
    ],
    VLOGA_S4 => [
        'ime' => 'Veliki Mojster',
        'opis' => 'Dostop do premium vsebin',
        'barva' => 'pink',
        'ikon' => '💎'
    ],
    VLOGA_S5 => [
        'ime' => 'Arhitekt',
        'opis' => 'Dostop do vseh modulov in nastavitev',
        'barva' => 'red',
        'ikon' => '⚡'
    ],
    VLOGA_ADMIN => [
        'ime' => 'Administrator',
        'opis' => 'Poln dostop do celotnega sistema',
        'barva' => 'darkred',
        'ikon' => '👑'
    ]
];

return $opisi[$vloga] ?? $opisi[VLOGA_GOST];
}

function RBAC_zahteva_dovoljenje(string $dovoljenje, ?callable $callback = null): bool
{
$imaDovoljenje = RBAC_preveri_dovoljenje($dovoljenje);

if (!$imaDovoljenje && $callback !== null) {
    $callback();
}

return $imaDovoljenje;
}

function RBAC_vloge_z_dovoljenjem(string $dovoljenje): array
{
$vseVloge = pravice_vse_vloge();
$ustrezneVloge = [];

foreach ($vseVloge as $vloga => $imeVloge) {
    if (pravice_ima_dovoljenje($dovoljenje, $vloga)) {
        $ustrezneVloge[$vloga] = $imeVloge;
    }
}

return $ustrezneVloge;
}

function RBAC_statistika(): array
{
$trenutnaVloga = seja_pridobi_vlogo();

return [
    'trenutna_vloga' => [
        'stevilka' => $trenutnaVloga,
        'ime' => RBAC_vloga_ime($trenutnaVloga),
        'opis' => RBAC_opis_vloge($trenutnaVloga)['opis']
    ],
    'naslednja_vloga' => RBAC_naslednja_vloga($trenutnaVloga),
    'prejsnja_vloga' => RBAC_prejsnja_vloga($trenutnaVloga),
    'dovoljene_poti' => RBAC_dovoljene_poti($trenutnaVloga),
    'dovoljeni_moduli' => RBAC_dovoljeni_moduli($trenutnaVloga),
    'stevilo_dovoljenj' => count(pravice_dovoljenja_za_vlogo($trenutnaVloga))
];
}

function RBAC_preveri_visekrat(array $dovoljenja, ?int $vloga = null): array
{
$rezultati = [];

foreach ($dovoljenja as $dovoljenje) {
    $rezultati[$dovoljenje] = RBAC_preveri_dovoljenje($dovoljenje, $vloga);
}

return $rezultati;
}

function RBAC_pooblastila_uporabnika(int $uporabnikId): array
{
$uporabnik = baza_beri_enega('uporabniki', $uporabnikId);
if ($uporabnik === null) {
    return [];
}

$vloga = $uporabnik['vloga'] ?? VLOGA_GOST;

return [
    'uporabnik_id' => $uporabnikId,
    'vloga' => $vloga,
    'vloga_ime' => RBAC_vloga_ime($vloga),
    'dovoljenja' => pravice_dovoljenja_za_vlogo($vloga),
    'dovoljene_poti' => RBAC_dovoljene_poti($vloga),
    'dovoljeni_moduli' => RBAC_dovoljeni_moduli($vloga)
];
}