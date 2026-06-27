<?php
/**
 * ============================================================
 * POT: AI/arhitekt_moduli.php
 * 📅 VERZIJA: v1.0 (18.6.2026)
 * ============================================================
 *
 * 🏛️ NIVO: AI AGENT (SPECIFIČEN)
 *
 * 📰 NAMEN:
 *     Pregleda MODULI/ mapo in išče kršitve pravil.
 *     Specifičen za: module, manifeste, izolacijo
 *
 * 🔧 PREVERJA:
 *     - Vsak modul ima modul.php
 *     - Vsak modul ima manifest.json
 *     - Modul ne vsebuje SISTEM/ poti
 *     - Modul nima dostopa do GLOBALNO/ direktno
 *     - Imena modulov PascalCase
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

echo "🔍 ArhitektModuli začenja pregled MODULI/...\n";

$napake = [];
$opombe = [];

// ============================================================
// 1. POIŠČI VSE MODULE
// ============================================================
$moduliMape = glob($ROOT . "/MODULI/*/*/", GLOB_ONLYDIR);
$moduli = [];

foreach ($moduliMape as $mapa) {
    $ime = basename($mapa);
    $kategorija = basename(dirname($mapa));
    $moduli[] = [
        "ime" => $ime,
        "kategorija" => $kategorija,
        "pot" => str_replace($ROOT . "/", "", $mapa)
    ];
}

echo "📦 Najdenih modulov: " . count($moduli) . "\n";

// ============================================================
// 2. PREVERI VSAK MODUL
// ============================================================
foreach ($moduli as $modul) {
    $pot = $ROOT . "/" . $modul["pot"];
    $rel = $modul["pot"];

    // Preveri modul.php
    if (!file_exists($pot . "modul.php")) {
        $napake[] = [
            "tip" => "ustvari",
            "lokacija" => $rel . "modul.php",
            "razlog" => "MODULI.md – vsak modul mora imeti modul.php",
            "vpliv" => "visok",
            "predlog" => "ustvari modul.php z modul_{ime}_akcija()"
        ];
    } else {
        // Preveri vsebino modul.php
        $vsebina = file_get_contents($pot . "modul.php");

        // Preveri SISTEM/ poti
        if (strpos($vsebina, "SISTEM/") !== false || strpos($vsebina, "POT_SISTEM") !== false) {
            $napake[] = [
                "tip" => "popravek",
                "lokacija" => $rel . "modul.php",
                "razlog" => "MODULI.md – modul ne sme direktno klicati SISTEM/",
                "vpliv" => "kritičen",
                "predlog" => "uporabi Modul_Bridge za komunikacijo"
            ];
        }

        // Preveri GLOBALNO/ poti
        if (strpos($vsebina, "GLOBALNO/") !== false || strpos($vsebina, "POT_GLOBALNO") !== false) {
            $napake[] = [
                "tip" => "popravek",
                "lokacija" => $rel . "modul.php",
                "razlog" => "MODULI.md – modul ne sme direktno klicati GLOBALNO/",
                "vpliv" => "visok",
                "predlog" => "uporabi Modul_Bridge za prikaz"
            ];
        }
    }

    // Preveri manifest.json
    if (!file_exists($pot . "podatki/manifest.json")) {
        $napake[] = [
            "tip" => "ustvari",
            "lokacija" => $rel . "podatki/manifest.json",
            "razlog" => "MODULI.md – vsak modul mora imeti manifest.json",
            "vpliv" => "visok",
            "predlog" => "ustvari manifest.json z ime, verzija, opis, vloga"
        ];
    } else {
        // Preveri ali je valid JSON
        $vsebina = file_get_contents($pot . "podatki/manifest.json");
        if (json_decode($vsebina) === null) {
            $napake[] = [
                "tip" => "popravek",
                "lokacija" => $rel . "podatki/manifest.json",
                "razlog" => "Neveljaven JSON v manifestu",
                "vpliv" => "visok",
                "predlog" => "popravi JSON sintakso"
            ];
        }
    }

    // Preveri ali ime modula ustreza PascalCase
    if (!preg_match('/^[A-Z][a-zA-Z]+$/', $modul["ime"])) {
        $opombe[] = [
            "tip" => "opomba",
            "lokacija" => $rel,
            "razlog" => "MODULI.md – imena modulov naj bodo PascalCase",
            "vpliv" => "nizek",
            "predlog" => "preimenuj mapo v {$modul['ime']} (če je smiselno)"
        ];
    }
}

// ============================================================
// 3. PREVERI BRIDGE
// ============================================================
if (!file_exists($ROOT . "/MODULI/Modul_Bridge/index.php")) {
    $opombe[] = [
        "tip" => "opomba",
        "lokacija" => "MODULI/Modul_Bridge/",
        "razlog" => "Modul_Bridge omogoča razvoj modulov brez sistema",
        "vpliv" => "nizek",
        "predlog" => "ustvari Modul_Bridge (priporočljivo)"
    ];
}

// ============================================================
// 4. SHRANI POROČILO
// ============================================================
$porocilo = [
    "datum" => date("Y-m-d H:i:s"),
    "agent" => "ArhitektModuli",
    "mapa" => "MODULI/",
    "moduli" => $moduli,
    "napake" => $napake,
    "opombe" => $opombe,
    "skupaj_napak" => count($napake),
    "skupaj_modulov" => count($moduli),
    "priporocilo" => count($napake) > 0
        ? "Potrebno popravilo – zaženi Koderja"
        : "MODULI/ so skladni"
];

zapisiDatoteko(
    "AI/sistemskiAI/naloge/porocila/arhitekt_moduli_" . date("Y-m-d_H-i-s") . ".json",
    json_encode($porocilo, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
    $ROOT
);

echo "✅ ArhitektModuli končal.\n";
echo "📊 Modulov: " . count($moduli) . ", Napak: " . count($napake) . "\n";