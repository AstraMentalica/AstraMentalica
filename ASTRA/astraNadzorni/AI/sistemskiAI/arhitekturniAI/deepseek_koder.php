<?php
/**
 * ============================================================
 * POT: AI/sistemskiAI/arhitekturniAI/deepseek_koder.php
 * 📅 VERZIJA: v2.1 (18.6.2026)
 * ============================================================
 *
 * 🏛️ NIVO: AI AGENT
 *
 * 📰 NAMEN:
 *     Prebere arhitektovo poročilo in popravi najdene napake.
 *     Vsaka sprememba dobi backup + unified diff (patch datoteka).
 *
 * 🔧 POPRAVLJENE NAPAKE (v2.1):
 *     - Regex bug: dvojni preg_replace je delal neveljavno PHP sintakso
 *     - die() in exit() zamenjano s throw new Exception()
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

echo "🔧 DeepSeek Koder začenja popravljanje...\n";

// ============================================================
// 1. Preberi zadnje arhitektovo poročilo
// ============================================================

$porocilaDir = $ROOT . "/AI/sistemskiAI/naloge/porocila/";
$datoteke    = glob($porocilaDir . "arhitekt_*.json");

if (empty($datoteke)) {
    throw new Exception("Ni arhitektovega poročila. Najprej zaženi Arhitekta.");
}

$zadnja   = end($datoteke);
$porocilo = json_decode(file_get_contents($zadnja), true);

if (empty($porocilo['napake'])) {
    echo "✅ Ni napak za popravit.\n";
    return; // namesto exit(0) — varno zaključi izvajanje brez prekinitve
}

echo "📄 Napak za popravit: " . count($porocilo['napake']) . "\n";

// ============================================================
// 2. Popravi napake
// ============================================================

$popravki = [];

foreach ($porocilo['napake'] as $napaka) {
    $id       = $napaka['id'];
    $lokacija = $napaka['lokacija'];
    $tip      = $napaka['tip'];

    try {

        // Backup pred vsako spremembo
        $backup = ustvariBackup($lokacija, $ROOT);

        // --------------------------------------------------------
        // BRISANJE — samo zabeleži, ne briši avtomatsko
        // Brisanje zahteva eksplicitno odobritev (USTAVA §5)
        // --------------------------------------------------------
        if ($tip === "brisanje") {
            $popravki[] = [
                "id"       => $id,
                "akcija"   => "brisanje_pripravljeno",
                "lokacija" => $lokacija,
                "backup"   => $backup,
                "status"   => "čaka_odobritev",
                "opomba"   => "Brisanje zahteva ročno potrditev lastnika.",
            ];
            continue;
        }

        // --------------------------------------------------------
        // POPRAVEK — preberi, popravi, zapiši, patch
        // --------------------------------------------------------
        if ($tip === "popravek") {
            $original    = preberiDatoteko($lokacija, $ROOT);
            $modificiran = $original;

            // Popravi die() → throw new Exception()
            if (strpos($modificiran, "die(") !== false) {
                $modificiran = preg_replace(
                    '/\bdie\s*\(([^)]*)\)\s*;/',
                    'throw new Exception($1);',
                    $modificiran
                );
            }

            // Popravi exit() → throw new Exception()
            if (strpos($modificiran, "exit(") !== false) {
                $modificiran = preg_replace(
                    '/\bexit\s*\(([^)]*)\)\s*;/',
                    'throw new Exception($1);',
                    $modificiran
                );
            }

            // Popravi __DIR__ → POT_KOREN (razen v pot.php)
            if (
                strpos($modificiran, "__DIR__") !== false &&
                basename($lokacija) !== "pot.php"
            ) {
                $modificiran = str_replace("__DIR__", "POT_KOREN", $modificiran);
            }

            // Zapiši samo če je dejansko sprememba
            if ($modificiran === $original) {
                $popravki[] = [
                    "id"       => $id,
                    "akcija"   => "popravek",
                    "lokacija" => $lokacija,
                    "status"   => "brez_spremembe",
                    "opomba"   => "Vsebina enaka po popravku — ni bilo kaj spremeniti.",
                ];
                continue;
            }

            zapisiDatoteko($lokacija, $modificiran, $ROOT);

            // Shrani patch datoteko
            $patchMapa = $ROOT . "/AI/sistemskiAI/naloge/patch/";
            if (!is_dir($patchMapa)) {
                mkdir($patchMapa, 0755, true);
            }

            $imePatch      = str_replace("/", "_", $lokacija) . ".patch_" . date("Y-m-d_H-i-s");
            $patchDatoteka = $patchMapa . $imePatch;
            $diff          = ustvariDiff($original, $modificiran, basename($lokacija));
            file_put_contents($patchDatoteka, $diff);

            $popravki[] = [
                "id"       => $id,
                "akcija"   => "popravek",
                "lokacija" => $lokacija,
                "backup"   => $backup,
                "patch"    => $patchDatoteka,
                "status"   => "izvedeno",
            ];
        }

    } catch (Exception $e) {
        $popravki[] = [
            "id"        => $id,
            "lokacija"  => $lokacija,
            "status"    => "napaka",
            "sporocilo" => $e->getMessage(),
        ];
    }
}

// ============================================================
// 3. Shrani poročilo
// ============================================================

$porociloKoder = [
    "datum"            => date("Y-m-d H:i:s"),
    "agent"            => "DeepSeekKoder",
    "skupaj_popravkov" => count($popravki),
    "popravki"         => $popravki,
];

zapisiDatoteko(
    "AI/sistemskiAI/naloge/porocila/koder_" . date("Y-m-d_H-i-s") . ".json",
    json_encode($porociloKoder, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
    $ROOT
);

echo "✅ Koder končal.\n";
echo "📄 Popravkov: " . count($popravki) . "\n";
