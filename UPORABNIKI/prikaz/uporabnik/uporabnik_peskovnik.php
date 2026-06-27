<?php
/**
 * ---------------------------------------------------------
 * POT: UPORABNIKI/prikaz/uporabnik/uporabnik_peskovnik.php
 * v1 (24.6.2026)
 * ---------------------------------------------------------
 * OPIS: Osebni peskovnik – profil, widgeti, AI asistent
 * ---------------------------------------------------------
 *
 * ODVISNOSTI:
 * - pot.php
 * - AI/llama_helper.php
 * - SISTEM/kernel/jedro/04_seja.php
 *
 * FUNKCIJE:
 * - Shranjevanje/nalaganje profila (JSON)
 * - AI chat proxy (Llama/Gemma preko Ollama)
 * - OpenClaw endpoint za DeepSeek ukaze
 *
 * ---------------------------------------------------------
 */

declare(strict_types=1);

require_once __DIR__ . '/../pot.php';
defined('SIDRO_AKTIVNO') or die('Direkten dostop ni dovoljen.');
require_once __DIR__ . '/../../AI/llama_helper.php';

// Glavna obdelava zahtev
adapter_obdelaj_zahtevo([
    'svet' => 'UPORABNIKI',
    'pot'  => 'peskovnik',
    'metoda' => $_SERVER['REQUEST_METHOD'] ?? 'GET',
    'get'  => $_GET,
    'post' => $_POST
]);

// ─── POMOŽNE FUNKCIJE ─────────────────────────────────────

function peskovnik_pridobi_profil(string $uporabnikId): array {
    $datoteka = __DIR__ . '/../../2025/' . $uporabnikId . '/profil_peskovnik.json';
    if (!file_exists($datoteka)) {
        return [
            'profil' => [
                'ime' => 'Meglica',
                'spol' => 'nevtralen',
                'arhetip' => 'modrec',
                'barva' => '#8b5cf6',
            ],
            'xp' => 0,
            'widgeti' => ['runa_dneva', 'tarot_dneva', 'meditacija'],
            'model' => 'llama',
            'chat_sporocila' => [],
        ];
    }
    $vsebina = file_get_contents($datoteka);
    return json_decode($vsebina, true) ?: [];
}

function peskovnik_shrani_profil(string $uporabnikId, array $podatki): bool {
    $mapa = __DIR__ . '/../../2025/' . $uporabnikId;
    if (!is_dir($mapa)) {
        mkdir($mapa, 0755, true);
    }
    $datoteka = $mapa . '/profil_peskovnik.json';
    $json = json_encode($podatki, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    return file_put_contents($datoteka, $json) !== false;
}

function peskovnik_ai_odgovor(string $sporocilo, string $model): string {
    $sistemski_prompt = "Si AstraMentalica varuh. Govoriš v slovenščini, sočno in modro. " .
        "Uporabnik te vprašuje o duhovnih temah: rune, tarot, meditacija, modrosti. " .
        "Odgovarjaj kratek (2-3 stavki), prilagojeno uporabniku.";

    $sporocila = [
        ['role' => 'system', 'content' => $sistemski_prompt],
        ['role' => 'user', 'content' => $sporocilo],
    ];

    try {
        $odgovor = llama_chat($sporocila, $model === 'gemma' ? 'gemma2:2b' : 'llama3.2', 0.7, 512);
        return $odgovor;
    } catch (Exception $e) {
        return "Oprosti, trenutno ne morem odgovoriti. (" . $e->getMessage() . ")";
    }
}

// ─── ROUTER ──────────────────────────────────────────────

$vhod = file_get_contents('php://input');
$podatki = json_decode($vhod, true) ?: $_POST;
$akcija = $podatki['akcija'] ?? '';

header('Content-Type: application/json; charset=utf-8');

switch ($akcija) {

    // ── Shrani profil ────────────────────────────────────
    case 'shrani_profil':
        $uporabnikId = $podatki['uporabnik_id'] ?? 'anonimen';
        $profil = $podatki['profil'] ?? [];
        $xp = $podatki['xp'] ?? 0;
        $widgeti = $podatki['widgeti'] ?? [];
        $model = $podatki['model'] ?? 'llama';
        $chat = $podatki['chat_sporocila'] ?? [];

        $shrani = [
            'profil' => $profil,
            'xp' => $xp,
            'widgeti' => $widgeti,
            'model' => $model,
            'chat_sporocila' => $chat,
            'shranjeno' => date('c'),
        ];

        if (peskovnik_shrani_profil($uporabnikId, $shrani)) {
            echo json_encode(['status' => 'uspeh', 'sporocilo' => 'Profil shranjen']);
        } else {
            echo json_encode(['status' => 'napaka', 'sporocilo' => 'Ni uspelo shraniti']);
        }
        break;

    // ── Naloži profil ────────────────────────────────────
    case 'nalozi_profil':
        $uporabnikId = $podatki['uporabnik_id'] ?? 'anonimen';
        $profil = peskovnik_pridobi_profil($uporabnikId);
        echo json_encode(['status' => 'uspeh', 'vsebina' => $profil]);
        break;

    // ── AI chat ──────────────────────────────────────────
    case 'ai_chat':
        $sporocilo = $podatki['sporocilo'] ?? '';
        $model = $podatki['model'] ?? 'llama';

        if (empty($sporocilo)) {
            echo json_encode(['status' => 'napaka', 'sporocilo' => 'Prazen sporočilo']);
            break;
        }

        $odgovor = peskovnik_ai_odgovor($sporocilo, $model);
        echo json_encode(['status' => 'uspeh', 'odgovor' => $odgovor]);
        break;

    // ── OpenClaw endpoint ────────────────────────────────
    case 'openclaw':
        // Tukaj OpenClaw pošilja ukaze od DeepSeek
        $ukaz = $podatki['ukaz'] ?? '';
        $parametri = $podatki['parametri'] ?? [];

        // Logiraj za debugging
        error_log("OpenClaw ukaz: $ukaz " . json_encode($parametri));

        // Obdelaj ukaz
        $rezultat = obdeli_openclaw_ukaz($ukaz, $parametri);

        echo json_encode([
            'status' => 'uspeh',
            'ukaz' => $ukaz,
            'rezultat' => $rezultat,
        ]);
        break;

    // ── Privzeto ────────────────────────────────────────
    default:
        echo json_encode([
            'status' => 'uspeh',
            'sporocilo' => 'Peskovnik API pripravljen',
            'akcija' => $akcija ?: 'brez',
        ]);
        break;
}

// ─── OPENCLAW UKazi ───────────────────────────────────────

function obdeli_openclaw_ukaz(string $ukaz, array $parametri): string {
    switch ($ukaz) {

        case 'dodaj_widget':
            // DeepSeek lahko pošlje: {ukaz:'dodaj_widget', parametri:{tip:'runa_dneva'}}
            $tip = $parametri['tip'] ?? '';
            if ($tip) {
                // Shrani v PASSPORT dnevnik
                $uporabnikId = $parametri['uporabnik_id'] ?? 'anonimen';
                $dnevnik = __DIR__ . '/../../2025/' . $uporabnikId . '/PASSPORT/dnevnik.json';
                $vnos = [
                    'cas' => date('c'),
                    'tip' => 'openclaw_widget',
                    'vsebina' => "Dodan widget: $tip",
                ];
                $zapis = [];
                if (file_exists($dnevnik)) {
                    $zapis = json_decode(file_get_contents($dnevnik), true) ?: [];
                }
                $zapis[] = $vnos;
                file_put_contents($dnevnik, json_encode($zapis, JSON_PRETTY_PRINT));
                return "Widget $tip dodan";
            }
            return "Manjka parameter 'tip'";

        case 'zaženi_meditacijo':
            $min = $parametri['min'] ?? 3;
            return "Meditacija $min min zažeta (izvede se v browserju)";

        case 'postavi_profil':
            // DeepSeek lahko spremeni profil uporabnika
            return "Profil posodobljen (izvede se v browserju)";

        case 'ai_chat':
            // DeepSeek pošilja odgovor v chat
            return "AI odgovor prejet (izvede se v browserju)";

        case 'preveri_status':
            return "Peskovnik aktiven. Ollama: " . (is_ollama_aktiven() ? 'Povezano' : 'Ni povezave');

        default:
            return "Neznan ukaz: $ukaz";
    }
}

function is_ollama_aktiven(): bool {
    $url = getenv('OLLAMA_URL') ?: 'http://localhost:11434';
    $ch = curl_init($url . '/api/tags');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 2,
        CURLOPT_CONNECTTIMEOUT => 2,
    ]);
    $resp = curl_exec($ch);
    curl_close($ch);
    return $resp !== false && strpos($resp, 'models') !== false;
}