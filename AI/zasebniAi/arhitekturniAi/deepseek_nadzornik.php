<?php
/**
 * ============================================================
 * POT: AI/sistemskiAI/arhitekturniAI/deepseek_nadzornik.php
 * VERZIJA: v2.3 (18.6.2026)
 * ============================================================
 *
 * NAMEN: Pol-avtonomni Nadzornik
 *        Vse spremembe predlaga v /nepotrjeno/ mapo.
 *        Lastnik potrjuje ročno.
 * ============================================================
 */

declare(strict_types=1);

require_once __DIR__ . "/../../varnost.php";
require_once POT_KOREN . "pot.php";

echo "👁️‍🗨️ Nadzornik v2.3 aktiven\n";

$NEPOTRJENO = POT_NEPOTrJENO;
$KARANTENA  = POT_KARANTENA;

if (!is_dir($NEPOTRJENO)) mkdir($NEPOTRJENO, 0755, true);
if (!is_dir($KARANTENA))  mkdir($KARANTENA, 0755, true);

/**
 * Glavna funkcija za predloge
 */
function nadzornik_predlagaj(string $tip, string $lokacija, string $opis, array $podatki = []) {
    global $NEPOTRJENO;

    $timestamp = date("Y-m-d_H-i-s");
    $mapa = $NEPOTRJENO . $timestamp . "_" . preg_replace('/[^a-z0-9_-]/', '_', strtolower($tip));

    if (!is_dir($mapa)) mkdir($mapa, 0755, true);

    $predlog = [
        "id"         => $timestamp,
        "tip"        => $tip,           // ustvari | popravi | struktura | brisi
        "lokacija"   => $lokacija,
        "opis"       => $opis,
        "cas"        => date("Y-m-d H:i:s"),
        "status"     => "nepotrjeno",
        "avtor"      => "Nadzornik",
        "podatki"    => $podatki
    ];

    zapisiDatoteko($mapa . "/predlog.json", json_encode($predlog, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), POT_KOREN);

    echo "📬 [Nadzornik] Predlog ustvarjen → $mapa\n";
    echo "   Tip: $tip | $lokacija\n";
    return $mapa;
}

echo "✅ Nadzornik pripravljen za delo.\n";
echo "   → Vse nove spremembe bodo šle v nepotrjeno/\n";