<?php
/**
 * ============================================================
 * POT: SISTEM/kernel/jedro/04_seja.php
 * 📅 VERZIJA: v115 (14.6.2026 18:00)
 * ============================================================
 *
 * 🏛️ NIVO: KERNEL N3 (JEDRO)
 *
 * 📰 NAMEN:
 *     Upravljanje sej.
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - seja_izvedi(array $zahteva): array
 *     - seja_zacni(): void
 *     - seja_prijavi(string $id, string $ime, string $email, int $vloga): void
 *     - seja_odjavi(): void
 *     - seja_je_prijavljen(): bool
 *     - seja_pridobi_uporabnika(): ?array
 *
 * 📡 ODVISNOSTI:
 *     - pot.php
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
 *     kernel, jedro, seja
 * ============================================================
 */
declare(strict_types=1);

defined('SISTEM_VARNOST') or die('Direkten dostop ni dovoljen.');

function seja_zacni(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_name('ASTRAMENTALICA_SEJA');
        session_start();
    }
}

function seja_prijavi(string $id, string $ime, string $email, int $vloga): void
{
    seja_zacni();
    session_regenerate_id(true);
    $_SESSION['uporabnik_id'] = $id;
    $_SESSION['uporabnik_ime'] = $ime;
    $_SESSION['uporabnik_email'] = $email;
    $_SESSION['uporabnik_vloga'] = $vloga;
    $_SESSION['uporabnik_cas'] = time();
}

function seja_odjavi(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        $_SESSION = [];
        session_destroy();
    }
}

function seja_je_prijavljen(): bool
{
    seja_zacni();
    return isset($_SESSION['uporabnik_id']) && $_SESSION['uporabnik_id'] !== '';
}

function seja_pridobi_uporabnika(): ?array
{
    if (!seja_je_prijavljen()) {
        return null;
    }
    
    return [
        'id' => $_SESSION['uporabnik_id'],
        'ime' => $_SESSION['uporabnik_ime'] ?? '',
        'email' => $_SESSION['uporabnik_email'] ?? '',
        'vloga' => $_SESSION['uporabnik_vloga'] ?? VLOGA_GOST
    ];
}

function seja_izvedi(array $zahteva): array
{
    seja_zacni();
    $zahteva['uporabnik'] = seja_pridobi_uporabnika();
    $zahteva['sistem']['seja_aktivna'] = seja_je_prijavljen();
    return $zahteva;
}