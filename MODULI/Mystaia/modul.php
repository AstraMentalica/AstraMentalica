<?php
/**
 * MODUL: Mystaia
 * POT:   MODULI/Mystaia/modul.php
 * TIP:   izvajalec (nivo 3)
 *
 * Mystaia ne ve za Numyro ali Jyotir.
 * Samo prebere diagnoza.json od Svetovalca (če obstaja)
 * in priporoči ustrezne izdelke.
 */

declare(strict_types=1);

define('MYSTAIA_PODATKI',    __DIR__ . '/../../../PODATKI/moduli/modul_mystaia/');
define('MYSTAIA_NAROCILA',   MYSTAIA_PODATKI . 'narocila.json');
define('MYSTAIA_KOSARICA',   MYSTAIA_PODATKI . 'kosarice/');
define('SVETOVALEC_PODATKI', __DIR__ . '/../../../PODATKI/moduli/modul_svetovalec/');

// ═════════════════════════════════════════════════════════════════════════════
// BAZA IZDELKOV (v produkciji → MySQL / corpusmysticum)
// Struktura sledi manifest SQL: kategorija, vibracijska_koda, planetarna_pripadnost
// ═════════════════════════════════════════════════════════════════════════════

const MYSTAIA_KATALOG = [

    // ── Kristali ────────────────────────────────────────────────────────────
    'hematit' => [
        'id' => 'hematit', 'ime' => 'Hematit', 'kategorija' => 'kristali',
        'vibracijska_koda' => 4, 'planetarna_pripadnost' => 'Saturn',
        'opis' => 'Kamen uzemljenosti in zaščite. Krepi red in strukturo.',
        'energija' => ['stabilnost','zemlja','zaščita','fokus'],
        'cena' => 18.90, 'zaloga' => 24, 'ikona' => '⚫',
    ],
    'ametist' => [
        'id' => 'ametist', 'ime' => 'Ametist', 'kategorija' => 'kristali',
        'vibracijska_koda' => 7, 'planetarna_pripadnost' => 'Jupiter',
        'opis' => 'Kamen duhovnosti in intuicije. Umirja um in odpira višje zaznave.',
        'energija' => ['intuicija','mir','duhovnost','zaščita'],
        'cena' => 22.50, 'zaloga' => 18, 'ikona' => '💜',
    ],
    'rozenkvarc' => [
        'id' => 'rozenkvarc', 'ime' => 'Rozen Kvarc', 'kategorija' => 'kristali',
        'vibracijska_koda' => 6, 'planetarna_pripadnost' => 'Venera',
        'opis' => 'Kamen brezpogojne ljubezni. Odpira srčno čakro.',
        'energija' => ['ljubezen','sočutje','harmonija','odnosi'],
        'cena' => 15.00, 'zaloga' => 32, 'ikona' => '🌸',
    ],
    'obsidian' => [
        'id' => 'obsidian', 'ime' => 'Črni Obsidian', 'kategorija' => 'kristali',
        'vibracijska_koda' => 4, 'planetarna_pripadnost' => 'Pluto',
        'opis' => 'Moćan zaščitni kamen. Absorbira negativne energije.',
        'energija' => ['zaščita','čiščenje','resnica','zemlja'],
        'cena' => 19.90, 'zaloga' => 15, 'ikona' => '🖤',
    ],
    'citrin' => [
        'id' => 'citrin', 'ime' => 'Citrin', 'kategorija' => 'kristali',
        'vibracijska_koda' => 3, 'planetarna_pripadnost' => 'Sonce',
        'opis' => 'Kamen manifestacije in abundance. Privablja uspeh.',
        'energija' => ['abundance','energija','kreativnost','optimizem'],
        'cena' => 24.00, 'zaloga' => 20, 'ikona' => '💛',
    ],
    'mesecnik' => [
        'id' => 'mesecnik', 'ime' => 'Mesečnik', 'kategorija' => 'kristali',
        'vibracijska_koda' => 2, 'planetarna_pripadnost' => 'Luna',
        'opis' => 'Kamen intuicije in ženske moči. Uglašen z lunin ciklom.',
        'energija' => ['intuicija','ženskost','cikli','sanje'],
        'cena' => 21.00, 'zaloga' => 22, 'ikona' => '🌙',
    ],
    'labradorit' => [
        'id' => 'labradorit', 'ime' => 'Labradorit', 'kategorija' => 'kristali',
        'vibracijska_koda' => 7, 'planetarna_pripadnost' => 'Uran',
        'opis' => 'Kamen transformacije in zaščite aure.',
        'energija' => ['transformacija','zaščita','magija','intuicija'],
        'cena' => 27.50, 'zaloga' => 12, 'ikona' => '🔵',
    ],
    'granat' => [
        'id' => 'granat', 'ime' => 'Rdeči Granat', 'kategorija' => 'kristali',
        'vibracijska_koda' => 1, 'planetarna_pripadnost' => 'Mars',
        'opis' => 'Kamen vitalnosti in poguma. Krepi življenjsko silo.',
        'energija' => ['vitalnost','pogum','strast','akcija'],
        'cena' => 23.00, 'zaloga' => 17, 'ikona' => '🔴',
    ],

    // ── Olja ────────────────────────────────────────────────────────────────
    'olje_sandalovina' => [
        'id' => 'olje_sandalovina', 'ime' => 'Eterično olje Sandalovina', 'kategorija' => 'olja',
        'vibracijska_koda' => 7, 'planetarna_pripadnost' => 'Saturn',
        'opis' => 'Za meditacijo, zemeljitev in notranjo mirnost.',
        'energija' => ['meditacija','mir','zemlja','duhovnost'],
        'cena' => 16.50, 'zaloga' => 30, 'ikona' => '🌿',
    ],
    'olje_bergamot' => [
        'id' => 'olje_bergamot', 'ime' => 'Eterično olje Bergamot', 'kategorija' => 'olja',
        'vibracijska_koda' => 3, 'planetarna_pripadnost' => 'Merkur',
        'opis' => 'Dviguje razpoloženje, krepi samozavest in ustvarjalnost.',
        'energija' => ['veselje','optimizem','ustvarjalnost','komunikacija'],
        'cena' => 14.90, 'zaloga' => 25, 'ikona' => '🍋',
    ],

    // ── Seti ─────────────────────────────────────────────────────────────────
    'set_zemlja' => [
        'id' => 'set_zemlja', 'ime' => 'Set Zemlja — Utemeljevanje', 'kategorija' => 'seti',
        'vibracijska_koda' => 4, 'planetarna_pripadnost' => 'Saturn',
        'opis' => 'Hematit + Obsidian + Sandalovina olje + navodila za zemeljitev.',
        'energija' => ['stabilnost','zemlja','zaščita','red'],
        'cena' => 49.90, 'zaloga' => 8, 'ikona' => '🌍',
        'vsebuje' => ['hematit','obsidian','olje_sandalovina'],
    ],
    'set_luna' => [
        'id' => 'set_luna', 'ime' => 'Set Luna — Intuicija', 'kategorija' => 'seti',
        'vibracijska_koda' => 2, 'planetarna_pripadnost' => 'Luna',
        'opis' => 'Mesečnik + Labradorit + ritual lunarnega cikla.',
        'energija' => ['intuicija','sanje','ženskost','magija'],
        'cena' => 54.90, 'zaloga' => 6, 'ikona' => '🌙',
        'vsebuje' => ['mesecnik','labradorit'],
    ],
];

// ═════════════════════════════════════════════════════════════════════════════
// PRIPOROČILNI MOTOR
// Bere diagnoza.json od Svetovalca → izbere ustrezne izdelke
// ═════════════════════════════════════════════════════════════════════════════

const MYSTAIA_MAPIRANJE = [
    // manjkajoče vibracije → priporočeni izdelki
    'manjka_1' => ['granat'],
    'manjka_2' => ['mesecnik', 'set_luna'],
    'manjka_3' => ['citrin', 'olje_bergamot'],
    'manjka_4' => ['hematit', 'obsidian', 'set_zemlja'],
    'manjka_5' => ['labradorit', 'ametist'],
    'manjka_6' => ['rozenkvarc'],
    'manjka_7' => ['ametist', 'labradorit', 'olje_sandalovina'],
    'manjka_8' => ['hematit', 'granat'],
    'manjka_9' => ['rozenkvarc', 'ametist'],

    // planetarni vplivi
    'mars_sibek'    => ['granat'],
    'venera_sibka'  => ['rozenkvarc'],
    'saturn_sibek'  => ['hematit', 'olje_sandalovina'],
    'luna_sibka'    => ['mesecnik'],
    'merkur_sibek'  => ['olje_bergamot', 'citrin'],
    'jupiter_sibek' => ['ametist'],

    // kozmični pogoji
    'kp_visok'    => ['ametist', 'labradorit', 'obsidian'],
    'gst_aktivno' => ['obsidian', 'labradorit'],
    'luna_polna'  => ['mesecnik', 'set_luna'],
];

function mystaia_priporocila(?int $user_id, array $senzorji = []): array {
    $diagnoza = mystaia_beri_diagnozo($user_id);
    $kljuci   = [];

    if ($diagnoza) {
        // Manjkajoče vibracije iz Numyre
        foreach (($diagnoza['numyra']['bridge']['manjkajoca_stevila'] ?? []) as $st) {
            $kljuci[] = "manjka_{$st}";
        }
        // Šibki planeti iz Jyotirja
        foreach (($diagnoza['jyotir']['sibki_planeti'] ?? []) as $planet) {
            $kljuci[] = strtolower($planet) . '_sibek';
        }
    }

    // Kozmični senzorji
    if (($senzorji['kp_index'] ?? 0) > 4)  $kljuci[] = 'kp_visok';
    if ($senzorji['gst_aktivno'] ?? false)  $kljuci[] = 'gst_aktivno';

    // Mesečna faza (če je polna luna)
    $luna_dan = fmod((time() / 86400.0) - 2451550.1, 29.530588853);
    if ($luna_dan >= 14 && $luna_dan <= 16) $kljuci[] = 'luna_polna';

    // Zgradi seznam priporočenih ID-jev (brez duplikatov, po prioriteti)
    $priporoceni_id = [];
    foreach ($kljuci as $kljuc) {
        foreach (MYSTAIA_MAPIRANJE[$kljuc] ?? [] as $id) {
            if (!in_array($id, $priporoceni_id, true)) {
                $priporoceni_id[] = $id;
            }
        }
    }

    // Če ni diagnoze → prikaži bestselerje
    if (empty($priporoceni_id)) {
        $priporoceni_id = ['ametist', 'rozenkvarc', 'citrin', 'hematit'];
    }

    // Zgradi odgovor z izdelki
    $izdelki = [];
    foreach (array_slice($priporoceni_id, 0, 6) as $id) {
        if (isset(MYSTAIA_KATALOG[$id])) {
            $izdelek = MYSTAIA_KATALOG[$id];
            // Personalizirano sporočilo
            $izdelek['razlog'] = mystaia_razlog($id, $kljuci, $diagnoza);
            $izdelki[] = $izdelek;
        }
    }

    return [
        'personalizirano' => !empty($diagnoza),
        'kljuci'          => $kljuci,
        'izdelki'         => $izdelki,
        'skupaj'          => count($izdelki),
    ];
}

function mystaia_razlog(string $id, array $kljuci, ?array $diagnoza): string {
    $izdelek = MYSTAIA_KATALOG[$id] ?? [];
    $vib     = $izdelek['vibracijska_koda'] ?? 0;
    $planet  = $izdelek['planetarna_pripadnost'] ?? '';

    if (in_array("manjka_{$vib}", $kljuci, true)) {
        return "Tvoja osebna koda pogreša vibracijo {$vib}. {$izdelek['ime']} jo polni.";
    }
    if (in_array(strtolower($planet) . '_sibek', $kljuci, true)) {
        return "{$planet} je v tvojem horoskopu šibak. {$izdelek['ime']} krepi to energijo.";
    }
    if (in_array('kp_visok', $kljuci, true)) {
        return "Geomagnetna aktivnost je visoka. {$izdelek['ime']} ščiti tvojo avro.";
    }
    if (in_array('luna_polna', $kljuci, true)) {
        return "Polna luna ojača energijo tega kristala. Pravi čas za {$izdelek['ime']}.";
    }
    return "Priporočeno za tvojo energijsko pot.";
}

function mystaia_beri_diagnozo(?int $user_id): ?array {
    if (!$user_id) return null;
    $pot = SVETOVALEC_PODATKI . "{$user_id}_diagnoza.json";
    if (!file_exists($pot)) return null;
    return json_decode(file_get_contents($pot), true);
}

// ── Košarica ──────────────────────────────────────────────────────────────────
function mystaia_kosarica_beri(string $session_id): array {
    @mkdir(MYSTAIA_KOSARICA, 0755, true);
    $pot = MYSTAIA_KOSARICA . md5($session_id) . '.json';
    if (!file_exists($pot)) return ['postavke' => [], 'skupaj' => 0.0];
    return json_decode(file_get_contents($pot), true) ?? ['postavke' => [], 'skupaj' => 0.0];
}

function mystaia_kosarica_dodaj(string $session_id, string $izdelek_id, int $kolicina = 1): array {
    $kosarica = mystaia_kosarica_beri($session_id);
    $izdelek  = MYSTAIA_KATALOG[$izdelek_id] ?? null;

    if (!$izdelek) return ['napaka' => "Izdelek '{$izdelek_id}' ne obstaja."];
    if ($izdelek['zaloga'] < $kolicina) return ['napaka' => 'Premalo na zalogi.'];

    // Posodobi ali dodaj postavko
    $najden = false;
    foreach ($kosarica['postavke'] as &$p) {
        if ($p['id'] === $izdelek_id) {
            $p['kolicina'] += $kolicina;
            $najden = true;
            break;
        }
    }
    if (!$najden) {
        $kosarica['postavke'][] = [
            'id'       => $izdelek_id,
            'ime'      => $izdelek['ime'],
            'kolicina' => $kolicina,
            'cena'     => $izdelek['cena'],
            'ikona'    => $izdelek['ikona'],
        ];
    }

    // Preračunaj skupaj
    $kosarica['skupaj'] = array_sum(array_map(
        fn($p) => $p['cena'] * $p['kolicina'],
        $kosarica['postavke']
    ));

    $pot = MYSTAIA_KOSARICA . md5($session_id) . '.json';
    @mkdir(MYSTAIA_KOSARICA, 0755, true);
    file_put_contents($pot, json_encode($kosarica, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

    return $kosarica;
}

function mystaia_kosarica_odstrani(string $session_id, string $izdelek_id): array {
    $kosarica = mystaia_kosarica_beri($session_id);
    $kosarica['postavke'] = array_values(
        array_filter($kosarica['postavke'], fn($p) => $p['id'] !== $izdelek_id)
    );
    $kosarica['skupaj'] = array_sum(array_map(
        fn($p) => $p['cena'] * $p['kolicina'],
        $kosarica['postavke']
    ));
    $pot = MYSTAIA_KOSARICA . md5($session_id) . '.json';
    file_put_contents($pot, json_encode($kosarica, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    return $kosarica;
}

// ── Naročilo ─────────────────────────────────────────────────────────────────
function mystaia_narocilo_ustvari(string $session_id, array $kontakt): array {
    $kosarica = mystaia_kosarica_beri($session_id);
    if (empty($kosarica['postavke'])) return ['napaka' => 'Košarica je prazna.'];

    @mkdir(MYSTAIA_PODATKI, 0755, true);

    $narocila = [];
    if (file_exists(MYSTAIA_NAROCILA)) {
        $narocila = json_decode(file_get_contents(MYSTAIA_NAROCILA), true) ?? [];
    }

    $narocilo = [
        'id'         => 'NAR-' . date('Ymd') . '-' . strtoupper(substr(md5(uniqid()), 0,6)),
        'cas'        => date('Y-m-d H:i:s'),
        'kontakt'    => $kontakt,
        'postavke'   => $kosarica['postavke'],
        'skupaj'     => $kosarica['skupaj'],
        'status'     => 'novo',
        'session_id' => $session_id,
    ];

    $narocila[] = $narocilo;
    file_put_contents(MYSTAIA_NAROCILA, json_encode($narocila, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

    // Počisti košarico
    $pot = MYSTAIA_KOSARICA . md5($session_id) . '.json';
    if (file_exists($pot)) unlink($pot);

    return ['uspeh' => true, 'narocilo_id' => $narocilo['id'], 'skupaj' => $narocilo['skupaj']];
}

// ═════════════════════════════════════════════════════════════════════════════
// VSTOPNA TOČKA
// ═════════════════════════════════════════════════════════════════════════════

if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'] ?? '')) {
    header('Content-Type: application/json; charset=utf-8');

    $metoda     = $_SERVER['REQUEST_METHOD'] ?? 'GET';
    $pot        = $_SERVER['PATH_INFO'] ?? $_GET['pot'] ?? '/mystaia/izdelki';
    $session_id = $_COOKIE['session_id'] ?? session_id() ?: 'anon';
    $user_id    = (int)($_SESSION['user_id'] ?? $_GET['user_id'] ?? 0) ?: null;
    $body       = json_decode(file_get_contents('php://input'), true) ?? [];

    // Senzorji bridge (če so na voljo)
    $snap_pot = __DIR__ . '/../../../PODATKI/moduli/modul_senzorji/snapshot.json';
    $senzorji_bridge = [];
    if (file_exists($snap_pot)) {
        $snap = json_decode(file_get_contents($snap_pot), true);
        $senzorji_bridge = $snap['bridge'] ?? [];
    }

    $odgovor = match(true) {
        // GET /mystaia/izdelki?kategorija=kristali
        $pot === '/mystaia/izdelki' && $metoda === 'GET' => (function() use ($senzorji_bridge, $user_id) {
            $kat = $_GET['kategorija'] ?? null;
            $vsi = MYSTAIA_KATALOG;
            if ($kat) $vsi = array_filter($vsi, fn($i) => $i['kategorija'] === $kat);
            return [
                'priporocila' => mystaia_priporocila($user_id, $senzorji_bridge),
                'katalog'     => array_values($vsi),
            ];
        })(),

        // GET /mystaia/kosarica
        $pot === '/mystaia/kosarica' && $metoda === 'GET' =>
            mystaia_kosarica_beri($session_id),

        // POST /mystaia/kosarica  {izdelek_id, kolicina}
        $pot === '/mystaia/kosarica' && $metoda === 'POST' =>
            mystaia_kosarica_dodaj($session_id, $body['izdelek_id'] ?? '', (int)($body['kolicina'] ?? 1)),

        // DELETE /mystaia/kosarica  {izdelek_id}
        $pot === '/mystaia/kosarica' && $metoda === 'DELETE' =>
            mystaia_kosarica_odstrani($session_id, $body['izdelek_id'] ?? ''),

        // POST /mystaia/narocilo  {ime, email, naslov}
        $pot === '/mystaia/narocilo' && $metoda === 'POST' =>
            mystaia_narocilo_ustvari($session_id, $body),

        default => ['napaka' => "Neznana pot: {$pot} [{$metoda}]"],
    };

    echo json_encode(['status' => 'ok', 'modul' => 'mystaia', ...$odgovor],
                     JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}
