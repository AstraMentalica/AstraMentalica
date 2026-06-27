<?php
/**
 * ---------------------------------------------------------
 * POT: SISTEM/storitve_svetov/uporabniki/uporabnik_seja.php
 * v111 (27.5.2026 05:15)
 * ---------------------------------------------------------
 * OPIS: Poslovna logika za prijavo in odjavo
 * ---------------------------------------------------------
 *
 * ODVISNOSTI:
 * - pot.php
 * - SISTEM/kernel/baze/upravljalec_baz.php
 * - SISTEM/kernel/jedro/04_seja.php
 *
 * UPORABA:
 * - API / uporabniški vmesnik
 *
 * FUNKCIJE:
 * - uporabniki_prijavi() – prijava uporabnika
 * - uporabniki_preveri_prijavo() – preverjanje prijave
 * - uporabniki_odjavi() – odjava
 *
 * PREPOVEDI:
 * - Brez echo, print_r, var_dump
 * - Brez HTML
 *
 * STATUS: Stabilno
 *
 * ZGODOVINA:
 * - v111: FAZA 2b – implementacija
 *
 * ---------------------------------------------------------
 * AVTOR: AstraMentalica Mojster
 * ---------------------------------------------------------
 */

declare(strict_types=1);

function uporabniki_prijavi(string $email, string $geslo, bool $zapomniSe = false): array
{
// Poišči uporabnika po emailu
$uporabniki = baza_beri('uporabniki');
$najden = null;

foreach ($uporabniki as $uporabnik) {
    if (($uporabnik['elektronski_naslov'] ?? '') === $email) {
        $najden = $uporabnik;
        break;
    }
}

if ($najden === null) {
    return [
        'status' => 'napaka',
        'status_koda' => 401,
        'sporocilo' => 'Napačen elektronski naslov ali geslo.'
    ];
}

// Preveri ali je račun aktiviran
if (!$najden['aktiviran']) {
    return [
        'status' => 'napaka',
        'status_koda' => 403,
        'sporocilo' => 'Račun še ni aktiviran. Preverite elektronsko pošto.'
    ];
}

// Preveri geslo
if (!password_verify($geslo, $najden['hash_gesla'])) {
    return [
        'status' => 'napaka',
        'status_koda' => 401,
        'sporocilo' => 'Napačen elektronski naslov ali geslo.'
    ];
}

// Posodobi zadnjo prijavo
baza_posodobi('uporabniki', $najden['id'], [
    'nazadnje_prijavljen' => time()
]);

// Ustvari sejo
seja_prijavi(
    $najden['id'],
    $najden['ime'],
    $najden['elektronski_naslov'],
    $najden['vloga']
);

// Če je zapomniSe, podaljšaj čas seje
if ($zapomniSe) {
    ini_set('session.cookie_lifetime', 86400 * 30); // 30 dni
}

// Generiraj JWT token za API
$jwtToken = null;
if (function_exists('jwt_ustvari')) {
    $jwtToken = jwt_ustvari([
        'uporabnik_id' => $najden['id'],
        'email' => $najden['elektronski_naslov'],
        'vloga' => $najden['vloga']
    ], 86400);
}

// Sproži dogodek
dogodek_sprozi('uporabnik.prijavljen', [
    'uporabnik_id' => $najden['id'],
    'email' => $email,
    'vloga' => $najden['vloga']
]);

return [
    'status' => 'uspeh',
    'status_koda' => 200,
    'sporocilo' => 'Prijava uspešna.',
    'vsebina' => [
        'uporabnik' => [
            'id' => $najden['id'],
            'ime' => $najden['ime'],
            'email' => $najden['elektronski_naslov'],
            'vloga' => $najden['vloga']
        ],
        'token' => $jwtToken
    ]
];
}

function uporabniki_preveri_prijavo(): array
{
if (!seja_je_prijavljen()) {
    return [
        'status' => 'napaka',
        'status_koda' => 401,
        'sporocilo' => 'Niste prijavljeni.'
    ];
}

$uporabnik = seja_pridobi_uporabnika();

return [
    'status' => 'uspeh',
    'status_koda' => 200,
    'sporocilo' => 'Uporabnik je prijavljen.',
    'vsebina' => ['uporabnik' => $uporabnik]
];
}

function uporabniki_odjavi(): array
{
$uporabnik = seja_pridobi_uporabnika();

seja_odjavi();

if ($uporabnik) {
    dogodek_sprozi('uporabnik.odjavljen', [
        'uporabnik_id' => $uporabnik['id'],
        'email' => $uporabnik['elektronski_naslov']
    ]);
}

return [
    'status' => 'uspeh',
    'status_koda' => 200,
    'sporocilo' => 'Odjava uspešna.'
];
}

function uporabniki_obnovi_geslo(string $email): array
{
$uporabniki = baza_beri('uporabniki');
$najden = null;

foreach ($uporabniki as $uporabnik) {
    if (($uporabnik['elektronski_naslov'] ?? '') === $email) {
        $najden = $uporabnik;
        break;
    }
}

if ($najden === null) {
    // Ne razkrivamo ali email obstaja – vrnemo isti odziv
    return [
        'status' => 'uspeh',
        'status_koda' => 200,
        'sporocilo' => 'Če elektronski naslov obstaja, smo poslali navodila za obnovitev gesla.'
    ];
}

$obnovitveniZeton = bin2hex(random_bytes(32));

baza_posodobi('uporabniki', $najden['id'], [
    'obnovitveni_zeton' => $obnovitveniZeton,
    'obnovitveni_zeton_cas' => time()
]);

// TODO: Pošlji email z navodili
// email_poslji($email, 'Obnovitev gesla', "Kliknite: ?akcija=obnovi&zeton=$obnovitveniZeton");

return [
    'status' => 'uspeh',
    'status_koda' => 200,
    'sporocilo' => 'Če elektronski naslov obstaja, smo poslali navodila za obnovitev gesla.'
];
}