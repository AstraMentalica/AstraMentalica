<?php
/**
 * ============================================================
 * POT: AI/sistemskiAI/arhitekturniAI/deepseek_nacrtovalec.php
 * VERZIJA: v2.2 (18.6.2026)
 * ============================================================
 *
 * NAMEN: Načrtovalec - globoko razmišlja in načrtuje strukturo
 * ============================================================
 */

declare(strict_types=1);

require_once __DIR__ . "/../../varnost.php";
require_once POT_KOREN . "pot.php";

echo "🧠 Načrtovalec začenja...\n";

$vizijaDir = POT_AI . "/sistemskiAI/vizija/";
$struktureDir = POT_AI . "/sistemskiAI/strukture/";

function preberiVseDatoteke(string $mapa): array {
    $vsebina = [];
    if (!is_dir($mapa)) return $vsebina;
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($mapa, RecursiveDirectoryIterator::SKIP_DOTS));
    foreach ($iterator as $file) {
        if ($file->isFile()) {
            $rel = str_replace(POT_KOREN, "", $file->getPathname());
            $vsebina[$rel] = file_get_contents($file->getPathname());
        }
    }
    return $vsebina;
}

$vizija = preberiVseDatoteke($vizijaDir);
$strukture = preberiVseDatoteke($struktureDir);

echo "📖 Naloženo: " . count($vizija) . " vizij in " . count($strukture) . " struktur.\n";
echo "✅ Načrtovalec pripravljen.\n";
Reading pot.php file/root_fixed_extracted/root/pot.phpWrote file/root_fixed_extracted/root/pot.php