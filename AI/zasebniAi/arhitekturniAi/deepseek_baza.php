<?php
/**
 * ============================================================
 * POT: AI/zasebniAi/arhitekturniAi/deepseek_baza.php
 * 📅 VERZIJA: v120_SISTEMSKO_SIDRO (18.6.2026)
 * ============================================================
 * 🏛️ NIVO: SISTEMSKI / ARHITEKTURNI
 * 
 * 📰 NAMEN:
 *     Skupna baza za vse DeepSeek agente.
 *     Vključuje varnost.php za vse poti.
 * ============================================================
 */

declare(strict_types=1);

// ============================================================
// OBVEZNO: VKLJUČI VARNOST (edina določa poti)
// ============================================================
if (!defined('AI_VSTOP')) {
    $varnostPath = __DIR__ . '/../../varnost.php';
    if (file_exists($varnostPath)) {
        require_once $varnostPath;
    } else {
        die('❌ varnost.php ni najden! Sistem se zaustavlja.');
    }
}

// Preveri, ali so vse konstante definirane
$potrebne = ['POT_AI', 'POT_AI_NALOGE', 'POT_AI_POROCILA', 'POT_AI_NEPOTRJENO', 'POT_AI_KARANTENA', 'POT_AI_ZGODOVINA'];
foreach ($potrebne as $konst) {
    if (!defined($konst)) {
        die("❌ Konstanta $konst ni definirana. Preveri varnost.php.");
    }
}

// ============================================================
// DEEPSEEK API KONFIGURACIJA
// ============================================================
if (!defined('DEEPSEEK_API_URL')) {
    define('DEEPSEEK_API_URL', 'https://api.deepseek.com/v1/chat/completions');
}
if (!defined('DEEPSEEK_MODEL')) {
    define('DEEPSEEK_MODEL', 'deepseek-chat');
}

/**
 * Pridobi API ključ (iz varnost.php ali okolja)
 */
function deepseek_api_kljuc(): string {
    if (defined('DEEPSEEK_API_KEY') && DEEPSEEK_API_KEY !== '') {
        return DEEPSEEK_API_KEY;
    }
    $kljuc = getenv('DEEPSEEK_API_KEY');
    if ($kljuc) {
        return $kljuc;
    }
    throw new RuntimeException('DEEPSEEK_API_KEY ni nastavljen. Dodaj ga v varnost.php.');
}

// ============================================================
// KLIC DEEPSEEK API
// ============================================================
function deepseek_klic(string $sistemsko, string $sporocilo, float $temperatura = 0.3): string {
    $payload = json_encode([
        'model'       => DEEPSEEK_MODEL,
        'temperature' => $temperatura,
        'messages'    => [
            ['role' => 'system',  'content' => $sistemsko],
            ['role' => 'user',    'content' => $sporocilo],
        ],
    ]);

    $ch = curl_init(DEEPSEEK_API_URL);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $payload,
        CURLOPT_HTTPHEADER     => [
            'Content-Type: application/json',
            'Authorization: Bearer ' . deepseek_api_kljuc(),
        ],
        CURLOPT_TIMEOUT        => 120,
    ]);

    $odgovor = curl_exec($ch);
    $napaka  = curl_error($ch);
    curl_close($ch);

    if ($napaka) {
        throw new RuntimeException("cURL napaka: $napaka");
    }

    $data = json_decode((string)$odgovor, true);

    if (!isset($data['choices'][0]['message']['content'])) {
        $err = $data['error']['message'] ?? $odgovor;
        throw new RuntimeException("DeepSeek API napaka: $err");
    }

    return trim($data['choices'][0]['message']['content']);
}

// ============================================================
// NALOGE — branje iz mape
// ============================================================
function preberi_naloge(string $agentId): array {
    $mapa = POT_AI_NALOGE . '/' . $agentId;
    if (!is_dir($mapa)) {
        return [];
    }

    $naloge = [];
    foreach (glob($mapa . '/*.json') as $file) {
        $vsebina = file_get_contents($file);
        if ($vsebina === false) continue;
        $data = json_decode($vsebina, true);
        if ($data && ($data['status'] ?? '') === 'odprta') {
            $data['_file'] = $file;
            $naloge[] = $data;
        }
    }
    return $naloge;
}

function zakljuci_nalogo(string $filePot, string $rezultat = ''): void {
    if (!file_exists($filePot)) return;
    $vsebina = file_get_contents($filePot);
    if ($vsebina === false) return;
    $data = json_decode($vsebina, true);
    if (!$data) return;
    $data['status'] = 'zaključena';
    $data['zaključen'] = date('Y-m-d H:i:s');
    $data['rezultat'] = $rezultat;
    file_put_contents($filePot, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    ai_log('NALOGA', 'zakljuci', $filePot, $rezultat);
}

// ============================================================
// POROČILA
// ============================================================
function shrani_porocilo(string $agentId, array $podatki): string {
    $dir = POT_AI_POROCILA;
    if (!is_dir($dir)) mkdir($dir, 0755, true);

    $ime  = $agentId . '_' . date('Ymd_His') . '.json';
    $file = $dir . '/' . $ime;

    $porocilo = array_merge([
        'agent'  => $agentId,
        'datum'  => date('Y-m-d H:i:s'),
        'verzija'=> defined('AI_VERZIJA') ? AI_VERZIJA : 'v121',
    ], $podatki);

    file_put_contents($file, json_encode($porocilo, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    ai_log('POROCILO', $agentId, $file, 'poročilo shranjeno');
    return $file;
}

// ============================================================
// PREDLOGI
// ============================================================
function ustvari_predlog(string $agentId, string $ciljnaPot, string $vsebina, string $opis = ''): string {
    $id  = date('Ymd_His') . '_' . substr(md5($ciljnaPot . $vsebina), 0, 8);
    $dir = POT_AI_NEPOTRJENO . '/' . $id;
    mkdir($dir, 0755, true);

    // Relativna pot glede na ROOT
    $relativna = str_replace(ROOT . '/', '', $ciljnaPot);

    $predlog = [
        'id'         => $id,
        'agent'      => $agentId,
        'cas'        => date('Y-m-d H:i:s'),
        'ciljna_pot' => $relativna,
        'opis'       => $opis,
        'status'     => 'čaka',
    ];

    file_put_contents($dir . '/predlog.json', json_encode($predlog, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    file_put_contents($dir . '/vsebina.txt', $vsebina);

    ai_log('PREDLOG', $agentId, $ciljnaPot, "predlog/$id — $opis");
    return $id;
}

// ============================================================
// PRAVILA - branje iz mape
// ============================================================
function preberi_pravila(string $kategorija = ''): array {
    $mapa = POT_AI_PRAVILA;
    if (!is_dir($mapa)) return [];

    $pravila = [];
    $vzorec = $kategorija ? $mapa . '/' . $kategorija . '/*.json' : $mapa . '/*.json';
    foreach (glob($vzorec) as $file) {
        $vsebina = file_get_contents($file);
        if ($vsebina !== false) {
            $data = json_decode($vsebina, true);
            if ($data) $pravila[] = $data;
        }
    }
    return $pravila;
}