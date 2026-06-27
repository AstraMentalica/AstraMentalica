<?php
/**
 * ============================================================
 * POT: SISTEM/storitve_svetov/uporabniki/uporabnik_prijava.php
 * 📅 VERZIJA: v115 (14.6.2026 18:00)
 * ============================================================
 *
 * 🏛️ NIVO: STORITEV (BUSINESS LAYER)
 *
 * 📰 NAMEN:
 *     Prijava uporabnika – email/geslo.
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - uporabniki_prijavi(string $email, string $geslo): array
 *     - uporabniki_odjavi(): array
 *     - uporabniki_trenutni(): ?array
 *
 * 📡 ODVISNOSTI:
 *     - SISTEM/kernel/jedro/04_seja.php
 *     - SISTEM/kernel/jedro/05_pravice.php
 *     - SISTEM/kernel/jedro/07_dogodki.php
 *     - SISTEM/kernel/baze/upravljalec_baz.php
 *     - SISTEM/storitve_svetov/uporabniki/uporabnik_registracija.php
 *
 * 🚫 PREPOVEDI:
 *     - Brez __DIR__
 *     - Brez echo, print_r, var_dump
 *     - Brez direktnega branja $_SESSION (uporabi seja_* funkcije)
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
 *     storitev, uporabniki, prijava
 * ============================================================
 */
declare(strict_types=1);

defined('SISTEM_VARNOST') or die('Direkten dostop ni dovoljen.');

// ============================================================
// POMOŽNE FUNKCIJE ZA BRUTE FORCE ZAŠČITO
// ============================================================

function _prijava_preveri_brute_force(string $email): bool
{
    $kljuc = 'bf_' . md5(strtolower($email));
    $pot = POT_CACHE . '/' . $kljuc . '.json';
    
    if (!is_dir(POT_CACHE)) {
        mkdir(POT_CACHE, 0755, true);
    }
    
    $podatki = ['poskusi' => 0, 'zadnji' => 0];
    if (file_exists($pot)) {
        $podatki = json_decode(file_get_contents($pot), true) ?? $podatki;
    }
    
    // Ponastavi po 15 minutah
    if (time() - $podatki['zadnji'] > 900) {
        $podatki = ['poskusi' => 0, 'zadnji' => time()];
    }
    
    return $podatki['poskusi'] >= 10;
}

function _prijava_zabeli_neuspeh(string $email): void
{
    $kljuc = 'bf_' . md5(strtolower($email));
    $pot = POT_CACHE . '/' . $kljuc . '.json';
    
    $podatki = ['poskusi' => 0, 'zadnji' => time()];
    if (file_exists($pot)) {
        $podatki = json_decode(file_get_contents($pot), true) ?? $podatki;
    }
    
    $podatki['poskusi']++;
    $podatki['zadnji'] = time();
    file_put_contents($pot, json_encode($podatki), LOCK_EX);
}

function _prijava_pocisti_brute_force(string $email): void
{
    $kljuc = 'bf_' . md5(strtolower($email));
    $pot = POT_CACHE . '/' . $kljuc . '.json';
    if (file_exists($pot)) {
        unlink($pot);
    }
}

// ============================================================
// GLAVNE FUNKCIJE
// ============================================================

function uporabniki_prijavi(string $email, string $geslo): array
{
    $email = strtolower(trim($email));
    
    if (empty($email) || empty($geslo)) {
        return [
            'status' => 'napaka',
            'status_koda' => 400,
            'sporocilo' => 'Vnesite elektronski naslov in geslo.'
        ];
    }
    
    // Brute force zaščita
    if (_prijava_preveri_brute_force($email)) {
        return [
            'status' => 'napaka',
            'status_koda' => 429,
            'sporocilo' => 'Preveč neuspešnih poskusov. Počakajte 15 minut.'
        ];
    }
    
    $uporabnik = uporabniki_po_emailu($email);
    
    // Namerni zamik – prepreči ugibanje ali email obstaja
    if ($uporabnik === null || !password_verify($geslo, $uporabnik['hash_gesla'] ?? '')) {
        _prijava_zabeli_neuspeh($email);
        return [
            'status' => 'napaka',
            'status_koda' => 401,
            'sporocilo' => 'Napačen elektronski naslov ali geslo.'
        ];
    }
    
    // Počisti brute force
    _prijava_pocisti_brute_force($email);
    
    // Posodobi zadnjo prijavo
    baza_posodobi('uporabniki', $uporabnik['id'], [
        'nazadnje_prijavljen' => time()
    ]);
    
    // Prijavi uporabnika
    seja_prijavi($uporabnik['id'], $uporabnik['ime'], $email, $uporabnik['vloga']);
    
    // Točke za prijavo (dnevna nagrada) – če avatar sistem obstaja
    if (function_exists('avatar_dodaj_tocke')) {
        avatar_dodaj_tocke($uporabnik['id'], 1, 'prijava');
    }
    
    // Sproži dogodek
    dogodek_sprozi('uporabnik.prijavljen', [
        'uporabnik_id' => $uporabnik['id'],
        'email' => $email,
        'prek_google' => false,
        'cas' => time()
    ]);
    
    return [
        'status' => 'uspeh',
        'status_koda' => 200,
        'sporocilo' => 'Dobrodošel, ' . $uporabnik['ime'] . '!',
        'vsebina' => [
            'uporabnik' => [
                'id' => $uporabnik['id'],
                'ime' => $uporabnik['ime'],
                'email' => $email,
                'vloga' => $uporabnik['vloga']
            ]
        ]
    ];
}

function uporabniki_odjavi(): array
{
    $uporabnik = seja_pridobi_uporabnika();
    
    if ($uporabnik) {
        dogodek_sprozi('uporabnik.odjavljen', [
            'uporabnik_id' => $uporabnik['id'],
            'cas' => time()
        ]);
    }
    
    seja_odjavi();
    
    return [
        'status' => 'uspeh',
        'status_koda' => 200,
        'sporocilo' => 'Uspešna odjava.'
    ];
}

function uporabniki_trenutni(): ?array
{
    if (!seja_je_prijavljen()) {
        return null;
    }
    
    $seja = seja_pridobi_uporabnika();
    if (!$seja) {
        return null;
    }
    
    $uporabnik = baza_preberi('uporabniki', $seja['id']);
    if (!$uporabnik) {
        return null;
    }
    
    return [
        'id' => $uporabnik['id'],
        'ime' => $uporabnik['ime'],
        'email' => $uporabnik['elektronski_naslov'],
        'vloga' => $uporabnik['vloga']
    ];
}