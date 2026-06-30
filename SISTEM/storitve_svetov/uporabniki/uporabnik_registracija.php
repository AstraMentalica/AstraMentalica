<?php
/**
 * ============================================================
 * POT: SISTEM/storitve_svetov/uporabniki/uporabnik_registracija.php
 * 📅 VERZIJA: v115 (14.6.2026 18:00)
 * ============================================================
 *
 * 🏛️ NIVO: STORITEV (BUSINESS LAYER)
 *
 * 📰 NAMEN:
 *     Registracija uporabnika – email in Google.
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - uporabniki_registriraj(array $podatki): array
 *     - uporabniki_registriraj_google(array $podatki): array
 *     - uporabniki_po_emailu(string $email): ?array
 *     - uporabniki_po_id(string $id): ?array
 *     - uporabniki_generiraj_id(): string
 *
 * 📡 ODVISNOSTI:
 *     - SISTEM/kernel/jedro/04_seja.php
 *     - SISTEM/kernel/jedro/07_dogodki.php
 *     - SISTEM/kernel/baze/upravljalec_baz.php
 *
 * 🚫 PREPOVEDI:
 *     - Brez __DIR__
 *     - Brez echo, print_r, var_dump
 *
 * 📌 STATUS:
 *     Stabilno
 *
 * 📅 ZGODOVINA:
 *     - v115: uskladitev s Header Standard v115
 *
 * 👤 AVTOR:
 *     AstraMentalica Mojster
 *
 * 🌐 JEZIK:
 *     sl
 *
 * 🏷️ OZNAKE:
 *     storitev, uporabniki, registracija
 * ============================================================
 */
declare(strict_types=1);

defined('SISTEM_VARNOST') or die('Direkten dostop ni dovoljen.');

// ============================================================
// KONSTANTE – VLOGA_* so definirane v pot.php (integer 0-100)
// Tu NE redefiniramo – uporabljamo VLOGA_GOST (=0) za nove
// uporabnike, potem se vloga dviga skozi napredovanje.
// ============================================================

// ============================================================
// POMOŽNE FUNKCIJE
// ============================================================

function uporabniki_generiraj_id(): string
{
    return 'usr_' . bin2hex(random_bytes(8));
}

function uporabniki_po_emailu(string $email): ?array
{
    return baza_poisci('uporabniki', 'elektronski_naslov', strtolower(trim($email)));
}

function uporabniki_po_id(string $id): ?array
{
    return baza_preberi('uporabniki', $id);
}

// ============================================================
// REGISTRACIJA Z EMAILOM
// ============================================================

function uporabniki_registriraj(array $podatki): array
{
    $ime   = trim($podatki['ime']   ?? '');
    $email = strtolower(trim($podatki['email'] ?? ''));
    $geslo = $podatki['geslo'] ?? '';
    
    // Validacija
    if (strlen($ime) < 2) {
        return [
            'status' => 'napaka',
            'status_koda' => 400,
            'sporocilo' => 'Ime je prekratko.'
        ];
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return [
            'status' => 'napaka',
            'status_koda' => 400,
            'sporocilo' => 'Elektronski naslov ni veljaven.'
        ];
    }
    
    if (strlen($geslo) < 8) {
        return [
            'status' => 'napaka',
            'status_koda' => 400,
            'sporocilo' => 'Geslo mora imeti vsaj 8 znakov.'
        ];
    }
    
    // Preveri ali email že obstaja
    if (uporabniki_po_emailu($email) !== null) {
        return [
            'status' => 'napaka',
            'status_koda' => 409,
            'sporocilo' => 'Ta elektronski naslov je že registriran.'
        ];
    }
    
    $id = uporabniki_generiraj_id();
    
    $uporabnik = [
        'id'                  => $id,
        'ime'                 => $ime,
        'elektronski_naslov'  => $email,
        'hash_gesla'          => password_hash($geslo, PASSWORD_BCRYPT),
        'vloga'               => VLOGA_GOST,
        'aktiviran'           => true,  // Po registraciji takoj aktiven
        'google_id'           => null,
        'ustvarjeno'          => time(),
        'nazadnje_prijavljen' => null
    ];
    
    baza_zapisi('uporabniki', $uporabnik);
    
    // Ustvari avatar – če sistem obstaja
    if (function_exists('avatar_ustvari')) {
        avatar_ustvari($id);
    }
    
    // Dogodki
    dogodek_sprozi('uporabnik.registriran', [
        'uporabnik_id' => $id,
        'email' => $email,
        'prek_google' => false,
        'cas' => time()
    ]);
    
    dogodek_sprozi('uporabnik.aktiviran', [
        'uporabnik_id' => $id,
        'email' => $email
    ]);
    
    // Prijavi uporabnika
    seja_prijavi($id, $ime, $email, VLOGA_GOST);
    
    return [
        'status' => 'uspeh',
        'status_koda' => 201,
        'sporocilo' => 'Registracija uspešna. Dobrodošel, ' . $ime . '!',
        'vsebina' => [
            'uporabnik' => [
                'id' => $id,
                'ime' => $ime,
                'email' => $email,
                'vloga' => VLOGA_GOST
            ]
        ]
    ];
}

// ============================================================
// REGISTRACIJA / PRIJAVA Z GOOGLE
// ============================================================

function uporabniki_registriraj_google(array $podatki): array
{
    $email    = strtolower(trim($podatki['email'] ?? ''));
    $ime      = trim($podatki['ime'] ?? '');
    $googleId = trim($podatki['google_id'] ?? '');
    
    if (empty($email) || empty($googleId)) {
        return [
            'status' => 'napaka',
            'status_koda' => 400,
            'sporocilo' => 'Manjkajo podatki iz Google računa.'
        ];
    }
    
    // Če uporabnik že obstaja → prijavi
    $obstojeci = uporabniki_po_emailu($email);
    if ($obstojeci !== null) {
        return _uporabniki_google_prijavi($obstojeci, $googleId);
    }
    
    // Nov uporabnik
    $id = uporabniki_generiraj_id();
    
    $uporabnik = [
        'id'                  => $id,
        'ime'                 => $ime ?: explode('@', $email)[0],
        'elektronski_naslov'  => $email,
        'hash_gesla'          => password_hash(bin2hex(random_bytes(16)), PASSWORD_BCRYPT),
        'vloga'               => VLOGA_GOST,
        'aktiviran'           => true, // Google računi so aktivirani takoj
        'google_id'           => $googleId,
        'ustvarjeno'          => time(),
        'nazadnje_prijavljen' => time()
    ];
    
    baza_zapisi('uporabniki', $uporabnik);
    
    // Ustvari avatar
    if (function_exists('avatar_ustvari')) {
        avatar_ustvari($id);
    }
    
    // Dogodki
    dogodek_sprozi('uporabnik.registriran', [
        'uporabnik_id' => $id,
        'email' => $email,
        'prek_google' => true,
        'cas' => time()
    ]);
    dogodek_sprozi('uporabnik.aktiviran', [
        'uporabnik_id' => $id,
        'email' => $email
    ]);
    
    // Prijavi uporabnika
    seja_prijavi($id, $uporabnik['ime'], $email, VLOGA_GOST);
    
    return [
        'status' => 'uspeh',
        'status_koda' => 201,
        'sporocilo' => 'Uspešna registracija z Google računom.',
        'vsebina' => [
            'uporabnik' => [
                'id' => $id,
                'ime' => $uporabnik['ime'],
                'email' => $email,
                'vloga' => VLOGA_GOST
            ]
        ]
    ];
}

function _uporabniki_google_prijavi(array $uporabnik, string $googleId): array
{
    // Shrani google_id če ga še nima
    if (empty($uporabnik['google_id'])) {
        baza_posodobi('uporabniki', $uporabnik['id'], [
            'google_id' => $googleId,
            'aktiviran' => true,
            'nazadnje_prijavljen' => time()
        ]);
    } else {
        baza_posodobi('uporabniki', $uporabnik['id'], [
            'nazadnje_prijavljen' => time()
        ]);
    }
    
    // Prijavi uporabnika
    seja_prijavi(
        $uporabnik['id'],
        $uporabnik['ime'],
        $uporabnik['elektronski_naslov'],
        $uporabnik['vloga']
    );
    
    dogodek_sprozi('uporabnik.prijavljen', [
        'uporabnik_id' => $uporabnik['id'],
        'email' => $uporabnik['elektronski_naslov'],
        'prek_google' => true,
        'cas' => time()
    ]);
    
    return [
        'status' => 'uspeh',
        'status_koda' => 200,
        'sporocilo' => 'Uspešna prijava z Google računom.',
        'vsebina' => [
            'uporabnik' => [
                'id' => $uporabnik['id'],
                'ime' => $uporabnik['ime'],
                'email' => $uporabnik['elektronski_naslov'],
                'vloga' => $uporabnik['vloga']
            ]
        ]
    ];
}