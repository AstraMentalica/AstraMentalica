<?php
/**
 * ============================================================
 * POT: SISTEM/storitve_svetov/uporabniki/uporabnik_profil.php
 * 📅 VERZIJA: v115 (14.6.2026 18:00)
 * ============================================================
 *
 * 🏛️ NIVO: STORITEV (BUSINESS LAYER)
 *
 * 📰 NAMEN:
 *     Upravljanje uporabniškega profila.
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - uporabniki_profil_pridobi(?string $uporabnikId = null): array
 *     - uporabniki_profil_posodobi(array $podatki): array
 *     - uporabniki_profil_spremeni_geslo(string $staroGeslo, string $novoGeslo): array
 *
 * 📡 ODVISNOSTI:
 *     - SISTEM/kernel/jedro/04_seja.php
 *     - SISTEM/kernel/jedro/05_pravice.php
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
 *     storitev, uporabniki, profil
 * ============================================================
 */
declare(strict_types=1);

defined('SISTEM_VARNOST') or die('Direkten dostop ni dovoljen.');

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
        $uporabnikId = $_SESSION['uporabnik_id'] ?? null;
    }
    
    if (!$uporabnikId) {
        return [
            'status' => 'napaka',
            'status_koda' => 400,
            'sporocilo' => 'Manjka ID uporabnika.'
        ];
    }
    
    $uporabnik = baza_preberi('uporabniki', $uporabnikId);
    
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
    unset($uporabnik['2fa_secret']);
    
    return [
        'status' => 'uspeh',
        'status_koda' => 200,
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
    
    $uporabnikId = $_SESSION['uporabnik_id'] ?? null;
    if (!$uporabnikId) {
        return [
            'status' => 'napaka',
            'status_koda' => 400,
            'sporocilo' => 'Manjka ID uporabnika.'
        ];
    }
    
    $uporabnik = baza_preberi('uporabniki', $uporabnikId);
    if ($uporabnik === null) {
        return [
            'status' => 'napaka',
            'status_koda' => 404,
            'sporocilo' => 'Uporabnik ne obstaja.'
        ];
    }
    
    // Dovoljena polja za posodobitev
    $dovoljenaPolja = ['ime', 'telefon', 'naslov'];
    $posodobitve = [];
    
    foreach ($dovoljenaPolja as $polje) {
        if (isset($podatki[$polje])) {
            $posodobitve[$polje] = trim($podatki[$polje]);
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
        $_SESSION['uporabnik_ime'] = $posodobitve['ime'];
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
    
    $uporabnikId = $_SESSION['uporabnik_id'] ?? null;
    if (!$uporabnikId) {
        return [
            'status' => 'napaka',
            'status_koda' => 400,
            'sporocilo' => 'Manjka ID uporabnika.'
        ];
    }
    
    $uporabnik = baza_preberi('uporabniki', $uporabnikId);
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
            'sporocilo' => 'Staro geslo ni pravilno.'
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