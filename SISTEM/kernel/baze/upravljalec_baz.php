<?php
/**
 * ============================================================
 * POT: SISTEM/sistem_runtime/upravljalec_baz.php
 * ============================================================
 * 
 * @package AstraMentalica\Runtime
 * 
 * 📦 NAMEN:
 *     Upravljalec baz - EDINI dostop do podatkov
 * 
 * 🔧 JAVNE FUNKCIJE:
 *     - shramba_beri(string $pot, array $pogoji = []): array
 *     - shramba_beri_enega(string $pot, string $id): ?array
 *     - shramba_zapisi(string $pot, array $podatki): bool
 *     - shramba_posodobi(string $pot, string $id, array $podatki): bool
 *     - shramba_zbrisi(string $pot, string $id): bool
 *     - transakcija_zacni(): void
 *     - transakcija_potrdi(): void
 *     - transakcija_preklici(): void
 *     - zaklep_pridobi(string $ime, int $timeout = 30): bool
 *     - zaklep_spusti(string $ime): void
 * 
 * @author ASTRAMENTALICA
 * @version 1.0.0
 * @since FAZA 2
 * ============================================================
 */

namespace AstraMentalica\Runtime;

use AstraMentalica\Runtime\Baze\AdapterJson;
use AstraMentalica\Runtime\Baze\AdapterSqlite;
use AstraMentalica\Runtime\Baze\AdapterMysql;
use AstraMentalica\Runtime\Izjeme\NapakaBaze;

// Globalni register adapterjev
$GLOBALS['SHRAMBA_ADAPTERJI'] = [];
$GLOBALS['SHRAMBA_AKTIVNI_ADAPTER'] = 'json';
$GLOBALS['SHRAMBA_V_TRANSAKCIJI'] = false;
$GLOBALS['SHRAMBA_TRANSAKCIJA_SPREMEMBE'] = [];
$GLOBALS['SHRAMBA_AKTIVNI_ZAKLEPI'] = [];

// Registracija adapterjev
$GLOBALS['SHRAMBA_ADAPTERJI']['json'] = new AdapterJson();
$GLOBALS['SHRAMBA_ADAPTERJI']['sqlite'] = new AdapterSqlite();
$GLOBALS['SHRAMBA_ADAPTERJI']['mysql'] = new AdapterMysql();

/**
 * Vrne trenutni adapter
 */
function shramba_adapter()
{
    $ime = $GLOBALS['SHRAMBA_AKTIVNI_ADAPTER'];
    
    if (!isset($GLOBALS['SHRAMBA_ADAPTERJI'][$ime])) {
        throw new NapakaBaze("Adapter '$ime' ni registriran");
    }
    
    return $GLOBALS['SHRAMBA_ADAPTERJI'][$ime];
}

/**
 * Nastavi aktivni adapter
 */
function shramba_nastavi_adapter(string $ime): void
{
    if (!isset($GLOBALS['SHRAMBA_ADAPTERJI'][$ime])) {
        throw new NapakaBaze("Adapter '$ime' ne obstaja");
    }
    $GLOBALS['SHRAMBA_AKTIVNI_ADAPTER'] = $ime;
}

// ============================================================
// JAVNE FUNKCIJE (iz kontraktov)
// ============================================================

function shramba_beri(string $pot, array $pogoji = []): array
{
    return shramba_adapter()->beri($pot, $pogoji);
}

function shramba_beri_enega(string $pot, string $id): ?array
{
    return shramba_adapter()->beri_enega($pot, $id);
}

function shramba_zapisi(string $pot, array $podatki): bool
{
    if ($GLOBALS['SHRAMBA_V_TRANSAKCIJI']) {
        $GLOBALS['SHRAMBA_TRANSAKCIJA_SPREMEMBE'][] = [
            'tip' => 'zapisi', 
            'pot' => $pot, 
            'podatki' => $podatki
        ];
        return true;
    }
    
    return shramba_adapter()->zapisi($pot, $podatki);
}

function shramba_posodobi(string $pot, string $id, array $podatki): bool
{
    if ($GLOBALS['SHRAMBA_V_TRANSAKCIJI']) {
        $GLOBALS['SHRAMBA_TRANSAKCIJA_SPREMEMBE'][] = [
            'tip' => 'posodobi', 
            'pot' => $pot, 
            'id' => $id, 
            'podatki' => $podatki
        ];
        return true;
    }
    
    return shramba_adapter()->posodobi($pot, $id, $podatki);
}

function shramba_zbrisi(string $pot, string $id): bool
{
    if ($GLOBALS['SHRAMBA_V_TRANSAKCIJI']) {
        $GLOBALS['SHRAMBA_TRANSAKCIJA_SPREMEMBE'][] = [
            'tip' => 'zbrisi', 
            'pot' => $pot, 
            'id' => $id
        ];
        return true;
    }
    
    return shramba_adapter()->zbrisi($pot, $id);
}

function transakcija_zacni(): void
{
    if ($GLOBALS['SHRAMBA_V_TRANSAKCIJI']) {
        throw new NapakaBaze('Transakcija je že aktivna');
    }
    
    $GLOBALS['SHRAMBA_V_TRANSAKCIJI'] = true;
    $GLOBALS['SHRAMBA_TRANSAKCIJA_SPREMEMBE'] = [];
    shramba_adapter()->transakcija_zacni();
}

function transakcija_potrdi(): void
{
    if (!$GLOBALS['SHRAMBA_V_TRANSAKCIJI']) {
        throw new NapakaBaze('Ni aktivne transakcije');
    }
    
    try {
        foreach ($GLOBALS['SHRAMBA_TRANSAKCIJA_SPREMEMBE'] as $sprememba) {
            switch ($sprememba['tip']) {
                case 'zapisi':
                    shramba_adapter()->zapisi($sprememba['pot'], $sprememba['podatki']);
                    break;
                case 'posodobi':
                    shramba_adapter()->posodobi($sprememba['pot'], $sprememba['id'], $sprememba['podatki']);
                    break;
                case 'zbrisi':
                    shramba_adapter()->zbrisi($sprememba['pot'], $sprememba['id']);
                    break;
            }
        }
        
        shramba_adapter()->transakcija_potrdi();
        $GLOBALS['SHRAMBA_V_TRANSAKCIJI'] = false;
        $GLOBALS['SHRAMBA_TRANSAKCIJA_SPREMEMBE'] = [];
        
    } catch (\Throwable $e) {
        transakcija_preklici();
        throw new NapakaBaze('Transakcija potrditev spodletela: ' . $e->getMessage());
    }
}

function transakcija_preklici(): void
{
    if (!$GLOBALS['SHRAMBA_V_TRANSAKCIJI']) {
        return;
    }
    
    shramba_adapter()->transakcija_preklici();
    $GLOBALS['SHRAMBA_V_TRANSAKCIJI'] = false;
    $GLOBALS['SHRAMBA_TRANSAKCIJA_SPREMEMBE'] = [];
}

function zaklep_pridobi(string $ime, int $timeout = 30): bool
{
    $kljuc = "zaklep_{$ime}";
    $pid = getmypid();
    
    if (isset($GLOBALS['SHRAMBA_AKTIVNI_ZAKLEPI'][$kljuc])) {
        return true;
    }
    
    $zaklep_pot = POT_PODATKI . "/sistem/zaklepi/{$kljuc}.lock";
    $zaklep_dir = dirname($zaklep_pot);
    
    if (!is_dir($zaklep_dir)) {
        mkdir($zaklep_dir, 0755, true);
    }
    
    $zacetek = time();
    
    while (time() - $zacetek < $timeout) {
        if (!file_exists($zaklep_pot)) {
            file_put_contents($zaklep_pot, $pid);
            $GLOBALS['SHRAMBA_AKTIVNI_ZAKLEPI'][$kljuc] = true;
            return true;
        }
        
        $lock_pid = (int)file_get_contents($zaklep_pot);
        
        // Preveri ali proces še obstaja (cross-platform)
        if (function_exists('posix_kill')) {
            // UNIX/Linux
            if (!posix_kill($lock_pid, 0)) {
                unlink($zaklep_pot);
                continue;
            }
        } else {
            // Windows - preveri ali obstaja lock starejši od 30 sekund
            $lock_time = filemtime($zaklep_pot);
            if ($lock_time && (time() - $lock_time) > 30) {
                unlink($zaklep_pot);
                continue;
            }
        }
        
        usleep(100000);
    }
    
    return false;
}

function zaklep_spusti(string $ime): void
{
    $kljuc = "zaklep_{$ime}";
    $zaklep_pot = POT_PODATKI . "/sistem/zaklepi/{$kljuc}.lock";
    
    if (file_exists($zaklep_pot)) {
        unlink($zaklep_pot);
    }
    
    unset($GLOBALS['SHRAMBA_AKTIVNI_ZAKLEPI'][$kljuc]);
}