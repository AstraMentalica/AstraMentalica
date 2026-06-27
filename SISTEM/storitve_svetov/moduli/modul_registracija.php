<?php
/**
 * ============================================================
 * POT: SISTEM/storitve/moduli/moduli_registracija.php
 * ============================================================
 * 
 * @package AstraMentalica\Storitve\Moduli
 * 
 * 📦 NAMEN:
 *     Backend logika za registracijo modulov
 * 
 * 🔧 FUNKCIJE:
 *     - modul_registriraj(string $ime, array $manifest): bool
 *     - modul_odstrani(string $ime): bool
 *     - modul_je_registriran(string $ime): bool
 *     - modul_pridobi_vse(): array
 * 
 * @author ASTRAMENTALICA
 * @version 1.0.0
 * @since FAZA 2b
 * ============================================================
 */

namespace AstraMentalica\Storitve\Moduli;

use AstraMentalica\Runtime\shramba_beri;
use AstraMentalica\Runtime\shramba_zapisi;
use AstraMentalica\Runtime\shramba_zbrisi;
use AstraMentalica\Runtime\Izjeme\NapakaValidacije;

function modul_registriraj(string $ime, array $manifest): bool
{
    // Preveri obvezna polja
    $obvezna = ['ime', 'oznaka', 'verzija', 'vloga'];
    foreach ($obvezna as $polje) {
        if (!isset($manifest[$polje])) {
            throw new NapakaValidacije("Manifest za modul '$ime' nima obveznega polja '$polje'");
        }
    }
    
    // Preveri ali modul že obstaja
    if (modul_je_registriran($ime)) {
        throw new NapakaValidacije("Modul '$ime' je že registriran");
    }
    
    // Preveri ali mapa modula obstaja
    $modul_pot = POT_MODULI . "/{$ime}";
    if (!is_dir($modul_pot)) {
        throw new NapakaValidacije("Mapa modula '$ime' ne obstaja v MODULI/");
    }
    
    // Preveri ali obstaja modul.php
    if (!file_exists($modul_pot . "/modul.php")) {
        throw new NapakaValidacije("Modul '$ime' nima vstopne točke modul.php");
    }
    
    // Dodaj dodatne podatke v manifest
    $manifest['registriran'] = time();
    $manifest['status'] = $manifest['status'] ?? 'inactive';
    $manifest['pot'] = $modul_pot;
    
    // Shrani v register modulov
    $registri = shramba_beri('sistem/registri/moduli_reg');
    $registri[$ime] = $manifest;
    
    return shramba_zapisi('sistem/registri/moduli_reg', $registri);
}

function modul_odstrani(string $ime): bool
{
    if (!modul_je_registriran($ime)) {
        return false;
    }
    
    $registri = shramba_beri('sistem/registri/moduli_reg');
    unset($registri[$ime]);
    
    return shramba_zapisi('sistem/registri/moduli_reg', $registri);
}

function modul_je_registriran(string $ime): bool
{
    $registri = shramba_beri('sistem/registri/moduli_reg');
    return isset($registri[$ime]);
}

function modul_pridobi_vse(): array
{
    return shramba_beri('sistem/registri/moduli_reg');
}

function modul_pridobi(string $ime): ?array
{
    $registri = shramba_beri('sistem/registri/moduli_reg');
    return $registri[$ime] ?? null;
}

function modul_pridobi_po_oznaki(string $oznaka): ?array
{
    $registri = shramba_beri('sistem/registri/moduli_reg');
    
    foreach ($registri as $ime => $manifest) {
        if ($manifest['oznaka'] === $oznaka) {
            return $manifest;
        }
    }
    
    return null;
}