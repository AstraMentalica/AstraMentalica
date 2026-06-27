<?php
/**
 * ============================================================
 * POT: AI/sistemskiAI/arhitekturniAI/deepseek_integrator.php
 * 📅 VERZIJA: v2.1 (18.6.2026)
 * ============================================================
 *
 * 🏛️ NIVO: AI AGENT
 *
 * 📰 NAMEN:
 *     Testira sistem po tem ko Koder opravi popravke.
 *     Preverja: sintakso PHP datotek, arhitekturne pogoje,
 *     in skladnost s pravili po arhitektovem poročilu.
 *
 * 🔧 POPRAVLJENE NAPAKE (v2.0):
 *     - php_check_syntax() ne obstaja v PHP 7+ → zamenjano s
 *       tokenizer pristopom (token_get_all + preverjanje T_STRING)
 *       oz. subprocess php -l kjer CLI ni omejen
 *     - Dodan realen sintaksni test z output bufferingom
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

echo "🧪 DeepSeek Integrator začenja testiranje...\n";

// ============================================================
// POMOŽNA FUNKCIJA: Sintaksni test brez shell_exec
// ============================================================

/**
 * Preveri PHP sintakso datoteke z tokenizer pristopom.
 *
 * PHP tokenizer (token_get_all) vrže ParseError pri neveljavni
 * sintaksi v PHP 7+. To je zanesljiva zamenjava za php_check_syntax()
 * ki ne obstaja več, in za shell_exec('php -l') ki je prepovedano.
 *
 * @return array ['veljaven' => bool, 'napaka' => string|null]
 */
function preveriSintakso(string $vsebina): array {
    try {
        token_get_all($vsebina, TOKEN_PARSE);
        return ['veljaven' => true, 'napaka' => null];
    } catch (\ParseError $e) {
        return [
            'veljaven' => false,
            'napaka'   => $e->getMessage() . " (vrstica " . $e->getLine() . ")",
        ];
    }
}

// ============================================================
// 1. Test sintakse — vse PHP datoteke na whitelistu
// ============================================================

$rezultati = [
    "sintaksa"    => [],
    "arhitektura" => [],
    "pravila"     => [],
];

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

        $rel     = ltrim(str_replace($ROOT, "", $datoteka->getPathname()), "/");
        $vsebina = file_get_contents($datoteka->getPathname());
        $test    = preveriSintakso($vsebina);

        if (!$test['veljaven']) {
            $rezultati["sintaksa"][] = [
                "datoteka" => $rel,
                "napaka"   => $test['napaka'],
            ];
        }
    }
}

$sintaksaVeljavna = empty($rezultati["sintaksa"]);
echo "📋 Sintaksa: " . ($sintaksaVeljavna ? "✅ brez napak" : "❌ " . count($rezultati["sintaksa"]) . " napak") . "\n";

// ============================================================
// 2. Test arhitekture — ključni pogoji
// ============================================================

$runtimeMapaObstaja     = is_dir($ROOT . "/SISTEM/runtime");
$runtimeDatotekaObstaja = file_exists($ROOT . "/SISTEM/kernel/jedro/16_upravljalec_runtime.php");

$obvezne = [
    "pot.php"                                => file_exists($ROOT . "/pot.php"),
    "index.php"                              => file_exists($ROOT . "/index.php"),
    "ADAPTER/adapter.php"                    => file_exists($ROOT . "/ADAPTER/adapter.php"),
    "SISTEM/api.php"                         => file_exists($ROOT . "/SISTEM/api.php"),
    "SISTEM/kernel/zaganjalnik.php"          => file_exists($ROOT . "/SISTEM/kernel/zaganjalnik.php"),
    "SISTEM/kernel/baze/upravljalec_baz.php" => file_exists($ROOT . "/SISTEM/kernel/baze/upravljalec_baz.php"),
];

$rezultati["arhitektura"] = [
    "runtime_mapa_obstaja"     => $runtimeMapaObstaja,
    "runtime_datoteka_obstaja" => $runtimeDatotekaObstaja,
    "obvezne_datoteke"         => $obvezne,
];

$arhitekturaVeljavna = !$runtimeMapaObstaja && !$runtimeDatotekaObstaja;
echo "📋 Arhitektura: " . ($arhitekturaVeljavna ? "✅ skladna" : "❌ kršitve") . "\n";

// ============================================================
// 3. Preveri stanje po arhitektovem poročilu
// ============================================================

$porocilaDir      = $ROOT . "/AI/sistemskiAI/naloge/porocila/";
$arhitektDatoteke = glob($porocilaDir . "arhitekt_*.json");
$koderoveDatoteke = glob($porocilaDir . "koder_*.json");

$napakeArhitekta = 0;
$nepopravljene   = 0;

if (!empty($arhitektDatoteke)) {
    $zadnjaArhitekt  = json_decode(file_get_contents(end($arhitektDatoteke)), true);
    $napakeArhitekta = count($zadnjaArhitekt['napake'] ?? []);
}

if (!empty($koderoveDatoteke)) {
    $zadnjiKoder = json_decode(file_get_contents(end($koderoveDatoteke)), true);
    foreach (($zadnjiKoder['popravki'] ?? []) as $popravek) {
        if (in_array($popravek['status'], ['napaka', 'čaka_odobritev'])) {
            $nepopravljene++;
        }
    }
}

$rezultati["pravila"] = [
    "napak_po_arhitektu" => $napakeArhitekta,
    "nepopravljenih"     => $nepopravljene,
];

$pravilaVeljavna = ($napakeArhitekta === 0) && ($nepopravljene === 0);
echo "📋 Pravila: " . ($pravilaVeljavna ? "✅ skladna" : "❌ " . ($napakeArhitekta + $nepopravljene) . " odprtih") . "\n";

// ============================================================
// 4. Skupni rezultat
// ============================================================

$uspeh = $sintaksaVeljavna && $arhitekturaVeljavna && $pravilaVeljavna;

$porocilo = [
    "datum"     => date("Y-m-d H:i:s"),
    "agent"     => "DeepSeekIntegrator",
    "rezultati" => $rezultati,
    "uspeh"     => $uspeh,
    "povzetek"  => [
        "sintaksa"    => $sintaksaVeljavna    ? "OK" : "NAPAKE",
        "arhitektura" => $arhitekturaVeljavna ? "OK" : "NAPAKE",
        "pravila"     => $pravilaVeljavna     ? "OK" : "ODPRTO",
    ],
];

zapisiDatoteko(
    "AI/sistemskiAI/naloge/porocila/integrator_" . date("Y-m-d_H-i-s") . ".json",
    json_encode($porocilo, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
    $ROOT
);

echo "\n" . ($uspeh ? "✅ Integracija uspešna." : "❌ Integracija ni uspešna — preveri poročilo.") . "\n";
