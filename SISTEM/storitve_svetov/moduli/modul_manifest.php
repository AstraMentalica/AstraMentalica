<?php
/**
 * ============================================================
 * POT: SISTEM/storitve/moduli/moduli_manifest.php
 * ============================================================
 * 
 * @package AstraMentalica\Storitve\Moduli
 * 
 * 📦 NAMEN:
 *     Backend logika za nalaganje in validacijo manifestov modulov
 * 
 * 🔧 FUNKCIJE:
 *     - modul_manifest_nalozi(string $ime): ?array
 *     - modul_manifest_validiraj(array $manifest): bool
 *     - modul_manifest_posodobi(string $ime, array $podatki): bool
 * 
 * @author ASTRAMENTALICA
 * @version 1.0.0
 * @since FAZA 2b
 * ============================================================
 */

namespace AstraMentalica\Storitve\Moduli;

use AstraMentalica\Runtime\Izjeme\NapakaValidacije;

function modul_manifest_nalozi(string $ime): ?array
{
    $manifest_pot = POT_MODULI . "/{$ime}/konfiguracija/manifest.json";
    
    if (!file_exists($manifest_pot)) {
        return null;
    }
    
    $manifest = json_decode(file_get_contents($manifest_pot), true);
    
    if (!is_array($manifest)) {
        return null;
    }
    
    return $manifest;
}

function modul_manifest_validiraj(array $manifest): bool
{
    $obvezna_polja = [
        'ime', 'oznaka', 'verzija', 'status', 'vloga', 'kanali', 'vstop'
    ];
    
    foreach ($obvezna_polja as $polje) {
        if (!isset($manifest[$polje])) {
            throw new NapakaValidacije("Manifest nima obveznega polja: $polje");
        }
    }
    
    // Preveri tip polj
    if (!is_string($manifest['ime'])) {
        throw new NapakaValidacije("Polje 'ime' mora biti string");
    }
    
    if (!is_string($manifest['oznaka'])) {
        throw new NapakaValidacije("Polje 'oznaka' mora biti string");
    }
    
    if (!is_string($manifest['verzija'])) {
        throw new NapakaValidacije("Polje 'verzija' mora biti string");
    }
    
    if (!is_int($manifest['vloga']) && !is_numeric($manifest['vloga'])) {
        throw new NapakaValidacije("Polje 'vloga' mora biti število");
    }
    
    if (!is_array($manifest['kanali'])) {
        throw new NapakaValidacije("Polje 'kanali' mora biti array");
    }
    
    if (!is_array($manifest['vstop'])) {
        throw new NapakaValidacije("Polje 'vstop' mora biti array");
    }
    
    return true;
}

function modul_manifest_posodobi(string $ime, array $podatki): bool
{
    $trenutni = modul_manifest_nalozi($ime);
    
    if (!$trenutni) {
        return false;
    }
    
    $posodobljen = array_merge($trenutni, $podatki);
    $posodobljen['verzija'] = modul_verzija_povisaj($posodobljen['verzija'], 'patch');
    
    $manifest_pot = POT_MODULI . "/{$ime}/konfiguracija/manifest.json";
    
    return file_put_contents($manifest_pot, json_encode($posodobljen, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) !== false;
}

function modul_verzija_povisaj(string $verzija, string $tip = 'patch'): string
{
    $deli = explode('.', $verzija);
    
    if (count($deli) !== 3) {
        return $verzija;
    }
    
    switch ($tip) {
        case 'major':
            $deli[0]++;
            $deli[1] = 0;
            $deli[2] = 0;
            break;
        case 'minor':
            $deli[1]++;
            $deli[2] = 0;
            break;
        case 'patch':
            $deli[2]++;
            break;
    }
    
    return implode('.', $deli);
}