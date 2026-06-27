<?php
/**
 * ---------------------------------------------------------
 * POT: SISTEM/storitve_svetov/uporabniki/uporabnik_profil.php
 * v111 (27.5.2026 05:15)
 * ---------------------------------------------------------
 * OPIS: Poslovna logika za uporabniški profil
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
 * - uporabniki_profil_pridobi() – pridobitev profila
 * - uporabniki_profil_posodobi() – posodobitev profila
 * - uporabniki_profil_spremeni_geslo() – sprememba gesla
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

function uporabniki_profil_pridobi(?string $uporabnikId = null): array
{
if ($uporabnikId === null) {
    if (!seja_je_prijavljen()) {
        return [
            'status' => 'napaka',
            'status_koda' => 401,
            'sporocilo' => 'Niste prijavljeni.'
        ];
    }
    $uporabnikId = seja_pridobi('uporabnik_id');
}

$uporabnik = baza_beri_enega('uporabniki', $uporabnikId);

if ($uporabnik === null) {
    return [
        'status' => 'napaka',
        'status_koda' => 404,
        'sporocilo' => 'Uporabnik ne obstaja.'
    ];
}

// Ne vračamo občutljivih podatkov
unset($uporabnik['hash_gesla']);
unset($uporabnik['aktivacijski_zeton']);
unset($uporabnik['obnovitveni_zeton']);
unset($uporabnik['obnovitveni_zeton_cas']);

return [
    'status' => 'uspeh',
    'status_koda' => 200,
    'sporocilo' => '',
    'vsebina' => ['uporabnik' => $uporabnik]
];
}

function uporabniki_profil_posodobi(array $podatki): array
{
if (!seja_je_prijavljen()) {
    return [
        'status' => 'napaka',
        'status_koda' => 401,
        'sporocilo' => 'Niste prijavljeni.'
    ];
}

$uporabnikId = seja_pridobi('uporabnik_id');
$uporabnik = baza_beri_enega('uporabniki', $uporabnikId);

if ($uporabnik === null) {
    return [
        'status' => 'napaka',
        'status_koda' => 404,
        'sporocilo' => 'Uporabnik ne obstaja.'
    ];
}

// Dovoljena polja za posodobitev
$dovoljenaPolja = ['ime'];
$posodobitve = [];

foreach ($dovoljenaPolja as $polje) {
    if (isset($podatki[$polje])) {
        $posodobitve[$polje] = $podatki[$polje];
    }
}

if (empty($posodobitve)) {
    return [
        'status' => 'opozorilo',
        'status_koda' => 200,
        'sporocilo' => 'Ni podatkov za posodobitev.'
    ];
}

baza_posodobi('uporabniki', $uporabnikId, $posodobitve);

// Posodobi sejo če se je ime spremenilo
if (isset($posodobitve['ime'])) {
    seja_nastavi('uporabnik_ime', $posodobitve['ime']);
}

dogodek_sprozi('uporabnik.profil_posodobljen', [
    'uporabnik_id' => $uporabnikId,
    'spremembe' => array_keys($posodobitve)
]);

return [
    'status' => 'uspeh',
    'status_koda' => 200,
    'sporocilo' => 'Profil uspešno posodobljen.',
    'vsebina' => ['spremembe' => $posodobitve]
];
}

function uporabniki_profil_spremeni_geslo(string $staroGeslo, string $novoGeslo): array
{
if (!seja_je_prijavljen()) {
    return [
        'status' => 'napaka',
        'status_koda' => 401,
        'sporocilo' => 'Niste prijavljeni.'
    ];
}

$uporabnikId = seja_pridobi('uporabnik_id');
$uporabnik = baza_beri_enega('uporabniki', $uporabnikId);

if ($uporabnik === null) {
    return [
        'status' => 'napaka',
        'status_koda' => 404,
        'sporocilo' => 'Uporabnik ne obstaja.'
    ];
}

// Preveri staro geslo
if (!password_verify($staroGeslo, $uporabnik['hash_gesla'])) {
    return [
        'status' => 'napaka',
        'status_koda' => 401,
        'sporocilo' => 'Starogeslo ni pravilno.'
    ];
}

// Validacija novega gesla
if (strlen($novoGeslo) < 8) {
    return [
        'status' => 'napaka',
        'status_koda' => 400,
        'sporocilo' => 'Novo geslo mora imeti vsaj 8 znakov.'
    ];
}

$noviHash = password_hash($novoGeslo, PASSWORD_BCRYPT);

baza_posodobi('uporabniki', $uporabnikId, [
    'hash_gesla' => $noviHash
]);

dogodek_sprozi('uporabnik.geslo_spremenjeno', [
    'uporabnik_id' => $uporabnikId
]);

return [
    'status' => 'uspeh',
    'status_koda' => 200,
    'sporocilo' => 'Geslo uspešno spremenjeno.'
];
}