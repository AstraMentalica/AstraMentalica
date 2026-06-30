<?php
/**
 * ============================================================
 * POT: AI/arhitekt_globalno.php
 * 📅 VERZIJA: v1.0 (18.6.2026)
 * ============================================================
 *
 * 🏛️ NIVO: AI AGENT (SPECIFIČEN)
 *
 * 📰 NAMEN:
 *     Pregleda GLOBALNO/ mapo in išče kršitve pravil.
 *     Specifičen za: frontend/, render/, vmesnik/
 *
 * 🔧 PREVERJA:
 *     - SAMO frontend (brez business logike)
 *     - Render: glava.php, noga.php, navigacija.php, domov.php
 *     - CSS v slovenščini (kebab-case)
 *     - Ni session_start() v render/
 *
 * 📡 ODVISNOSTI:
 *     - AI/varnost.php
 *
 * 🚫 PREPOVEDI:
 *     - Brez spreminjanja
 *
 * 📌 STATUS:
 *     Aktivno
 *
 * 👤 AVTOR:
 *     AI / DeepSeek
 *
 * 🌐 JEZIK:
 *     sl
 * ============================================================
 */

declare(strict_types=1);

require_once __DIR__ . "/../../varnost.php";

echo "🔍 ArhitektGlobalno začenja pregled GLOBALNO/...\n";

$napake = [];
$opombe = [];

// ============================================================
// 1. PREVERI RENDER (obvezne datoteke)
// ============================================================
$renderMape = $ROOT . "/GLOBALNO/render/";
$obvezneRender = ["glava.php", "noga.php", "navigacija.php", "domov.php"];

foreach ($obvezneRender as $datoteka) {
    $pot = $renderMape . $datoteka;
    if (!file_exists($pot)) {
        $napake[] = [
            "tip" => "ustvari",
            "lokacija" => "GLOBALNO/render/$datoteka",
            "razlog" => "ARHITEKTURA.md – render mora vsebovati osnovne dele",
            "vpliv" => "visok",
            "predlog" => "ustvari $datoteka"
        ];
    }
}

// ============================================================
// 2. PREVERI BUSINESS LOGIKO (ne sme biti v GLOBALNO/)
// ============================================================
$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($ROOT . "/GLOBALNO/", RecursiveDirectoryIterator::SKIP_DOTS)
);

foreach ($iterator as $file) {
    if ($file->isFile() && pathinfo($file, PATHINFO_EXTENSION) === "php") {
        $rel = str_replace($ROOT . "/", "", $file->getPathname());
        $vsebina = file_get_contents($file->getPathname());

        // Preveri session_start()
        if (strpos($vsebina, "session_start()") !== false) {
            $napake[] = [
                "tip" => "popravek",
                "lokacija" => $rel,
                "razlog" => "ARHITEKTURA.md – GLOBALNO ne sme upravljati sej",
                "vpliv" => "visok",
                "predlog" => "odstrani session_start(), uporabi SISTEM/kernel/jedro/04_seja.php"
            ];
        }

        // Preveri direktne SQL
        if (preg_match('/\b(SELECT|INSERT|UPDATE|DELETE)\s+.*?\bFROM\b/i', $vsebina)) {
            $napake[] = [
                "tip" => "popravek",
                "lokacija" => $rel,
                "razlog" => "USTAVA.md – GLOBALNO ne sme direktno dostopati do podatkov",
                "vpliv" => "kritičen",
                "predlog" => "premakni logiko v SISTEM/storitve_svetov/"
            ];
        }
    }
}

// ============================================================
// 3. PREVERI CSS (slovenska imena)
// ============================================================
$cssMape = $ROOT . "/GLOBALNO/vmesnik/css/";
if (is_dir($cssMape)) {
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($cssMape, RecursiveDirectoryIterator::SKIP_DOTS)
    );
    foreach ($iterator as $file) {
        if ($file->isFile() && pathinfo($file, PATHINFO_EXTENSION) === "css") {
            $rel = str_replace($ROOT . "/", "", $file->getPathname());
            $vsebina = file_get_contents($file->getPathname());

            // Preveri angleška imena razredov
            if (preg_match('/\.([a-zA-Z]+[-_][a-zA-Z]+)/', $vsebina, $matches)) {
                foreach ($matches as $match) {
                    if (!preg_match('/[čšžČŠŽ]/', $match)) {
                        $opombe[] = [
                            "tip" => "opomba",
                            "lokacija" => $rel,
                            "razlog" => "JEZIKOVNI_STANDARD.md – CSS razredi morajo biti slovenski (kebab-case)",
                            "vpliv" => "nizek",
                            "predlog" => "preimenuj razred $match v slovensko"
                        ];
                        break;
                    }
                }
            }
        }
    }
}

// ============================================================
// 4. SHRANI POROČILO
// ============================================================
$porocilo = [
    "datum" => date("Y-m-d H:i:s"),
    "agent" => "ArhitektGlobalno",
    "mapa" => "GLOBALNO/",
    "napake" => $napake,
    "opombe" => $opombe,
    "skupaj_napak" => count($napake),
    "priporocilo" => count($napake) > 0
        ? "Potrebno popravilo – zaženi Koderja"
        : "GLOBALNO/ je skladen"
];

zapisiDatoteko(
    "AI/sistemskiAI/naloge/porocila/arhitekt_globalno_" . date("Y-m-d_H-i-s") . ".json",
    json_encode($porocilo, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
    $ROOT
);

echo "✅ ArhitektGlobalno končal.\n";
echo "📊 Napak: " . count($napake) . ", Opomb: " . count($opombe) . "\n";