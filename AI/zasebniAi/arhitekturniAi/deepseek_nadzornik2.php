<?php
/**
 * ============================================================
 * POT: AI/sistemskiAI/arhitekturniAI/deepseek_nadzornik.php
 * VERZIJA: v2.3 (18.6.2026)
 * ============================================================
 *
 * NAMEN: Pol-avtonomni Nadzornik
 * Vse spremembe grejo v nepotrjeno/ dokler lastnik ne potrdi.
 * ============================================================
 */

declare(strict_types=1);

require_once __DIR__ . "/../../varnost.php";
require_once POT_KOREN . "pot.php";

echo "👁️‍🗨️ Nadzornik v2.3 aktiven...\n";

$NEPOTRJENO = POT_AI . "/sistemskiAI/nepotrjeno/";
$KARANTENA  = POT_AI . "/sistemskiAI/karantena/";

if (!is_dir($NEPOTRJENO)) mkdir($NEPOTRJENO, 0755, true);
if (!is_dir($KARANTENA))  mkdir($KARANTENA, 0755, true);

function nadzornik_predlagaj(string $tip, string $lokacija, string $opis, array $vsebina = []) {
    global $NEPOTRJENO;

    $timestamp = date("Y-m-d_H-i-s");
    $varna_mapa = $NEPOTRJENO . $timestamp . "_" . preg_replace('/[^a-z0-9_]/', '_', strtolower($tip));

    if (!is_dir($varna_mapa)) mkdir($varna_mapa, 0755, true);

    $predlog = [
        "id"       => $timestamp,
        "tip"      => $tip,
        "lokacija" => $lokacija,
        "opis"     => $opis,
        "cas"      => date("Y-m-d H:i:s"),
        "status"   => "nepotrjeno",
        "vsebina"  => $vsebina
    ];

    zapisiDatoteko($varna_mapa . "/predlog.json", json_encode($predlog, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), POT_KOREN);

    echo "📬 Predlog ustvarjen v: $varna_mapa\n";
    return $varna_mapa;
}

echo "✅ Nadzornik pripravljen za delo.\n";
echo "   Vse nove spremembe bodo šle v /nepotrjeno/ mapo.\n";