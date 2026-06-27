<?php
/**
 * ============================================================
 * POT: SISTEM/storitve/moduli/moduli_peskovnik.php
 * ============================================================
 * 
 * @package AstraMentalica\Storitve\Moduli
 * 
 * 📦 NAMEN:
 *     Backend logika za peskovnik modulov (izolacija)
 * 
 * 🔧 FUNKCIJE:
 *     - modul_peskovnik_nalozi(string $ime): ?object
 *     - modul_peskovnik_izvedi(string $ime, array $zahteva): array
 *     - modul_peskovnik_je_varen(string $ime): bool
 * 
 * @author ASTRAMENTALICA
 * @version 1.0.0
 * @since FAZA 2b
 * ============================================================
 */

namespace AstraMentalica\Storitve\Moduli;

use AstraMentalica\Runtime\Runtime\RuntimeKontekst;
use AstraMentalica\Runtime\Runtime\RuntimeOdziv;
use AstraMentalica\Runtime\Izjeme\NapakaDovoljenj;

function modul_peskovnik_nalozi(string $ime): ?object
{
    if (!modul_je_aktiven($ime)) {
        throw new NapakaDovoljenj("Modul '$ime' ni aktiven");
    }
    
    $modul_pot = POT_MODULI . "/{$ime}/modul.php";
    
    if (!file_exists($modul_pot)) {
        throw new NapakaDovoljenj("Modul '$ime' nima vstopne točke");
    }
    
    // Naložimo modul v izoliranem kontekstu
    $modul_funkcija = require $modul_pot;
    
    if (!is_callable($modul_funkcija)) {
        throw new NapakaDovoljenj("Modul '$ime' ne vrača callable funkcije");
    }
    
    return $modul_funkcija;
}

function modul_peskovnik_izvedi(string $ime, RuntimeKontekst $kontekst): RuntimeOdziv
{
    // Preveri ali ima uporabnik pravico do tega modula
    $modul = modul_pridobi($ime);
    if (!$modul) {
        return RuntimeOdziv::napaka("Modul '$ime' ne obstaja", 404);
    }
    
    $potrebna_vloga = $modul['vloga'];
    $trenutna_vloga = $kontekst->uporabnik()['vloga'] ?? 0;
    
    if ($trenutna_vloga < $potrebna_vloga) {
        return RuntimeOdziv::napaka("Nimate dostopa do modula '$ime'", 403);
    }
    
    try {
        $modul_funkcija = modul_peskovnik_nalozi($ime);
        $odziv = $modul_funkcija($kontekst);
        
        if (!$odziv instanceof RuntimeOdziv) {
            return RuntimeOdziv::napaka("Modul '$ime' je vrnil neveljaven odziv", 500);
        }
        
        return $odziv;
        
    } catch (\Throwable $e) {
        return RuntimeOdziv::napaka("Napaka v modulu '$ime': " . $e->getMessage(), 500);
    }
}

function modul_peskovnik_je_varen(string $ime): bool
{
    // Preveri ali modul nima prepovedanih vzorcev
    $modul_pot = POT_MODULI . "/{$ime}";
    
    $prepovedani_vzorci = [
        '/\bSISTEM\b/',
        '/\bPODATKI\b/',
        '/\bADAPTER\b/',
        '/\$_SESSION/',
        '/\$_GET/',
        '/\$_POST/',
        '/\bdie\b/',
        '/\bexit\b/',
        '/\becho\b/',
        //'/\bvar_dump\b/',
        '/\bprint_r\b/'
    ];
    
    $iterator = new \RecursiveIteratorIterator(
        new \RecursiveDirectoryIterator($modul_pot, \RecursiveDirectoryIterator::SKIP_DOTS)
    );
    
    foreach ($iterator as $file) {
        if ($file->getExtension() === 'php') {
            $vsebina = file_get_contents($file->getRealPath());
            foreach ($prepovedani_vzorci as $vzorec) {
                if (preg_match($vzorec, $vsebina)) {
                    return false;
                }
            }
        }
    }
    
    return true;
}

function modul_peskovnik_omejitve(string $ime): array
{
    $modul = modul_pridobi($ime);
    $privzete = [
        'max_memory' => '64MB',
        'max_execution' => 30,
        'max_queue_spawn' => 100
    ];
    
    if ($modul && isset($modul['resource_limits'])) {
        return array_merge($privzete, $modul['resource_limits']);
    }
    
    return $privzete;
}