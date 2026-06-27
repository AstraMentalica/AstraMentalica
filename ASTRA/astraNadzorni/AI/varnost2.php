<?php
/**
 * ============================================================
 * POT: AI/varnost.php
 * VERZIJA: v2.3 (18.6.2026)
 * ============================================================
 */

declare(strict_types=1);

$ROOT = realpath(__DIR__ . "/..");
if ($ROOT === false) {
    $ROOT = dirname(__DIR__);
}

// Naloži sidro
require_once $ROOT . "/pot.php";

// ============================================================
// DOVOLJENO PISANJE (whitelist)
// ============================================================
$PISANJE_DOVOLJENO = [
    "ADAPTER/",
    "SISTEM/",
    "GLOBALNO/",
    "MODULI/",
    "UPORABNIKI/",
    "AI/sistemskiAI/naloge/",
    "AI/sistemskiAI/nepotrjeno/",     // ← novo
    "AI/sistemskiAI/karantena/",      // ← novo
    "AI/sistemskiAI/strukture/",      // ← novo
    "AI/sistemskiAI/vizija/",         // ← novo
];

// ============================================================
// SAMO BRANJE
// ============================================================
$SAMO_BRANJE = [
    "PODATKI/",
    "VSEBINA/",
    "AI/sistemskiAI/pravila/",
];

// ============================================================
// POSEBNA ZAŠČITA
// ============================================================
$POSEBNA_ZASCITA = [
    "pot.php",              
    "index.php",            
    "SISTEM/api.php",       
];

echo "✅ varnost.php v2.3 naložen (z novimi mapami)\n";

// ... (ostale funkcije ostanejo enake kot prej - normaliziraj, varnoRazresi, uveljavljPisanje itd.)

// Če hočeš, lahko kasneje dodam še izboljšave funkcij.