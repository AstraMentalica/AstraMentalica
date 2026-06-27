<?php
/**
 * ============================================================
 * POT: SISTEM/storitve_svetov/uporabniki/uporabnik_odjava.php
 * 📅 VERZIJA: v115 (14.6.2026 18:00)
 * ============================================================
 *
 * 🏛️ NIVO: STORITEV (BUSINESS LAYER)
 *
 * 📰 NAMEN:
 *     Odjava uporabnika iz sistema.
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - uporabniki_odjavi(): array
 *
 * 📡 ODVISNOSTI:
 *     - SISTEM/kernel/jedro/04_seja.php
 *     - SISTEM/kernel/jedro/07_dogodki.php
 *
 * 🚫 PREPOVEDI:
 *     - Brez __DIR__
 *     - Brez echo, print_r, var_dump
 *
 * 📌 STATUS:
 *     Stabilno
 * ============================================================
 */
declare(strict_types=1);

defined('SISTEM_VARNOST') or die('Direkten dostop ni dovoljen.');

function uporabniki_odjavi(): array
{
    $uporabnik = null;

    if (function_exists('seja_pridobi_uporabnika')) {
        $uporabnik = seja_pridobi_uporabnika();
    }

    if ($uporabnik && function_exists('dogodek_sprozi')) {
        dogodek_sprozi('uporabnik.odjavljen', [
            'uporabnik_id' => $uporabnik['id'] ?? null,
            'cas' => time(),
        ]);
    }

    if (function_exists('seja_odjavi')) {
        seja_odjavi();
    }

    return [
        'status' => 'uspeh',
        'status_koda' => 200,
        'sporocilo' => 'Uspešna odjava.'
    ];
}