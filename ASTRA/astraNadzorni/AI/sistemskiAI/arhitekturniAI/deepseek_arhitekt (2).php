<?php
/**
 * ============================================================
 * POT: AI/sistemskiAI/arhitekturniAI/deepseek_arhitekt.php
 * VERZIJA: v2.3 (18.6.2026)
 * ============================================================
 *
 * NAMEN: Pametni Arhitekt — pregleda sistem in vse predloge pošlje prek Nadzornika
 * ============================================================
 */

declare(strict_types=1);

require_once __DIR__ . "/../../varnost.php";
require_once POT_KOREN . "pot.php";

echo "🔍 Arhitekt v2.3 začenja pregled...\n";

// Uporabi nove strukture in vizijo
echo "📖 Nalagam strukture in vizijo...\n";

// Tukaj bi lahko klical Načrtovalca za globlji pregled
nacrtuj("Splošni arhitekturni pregled sistema", "Upoštevaj vse obstoječe mape in pravila");

$napake = [];

// Primer: preveri runtime (kot prej)
if (is_dir(POT_SISTEM . "/runtime")) {
    nadzornik_predlagaj(
        "brisanje",
        "SISTEM/runtime/",
        "Kršitev TEMELJ.md — runtime mapa ne sme obstajati",
        ["vpliv" => "kritičen"]
    );
}

echo "✅ Arhitekt končal pregled.\n";
echo "   → Vsi predlogi so bili posredovani Nadzorniku v nepotrjeno/.\n";
echo "   → Zdaj preveri ASTRA → Nepotrjeno.\n";