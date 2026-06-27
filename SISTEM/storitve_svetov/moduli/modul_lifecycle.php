<?php
/**
 * ============================================================
 * POT: SISTEM/storitve/moduli/moduli_lifecycle.php
 * ============================================================
 * 
 * @package AstraMentalica\Storitve\Moduli
 * 
 * 📦 NAMEN:
 *     Backend logika za življenjski krog modulov (install/enable/disable/remove)
 * 
 * 🔧 FUNKCIJE:
 *     - modul_install(string $ime): bool
 *     - modul_enable(string $ime): bool
 *     - modul_disable(string $ime): bool
 *     - modul_remove(string $ime): bool
 * 
 * @author ASTRAMENTALICA
 * @version 1.0.0
 * @since FAZA 2b
 * ============================================================
 */

namespace AstraMentalica\Storitve\Moduli;

use AstraMentalica\Runtime\Izjeme\NapakaValidacije;

function modul_install(string $ime): bool
{
    // Preveri ali modul že obstaja
    if (modul_je_registriran($ime)) {
        throw new NapakaValidacije("Modul '$ime' je že registriran");
    }
    
    // Naloži manifest
    $manifest = modul_manifest_nalozi($ime);
    if (!$manifest) {
        throw new NapakaValidacije("Modul '$ime' nima veljavnega manifesta");
    }
    
    // Validiraj manifest
    modul_manifest_validiraj($manifest);
    
    // Registriraj modul
    $manifest['status'] = 'installed';
    modul_registriraj($ime, $manifest);
    
    // Ustvari podatkovno mapo za modul
    $modul_podatki_pot = POT_PODATKI . "/moduli/{$ime}";
    if (!is_dir($modul_podatki_pot)) {
        mkdir($modul_podatki_pot, 0755, true);
    }
    
    // Ustvari začasno mapo
    $modul_tmp_pot = POT_PODATKI . "/moduli/tmp/{$ime}";
    if (!is_dir($modul_tmp_pot)) {
        mkdir($modul_tmp_pot, 0755, true);
    }
    
    // Zabeleži v dnevnik
    $zgodovina_pot = $modul_podatki_pot . "/zgodovina.json";
    $zgodovina = [];
    if (file_exists($zgodovina_pot)) {
        $zgodovina = json_decode(file_get_contents($zgodovina_pot), true) ?: [];
    }
    $zgodovina[] = [
        'akcija' => 'install',
        'verzija' => $manifest['verzija'],
        'cas' => time()
    ];
    file_put_contents($zgodovina_pot, json_encode($zgodovina, JSON_PRETTY_PRINT));
    
    return true;
}

function modul_enable(string $ime): bool
{
    $modul = modul_pridobi($ime);
    
    if (!$modul) {
        throw new NapakaValidacije("Modul '$ime' ni registriran");
    }
    
    if ($modul['status'] === 'active') {
        return true;
    }
    
    $registri = shramba_beri('sistem/registri/moduli_reg');
    $registri[$ime]['status'] = 'active';
    $registri[$ime]['enabled_at'] = time();
    
    // Zabeleži v zgodovino
    $zgodovina_pot = POT_PODATKI . "/moduli/{$ime}/zgodovina.json";
    $zgodovina = [];
    if (file_exists($zgodovina_pot)) {
        $zgodovina = json_decode(file_get_contents($zgodovina_pot), true) ?: [];
    }
    $zgodovina[] = [
        'akcija' => 'enable',
        'cas' => time()
    ];
    file_put_contents($zgodovina_pot, json_encode($zgodovina, JSON_PRETTY_PRINT));
    
    return shramba_zapisi('sistem/registri/moduli_reg', $registri);
}

function modul_disable(string $ime): bool
{
    $modul = modul_pridobi($ime);
    
    if (!$modul) {
        throw new NapakaValidacije("Modul '$ime' ni registriran");
    }
    
    if ($modul['status'] === 'inactive') {
        return true;
    }
    
    $registri = shramba_beri('sistem/registri/moduli_reg');
    $registri[$ime]['status'] = 'inactive';
    
    // Zabeleži v zgodovino
    $zgodovina_pot = POT_PODATKI . "/moduli/{$ime}/zgodovina.json";
    $zgodovina = [];
    if (file_exists($zgodovina_pot)) {
        $zgodovina = json_decode(file_get_contents($zgodovina_pot), true) ?: [];
    }
    $zgodovina[] = [
        'akcija' => 'disable',
        'cas' => time()
    ];
    file_put_contents($zgodovina_pot, json_encode($zgodovina, JSON_PRETTY_PRINT));
    
    return shramba_zapisi('sistem/registri/moduli_reg', $registri);
}

function modul_remove(string $ime): bool
{
    $modul = modul_pridobi($ime);
    
    if (!$modul) {
        return false;
    }
    
    // Odstrani iz registra
    modul_odstrani($ime);
    
    // Ne brišemo fizičnih datotek – samo označimo kot odstranjen
    $modul_podatki_pot = POT_PODATKI . "/moduli/{$ime}";
    if (is_dir($modul_podatki_pot)) {
        // Shranimo arhivsko datoteko
        $archived_pot = POT_PODATKI . "/skladišče/arhiv/modul_{$ime}_" . time() . ".json";
        $zgodovina = [];
        if (file_exists($modul_podatki_pot . "/zgodovina.json")) {
            $zgodovina = json_decode(file_get_contents($modul_podatki_pot . "/zgodovina.json"), true) ?: [];
        }
        $zgodovina[] = [
            'akcija' => 'remove',
            'cas' => time()
        ];
        file_put_contents($archived_pot, json_encode($zgodovina, JSON_PRETTY_PRINT));
        
        // Izbriši podatke modula
        $this->rrmdir($modul_podatki_pot);
    }
    
    return true;
}

function modul_je_aktiven(string $ime): bool
{
    $modul = modul_pridobi($ime);
    return $modul && $modul['status'] === 'active';
}

function modul_je_instaliran(string $ime): bool
{
    return modul_je_registriran($ime);
}

// Helper za rekurzivno brisanje mape
function rrmdir(string $dir): void
{
    if (!is_dir($dir)) {
        return;
    }
    
    $files = scandir($dir);
    foreach ($files as $file) {
        if ($file !== '.' && $file !== '..') {
            $path = $dir . '/' . $file;
            if (is_dir($path)) {
                rrmdir($path);
            } else {
                unlink($path);
            }
        }
    }
    rmdir($dir);
}