<?php
/**
 * ============================================================
 * POT: AI/arhitekt_vsebina.php
 * 📅 VERZIJA: v1.0 (18.6.2026)
 * ============================================================
 *
 * 🏛️ NIVO: AI AGENT (SPECIFIČEN)
 *
 * 📰 NAMEN:
 *     Pregleda VSEBINA/ mapo in išče kršitve pravil.
 *     SAMO BRANJE – ne spreminja ničesar!
 *
 * 🔧 PREVERJA:
 *     - SAMO BRANJE (ne sme pisati)
 *     - Vsebine v Markdown (.md)
 *     - Struktura: javno/, faq/, branja/, manifest/
 *
 * 📡 ODVISNOSTI:
 *     - AI/varnost.php
 *
 * 🚫 PREPOVEDI:
 *     - SAMO BRANJE – NE SPREMINJA!
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

echo "🔍 ArhitektVsebina začenja pregled VSEBINA/ (SAMO BRANJE)...\n";

$napake = [];
$opombe = [];

// ============================================================
// 1. PREVERI OSNOVNE MAPE
// ============================================================
$obvezneMape = ["javno", "faq", "branja", "manifest"];

foreach ($obvezneMape as $mapa) {
    $pot = $ROOT . "/VSEBINA/$mapa/";
    if (!is_dir($pot)) {
        $opombe[] = [
            "tip" => "opomba",
            "lokacija" => "VSEBINA/$mapa/",
            "razlog" => "Priporočljive mape za vsebine",
            "vpliv" => "nizek",
            "predlog" => "ustvari mapo $mapa (ni obvezno)"
        ];
    }
}

// ============================================================
// 2. PREVERI MARKDOWN DATOTEKE
// ============================================================
$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($ROOT . "/VSEBINA/", RecursiveDirectoryIterator::SKIP_DOTS)
);

$mdDatoteke = 0;
foreach ($iterator as $file) {
    if ($file->isFile() && pathinfo($file, PATHINFO_EXTENSION) === "md") {
        $mdDatoteke++;
        $rel = str_replace($ROOT . "/", "", $file->getPathname());
        $vsebina = file_get_contents($file->getPathname());

        // Preveri ali ima naslov (#)
        if (!preg_match('/^#\s+.+/m', $vsebina)) {
            $opombe[] = [
                "tip" => "opomba",
                "lokacija" => $rel,
                "razlog" => "Markdown datoteka nima naslova (#)",
                "vpliv" => "nizek",
                "predlog" => "dodaj # Naslov na začetek"
            ];
        }
    }
}

if ($mdDatoteke === 0) {
    $opombe[] = [
        "tip" => "opomba",
        "lokacija" => "VSEBINA/",
        "razlog" => "Ni Markdown datotek v VSEBINA/",
        "vpliv" => "nizek",
        "predlog" => "dodaj vsebine v .md formatu"
    ];
}

// ============================================================
// 3. SHRANI POROČILO
// ============================================================
$porocilo = [
    "datum" => date("Y-m-d H:i:s"),
    "agent" => "ArhitektVsebina",
    "mapa" => "VSEBINA/",
    "napake" => $napake,
    "opombe" => $opombe,
    "skupaj_napak" => count($napake),
    "md_datotek" => $mdDatoteke,
    "priporocilo" => "SAMO BRANJE – ničesar ne spreminjaj!",
    "status" => "preverjeno"
];

zapisiDatoteko(
    "AI/sistemskiAI/naloge/porocila/arhitekt_vsebina_" . date("Y-m-d_H-i-s") . ".json",
    json_encode($porocilo, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
    $ROOT
);

echo "✅ ArhitektVsebina končal (SAMO BRANJE).\n";
echo "📊 Najdenih .md datotek: $mdDatoteke\n";