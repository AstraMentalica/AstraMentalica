<?php
/**
 * ---------------------------------------------------------
 * POT: MODULI/test_moduli.php
 * VERZIJA: 1.0.0 (24.6.2026)
 * ---------------------------------------------------------
 * OPIS: Test skripta za preverjanje modulov
 * ---------------------------------------------------------
 */

declare(strict_types=1);

// Samo admin lahko testira
if (!defined('SISTEM_OBSTAJA')) {
    die('Sistem ni dostopen.');
}

// Naloži potrebne funkcije
require_once __DIR__ . '/Modul_Bridge/jedro/sistem_preveri.php';
require_once __DIR__ . '/Modul_Bridge/jedro/sistemske_funkcije.php';

bridge_inicijalizacija();

// Test 1: Preveri registracijo
echo "=== TEST 1: Preveri registracijo modulov ===\n\n";

$moduli = _bridge_moduli_iz_map();
echo "Najdenih modulov v mapi: " . count($moduli) . "\n\n";

foreach ($moduli as $modul) {
    $ime = $modul['manifest']['modul']['ime'] ?? 'Neznano';
    $id = $modul['manifest']['_id'] ?? 'brez ID';
    $status = $modul['manifest']['modul']['status'] ?? 'neznan';
    $aktiviran = $modul['manifest']['modul']['aktiviran'] ? 'DA' : 'NE';
    
    echo "✅ $ime\n";
    echo "   ID: $id\n";
    echo "   Status: $status\n";
    echo "   Aktiviran: $aktiviran\n\n";
}

// Test 2: Preveri sistemski register
echo "\n=== TEST 2: Preveri sistemski register ===\n\n";

if (function_exists('modul_pridobi_vse')) {
    $registrirani = modul_pridobi_vse();
    echo "Registriranih modulov v sistemu: " . count($registrirani) . "\n\n";
    
    foreach ($registrirani as $ime => $manifest) {
        $ime_prikaz = $manifest['ime'] ?? $ime;
        $status = $manifest['status'] ?? 'neznan';
        echo "📦 $ime_prikaz ($ime) - $status\n";
    }
} else {
    echo "⚠️ Funkcija modul_pridobi_vse() ni na voljo\n";
}

// Test 3: Preveri posamezne module
echo "\n=== TEST 3: Preveri posamezne module ===\n\n";

$testni_moduli = [
    'VibraMystica',
    'Energetica',
    'Celestara',
    'Tarot',
    'Runaris'
];

foreach ($testni_moduli as $ime_modula) {
    $pot = POT_MODULI . '/' . $ime_modula;
    $modul_php = $pot . '/modul.php';
    $manifest = $pot . '/podatki/manifest.json';
    
    echo "🔍 $ime_modula:\n";
    echo "   Mapa: " . (is_dir($pot) ? '✅' : '❌') . "\n";
    echo "   modul.php: " . (file_exists($modul_php) ? '✅' : '❌') . "\n";
    echo "   manifest.json: " . (file_exists($manifest) ? '✅' : '❌') . "\n";
    
    if (file_exists($manifest)) {
        $json = json_decode(file_get_contents($manifest), true);
        if ($json && isset($json['_id'])) {
            echo "   Veljaven manifest: ✅\n";
            echo "   ID: {$json['_id']}\n";
        } else {
            echo "   Veljaven manifest: ❌ (manjka _id)\n";
        }
    }
    
    echo "\n";
}

// Test 4: Pridobi informacije o VibraMystica
echo "\n=== TEST 4: Test VibraMystica modula ===\n\n";

$vibra_pot = POT_MODULI . '/VibraMystica/modul.php';
if (file_exists($vibra_pot)) {
    echo "✅ VibraMystica modul.php obstaja\n";
    
    // Preveri ali ima akcijske funkcije
    $vsebina = file_get_contents($vibra_pot);
    
    $funkcije = [
        'modul_vibramystica_akcija',
        '_modul_vibramystica_info',
        '_modul_vibramystica_domov'
    ];
    
    foreach ($funkcije as $funk) {
        $obstaja = strpos($vsebina, "function $funk") !== false;
        echo "   " . ($obstaja ? '✅' : '❌') . " $funk\n";
    }
} else {
    echo "❌ VibraMystica modul.php ne obstaja\n";
}

// Test 5: Preveri Energetica
echo "\n=== TEST 5: Test Energetica modula ===\n\n";

$energetica_pot = POT_MODULI . '/Energetica/modul_energetica.php';
if (file_exists($energetica_pot)) {
    echo "✅ Energetica modul obstaja\n";
    
    $vsebina = file_get_contents($energetica_pot);
    $obstaja = strpos($vsebina, 'class ModulEnergetica') !== false;
    echo "   " . ($obstaja ? '✅' : '❌') . " class ModulEnergetica\n";
} else {
    echo "❌ Energetica modul ne obstaja\n";
}

// Povzetek
echo "\n=== POVZETEK ===\n\n";
echo "Skupaj modulov v mapi: " . count($moduli) . "\n";
echo "Skupaj registriranih: " . (function_exists('modul_pridobi_vse') ? count(modul_pridobi_vse()) : 0) . "\n";
echo "\nZa registracijo vseh modulov poženi: MODULI/moduli_setup.php\n";