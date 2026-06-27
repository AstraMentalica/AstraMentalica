<?php
/**
 * ============================================================
 * POT: AI/sistemskiAI/arhitekturniAI/deepseek_arhitekt.php
 * 📅 VERZIJA: v2.1 (18.6.2026)
 * ============================================================
 *
 * 🏛️ NIVO: AI AGENT
 *
 * 📰 NAMEN:
 *     Pregleda sistem in poišče kršitve pravil (USTAVA, TEMELJ,
 *     RAZVOJNI_STANDARD, JEZIKOVNI_STANDARD).
 *     Rezultat zapiše v poročilo za Koderja.
 *
 * 🔧 PREVERJANJA:
 *     - Arhitekturni pogoji (runtime, obvezne datoteke)
 *     - die() / exit() v logiki
 *     - __DIR__ izven pot.php
 *     - $_GET / $_POST direktno v logiki (ne v ADAPTER/N1)
 *     - Hardcoded poti (/var/www/, /home/, /srv/)
 *     - Manjkajoča glava datoteke (PHP header)
 *     - Datoteke daljše od 300 vrstic
 *     - echo / var_dump / print_r v jedru in storitvah
 *
 * 📡 ODVISNOSTI:
 *     - AI/varnost.php
 *
 * 🚫 PREPOVEDI:
 *     - Brez die(), exit()
 *     - Brez direktnih poti
 *     - Brez shell_exec()
 *
 * 📌 STATUS:
 *     Aktivno
 *
 * 👤 AVTOR:
 *     AI / Claude
 *
 * 🌐 JEZIK:
 *     sl
 * ============================================================
 */

declare(strict_types=1);

// arhitekturniAI/ → sistemskiAI/ → AI/ → varnost.php
require_once __DIR__ . "/../../varnost.php";

echo "🔍 DeepSeek Arhitekt začenja pregled sistema...\n";

// ============================================================
// POMOŽNA FUNKCIJA: ustvari standardno napako
// ============================================================

function napaka_ustvari(
    string $tip,
    string $lokacija,
    string $razlog,
    string $vpliv,
    string $predlog
): array {
    static $stevec = 0;
    $stevec++;
    return [
        "id"       => $stevec,
        "tip"      => $tip,       // popravek | brisanje | opomba
        "lokacija" => $lokacija,
        "razlog"   => $razlog,
        "vpliv"    => $vpliv,     // kritičen | visok | srednji | nizek
        "predlog"  => $predlog,
    ];
}

// ============================================================
// 1. Preberi pravila (samo branje — USTAVA §5)
// ============================================================

$pravila = [];
$pravilaDir = $ROOT . "/AI/sistemskiAI/pravila/";

if (is_dir($pravilaDir)) {
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($pravilaDir, RecursiveDirectoryIterator::SKIP_DOTS)
    );
    foreach ($iterator as $datoteka) {
        if ($datoteka->isFile()) {
            $rel = ltrim(str_replace($ROOT, "", $datoteka->getPathname()), "/");
            $pravila[$rel] = true; // samo beležimo da smo prebrali
        }
    }
}

echo "📄 Prebranih pravil: " . count($pravila) . "\n";

// ============================================================
// 2. Arhitekturni pogoji (path-level, brez content scana)
// ============================================================

$napake = [];

// RUNTIME je izbrisan iz sistema (TEMELJ.md)
if (is_dir($ROOT . "/SISTEM/runtime")) {
    $napake[] = napaka_ustvari(
        "brisanje",
        "SISTEM/runtime/",
        "Kršitev TEMELJ.md — mapa runtime ne sme obstajati",
        "kritičen",
        "Ročno odstrani mapo SISTEM/runtime/ (zahteva odobritev lastnika)"
    );
}

if (file_exists($ROOT . "/SISTEM/kernel/jedro/16_upravljalec_runtime.php")) {
    $napake[] = napaka_ustvari(
        "brisanje",
        "SISTEM/kernel/jedro/16_upravljalec_runtime.php",
        "Kršitev TEMELJ.md — datoteka runtime upravljalca ne sme obstajati",
        "kritičen",
        "Ročno zbriši datoteko (zahteva odobritev lastnika)"
    );
}

// ============================================================
// 3. Content scan PHP datotek v dovoljenih mapah
// ============================================================

$mape = ["ADAPTER", "SISTEM", "GLOBALNO", "MODULI", "UPORABNIKI"];

foreach ($mape as $mapa) {
    $pot = $ROOT . "/" . $mapa . "/";
    if (!is_dir($pot)) {
        echo "⚠️ Mapa ne obstaja (še): $mapa\n";
        continue;
    }

    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($pot, RecursiveDirectoryIterator::SKIP_DOTS)
    );

    foreach ($iterator as $datoteka) {
        if (!$datoteka->isFile()) continue;
        if (pathinfo($datoteka, PATHINFO_EXTENSION) !== "php") continue;

        $rel      = ltrim(str_replace($ROOT, "", $datoteka->getPathname()), "/");
        $vsebina  = file_get_contents($datoteka->getPathname());
        $vrstice  = explode("\n", $vsebina);
        $stVrstic = count($vrstice);

        // ── die() / exit() ────────────────────────────────────
        if (preg_match('/\b(die|exit)\s*\(/', $vsebina)) {
            $napake[] = napaka_ustvari(
                "popravek",
                $rel,
                "Kršitev USTAVA §3 — die()/exit() sta prepovedana",
                "visok",
                "Zamenjaj z throw new Exception()"
            );
        }

        // ── __DIR__ izven pot.php ─────────────────────────────
        if (strpos($vsebina, "__DIR__") !== false && basename($rel) !== "pot.php") {
            $napake[] = napaka_ustvari(
                "popravek",
                $rel,
                "Kršitev USTAVA §1.0 — __DIR__ dovoljen samo v pot.php",
                "srednji",
                "Zamenjaj z ustrezno POT_* konstanto"
            );
        }

        // ── $_GET / $_POST direktno (ne v ADAPTER/) ──────────
        if (
            preg_match('/\$_(GET|POST)\s*\[/', $vsebina) &&
            strpos($rel, "ADAPTER/") !== 0
        ) {
            $napake[] = napaka_ustvari(
                "popravek",
                $rel,
                "Kršitev USTAVA §3 — \$_GET/\$_POST direktno izven ADAPTER-ja",
                "visok",
                "Premakni branje vhoda v ADAPTER/N1, tu uporabi sanitizirano vrednost"
            );
        }

        // ── Hardcoded poti ────────────────────────────────────
        if (preg_match('#["\']/(var/www|home/|srv/|etc/)#', $vsebina)) {
            $napake[] = napaka_ustvari(
                "popravek",
                $rel,
                "Kršitev USTAVA §3 — hardcoded absolutna pot",
                "srednji",
                "Zamenjaj z ustrezno POT_* konstanto iz pot.php"
            );
        }

        // ── echo / var_dump / print_r v jedru in storitvah ───
        $jeJedro    = strpos($rel, "SISTEM/kernel/") === 0;
        $jeStoritve = strpos($rel, "SISTEM/storitve_svetov/") === 0;
        if (($jeJedro || $jeStoritve) && preg_match('/\b(echo|var_dump|print_r)\b/', $vsebina)) {
            $napake[] = napaka_ustvari(
                "popravek",
                $rel,
                "Kršitev RAZVOJNI_STANDARD §10 — echo/var_dump/print_r v jedru ali storitvah",
                "srednji",
                "Odstrani — SISTEM nikoli ne izpisuje neposredno"
            );
        }

        // ── Manjkajoča glava datoteke ─────────────────────────
        if (strpos($vsebina, "* POT:") === false && strpos($vsebina, "* NAMEN:") === false) {
            $napake[] = napaka_ustvari(
                "opomba",
                $rel,
                "Kršitev JEZIKOVNI_STANDARD §7 — manjka standardna glava datoteke",
                "nizek",
                "Dodaj standardno glavo (POT, VERZIJA, NIVO, NAMEN, ODVISNOSTI...)"
            );
        }

        // ── Datoteka daljša od 300 vrstic ────────────────────
        if ($stVrstic > 300) {
            $napake[] = napaka_ustvari(
                "opomba",
                $rel,
                "Kršitev USTAVA §3 — datoteka ima {$stVrstic} vrstic (max 300)",
                "nizek",
                "Razdeli po odgovornosti na manjše datoteke"
            );
        }
    }
}

// ============================================================
// 4. Shrani poročilo
// ============================================================

$porocilo = [
    "datum"            => date("Y-m-d H:i:s"),
    "agent"            => "DeepSeekArhitekt",
    "prebrana_pravila" => array_keys($pravila),
    "skupaj_napak"     => count($napake),
    "po_vplivu"        => [
        "kritičen" => count(array_filter($napake, fn($n) => $n['vpliv'] === 'kritičen')),
        "visok"    => count(array_filter($napake, fn($n) => $n['vpliv'] === 'visok')),
        "srednji"  => count(array_filter($napake, fn($n) => $n['vpliv'] === 'srednji')),
        "nizek"    => count(array_filter($napake, fn($n) => $n['vpliv'] === 'nizek')),
    ],
    "napake"           => $napake,
    "priporocilo"      => count($napake) > 0
        ? "Potrebno popravilo — zaženi Koderja"
        : "Sistem je skladen — zaženi Integratorja",
];

zapisiDatoteko(
    "AI/sistemskiAI/naloge/porocila/arhitekt_" . date("Y-m-d_H-i-s") . ".json",
    json_encode($porocilo, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
    $ROOT
);

echo "✅ Arhitekt končal.\n";
echo "📊 Napak: " . count($napake) . " ";
echo "(kritičnih: {$porocilo['po_vplivu']['kritičen']}, ";
echo "visokih: {$porocilo['po_vplivu']['visok']}, ";
echo "srednjih: {$porocilo['po_vplivu']['srednji']}, ";
echo "nizkih: {$porocilo['po_vplivu']['nizek']})\n";
echo "📄 Priporočilo: " . $porocilo['priporocilo'] . "\n";
