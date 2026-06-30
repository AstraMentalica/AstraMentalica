<?php
/**
 * ============================================================
 * POT: ASTRA/potrdi_predlog.php
 * VERZIJA: v1.0 (18.6.2026)
 * ============================================================
 *
 * NAMEN: Enostavno orodje za pregled in potrjevanje predlogov iz nepotrjeno/
 * ============================================================
 */

declare(strict_types=1);

require_once __DIR__ . "/../pot.php";
require_once __DIR__ . "/../AI/varnost.php";

echo "📋 ASTRA Potrjevalnik\n\n";

$nepotrjenoDir = POT_NEPOTrJENO;

if (!is_dir($nepotrjenoDir)) {
    echo "❌ Mapa nepotrjeno/ še ne obstaja.\n";
    exit;
}

$mape = glob($nepotrjenoDir . "/*", GLOB_ONLYDIR);

if (empty($mape)) {
    echo "✅ Trenutno ni neodobrenih predlogov.\n";
    exit;
}

echo "Najdenih predlogov: " . count($mape) . "\n\n";

foreach ($mape as $mapa) {
    $predlogFile = $mapa . "/predlog.json";
    if (file_exists($predlogFile)) {
        $podatki = json_decode(file_get_contents($predlogFile), true);
        echo "📁 " . basename($mapa) . "\n";
        echo "   Tip: " . ($podatki['tip'] ?? '?') . "\n";
        echo "   Lokacija: " . ($podatki['lokacija'] ?? '?') . "\n";
        echo "   Opis: " . ($podatki['opis'] ?? '?') . "\n";
        echo "   ------------------------------------\n\n";
    }
}

echo "Za premik predloga iz 'nepotrjeno/' v pravo lokacijo uporabi ASTRA ali ročno kopiraj.\n";