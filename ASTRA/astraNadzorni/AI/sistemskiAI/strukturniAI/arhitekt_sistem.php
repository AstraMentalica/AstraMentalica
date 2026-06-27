<?php
/**
 * ============================================================
 * POT: AI/arhitekt_sistem.php
 * 📅 VERZIJA: v1.0 (18.6.2026)
 * ============================================================
 *
 * 🏛️ NIVO: AI AGENT (SPECIFIČEN)
 *
 * 📰 NAMEN:
 *     Pregleda SISTEM/ mapo in išče kršitve pravil.
 *     Specifičen za: kernel/, jedro/, storitve_svetov/, kanali/
 *
 * 🔧 PREVERJA:
 *     - Runtime NE OBSTAJA
 *     - Jedro 01-15 (brez 16)
 *     - upravljalec_baz.php obstaja
 *     - Storitve poimenovane pravilno (uporabnik_*)
 *     - Kanali: priprava.php, vrsta.php, obdelava.php
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

echo "🔍 ArhitektSistem začenja pregled SISTEM/...\n";

$napake = [];
$opombe = [];

// ============================================================
// 1. PREVERI RUNTIME (NE OBSTAJA)
// ============================================================
if (is_dir($ROOT . "/SISTEM/runtime")) {
    $napake[] = [
        "tip" => "brisanje",
        "lokacija" => "SISTEM/runtime/",
        "razlog" => "TEMELJ.md – runtime ne obstaja",
        "vpliv" => "kritičen",
        "predlog" => "odstrani celotno mapo SISTEM/runtime/"
    ];
}

if (file_exists($ROOT . "/SISTEM/kernel/jedro/16_upravljalec_runtime.php")) {
    $napake[] = [
        "tip" => "brisanje",
        "lokacija" => "SISTEM/kernel/jedro/16_upravljalec_runtime.php",
        "razlog" => "TEMELJ.md – runtime ne obstaja",
        "vpliv" => "kritičen",
        "predlog" => "zbriši datoteko"
    ];
}

// ============================================================
// 2. PREVERI JEDRO (01-15 OBVEZNO)
// ============================================================
$jedroMape = $ROOT . "/SISTEM/kernel/jedro/";
$obvezneJedro = [
    "01_upravljalec_svetov.php",
    "02_napake.php",
    "03_varnost.php",
    "04_seja.php",
    "05_pravice.php",
    "06_cache.php",
    "07_dogodki.php",
    "08_kavlji.php",
    "09_ponudniki.php",
    "10_middleware.php",
    "11_usmerjevalnik.php",
    "12_validacija.php",
    "13_api.php",
    "14_zagon.php",
    "15_pogon.php"
];

foreach ($obvezneJedro as $datoteka) {
    $pot = $jedroMape . $datoteka;
    if (!file_exists($pot)) {
        $napake[] = [
            "tip" => "ustvari",
            "lokacija" => "SISTEM/kernel/jedro/$datoteka",
            "razlog" => "Jedro mora vsebovati vse datoteke 01-15",
            "vpliv" => "visok",
            "predlog" => "ustvari osnovno datoteko z glavo"
        ];
    }
}

// ============================================================
// 3. PREVERI UPRAVLJALEC BAZ
// ============================================================
if (!file_exists($ROOT . "/SISTEM/kernel/baze/upravljalec_baz.php")) {
    $napake[] = [
        "tip" => "ustvari",
        "lokacija" => "SISTEM/kernel/baze/upravljalec_baz.php",
        "razlog" => "TEMELJ.md – edini dostop do podatkov",
        "vpliv" => "kritičen",
        "predlog" => "ustvari upravljalec_baz.php z baza_* funkcijami"
    ];
}

// ============================================================
// 4. PREVERI KANALE
// ============================================================
$kanali = ["priprava.php", "vrsta.php", "obdelava.php"];
foreach ($kanali as $datoteka) {
    $pot = $ROOT . "/SISTEM/kanali/$datoteka";
    if (!file_exists($pot)) {
        $napake[] = [
            "tip" => "ustvari",
            "lokacija" => "SISTEM/kanali/$datoteka",
            "razlog" => "ARHITEKTURA.md – kanali so tehnični izhod",
            "vpliv" => "visok",
            "predlog" => "ustvari $datoteka z osnovno vsebino"
        ];
    }
}

// ============================================================
// 5. PREVERI API.PHP
// ============================================================
if (!file_exists($ROOT . "/SISTEM/api.php")) {
    $napake[] = [
        "tip" => "ustvari",
        "lokacija" => "SISTEM/api.php",
        "razlog" => "USTAVA.md – edini vstop v sistem",
        "vpliv" => "kritičen",
        "predlog" => "ustvari api.php z _sistem_api_route()"
    ];
}

// ============================================================
// 6. PREVERI STORITVE (uporabniki)
// ============================================================
$uporabniskeStoritve = [
    "uporabnik_prijava.php",
    "uporabnik_registracija.php",
    "uporabnik_odjava.php",
    "uporabnik_profil.php"
];

foreach ($uporabniskeStoritve as $datoteka) {
    $pot = $ROOT . "/SISTEM/storitve_svetov/uporabniki/$datoteka";
    if (!file_exists($pot)) {
        $opombe[] = [
            "tip" => "ustvari",
            "lokacija" => "SISTEM/storitve_svetov/uporabniki/$datoteka",
            "razlog" => "Poimenovanje mora biti uporabnik_* (snake_case)",
            "vpliv" => "srednji",
            "predlog" => "ustvari osnovno storitev"
        ];
    }
}

// ============================================================
// 7. SHRANI POROČILO
// ============================================================
$porocilo = [
    "datum" => date("Y-m-d H:i:s"),
    "agent" => "ArhitektSistem",
    "mapa" => "SISTEM/",
    "napake" => $napake,
    "opombe" => $opombe,
    "skupaj_napak" => count($napake),
    "priporocilo" => count($napake) > 0
        ? "Potrebno popravilo – zaženi Koderja"
        : "SISTEM/ je skladen"
];

zapisiDatoteko(
    "AI/sistemskiAI/naloge/porocila/arhitekt_sistem_" . date("Y-m-d_H-i-s") . ".json",
    json_encode($porocilo, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
    $ROOT
);

echo "✅ ArhitektSistem končal.\n";
echo "📊 Napak: " . count($napake) . ", Opomb: " . count($opombe) . "\n";