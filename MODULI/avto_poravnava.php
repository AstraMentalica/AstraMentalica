<?php
/**
 * ============================================================
 * POT: MODULI/avto_poravnava.php
 * VERZIJA: 1.0.0 (24.6.2026)
 * ============================================================
 *
 * 🏛️ NIVO: MODULI
 *
 * 📰 NAMEN:
 *     Avtomatska poravnava vseh modulov
 *     Preveri vsak modul in po potrebi ustvari:
 *     - modul.php (če manjka)
 *     - podatki/manifest.json (če manjka)
 *
 * 🔧 DELOVANJE:
 *     1. Skenira vse mape v MODULI/
 *     2. Preskoči Modul_Bridge, SVETOVI, TemplateModul, skrite mape
 *     3. Ugotovi id-je iz obstoječih datotek
 *     4. Ustvari manjkajoče datoteke
 *
 * 👤 AVTOR:
 *     AstraMentalica Mojster
 *
 * 🌐 JEZIK:
 *     sl
 * ============================================================
 */

declare(strict_types=1);

echo "=== AVTOMATSKA PORAVNAVA MODULOV ===\n\n";

$pot_moduli = __DIR__;
$preskoci = ['Modul_Bridge', 'SVETOVI', 'TemplateModul', 'desktop.ini'];
$preskoci_prefix = ['.', 'modul', 'test_', 'debug_', 'avto_'];

$rezultati = [
    'ze_ok' => [],
    'ustvarjen_modul_php' => [],
    'ustvarjen_manifest' => [],
    'napake' => []
];

// Skeniraj vse mape
$mappe = glob($pot_moduli . '/*', GLOB_ONLYDIR);

foreach ($mappe as $pot_mape) {
    $ime = basename($pot_mape);
    
    // Preskoči
    if (in_array($ime, $preskoci)) continue;
    $preskoci_me = false;
    foreach ($preskoci_prefix as $prefix) {
        if (str_starts_with($ime, $prefix)) { $preskoci_me = true; break; }
    }
    if ($preskoci_me) continue;
    
    echo "🔍 $ime... ";
    
    $modul_php = $pot_mape . '/modul.php';
    $manifest_pot = $pot_mape . '/podatki';
    $manifest_json = $manifest_pot . '/manifest.json';
    
    $ima_modul_php = file_exists($modul_php);
    $ima_manifest = file_exists($manifest_json);
    
    // Ugotovi id
    $id = '';
    
    // Poskusi iz obstoječega modul.php
    if ($ima_modul_php) {
        $vsebina = file_get_contents($modul_php);
        if (preg_match("/'id'\s*=>\s*'([^']+)'/", $vsebina, $m)) {
            $id = $m[1];
        } elseif (preg_match("/function modul_([a-z]+)_akcija/", $vsebina, $m)) {
            $id = strtolower($m[1]);
        }
    }
    
    // Poskusi iz obstoječega manifesta
    if (empty($id) && $ima_manifest) {
        $json = json_decode(file_get_contents($manifest_json), true);
        if ($json && isset($json['_id'])) {
            $id = $json['_id'];
        } elseif ($json && isset($json['modul']['id'])) {
            $id = $json['modul']['id'];
        }
    }
    
    // Generiraj id iz imena mape
    if (empty($id)) {
        $id = strtolower($ime);
        $id = preg_replace('/[^a-z0-9]/', '', $id);
    }
    
    // Ustvari modul.php če manjka
    if (!$ima_modul_php) {
        $modul_content = generiraj_modul_php($id, $ime);
        file_put_contents($modul_php, $modul_content);
        $rezultati['ustvarjen_modul_php'][] = $ime;
        $ima_modul_php = true;
        echo "📝 modul.php+";
    }
    
    // Ustvari manifest če manjka
    if (!$ima_manifest) {
        if (!is_dir($manifest_pot)) {
            mkdir($manifest_pot, 0755, true);
        }
        $manifest_content = generiraj_manifest($id, $ime);
        file_put_contents($manifest_json, $manifest_content);
        $rezultati['ustvarjen_manifest'][] = $ime;
        echo "📝 manifest.json+";
    }
    
    // Preveri PHP sintakso
    if ($ima_modul_php) {
        $sintaksa = shell_exec('php -l ' . escapeshellarg($modul_php) . ' 2>&1');
        if (strpos($sintaksa, 'No syntax errors') === false) {
            echo " ❌ PHP napaka: " . trim(str_replace('PHP Fatal error:', '', explode("\n", $sintaksa)[0] ?? ''));
            $rezultati['napake'][] = "$ime (PHP: $sintaksa)";
        } else {
            echo " ✅";
            if (!in_array($ime, $rezultati['ze_ok']) && !in_array($ime, $rezultati['ustvarjen_modul_php'])) {
                $rezultati['ze_ok'][] = $ime;
            }
        }
    }
    
    echo "\n";
}

// Povzetek
echo "\n" . str_repeat("=", 60) . "\n";
echo "POVZETEK:\n\n";
echo "✅ Že v redu: " . count($rezultati['ze_ok']) . "\n";
echo "📝 Ustvarjen modul.php: " . count($rezultati['ustvarjen_modul_php']) . "\n";
echo "📝 Ustvarjen manifest.json: " . count($rezultati['ustvarjen_manifest']) . "\n";
echo "❌ Napake: " . count($rezultati['napake']) . "\n\n";

if (!empty($rezultati['ustvarjen_modul_php'])) {
    echo "Ustvarjen modul.php za:\n";
    foreach ($rezultati['ustvarjen_modul_php'] as $m) echo "  - $m\n";
    echo "\n";
}

if (!empty($rezultati['ustvarjen_manifest'])) {
    echo "Ustvarjen manifest.json za:\n";
    foreach ($rezultati['ustvarjen_manifest'] as $m) echo "  - $m\n";
    echo "\n";
}

if (!empty($rezultati['napake'])) {
    echo "Napake:\n";
    foreach ($rezultati['napake'] as $n) echo "  - $n\n";
    echo "\n";
}

echo "Skupaj pregledanih modulov: " . count($mappe) . "\n";

// ============================================================
// POMOŽNE FUNKCIJE
// ============================================================

function generiraj_modul_php(string $id, string $ime_mape): string {
    $funk_ime = 'modul_' . $id . '_akcija';
    $funk_info = '_' . $funk_ime . '_info';
    $funk_domov = '_' . $funk_ime . '_domov';
    
    return <<<PHP
<?php
/**
 * MODUL: {$ime_mape}
 * POT: MODULI/{$ime_mape}/modul.php
 * VERZIJA: 1.0.0 (24.6.2026)
 *
 * 🏛️ NIVO: MODUL
 *
 * 📰 NAMEN:
 *     Standardna vstopna točka za {$ime_mape} modul
 *     Avtomatsko generirano
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - {$funk_ime}(string \$akcija, array \$podatki): array
 *
 * 👤 AVTOR:
 *     AstraMentalica Mojster (avtomatsko)
 *
 * 🌐 JEZIK:
 *     sl
 * ============================================================
 */

declare(strict_types=1);

if (!defined('BRIDGE_VARNOST') && !defined('SISTEM_VARNOST')) {
    die('Direktni dostop je prepovedan');
}

// ============================
// VSTOPNA TOČKA MODULA
// ============================

function {$funk_ime}(string \$akcija, array \$podatki = []): array {
    if (!Modul_Bridge::vloga_preveri('S0')) {
        return odziv_napaka('Dostop zavrnjen', 403);
    }

    return match(\$akcija) {
        'info'  => {$funk_info}(\$podatki),
        'domov' => {$funk_domov}(\$podatki),
        default => odziv_napaka("Neznana akcija: \$akcija", 400),
    };
}

// ============================
// AKCIJE
// ============================

function {$funk_info}(array \$podatki): array {
    return odziv_uspeh([
        'ime'       => '{$ime_mape}',
        'id'        => '{$id}',
        'verzija'   => '1.0.0',
        'opis'      => 'Modul {$ime_mape}',
        'uporabnik' => Modul_Bridge::uporabnik_pridobi()['ime'] ?? 'Gost',
    ], 'Informacije o modulu');
}

function {$funk_domov}(array \$podatki): array {
    return odziv_uspeh([
        'sporocilo' => 'Pozdravljen v modulu {$ime_mape}!',
        'cas'       => time(),
    ], 'Domov');
}

// ── DIREKTEN KLIC ──────────────────
if (basename(\$_SERVER['SCRIPT_FILENAME'] ?? '') === 'modul.php' && !defined('SISTEM_OBSTAJA')) {
    \$akcija  = \$_REQUEST['akcija'] ?? 'domov';
    \$podatki = \$_REQUEST;
    \$odziv   = {$funk_ime}(\$akcija, \$podatki);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(\$odziv, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}
PHP;
}

function generiraj_manifest(string $id, string $ime_mape): string {
    return <<<JSON
{
    "_id": "{$id}",
    "_verzija": "1.0.0",
    "modul": {
        "id": "{$id}",
        "ime": "{$ime_mape}",
        "tip": "zbiralec",
        "nivo": 1,
        "verzija": "1.0.0",
        "aktiviran": true,
        "vstopna": "modul.php",
        "opis": "Modul {$ime_mape}",
        "status": "testni",
        "demo": false,
        "zacasen": false
    },
    "dostop": {
        "minimalna_vloga": "S0",
        "plan": "osnova",
        "javno_vidno": true,
        "placljivo": false,
        "otroski": false,
        "vidnost": "vsi",
        "dovoljenja": ["branje"]
    },
    "cache": {
        "omogocen": true,
        "ttl": 3600
    },
    "ui": {
        "ima_prikaz": true,
        "ikona": "📦",
        "barva": "#667eea",
        "kategorija": "DRUGO",
        "tags": ["modul", "avtomatsko"],
        "jeziki": ["sl"]
    },
    "izvajanje": {
        "tip": "ui",
        "api_only": false,
        "interval": null,
        "ob_zagonu": false,
        "prioriteta": 50,
        "bootstrap": null
    },
    "integriteta": {
        "checksum": null
    },
    "cas": {
        "ustvarjen": "2026-06-24T09:52:00Z",
        "posodobljen": "2026-06-24T09:52:00Z",
        "zadnji_zagon": null
    }
}
JSON;
}