<?php
/**
 * ============================================================
 * POT: SISTEM/storitve/moduli/moduli_runtime.php
 * ============================================================
 * 
 * @package AstraMentalica\Storitve\Moduli
 * 
 * 📦 NAMEN:
 *     Backend logika za izvajanje modulov v runtime-u
 * 
 * 🔧 FUNKCIJE:
 *     - modul_runtime_izvedi(string $ime, RuntimeKontekst $kontekst): RuntimeOdziv
 *     - modul_runtime_cache(string $ime, array $podatki, int $ttl = 3600): bool
 *     - modul_runtime_cache_preberi(string $ime, string $kljuc)
 * 
 * @author ASTRAMENTALICA
 * @version 1.0.0
 * @since FAZA 2b
 * ============================================================
 */

namespace AstraMentalica\Storitve\Moduli;

use AstraMentalica\Runtime\Runtime\RuntimeKontekst;
use AstraMentalica\Runtime\Runtime\RuntimeOdziv;
use AstraMentalica\Runtime\Jedro\cache_shrani;
use AstraMentalica\Runtime\Jedro\cache_preberi;
use AstraMentalica\Runtime\Jedro\cache_zbrisi;

function modul_runtime_izvedi(string $ime, RuntimeKontekst $kontekst): RuntimeOdziv
{
    $zacetek = microtime(true);
    
    // Preveri ali je modul aktiven
    if (!modul_je_aktiven($ime)) {
        return RuntimeOdziv::napaka("Modul '$ime' ni aktiven", 503);
    }
    
    // Preveri varnost
    if (!modul_peskovnik_je_varen($ime)) {
        return RuntimeOdziv::napaka("Modul '$ime' ni varen za izvajanje", 500);
    }
    
    // Izvedi modul
    $odziv = modul_peskovnik_izvedi($ime, $kontekst);
    
    // Dodaj runtime info
    $runtime_info = [
        'modul' => $ime,
        'cas_izvajanja' => microtime(true) - $zacetek,
        'memory_usage' => memory_get_peak_usage(true)
    ];
    
    return $odziv->zVsebino(array_merge($odziv->vsebina(), ['runtime' => $runtime_info]));
}

function modul_runtime_cache(string $ime, string $kljuc, $vrednost, int $ttl = 3600): bool
{
    $modul_cache_kljuc = "modul_{$ime}_" . md5($kljuc);
    return cache_shrani($modul_cache_kljuc, $vrednost, $ttl);
}

function modul_runtime_cache_preberi(string $ime, string $kljuc)
{
    $modul_cache_kljuc = "modul_{$ime}_" . md5($kljuc);
    return cache_preberi($modul_cache_kljuc);
}

function modul_runtime_cache_zbrisi(string $ime, string $kljuc): bool
{
    $modul_cache_kljuc = "modul_{$ime}_" . md5($kljuc);
    return cache_zbrisi($modul_cache_kljuc);
}

function modul_runtime_cache_pocisti(string $ime): void
{
    // Počisti vse cache za modul - implementacija specifična
    // Zaenkrat prazno
}