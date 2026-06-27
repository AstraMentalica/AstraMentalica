<?php
/**
 * ============================================================
 * POT: AI/arhitekt_podatki.php
 * 📅 VERZIJA: v1.0 (18.6.2026)
 * ============================================================
 *
 * 🏛️ NIVO: AI AGENT (SPECIFIČEN)
 *
 * 📰 NAMEN:
 *     Pregleda PODATKI/ mapo in išče kršitve pravil.
 *     SAMO BRANJE – ne spreminja ničesar!
 *
 * 🔧 PREVERJA:
 *     - SAMO BRANJE (ne sme pisati)
 *     - .env datoteke v sef/
 *     - Registri: moduli_register.json, rbac/vloge.json
 *     - Dnevniki: sistem.log, api.log
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

echo "🔍 ArhitektPodatki začenja pregled PODATKI/ (SAMO BRANJE)...\n";

$napake = [];
$opombe = [];

// ============================================================
// 1. PREVERI .env datoteke
// ============================================================
$sefMape = $ROOT . "/PODATKI/sef/";
$obvezneEnv = [".env_sistem", ".env_api", ".env_baza"];

foreach ($obvezneEnv as $datoteka) {
    $pot = $sefMape . $datoteka;
    if (!file_exists($pot)) {
        $opombe[] = [
            "tip" => "opomba",
            "lokacija" => "PODATKI/sef/$datoteka",
            "razlog" => "Sistemske .env datoteke naj obstajajo",
            "vpliv" => "srednji",
            "predlog" => "ustvari $datoteka (če ni, bo sistem delal s privzetimi vrednostmi)"
        ];
    }
}

// ============================================================
// 2. PREVERI REGISTRE
// ============================================================
$registriMape = $ROOT . "/PODATKI/registri/";
$obvezniRegistri = ["moduli_register.json"];

foreach ($obvezniRegistri as $datoteka) {
    $pot = $registriMape . $datoteka;
    if (!file_exists($pot)) {
        $opombe[] = [
            "tip" => "opomba",
            "lokacija" => "PODATKI/registri/$datoteka",
            "razlog" => "Register modulov je potreben za delovanje",
            "vpliv" => "srednji",
            "predlog" => "ustvari $datoteka s praznim JSON objektom"
        ];
    }
}

// Preveri rbac/vloge.json
if (!file_exists($registriMape . "rbac/vloge.json")) {
    $opombe[] = [
        "tip" => "opomba",
        "lokacija" => "PODATKI/registri/rbac/vloge.json",
        "razlog" => "RBAC vloge so potrebne za avtorizacijo",
        "vpliv" => "srednji",
        "predlog" => "ustvari vloge.json z osnovnimi vlogami"
    ];
}

// ============================================================
// 3. PREVERI DNEVNIKE
// ============================================================
$dnevnikMape = $ROOT . "/PODATKI/sistem/dnevnik/";
$obvezniDnevniki = ["sistem.log", "api.log"];

foreach ($obvezniDnevniki as $datoteka) {
    $pot = $dnevnikMape . $datoteka;
    if (!file_exists($pot)) {
        $opombe[] = [
            "tip" => "opomba",
            "lokacija" => "PODATKI/sistem/dnevnik/$datoteka",
            "razlog" => "Dnevniki se ustvarijo avtomatsko ob prvem pisanju",
            "vpliv" => "nizek",
            "predlog" => "ni potrebno ročno ustvarjati"
        ];
    }
}

// ============================================================
// 4. PREVERI DA NI NAPAČNIH DATOTEK
// ============================================================
$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($ROOT . "/PODATKI/", RecursiveDirectoryIterator::SKIP_DOTS)
);

foreach ($iterator as $file) {
    if ($file->isFile() && pathinfo($file, PATHINFO_EXTENSION) === "json") {
        $rel = str_replace($ROOT . "/", "", $file->getPathname());
        $vsebina = file_get_contents($file->getPathname());

        // Preveri ali je valid JSON
        if (json_decode($vsebina) === null && json_last_error() !== JSON_ERROR_NONE) {
            $napake[] = [
                "tip" => "opomba",
                "lokacija" => $rel,
                "razlog" => "Neveljaven JSON format",
                "vpliv" => "srednji",
                "predlog" => "popravi JSON sintakso"
            ];
        }
    }
}

// ============================================================
// 5. SHRANI POROČILO
// ============================================================
$porocilo = [
    "datum" => date("Y-m-d H:i:s"),
    "agent" => "ArhitektPodatki",
    "mapa" => "PODATKI/",
    "napake" => $napake,
    "opombe" => $opombe,
    "skupaj_napak" => count($napake),
    "priporocilo" => "SAMO BRANJE – ničesar ne spreminjaj!",
    "status" => "preverjeno"
];

zapisiDatoteko(
    "AI/sistemskiAI/naloge/porocila/arhitekt_podatki_" . date("Y-m-d_H-i-s") . ".json",
    json_encode($porocilo, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
    $ROOT
);

echo "✅ ArhitektPodatki končal (SAMO BRANJE).\n";
echo "📊 Napak: " . count($napake) . ", Opomb: " . count($opombe) . "\n";