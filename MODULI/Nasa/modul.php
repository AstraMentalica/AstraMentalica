<?php
/**
 * MODUL: Kozmični Senzorji
 * POT:   MODULI/Nasa/modul.php
 * TIP:   cron + api_only
 * TTL:   900s (15 min)
 *
 * Viri:
 *   NASA DONKI  → sončevi izbruhi (CME, solar flare, geomagnetic storm)
 *   NOAA SWPC   → Kp-indeks (geomagnetna aktivnost)
 *   OpenWeather → lokalno vreme (Ljubljana privzeto)
 */

declare(strict_types=1);

// ── Poti ─────────────────────────────────────────────────────────────────────
define('SENZORJI_CACHE_POT', __DIR__ . '/../../../PODATKI/moduli/nasa/');
define('SENZORJI_SNAPSHOT',  SENZORJI_CACHE_POT . 'snapshot.json');
define('SENZORJI_ZGODOVINA', SENZORJI_CACHE_POT . 'zgodovina.json');
define('SENZORJI_TTL', 900);

// ── API ključi (iz okolja ali .env) ──────────────────────────────────────────
$NASA_KEY     = getenv('NASA_API_KEY')         ?: 'DEMO_KEY'; // demo: 30 req/uro
$NOAA_URL     = 'https://services.swpc.noaa.gov/products/noaa-planetary-k-index.json';
$OW_KEY       = getenv('OPENWEATHER_API_KEY')  ?: '';
$OW_LAT       = (float)(getenv('LOKACIJA_LAT') ?: '46.0569');
$OW_LNG       = (float)(getenv('LOKACIJA_LNG') ?: '14.5058');

// ═════════════════════════════════════════════════════════════════════════════
// PRIDOBIVANJE PODATKOV
// ═════════════════════════════════════════════════════════════════════════════

/**
 * HTTP GET z timeout in user-agent.
 */
function senzorji_fetch(string $url, int $timeout = 10): ?array {
    $ctx = stream_context_create(['http' => [
        'timeout'       => $timeout,
        'user_agent'    => 'AstraMentalica/1.0 (contact@astramentalica.si)',
        'ignore_errors' => true,
    ]]);
    $raw = @file_get_contents($url, false, $ctx);
    if ($raw === false) return null;
    return json_decode($raw, true);
}

// ── NASA DONKI — sončevi izbruhi ─────────────────────────────────────────────
function senzorji_nasa(string $nasa_key): array {
    $od  = date('Y-m-d', strtotime('-3 days'));
    $do  = date('Y-m-d');

    // Solar Flare
    $flare_url = "https://api.nasa.gov/DONKI/FLR?startDate={$od}&endDate={$do}&api_key={$nasa_key}";
    $flari = senzorji_fetch($flare_url) ?? [];

    // CME (koronalni masni izmet)
    $cme_url = "https://api.nasa.gov/DONKI/CME?startDate={$od}&endDate={$do}&api_key={$nasa_key}";
    $cme = senzorji_fetch($cme_url) ?? [];

    // Geomagnetic Storm
    $gst_url = "https://api.nasa.gov/DONKI/GST?startDate={$od}&endDate={$do}&api_key={$nasa_key}";
    $gst = senzorji_fetch($gst_url) ?? [];

    // Zadnji izbruh
    $zadnji_flar = end($flari);
    $razred = $zadnji_flar['classType'] ?? null;

    // Jakost 0-100
    $jakost_map = ['A'=>5,'B'=>15,'C'=>35,'M'=>65,'X'=>90];
    $jakost = $razred ? ($jakost_map[substr($razred,0,1)] ?? 10) : 0;

    // Status besedilo
    $status = match(true) {
        $jakost >= 90 => 'EKSTREMNO',
        $jakost >= 65 => 'VISOKO',
        $jakost >= 35 => 'ZMERNO',
        $jakost >= 15 => 'NIZKO',
        default       => 'MIRNO',
    };

    return [
        'status'       => $status,
        'jakost'       => $jakost,
        'zadnji_izbruh'=> $zadnji_flar ? [
            'cas'    => $zadnji_flar['peakTime'] ?? $zadnji_flar['beginTime'] ?? null,
            'razred' => $razred,
        ] : null,
        'cme_stevilo'  => count($cme),
        'gst_aktivno'  => count($gst) > 0,
        'gst_stevilo'  => count($gst),
        'opozorilo'    => $jakost >= 65
            ? "⚡ Sončev izbruh razreda {$razred} — možne motnje v biopolju."
            : null,
    ];
}

// ── NOAA SWPC — Kp-indeks ────────────────────────────────────────────────────
function senzorji_noaa(): array {
    $podatki = senzorji_fetch('https://services.swpc.noaa.gov/products/noaa-planetary-k-index.json');

    if (empty($podatki) || !is_array($podatki)) {
        return ['kp' => null, 'status' => 'neznano', 'opozorilo' => null];
    }

    // Zadnji vnos (preskočimo header vrstico)
    $zadnji = null;
    for ($i = count($podatki) - 1; $i >= 1; $i--) {
        if (isset($podatki[$i][1]) && is_numeric($podatki[$i][1])) {
            $zadnji = $podatki[$i];
            break;
        }
    }

    $kp = $zadnji ? (float)$zadnji[1] : 0.0;

    $status = match(true) {
        $kp >= 8  => 'EKSTREMNA NEVIHTA',
        $kp >= 6  => 'HUDA NEVIHTA',
        $kp >= 5  => 'ZMERNA NEVIHTA',
        $kp >= 4  => 'POVIŠANO',
        $kp >= 2  => 'MIRNO',
        default   => 'ZELO MIRNO',
    };

    return [
        'kp'        => round($kp, 1),
        'status'    => $status,
        'opozorilo' => $kp >= 5
            ? "🌌 Geomagnetna nevihta Kp={$kp} — občutljivi posamezniki morda čutijo nemir."
            : null,
    ];
}

// ── OpenWeather — lokalno vreme ───────────────────────────────────────────────
function senzorji_vreme(string $ow_key, float $lat, float $lng): array {
    if (empty($ow_key)) {
        return ['dostopno' => false, 'razlog' => 'OPENWEATHER_API_KEY ni nastavljen'];
    }

    $url  = "https://api.openweathermap.org/data/2.5/weather?lat={$lat}&lon={$lng}&units=metric&lang=sl&appid={$ow_key}";
    $data = senzorji_fetch($url);

    if (!$data || isset($data['cod']) && $data['cod'] !== 200) {
        return ['dostopno' => false, 'razlog' => $data['message'] ?? 'API napaka'];
    }

    $tlak    = $data['main']['pressure'] ?? 1013;
    $vlaga   = $data['main']['humidity'] ?? 50;
    $temp    = $data['main']['temp'] ?? 20;
    $opis    = $data['weather'][0]['description'] ?? 'neznano';

    // Energijska vibracija (tlak + vlaga → 1-10)
    $vibracija = round(((($tlak - 980) / 70) * 5) + ((($vlaga - 20) / 80) * 5));
    $vibracija = max(1, min(10, (int)$vibracija));

    return [
        'dostopno'   => true,
        'kraj'       => $data['name'] ?? 'neznano',
        'temp'       => round($temp, 1),
        'tlak'       => $tlak,
        'vlaga'      => $vlaga,
        'opis'       => $opis,
        'vibracija'  => $vibracija,
        'koordinate' => ['lat' => $lat, 'lng' => $lng],
    ];
}

// ═════════════════════════════════════════════════════════════════════════════
// SNAPSHOT — sestavi in shrani
// ═════════════════════════════════════════════════════════════════════════════

function senzorji_ustvari_snapshot(string $nasa_key, string $ow_key, float $lat, float $lng): array {
    @mkdir(SENZORJI_CACHE_POT, 0755, true);

    $sonce    = senzorji_nasa($nasa_key);
    $geo      = senzorji_noaa();
    $vreme    = senzorji_vreme($ow_key, $lat, $lng);

    // Skupna kozmična vibracija (0-10)
    $vibracija_skupna = round(
        (10 - ($sonce['jakost'] / 10)) * 0.4 +
        (10 - min(10, $geo['kp'] * 1.25)) * 0.4 +
        (($vreme['vibracija'] ?? 5)) * 0.2
    , 1);

    // Bridge izhod — tisto kar bere Svetovalec/Mystaia
    $bridge = [
        'kp_index'      => $geo['kp'],
        'sun_status'    => $sonce['status'],
        'sun_jakost'    => $sonce['jakost'],
        'vibracija'     => $vibracija_skupna,
        'gst_aktivno'   => $sonce['gst_aktivno'] || ($geo['kp'] >= 5),
        'opozorila'     => array_filter([
            $sonce['opozorilo'],
            $geo['opozorilo'],
        ]),
    ];

    $snapshot = [
        'modul'   => 'senzorji',
        'verzija' => '1.0.0',
        'cas'     => time(),
        'datum'   => date('Y-m-d H:i:s'),
        'ttl'     => SENZORJI_TTL,
        'vir'     => 'nasa+noaa+openweather',
        'sonce'   => $sonce,
        'geomagnetno' => $geo,
        'vreme'   => $vreme,
        'bridge'  => $bridge,
    ];

    file_put_contents(SENZORJI_SNAPSHOT, json_encode($snapshot, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    // Dodaj v zgodovino (samo bridge podatki za varčevanje)
    senzorji_dodaj_zgodovino($bridge);

    return $snapshot;
}

function senzorji_dodaj_zgodovino(array $bridge): void {
    $zgodovina = [];
    if (file_exists(SENZORJI_ZGODOVINA)) {
        $zgodovina = json_decode(file_get_contents(SENZORJI_ZGODOVINA), true) ?? [];
    }
    $bridge['cas'] = time();
    $zgodovina[]   = $bridge;

    // Ohrani zadnjih 30 dni (30*24*4 = 2880 vnosov pri 15min intervalu)
    if (count($zgodovina) > 2880) {
        $zgodovina = array_slice($zgodovina, -2880);
    }
    file_put_contents(SENZORJI_ZGODOVINA, json_encode($zgodovina, JSON_UNESCAPED_UNICODE));
}

// ═════════════════════════════════════════════════════════════════════════════
// JAVNE METODE (kliče bridge.php)
// ═════════════════════════════════════════════════════════════════════════════

function senzorji_get_snapshot(): array {
    global $NASA_KEY, $OW_KEY, $OW_LAT, $OW_LNG;

    // Vrni cache če je svež
    if (file_exists(SENZORJI_SNAPSHOT)) {
        $cached = json_decode(file_get_contents(SENZORJI_SNAPSHOT), true);
        if ($cached && (time() - ($cached['cas'] ?? 0)) < SENZORJI_TTL) {
            $cached['_vir'] = 'cache';
            return $cached;
        }
    }
    return senzorji_ustvari_snapshot($NASA_KEY, $OW_KEY, $OW_LAT, $OW_LNG);
}

function senzorji_get_history(int $ur = 24): array {
    if (!file_exists(SENZORJI_ZGODOVINA)) return [];
    $zgodovina = json_decode(file_get_contents(SENZORJI_ZGODOVINA), true) ?? [];
    $od = time() - ($ur * 3600);
    return array_filter($zgodovina, fn($v) => ($v['cas'] ?? 0) >= $od);
}

function senzorji_get_alerts(): array {
    $snap = senzorji_get_snapshot();
    $opozorila = $snap['bridge']['opozorila'] ?? [];
    return array_values($opozorila);
}

function senzorji_force_update(): array {
    global $NASA_KEY, $OW_KEY, $OW_LAT, $OW_LNG;
    return senzorji_ustvari_snapshot($NASA_KEY, $OW_KEY, $OW_LAT, $OW_LNG);
}

// ═════════════════════════════════════════════════════════════════════════════
// VSTOPNA TOČKA (direktni HTTP klic)
// ═════════════════════════════════════════════════════════════════════════════

if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'] ?? '')) {
    header('Content-Type: application/json; charset=utf-8');

    $pot = $_SERVER['PATH_INFO'] ?? $_GET['pot'] ?? '/snapshot';

    $odgovor = match($pot) {
        '/senzorji/sonce'       => ['data' => senzorji_get_snapshot()['sonce'] ?? []],
        '/senzorji/vreme'       => ['data' => senzorji_get_snapshot()['vreme'] ?? []],
        '/senzorji/geomagnetno' => ['data' => senzorji_get_snapshot()['geomagnetno'] ?? []],
        '/senzorji/snapshot'    => senzorji_get_snapshot(),
        '/senzorji/alerts'      => ['opozorila' => senzorji_get_alerts()],
        '/senzorji/history'     => ['zgodovina' => senzorji_get_history((int)($_GET['ur'] ?? 24))],
        default                 => ['napaka' => 'Neznana pot: ' . $pot],
    };

    echo json_encode(['status' => 'ok', 'modul' => 'senzorji', ...$odgovor],
                     JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}
