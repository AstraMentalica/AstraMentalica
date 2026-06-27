<?php
/**
 * ============================================================
 * POT: SISTEM/kernel/knjiznice/avtonalagalnik.php
 * 📅 VERZIJA: v118 (19.6.2026 00:30)
 * ============================================================
 *
 * 🏛️ NIVO: SISTEM N2 (kernel/knjiznice)
 *
 * 📰 NAMEN:
 *     Pomožni avtonalagalnik – samodejno vključi datoteke iz
 *     map po vzorcu glob(). Dopolnjuje PSR-4 avtoloader
 *     v zaganjalnik.php za datoteke, ki niso razredi.
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - avto_nalozi_mapo(string $pot, string $tip, array $izjeme): int
 *     - avto_registriraj_middleware(string $pot): int
 *     - avto_registriraj_poslusalce(string $pot): int
 *
 * 📡 ODVISNOSTI:
 *     - pot.php
 *
 * 🚫 PREPOVEDI:
 *     - Brez spl_autoload_register() (to je v zaganjalnik.php)
 *     - Brez echo, print_r, var_dump
 *     - Brez die(), exit()
 *
 * 📌 STATUS:
 *     Stabilno
 *
 * 👤 AVTOR:
 *     AstraMentalica Mojster
 *
 * 🌐 JEZIK:
 *     sl
 *
 * 🏷️ OZNAKE:
 *     kernel, knjiznice, avtonalagalnik, glob
 * ============================================================
 */
declare(strict_types=1);

defined('SISTEM_VARNOST') or die('Direkten dostop ni dovoljen.');

// ============================================================
// NALAGANJE MAP
// ============================================================

/**
 * Samodejno naloži vse .php datoteke iz mape.
 *
 * @param string $pot      Absolutna pot do mape
 * @param string $tip      Opis za dnevnik (npr. 'middleware', 'poslusalec')
 * @param array  $izjeme   Nizi ki jih preskoči (substr ujemanje)
 * @return int             Število naloženih datotek
 */
function avto_nalozi_mapo(string $pot, string $tip = 'datoteka', array $izjeme = []): int
{
    if (!is_dir($pot)) {
        if (function_exists('dnevnik_zapis')) {
            dnevnik_zapis("AVTO: Mapa ne obstaja – $pot", 'WARNING');
        }
        return 0;
    }

    $stevilo   = 0;
    $datoteke  = glob($pot . '/*.php') ?: [];

    foreach ($datoteke as $datoteka) {
        $ime = basename($datoteka);

        foreach ($izjeme as $izjema) {
            if (str_contains($datoteka, $izjema)) {
                continue 2;
            }
        }

        require_once $datoteka;
        $stevilo++;

        if (function_exists('dnevnik_zapis')) {
            dnevnik_zapis("AVTO: Naloženo ($tip) – $ime", 'DEBUG');
        }
    }

    if ($stevilo > 0 && function_exists('dnevnik_zapis')) {
        dnevnik_zapis("AVTO: Naloženih $stevilo $tip iz " . basename($pot), 'INFO');
    }

    return $stevilo;
}

// ============================================================
// REGISTRACIJA MIDDLEWARE
// ============================================================

/**
 * Registrira middleware funkcije iz mape.
 * Išče funkcije oblike middleware_{ime}_preveri().
 */
function avto_registriraj_middleware(string $pot): int
{
    if (!is_dir($pot) || !function_exists('middleware_dodaj')) {
        return 0;
    }

    $stevilo  = 0;
    $datoteke = glob($pot . '/*.php') ?: [];

    foreach ($datoteke as $datoteka) {
        $ime      = basename($datoteka, '.php');
        $funkcija = "middleware_{$ime}_preveri";

        if (function_exists($funkcija)) {
            $prednost = match (true) {
                str_contains($ime, 'auth')  => 10,
                str_contains($ime, 'csrf')  => 20,
                str_contains($ime, 'rate')  => 30,
                str_contains($ime, 'cache') => 40,
                str_contains($ime, 'rbac')  => 50,
                default                     => 60,
            };

            middleware_dodaj($funkcija, $prednost);
            $stevilo++;

            if (function_exists('dnevnik_zapis')) {
                dnevnik_zapis("AVTO: Middleware registriran – $ime (prednost $prednost)", 'DEBUG');
            }
        }
    }

    if ($stevilo > 0 && function_exists('dnevnik_zapis')) {
        dnevnik_zapis("AVTO: Registriranih $stevilo middleware-jev", 'INFO');
    }

    return $stevilo;
}

// ============================================================
// REGISTRACIJA POSLUŠALCEV
// ============================================================

/**
 * Registrira poslušalce dogodkov iz mape.
 * Razred mora implementirati ObserverVmesnik.
 */
function avto_registriraj_poslusalce(string $pot): int
{
    if (!is_dir($pot) || !function_exists('dogodek_poslusaj')) {
        return 0;
    }

    $stevilo  = 0;
    $datoteke = glob($pot . '/*.php') ?: [];

    foreach ($datoteke as $datoteka) {
        $ime = basename($datoteka, '.php');

        if (!class_exists($ime) || !interface_exists('ObserverVmesnik')) {
            continue;
        }

        try {
            $refleksija = new ReflectionClass($ime);
            if (!$refleksija->implementsInterface('ObserverVmesnik')) {
                continue;
            }

            $poslusalec = new $ime();

            foreach ($poslusalec->dogodki() as $dogodek) {
                dogodek_poslusaj($dogodek, [$poslusalec, 'posodobi'], $poslusalec->prednost());
                $stevilo++;
            }

            if (function_exists('dnevnik_zapis')) {
                dnevnik_zapis("AVTO: Poslušalec registriran – $ime", 'DEBUG');
            }
        } catch (Throwable $e) {
            if (function_exists('dnevnik_zapis')) {
                dnevnik_zapis("AVTO: Napaka pri registraciji poslušalca $ime – " . $e->getMessage(), 'WARNING');
            }
        }
    }

    if ($stevilo > 0 && function_exists('dnevnik_zapis')) {
        dnevnik_zapis("AVTO: Registriranih $stevilo poslušalcev", 'INFO');
    }

    return $stevilo;
}

if (function_exists('dnevnik_zapis')) {
    dnevnik_zapis('AVTONALAGALNIK inicializiran', 'DEBUG');
}