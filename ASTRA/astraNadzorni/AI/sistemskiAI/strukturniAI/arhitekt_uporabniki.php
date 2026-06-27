<?php
/**
 * ============================================================
 * POT: AI/arhitekt_uporabniki.php
 * 📅 VERZIJA: v1.0 (18.6.2026)
 * ============================================================
 *
 * 🏛️ NIVO: AI AGENT (SPECIFIČEN)
 *
 * 📰 NAMEN:
 *     Pregleda UPORABNIKI/ mapo in išče kršitve pravil.
 *     Specifičen za: prikaz/sistem/, prikaz/uporabnik/, {id}/
 *
 * 🔧 PREVERJA:
 *     - SAMO frontend (brez business logike)
 *     - prikaz/sistem/ ima prijava, registracija, odjava, profil
 *     - Uporabniške mape {id}/ imajo profil.json, PASSPORT/
 *     - Ni direktnih SQL
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

echo "🔍 ArhitektUporabniki začenja pregled UPORABNIKI/...\n";

$napake = [];
$opombe = [];

// ============================================================
// 1. PREVERI prikaz/sistem/ (obvezne datoteke)
// ============================================================
$sistemMape = $ROOT . "/UPORABNIKI/prikaz/sistem/";
$obvezneSistem = [
    "uporabnik_prijava.php",
    "uporabnik_registracija.php",
    "uporabnik_odjava.php",
    "uporabnik_profil.php"
];

foreach ($obvezneSistem as $datoteka) {
    $pot = $sistemMape . $datoteka;
    if (!file_exists($pot)) {
        $napake[] = [
            "tip" => "ustvari",
            "lokacija" => "UPORABNIKI/prikaz/sistem/$datoteka",
            "razlog" => "ARHITEKTURA.md – prikaz/sistem/ mora vsebovati osnovne uporabniške strani",
            "vpliv" => "visok",
            "predlog" => "ustvari $datoteka"
        ];
    }
}

// ============================================================
// 2. PREVERI BUSINESS LOGIKO (ne sme biti v UPORABNIKI/)
// ============================================================
$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($ROOT . "/UPORABNIKI/", RecursiveDirectoryIterator::SKIP_DOTS)
);

foreach ($iterator as $file) {
    if ($file->isFile() && pathinfo($file, PATHINFO_EXTENSION) === "php") {
        $rel = str_replace($ROOT . "/", "", $file->getPathname());
        $vsebina = file_get_contents($file->getPathname());

        // Preveri direktne SQL
        if (preg_match('/\b(SELECT|INSERT|UPDATE|DELETE)\s+.*?\bFROM\b/i', $vsebina)) {
            $napake[] = [
                "tip" => "popravek",
                "lokacija" => $rel,
                "razlog" => "USTAVA.md – UPORABNIKI ne sme direktno dostopati do podatkov",
                "vpliv" => "kritičen",
                "predlog" => "premakni logiko v SISTEM/storitve_svetov/uporabniki/"
            ];
        }

        // Preveri session_start()
        if (strpos($vsebina, "session_start()") !== false) {
            $napake[] = [
                "tip" => "popravek",
                "lokacija" => $rel,
                "razlog" => "ARHITEKTURA.md – UPORABNIKI ne sme upravljati sej",
                "vpliv" => "visok",
                "predlog" => "odstrani session_start(), uporabi SISTEM/kernel/jedro/04_seja.php"
            ];
        }
    }
}

// ============================================================
// 3. PREVERI UPORABNIŠKE MAPE {id}/
// ============================================================
$uporabniskeMape = glob($ROOT . "/UPORABNIKI/[0-9]*/", GLOB_ONLYDIR);
foreach ($uporabniskeMape as $mapa) {
    $id = basename($mapa);

    // Preveri profil.json
    if (!file_exists($mapa . "profil.json")) {
        $opombe[] = [
            "tip" => "opomba",
            "lokacija" => "UPORABNIKI/$id/profil.json",
            "razlog" => "Vsak uporabnik mora imeti profil.json",
            "vpliv" => "srednji",
            "predlog" => "ustvari profil.json z id, vloga, plan, status"
        ];
    }

    // Preveri PASSPORT/
    if (!is_dir($mapa . "PASSPORT/")) {
        $opombe[] = [
            "tip" => "opomba",
            "lokacija" => "UPORABNIKI/$id/PASSPORT/",
            "razlog" => "Vsak uporabnik mora imeti PASSPORT/ mapo",
            "vpliv" => "srednji",
            "predlog" => "ustvari PASSPORT/ z dnevnik.json, modrosti.json, ..."
        ];
    }
}

// ============================================================
// 4. SHRANI POROČILO
// ============================================================
$porocilo = [
    "datum" => date("Y-m-d H:i:s"),
    "agent" => "ArhitektUporabniki",
    "mapa" => "UPORABNIKI/",
    "napake" => $napake,
    "opombe" => $opombe,
    "skupaj_napak" => count($napake),
    "priporocilo" => count($napake) > 0
        ? "Potrebno popravilo – zaženi Koderja"
        : "UPORABNIKI/ je skladen"
];

zapisiDatoteko(
    "AI/sistemskiAI/naloge/porocila/arhitekt_uporabniki_" . date("Y-m-d_H-i-s") . ".json",
    json_encode($porocilo, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
    $ROOT
);

echo "✅ ArhitektUporabniki končal.\n";
echo "📊 Napak: " . count($napake) . ", Opomb: " . count($opombe) . "\n";