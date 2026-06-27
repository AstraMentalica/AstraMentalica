<?php
/**
 * Test za VibraMystica modul
 */

echo "=== TEST: VibraMystica modul ===\n\n";

// Test 1: Preveri datoteke
$pot = __DIR__;
$datoteke = [
    'modul.php' => 'Vstopna točka',
    'podatki/manifest.json' => 'Manifest',
    'modul_vibramystica_funkcije.php' => 'Funkcije',
    'modul_vibramystica_pravila.php' => 'Pravila',
    'modul_vibramystica_jsonbaza.php' => 'JSON baza'
];

echo "1. Preverjanje datotek:\n";
foreach ($datoteke as $datoteka => $opis) {
    $obstaja = file_exists($pot . '/' . $datoteka);
    echo "   " . ($obstaja ? '✅' : '❌') . " $opis ($datoteka)\n";
}

// Test 2: Preveri manifest
echo "\n2. Preverjanje manifesta:\n";
$manifest = json_decode(file_get_contents($pot . '/podatki/manifest.json'), true);
if ($manifest && isset($manifest['_id'])) {
    echo "   ✅ Veljaven manifest\n";
    echo "   ID: {$manifest['_id']}\n";
    echo "   Ime: {$manifest['modul']['ime']}\n";
    echo "   Status: {$manifest['modul']['status']}\n";
    echo "   Aktiviran: " . ($manifest['modul']['aktiviran'] ? 'DA' : 'NE') . "\n";
} else {
    echo "   ❌ Neveljaven manifest\n";
}

// Test 3: Preveri sintakso
echo "\n3. Preverjanje PHP sintakse:\n";
$sintaksa = shell_exec('php -l ' . escapeshellarg($pot . '/modul.php') . ' 2>&1');
echo "   " . (strpos($sintaksa, 'No syntax errors') !== false ? '✅' : '❌') . " modul.php\n";

// Test 4: Preveri funkcije
echo "\n4. Preverjanje funkcij:\n";
$vsebina = file_get_contents($pot . '/modul.php');
$funkcije = [
    'modul_vibramystica_akcija',
    '_modul_vibramystica_info',
    '_modul_vibramystica_domov'
];

foreach ($funkcije as $funk) {
    $obstaja = strpos($vsebina, "function $funk") !== false;
    echo "   " . ($obstaja ? '✅' : '❌') . " $funk()\n";
}

// Test 5: Preveri odvisnosti
echo "\n5. Preverjanje odvisnosti:\n";
$odvisnosti = [
    'Modul_Bridge' => __DIR__ . '/../Modul_Bridge/modul_bridge.php'
];

foreach ($odvisnosti as $ime => $pot_odvisnosti) {
    $obstaja = file_exists($pot_odvisnosti);
    echo "   " . ($obstaja ? '✅' : '❌') . " $ime\n";
}

// Povzetek
echo "\n=== POVZETEK ===\n";
echo "VibraMystica modul je " . ($manifest && isset($manifest['_id']) ? '✅ PRIPRAVLJEN' : '❌ POTREBUJE POPRAVKE') . "\n";