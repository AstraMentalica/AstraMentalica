<?php
/**
 * ============================================================
 * POT: AI/sistemskiAI/arhitekturniAI/deepseek_revizor.php
 * 📅 VERZIJA: v2.1 (18.6.2026)
 * ============================================================
 *
 * 🏛️ NIVO: AI AGENT
 *
 * 📰 NAMEN:
 *     Zadnji v verigi. Prebere vsa poročila (Arhitekt, Koder,
 *     Integrator) in poda skupno oceno cikla.
 *     Zazna odprte točke, nepopravljene napake, čakajoča brisanja,
 *     in poda priporočilo za naslednji korak.
 *
 * 🔧 POPRAVLJENE NAPAKE (v2.1):
 *     - Napačen if pogoj za Integrator uspeh (original: !$p['uspeh']
 *       ki je bil vedno true ker je vrednost prišla kot string)
 *     - Dodan povzetek po agentih z dejanskimi statusi
 *     - Dodan "naslednji korak" da lastnik ve kaj storiti
 *     - Poti posodobljene na AI/sistemskiAI/
 *
 * 📡 ODVISNOSTI:
 *     - AI/varnost.php
 *
 * 🚫 PREPOVEDI:
 *     - Brez die(), exit()
 *     - Brez direktnih poti
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

echo "📋 DeepSeek Revizor začenja revizijo...\n";

// ============================================================
// 1. Preberi vsa poročila agentov
// ============================================================

$porocilaDir = $ROOT . "/AI/sistemskiAI/naloge/porocila/";
$datoteke    = glob($porocilaDir . "*.json");

if (empty($datoteke)) {
    throw new Exception("Ni nobenih poročil. Najprej zaženi Arhitekta → Koderja → Integratorja.");
}

// Razvrsti po datumu (najnovejše zadnje)
sort($datoteke);

// Zberi zadnje poročilo vsakega agenta
$zadnjaPorocila = [];
foreach ($datoteke as $datoteka) {
    $vsebina  = file_get_contents($datoteka);
    $porocilo = json_decode($vsebina, true);

    if (!isset($porocilo['agent'])) continue;

    $agent                   = $porocilo['agent'];
    $zadnjaPorocila[$agent]  = $porocilo; // overwrite → ostane zadnje
}

echo "📄 Agentov s poročilom: " . count($zadnjaPorocila) . "\n";

// ============================================================
// 2. Analiziraj vsako poročilo
// ============================================================

$revizija = [
    "datum"             => date("Y-m-d H:i:s"),
    "agent"             => "DeepSeekRevizor",
    "pregledani_agenti" => array_keys($zadnjaPorocila),
    "povzetek_agentov"  => [],
    "odprte_tocke"      => [],
    "skupna_ocena"      => "ni_ocenjeno",
    "naslednji_korak"   => "",
];

// ── Arhitekt ──────────────────────────────────────────────
if (isset($zadnjaPorocila['DeepSeekArhitekt'])) {
    $p        = $zadnjaPorocila['DeepSeekArhitekt'];
    $napake   = $p['napake'] ?? [];
    $kritični = array_filter($napake, fn($n) => $n['vpliv'] === 'kritičen');
    $visoki   = array_filter($napake, fn($n) => $n['vpliv'] === 'visok');

    $revizija["povzetek_agentov"]["DeepSeekArhitekt"] = [
        "datum"        => $p['datum'] ?? "?",
        "skupaj_napak" => count($napake),
        "kritičnih"    => count($kritični),
        "visokih"      => count($visoki),
        "status"       => count($napake) === 0 ? "✅ čisto" : "❌ napake najdene",
    ];

    foreach ($kritični as $n) {
        $revizija["odprte_tocke"][] = [
            "vir"      => "Arhitekt",
            "vpliv"    => "kritičen",
            "lokacija" => $n['lokacija'],
            "razlog"   => $n['razlog'],
            "predlog"  => $n['predlog'],
        ];
    }
} else {
    $revizija["odprte_tocke"][] = [
        "vir"    => "Revizor",
        "vpliv"  => "kritičen",
        "razlog" => "Arhitektovo poročilo manjka — cikel ni bil izveden pravilno",
    ];
}

// ── Koder ─────────────────────────────────────────────────
if (isset($zadnjaPorocila['DeepSeekKoder'])) {
    $p        = $zadnjaPorocila['DeepSeekKoder'];
    $popravki = $p['popravki'] ?? [];

    $izvedeni      = array_filter($popravki, fn($pp) => $pp['status'] === 'izvedeno');
    $napakeKoder   = array_filter($popravki, fn($pp) => $pp['status'] === 'napaka');
    $cakajo        = array_filter($popravki, fn($pp) => $pp['status'] === 'čaka_odobritev');
    $brezSpremembe = array_filter($popravki, fn($pp) => $pp['status'] === 'brez_spremembe');

    $revizija["povzetek_agentov"]["DeepSeekKoder"] = [
        "datum"          => $p['datum'] ?? "?",
        "izvedenih"      => count($izvedeni),
        "napak"          => count($napakeKoder),
        "čaka_odobritev" => count($cakajo),
        "brez_spremembe" => count($brezSpremembe),
        "status"         => count($napakeKoder) === 0 ? "✅ brez napak" : "❌ " . count($napakeKoder) . " neuspešnih",
    ];

    foreach ($cakajo as $pp) {
        $revizija["odprte_tocke"][] = [
            "vir"      => "Koder",
            "vpliv"    => "srednji",
            "lokacija" => $pp['lokacija'] ?? "?",
            "razlog"   => "Brisanje čaka ročno odobritev lastnika",
            "predlog"  => "Preveri in ročno zbriši ali ohrani: " . ($pp['lokacija'] ?? "?"),
        ];
    }

    foreach ($napakeKoder as $pp) {
        $revizija["odprte_tocke"][] = [
            "vir"      => "Koder",
            "vpliv"    => "visok",
            "lokacija" => $pp['lokacija'] ?? "?",
            "razlog"   => "Popravek ni uspel: " . ($pp['sporocilo'] ?? "neznan razlog"),
            "predlog"  => "Ročni pregled datoteke",
        ];
    }
} else {
    $revizija["odprte_tocke"][] = [
        "vir"    => "Revizor",
        "vpliv"  => "visok",
        "razlog" => "Koderjevega poročila ni — ali ni bilo napak ali koder ni tekel",
    ];
}

// ── Integrator ────────────────────────────────────────────
if (isset($zadnjaPorocila['DeepSeekIntegrator'])) {
    $p = $zadnjaPorocila['DeepSeekIntegrator'];

    // POPRAVLJENO: vrednost je bool, ne string
    $uspeh = filter_var($p['uspeh'] ?? false, FILTER_VALIDATE_BOOLEAN);

    $revizija["povzetek_agentov"]["DeepSeekIntegrator"] = [
        "datum"    => $p['datum'] ?? "?",
        "uspeh"    => $uspeh,
        "povzetek" => $p['povzetek'] ?? [],
        "status"   => $uspeh ? "✅ uspešno" : "❌ neuspešno",
    ];

    if (!$uspeh) {
        $povzetek = $p['povzetek'] ?? [];
        foreach ($povzetek as $kategorija => $stanje) {
            if ($stanje !== "OK") {
                $revizija["odprte_tocke"][] = [
                    "vir"     => "Integrator",
                    "vpliv"   => "visok",
                    "razlog"  => "Integracija ni uspela: $kategorija = $stanje",
                    "predlog" => "Preveri Integratorjevo poročilo za podrobnosti",
                ];
            }
        }
    }
} else {
    $revizija["odprte_tocke"][] = [
        "vir"    => "Revizor",
        "vpliv"  => "srednji",
        "razlog" => "Integratorjevega poročila ni — zaženi Integratorja",
    ];
}

// ============================================================
// 3. Skupna ocena in naslednji korak
// ============================================================

$kritičneOdprteTocke = array_filter(
    $revizija["odprte_tocke"],
    fn($t) => $t['vpliv'] === 'kritičen'
);

$visokihOdprteTocke = array_filter(
    $revizija["odprte_tocke"],
    fn($t) => $t['vpliv'] === 'visok'
);

$integUspeh = $revizija["povzetek_agentov"]["DeepSeekIntegrator"]["uspeh"] ?? false;

if (!empty($kritičneOdprteTocke)) {
    $revizija["skupna_ocena"]    = "KRITIČNO — sistem ni skladen";
    $revizija["naslednji_korak"] = "Ročno reši kritične odprte točke, nato zaženi cikel znova: Arhitekt → Koder → Integrator → Revizor";

} elseif (!empty($visokihOdprteTocke)) {
    $revizija["skupna_ocena"]    = "OPOZORILO — visoke napake ostajajo";
    $revizija["naslednji_korak"] = "Preveri visoke odprte točke. Če so razrešene, zaženi Integratorja in Revizorja znova.";

} elseif (!$integUspeh) {
    $revizija["skupna_ocena"]    = "DELNO — integracija ni uspela";
    $revizija["naslednji_korak"] = "Preveri Integratorjevo poročilo. Zaženi Koderja za morebitne preostale popravke.";

} elseif (!empty($revizija["odprte_tocke"])) {
    $revizija["skupna_ocena"]    = "OPOMBE — manjše odprte točke";
    $revizija["naslednji_korak"] = "Sistem deluje. Odprte točke so nizke prioritete — reši ob priložnosti.";

} else {
    $revizija["skupna_ocena"]    = "USPEŠNO — sistem je skladen";
    $revizija["naslednji_korak"] = "Ni potrebnih ukrepov. Naslednji cikel zaženi po naslednjih spremembah.";
}

// ============================================================
// 4. Shrani poročilo
// ============================================================

zapisiDatoteko(
    "AI/sistemskiAI/naloge/porocila/revizor_" . date("Y-m-d_H-i-s") . ".json",
    json_encode($revizija, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
    $ROOT
);

echo "✅ Revizor končal.\n";
echo "📊 Ocena: " . $revizija["skupna_ocena"] . "\n";
echo "👉 Naslednji korak: " . $revizija["naslednji_korak"] . "\n";

if (!empty($revizija["odprte_tocke"])) {
    echo "\n⚠️  Odprte točke (" . count($revizija["odprte_tocke"]) . "):\n";
    foreach ($revizija["odprte_tocke"] as $tocka) {
        $lok = isset($tocka['lokacija']) ? " [{$tocka['lokacija']}]" : "";
        echo "   [{$tocka['vpliv']}]{$lok} {$tocka['razlog']}\n";
    }
}
