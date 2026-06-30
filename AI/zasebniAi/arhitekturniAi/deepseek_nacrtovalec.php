<?php
/**
 * ============================================================
 * POT: AI/sistemskiAI/arhitekturniAI/deepseek_nacrtovalec.php
 * VERZIJA: v2.2 (18.6.2026)
 * ============================================================
 *
 * NAMEN: Načrtovalec — globoko razmišlja, dela načrte,
 *        upošteva vizijo, strukture in pravila.
 * ============================================================
 */

declare(strict_types=1);

require_once __DIR__ . "/../../varnost.php";
require_once POT_KOREN . "pot.php";

echo "🧠 Načrtovalec v2.2 aktiven\n";

// Naloži vizijo in strukture
$vizijaDir = POT_VIZIJA;
$struktureDir = POT_STRUKTURE;

function naloziMapo(string $mapa): array {
    $vsebina = [];
    if (!is_dir($mapa)) return $vsebina;

    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($mapa, RecursiveDirectoryIterator::SKIP_DOTS)
    );

    foreach ($iterator as $datoteka) {
        if ($datoteka->isFile()) {
            $rel = str_replace(POT_KOREN, "", $datoteka->getPathname());
            $vsebina[$rel] = file_get_contents($datoteka->getPathname());
        }
    }
    return $vsebina;
}

$vizija = naloziMapo($vizijaDir);
$strukture = naloziMapo($struktureDir);

echo "📖 Naloženo: " . count($vizija) . " vizij | " . count($strukture) . " struktur\n";

/**
 * Glavna funkcija za načrtovanje
 */
function nacrtuj(string $tema, string $dodatno = "") {
    global $vizija, $strukture;

    echo "\n🧠 Načrtujem: $tema\n";
    if ($dodatno) echo "   Dodatno: $dodatno\n";

    // Tukaj kasneje lahko dodaš klic API za globlje razmišljanje
    echo "   → Upoštevam trenutno vizijo in strukture.\n";
    echo "   → Predlog bo šel prek Nadzornika v nepotrjeno/.\n";

    // Primer predloga prek Nadzornika
    nadzornik_predlagaj(
        "struktura",
        "AI/sistemskiAI/strukture/",
        "Načrt za: " . $tema,
        ["tema" => $tema, "dodatno" => $dodatno]
    );
}

echo "✅ Načrtovalec pripravljen.\n";
echo "   Uporabi funkcijo nacrtuj('tema', 'dodatne želje');\n";