<?php
/**
 * Batch test za vse module
 */

echo "=== BATCH TEST: Vsi moduli ===\n\n";

$moduli_za_test = [
    'VibraMystica',
    'Energetica',
    'Celestara',
    'Tarot',
    'Runaris',
    'CodexDamiris',
    'Oracle',
    'Lapidaria',
    'Devorum',
    'Kabbaloria'
];

$rezultati = [
    'uspeh' => [],
    'napake' => [],
    'preskoceni' => []
];

foreach ($moduli_za_test as $ime_modula) {
    $pot = __DIR__ . '/' . $ime_modula;
    
    echo "🔍 $ime_modula:\n";
    
    // Preveri obstoj
    if (!is_dir($pot)) {
        echo "   ❌ Mapa ne obstaja\n\n";
        $rezultati['napake'][] = "$ime_modula (mapa ne obstaja)";
        continue;
    }
    
    // Preveri modul.php
    $modul_php = $pot . '/modul.php';
    if (!file_exists($modul_php)) {
        echo "   ❌ modul.php ne obstaja\n\n";
        $rezultati['napake'][] = "$ime_modula (modul.php manjka)";
        continue;
    }
    
    // Preveri sintakso
    $sintaksa = shell_exec('php -l ' . escapeshellarg($modul_php) . ' 2>&1');
    $sintaksa_ok = strpos($sintaksa, 'No syntax errors') !== false;
    
    if (!$sintaksa_ok) {
        echo "   ❌ PHP sintaksna napaka\n";
        echo "   $sintaksa\n\n";
        $rezultati['napake'][] = "$ime_modula (sintaksna napaka)";
        continue;
    }
    
    // Preveri manifest
    $manifest_pot = $pot . '/podatki/manifest.json';
    if (!file_exists($manifest_pot)) {
        echo "   ❌ manifest.json ne obstaja\n\n";
        $rezultati['napake'][] = "$ime_modula (manifest manjka)";
        continue;
    }
    
    $manifest = json_decode(file_get_contents($manifest_pot), true);
    if (!$manifest || !isset($manifest['_id'])) {
        echo "   ❌ Neveljaven manifest (manjka _id)\n\n";
        $rezultati['napake'][] = "$ime_modula (neveljaven manifest)";
        continue;
    }
    
    // Vse ok
    echo "   ✅ Vse datoteke prisotne\n";
    echo "   ✅ Sintaksa OK\n";
    echo "   ✅ Manifest veljaven (ID: {$manifest['_id']})\n\n";
    
    $rezultati['uspeh'][] = $ime_modula;
}

// Povzetek
echo "\n" . str_repeat("=", 50) . "\n";
echo "POVZETEK:\n\n";
echo "✅ Uspešno: " . count($rezultati['uspeh']) . "\n";
echo "❌ Napake: " . count($rezultati['napake']) . "\n";
echo "⚠️ Preskočeni: " . count($rezultati['preskoceni']) . "\n\n";

if (!empty($rezultati['uspeh'])) {
    echo "Uspešni moduli:\n";
    foreach ($rezultati['uspeh'] as $modul) {
        echo "  - $modul\n";
    }
    echo "\n";
}

if (!empty($rezultati['napake'])) {
    echo "Moduli z napakami:\n";
    foreach ($rezultati['napake'] as $napaka) {
        echo "  - $napaka\n";
    }
    echo "\n";
}

echo "Za registracijo uspešnih modulov poženi: php MODULI/moduli_setup.php\n";