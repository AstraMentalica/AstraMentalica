<?php
/**
 * ---------------------------------------------------------
 * POT: MODULI/moduli_setup_cli.php
 * VERZIJA: 1.0.0 (24.6.2026)
 * ---------------------------------------------------------
 * OPIS: CLI setup skripta za registracijo modulov
 *       Deluje brez AstraMentalica sistema
 * ---------------------------------------------------------
 */

declare(strict_types=1);

echo "=== MODULI SETUP CLI ===\n\n";

// Seznam modulov za registracijo
$moduli_za_registracijo = [
    'VibraMystica' => [
        'id' => 'vibramystica',
        'ime' => 'VibraMystica',
        'verzija' => '1.0.0',
        'oznaka' => 'vibramystica'
    ],
    'Energetica' => [
        'id' => 'energetica',
        'ime' => 'Energetica',
        'verzija' => '1.0.0',
        'oznaka' => 'energetica'
    ],
    'Celestara' => [
        'id' => 'celestara',
        'ime' => 'Celestara',
        'verzija' => '1.0.0',
        'oznaka' => 'celestara'
    ],
    'Tarot' => [
        'id' => 'tarot',
        'ime' => 'Tarot',
        'verzija' => '1.0.0',
        'oznaka' => 'tarot'
    ],
    'Runaris' => [
        'id' => 'runaris',
        'ime' => 'Runaris',
        'verzija' => '1.0.0',
        'oznaka' => 'runaris'
    ],
    'CodexDamiris' => [
        'id' => 'codexdamiris',
        'ime' => 'Codex Damiris',
        'verzija' => '1.0.0',
        'oznaka' => 'codex'
    ],
    'Oracle' => [
        'id' => 'oracle',
        'ime' => 'Oracle',
        'verzija' => '1.0.0',
        'oznaka' => 'oracle'
    ],
    'Lapidaria' => [
        'id' => 'lapidaria',
        'ime' => 'Lapidaria',
        'verzija' => '1.0.0',
        'oznaka' => 'lapidaria'
    ],
    'Devorum' => [
        'id' => 'devorum',
        'ime' => 'Devorum',
        'verzija' => '1.0.0',
        'oznaka' => 'devorum'
    ],
    'Kabbaloria' => [
        'id' => 'kabbaloria',
        'ime' => 'Kabbaloria',
        'verzija' => '1.0.0',
        'oznaka' => 'kabbaloria'
    ]
];

$rezultati = [
    'uspeh' => [],
    'napake' => [],
    'preskoceni' => []
];

$pot_moduli = __DIR__;

foreach ($moduli_za_registracijo as $ime_mape => $manifest) {
    echo "🔍 $ime_mape... ";
    
    $modul_pot = $pot_moduli . '/' . $ime_mape;
    
    // Preveri obstoj mape
    if (!is_dir($modul_pot)) {
        echo "❌ Mapa ne obstaja\n";
        $rezultati['napake'][] = "$ime_mape (mapa ne obstaja)";
        continue;
    }
    
    // Preveri modul.php
    if (!file_exists($modul_pot . '/modul.php')) {
        echo "❌ modul.php manjka\n";
        $rezultati['napake'][] = "$ime_mape (manjka modul.php)";
        continue;
    }
    
    // Preveri manifest
    $manifest_pot = $modul_pot . '/podatki/manifest.json';
    if (!file_exists($manifest_pot)) {
        echo "❌ manifest.json manjka\n";
        $rezultati['napake'][] = "$ime_mape (manjka manifest.json)";
        continue;
    }
    
    $json = json_decode(file_get_contents($manifest_pot), true);
    if (!$json || !isset($json['_id'])) {
        echo "❌ Neveljaven manifest\n";
        $rezultati['napake'][] = "$ime_mape (neveljaven manifest)";
        continue;
    }
    
    // Preveri PHP sintakso
    $sintaksa = shell_exec('php -l ' . escapeshellarg($modul_pot . '/modul.php') . ' 2>&1');
    if (strpos($sintaksa, 'No syntax errors') === false) {
        echo "❌ PHP napaka\n";
        $rezultati['napake'][] = "$ime_mape (PHP napaka)";
        continue;
    }
    
    echo "✅ Pripravljen\n";
    $rezultati['uspeh'][] = $ime_mape;
}

// Povzetek
echo "\n" . str_repeat("=", 50) . "\n";
echo "POVZETEK:\n\n";
echo "✅ Pripravljenih za registracijo: " . count($rezultati['uspeh']) . "\n";
echo "❌ Napak: " . count($rezultati['napake']) . "\n\n";

if (!empty($rezultati['uspeh'])) {
    echo "Moduli pripravljeni za registracijo:\n";
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

echo "Vsi moduli so pripravljeni. Aktivirajte jih preko:\n";
echo "  ?svet=UPORABNIKI&pot=moduli\n";