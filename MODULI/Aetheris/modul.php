<?php
/**
 * ============================================================
 * MODUL: Aetheris
 * POT: MODULI/Aetheris/modul.php
 * 📅 VERZIJA: v2.0.0 (24.6.2026)
 * ============================================================
 *
 * 🏛️ NIVO: MODUL
 *
 * 📰 NAMEN:
 *     Forum Aetheris — prostor za razprave in diskusije
 *     Uporabniki lahko ustvarjajo teme, pišejo objave in komentirajo
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - modul_aetheris_akcija(string $akcija, array $podatki): array
 *
 * 📡 ODVISNOSTI:
 *     - Modul_Bridge
 *     - Codex (skupna baza znanja)
 *
 * 📌 STATUS:
 *     Aktivno — Forum
 *
 * 👤 AVTOR:
 *     AstraMentalica Mojster
 *
 * 🌐 JEZIK:
 *     sl
 * ============================================================
 */

declare(strict_types=1);

// ── POIŠČI BRIDGE ─────────────────────────────────────────
$bridgePoti = [
    __DIR__ . '/../Modul_Bridge/modul_bridge.php',
];

$bridgeNajden = false;
foreach ($bridgePoti as $pot) {
    if (file_exists($pot)) {
        require_once $pot;
        $bridgeNajden = true;
        break;
    }
}

if (!$bridgeNajden) {
    header('Content-Type: application/json');
    echo json_encode(['napaka' => 'Modul_Bridge ni najden']);
    exit;
}

// ── STANDARDNI ODZIVI ──────────────────────────────────────
if (!function_exists('odziv_uspeh')) {
    function odziv_uspeh(array $vsebina, string $sporocilo = ''): array {
        return ['status' => 'uspeh', 'status_koda' => 200, 'sporocilo' => $sporocilo, 'vsebina' => $vsebina];
    }
    function odziv_napaka(string $sporocilo, int $koda = 400): array {
        return ['status' => 'napaka', 'status_koda' => $koda, 'sporocilo' => $sporocilo, 'vsebina' => []];
    }
}

require_once __DIR__ . '/modul_aetheris_baza.php';

// ============================
// VSTOPNA TOČKA MODULA — FORUM
// ============================

function modul_aetheris_akcija(string $akcija, array $podatki = []): array {
    if (!Modul_Bridge::vloga_preveri('S0')) {
        return odziv_napaka('Dostop zavrnjen', 403);
    }

    return match($akcija) {
        'info'        => _modul_aetheris_info($podatki),
        'domov'       => _modul_aetheris_domov($podatki),
        'teme'        => _modul_aetheris_teme($podatki),
        'tema'        => _modul_aetheris_tema($podatki),
        'nova_tema'   => _modul_aetheris_nova_tema($podatki),
        'objava'      => _modul_aetheris_objava($podatki),
        'isci'        => _modul_aetheris_isci($podatki),
        'kategorije'  => _modul_aetheris_kategorije($podatki),
        default       => odziv_napaka("Neznana akcija: $akcija", 400),
    };
}

// ============================
// AKCIJE
// ============================

function _modul_aetheris_info(array $podatki): array {
    $uporabnik = Modul_Bridge::uporabnik_pridobi();
    $baza = new ForumBaza();
    $stats = $baza->statistika();

    return odziv_uspeh([
        'ime'            => 'Aetheris',
        'id'             => 'aetheris',
        'verzija'        => '2.0.0',
        'tip'            => 'forum',
        'opis'           => 'Forum Aetheris — prostor za duhovne razprave, izmenjavo mnenj in skupnostno znanje',
        'uporabnik'      => $uporabnik['ime'] ?? 'Gost',
        'stevilo_tem'    => $stats['teme'],
        'stevilo_objav'  => $stats['objave'],
        'stevilo_clanov' => $stats['clani'],
    ], 'Informacije o forumu');
}

function _modul_aetheris_domov(array $podatki): array {
    $baza = new ForumBaza();
    $zadnjeTeme = $baza->zadnjeTeme(10);
    $kategorije = $baza->kategorije();

    return odziv_uspeh([
        'naslov'     => 'Forum Aetheris',
        'podnaslov'  => 'Dobrodošli v skupnostni razpravi',
        'zadnje_teme' => $zadnjeTeme,
        'kategorije' => $kategorije,
        'statistika' => $baza->statistika(),
    ], 'Forum domov');
}

function _modul_aetheris_teme(array $podatki): array {
    $baza = new ForumBaza();
    $kategorija = $podatki['kategorija'] ?? null;
    $stran = (int)($podatki['stran'] ?? 1);
    $limit = 20;

    $teme = $baza->pridobiTeme($kategorija, $stran, $limit);

    return odziv_uspeh([
        'teme'       => $teme,
        'stran'      => $stran,
        'limit'      => $limit,
        'kategorija' => $kategorija,
    ], 'Seznam tem');
}

function _modul_aetheris_tema(array $podatki): array {
    $id = $podatki['id'] ?? 0;
    $baza = new ForumBaza();
    $tema = $baza->pridobiTemo((int)$id);

    if (!$tema) {
        return odziv_napaka('Tema ne obstaja', 404);
    }

    return odziv_uspeh([
        'tema'   => $tema,
        'objave' => $baza->objaveTeme((int)$id),
    ], 'Prikaz teme');
}

function _modul_aetheris_nova_tema(array $podatki): array {
    $uporabnik = Modul_Bridge::uporabnik_pridobi();
    $naslov = $podatki['naslov'] ?? '';
    $vsebina = $podatki['vsebina'] ?? '';
    $kategorija = $podatki['kategorija'] ?? 'splošno';

    if (empty($naslov) || empty($vsebina)) {
        return odziv_napaka('Naslov in vsebina sta obvezna', 400);
    }

    $baza = new ForumBaza();
    $id = $baza->ustvariTemo(
        $naslov,
        $vsebina,
        $uporabnik['id'] ?? 0,
        $uporabnik['ime'] ?? 'Neznan',
        $kategorija
    );

    // Poveži s Codexom — dodaj v skupno bazo
    _modul_aetheris_povezi_s_codexom([
        'tip' => 'forum_tema',
        'naslov' => $naslov,
        'vsebina' => $vsebina,
        'avtor' => $uporabnik['ime'] ?? 'Neznan',
        'forum_id' => $id,
    ]);

    return odziv_uspeh([
        'id'      => $id,
        'naslov'  => $naslov,
        'kategorija' => $kategorija,
    ], 'Tema uspešno ustvarjena!');
}

function _modul_aetheris_objava(array $podatki): array {
    $uporabnik = Modul_Bridge::uporabnik_pridobi();
    $temaId = (int)($podatki['tema_id'] ?? 0);
    $vsebina = $podatki['vsebina'] ?? '';

    if (empty($vsebina) || $temaId === 0) {
        return odziv_napaka('Vsebina in ID teme sta obvezna', 400);
    }

    $baza = new ForumBaza();
    $id = $baza->dodajObjavo(
        $temaId,
        $vsebina,
        $uporabnik['id'] ?? 0,
        $uporabnik['ime'] ?? 'Neznan'
    );

    return odziv_uspeh([
        'id'      => $id,
        'tema_id' => $temaId,
    ], 'Objava uspešno dodana!');
}

function _modul_aetheris_isci(array $podatki): array {
    $query = $podatki['q'] ?? '';
    if (empty($query)) {
        return odziv_napaka('Vnesite iskalni niz', 400);
    }

    $baza = new ForumBaza();
    $rezultati = $baza->isci($query);

    return odziv_uspeh([
        'query'     => $query,
        'rezultati' => $rezultati,
        'stevilo'   => count($rezultati),
    ], 'Rezultati iskanja');
}

function _modul_aetheris_kategorije(array $podatki): array {
    $baza = new ForumBaza();
    return odziv_uspeh([
        'kategorije' => $baza->kategorije(),
    ], 'Seznam kategorij');
}

/**
 * Poveži forumsko temo s Codexom in po potrebi z blogom Celestara
 */
function _modul_aetheris_povezi_s_codexom(array $podatki): void {
    // Poskusi Codex
    $codexPot = __DIR__ . '/../Codex/modul_codex_api.php';
    if (file_exists($codexPot)) {
        require_once $codexPot;
        if (function_exists('modul_codex_dodaj_vnos')) {
            @modul_codex_dodaj_vnos($podatki);
        }
    }

    // Poskusi Celestara (ustvari blog post)
    $celesPot = __DIR__ . '/../Celestara/modul_celestara_api.php';
    if (file_exists($celesPot)) {
        require_once $celesPot;
        if (function_exists('modul_celestara_dodaj_post')) {
            @modul_celestara_dodaj_post([
                'naslov' => $podatki['naslov'] ?? ($podatki['title'] ?? ''),
                'vsebina' => $podatki['vsebina'] ?? ($podatki['content'] ?? ''),
                'avtor_ime' => $podatki['avtor'] ?? ($podatki['author'] ?? 'Anonim'),
                'avtor_id' => $podatki['avtor_id'] ?? 0,
            ]);
        }
    }
}

// ── DIREKTEN KLIC ──────────────────
if (basename($_SERVER['SCRIPT_FILENAME'] ?? '') === 'modul.php' && !defined('SISTEM_OBSTAJA')) {
    $akcija  = $_REQUEST['akcija'] ?? 'domov';
    $podatki = $_REQUEST;
    $odziv   = modul_aetheris_akcija($akcija, $podatki);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($odziv, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}