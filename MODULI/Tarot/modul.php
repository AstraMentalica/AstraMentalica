<?php
/**
 * ============================================================
 * POT: MODULI/ORAKLEUM/Tarot/modul.php
 * 📅 VERZIJA: v1.0.0 (19.6.2026)
 * ============================================================
 *
 * 🏛️ NIVO: MODUL
 *
 * 📰 NAMEN:
 *     Orakleum Tarot — 78 kart (velike + male arkane), vsaka
 *     lahko pride pokončno ali RX (reversed/obrnjena).
 *     KLJUČNO PRAVILO: uporabnik meša karte sam v vmesniku
 *     (UI animacija mešanja/vlečenja, RX se določi naključno
 *     na klientu) — sistem NE generira naključja, samo
 *     validira in interpretira karte, ki jih je uporabnik
 *     že izbral. API sprejme RX status pod ključem 'rx'
 *     (alias za notranje ime 'obrnjena').
 *
 * 🔧 JAVNE FUNKCIJE (akcije):
 *     - vedezi        POST /tarot/vedezi        – opravi vedeževanje
 *     - zgodovina     GET  /tarot/zgodovina      – seznam preteklih vedeževanj
 *     - priljubljene  GET|POST|DELETE /tarot/priljubljene – upravljaj priljubljene
 *     - karte         GET  /tarot/karte          – seznam vseh 78 kart (referenca za UI)
 *     - info          GET  /tarot/info           – informacije o modulu
 *
 * 📡 ODVISNOSTI:
 *     - logika/tarot_vedezi.php
 *     - logika/zgodovina.php
 *     - podatki/karte_78.php
 *     - Modul_Bridge (če SISTEM ne obstaja) ali SISTEM/ (če obstaja)
 *
 * 🚫 PREPOVEDI:
 *     - Brez direktnih klicev v SISTEM/ mimo bridge/api
 *     - Brez branja $_SESSION direktno (gre skozi kontekst)
 *     - Brez naključnega mešanja kart na strežniku
 *
 * 📌 STATUS:
 *     Stabilno
 *
 * 📅 ZGODOVINA:
 *     - v1.0.0: prva implementacija po manifestu tarot_manifest.json
 *
 * 👤 AVTOR:
 *     AstraMentalica Mojster
 *
 * 🌐 JEZIK:
 *     sl
 *
 * 🏷️ OZNAKE:
 *     modul, orakleum, tarot, vedezevanje
 * ============================================================
 */

declare(strict_types=1);

// ── BOOTSTRAP ───────────────────────────────────────────────
// Poskusi najprej pravi sistem (pot.php), sicer pade na Modul_Bridge
if (!defined('SISTEM_OBSTAJA')) {
    $sistem_obstaja = false;
    $iskane_poti = [
        __DIR__ . '/../../../pot.php',
        __DIR__ . '/../../../../pot.php',
    ];
    foreach ($iskane_poti as $pot) {
        if (file_exists($pot)) {
            require_once $pot;
            $sistem_obstaja = true;
            break;
        }
    }
    define('SISTEM_OBSTAJA', $sistem_obstaja);
}

// Standardni odzivi — fallback, če sistem še ni naložil svojih
if (!function_exists('odziv_uspeh')) {
    function odziv_uspeh(array $vsebina, string $sporocilo = ''): array {
        return ['status' => 'uspeh', 'status_koda' => 200, 'sporocilo' => $sporocilo, 'vsebina' => $vsebina];
    }
}
if (!function_exists('odziv_napaka')) {
    function odziv_napaka(string $sporocilo, int $koda = 400): array {
        return ['status' => 'napaka', 'status_koda' => $koda, 'sporocilo' => $sporocilo, 'vsebina' => []];
    }
}

require_once __DIR__ . '/logika/tarot_vedezi.php';
require_once __DIR__ . '/logika/zgodovina.php';
require_once __DIR__ . '/podatki/karte_78.php';

// ── KONTEKST UPORABNIKA ──────────────────────────────────────
// V pravem sistemu pride uporabnik_id iz seje preko ADAPTER → SISTEM.
// Modul nikoli ne bere $_SESSION direktno.
function _tarot_pridobi_kontekst(): array {
    if (function_exists('seja_je_prijavljen') && function_exists('trenutna_vloga')) {
        // Pravi sistem: kontekst pride preko kernel/jedro/04_seja.php
        $uporabnik_id = $GLOBALS['_BRIDGE_UPORABNIK_ID'] ?? null;
    } elseif (function_exists('mini_pridobi_uporabnika')) {
        // Modul_Bridge embed stack
        $uporabnik = mini_pridobi_uporabnika();
        $uporabnik_id = (string)($uporabnik['id'] ?? '0');
    } else {
        $uporabnik_id = 'gost';
    }

    return ['uporabnik_id' => $uporabnik_id ?: 'gost'];
}

// ── AKCIJE ──────────────────────────────────────────────────

function akcija_vedezi(array $vhod, array $kontekst): array {
    $odziv = tarot_vedezi($vhod, $kontekst);

    // Zapiši v zgodovino samo, če je vedeževanje uspelo
    if (($odziv['status'] ?? '') === 'uspeh') {
        tarot_zgodovina_zapisi($odziv['vsebina'], (string)$kontekst['uporabnik_id']);
    }

    return $odziv;
}

function akcija_zgodovina(array $kontekst): array {
    $zgodovina = tarot_zgodovina_beri((string)$kontekst['uporabnik_id']);

    // Najnovejša najprej
    $zgodovina = array_reverse($zgodovina);

    return odziv_uspeh(['zgodovina' => $zgodovina, 'stevilo' => count($zgodovina)], 'Zgodovina vedeževanj');
}

function akcija_priljubljene_get(array $kontekst): array {
    $priljubljene = tarot_priljubljene_beri((string)$kontekst['uporabnik_id']);
    return odziv_uspeh(['priljubljene' => $priljubljene, 'stevilo' => count($priljubljene)], 'Priljubljena vedeževanja');
}

function akcija_priljubljene_post(array $vhod, array $kontekst): array {
    $uid = trim($vhod['uid'] ?? '');
    if ($uid === '') {
        return odziv_napaka('Manjka UID vedeževanja.', 422);
    }

    $uspeh = tarot_priljubljene_dodaj($uid, (string)$kontekst['uporabnik_id']);

    if (!$uspeh) {
        return odziv_napaka('Vedeževanje z navedenim UID ni bilo najdeno v zgodovini.', 404);
    }

    return odziv_uspeh(['uid' => $uid], 'Dodano med priljubljene.');
}

function akcija_priljubljene_delete(array $vhod, array $kontekst): array {
    $uid = trim($vhod['uid'] ?? $_GET['uid'] ?? '');
    if ($uid === '') {
        return odziv_napaka('Manjka UID vedeževanja.', 422);
    }

    $uspeh = tarot_priljubljene_brisi($uid, (string)$kontekst['uporabnik_id']);

    if (!$uspeh) {
        return odziv_napaka('Vedeževanje ni bilo med priljubljenimi.', 404);
    }

    return odziv_uspeh(['uid' => $uid], 'Odstranjeno iz priljubljenih.');
}

function akcija_karte(): array {
    $karte = tarot_karte_vse();

    // UI potrebuje samo ime + arkano + id za prikaz hrbtne strani kart pred razkritjem
    $javne_karte = array_map(fn($k) => [
        'id'     => $k['id'],
        'ime'    => $k['ime'],
        'arkana' => $k['arkana'],
    ], $karte);

    return odziv_uspeh(['karte' => $javne_karte, 'stevilo' => count($javne_karte)], 'Seznam tarot kart');
}

function akcija_info(): array {
    return odziv_uspeh([
        'ime'        => 'Tarot',
        'kategorija' => 'ORAKLEUM',
        'verzija'    => '1.0.0',
        'opis'       => '78 kart + obrnjene. Ti meša – sistem ne naključi.',
        'stevilo_kart' => count(tarot_karte_vse()),
    ], 'Informacije o modulu');
}

// ── USMERJEVALNIK ───────────────────────────────────────────

$metoda  = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$akcija  = $_POST['akcija'] ?? $_GET['akcija'] ?? 'info';
$vhod    = json_decode(file_get_contents('php://input'), true) ?? [];
$vhod    = array_merge($_POST, $vhod);
$kontekst = _tarot_pridobi_kontekst();

$odziv = match (true) {
    $akcija === 'vedezi' && $metoda === 'POST'                          => akcija_vedezi($vhod, $kontekst),
    $akcija === 'zgodovina' && $metoda === 'GET'                        => akcija_zgodovina($kontekst),
    $akcija === 'priljubljene' && $metoda === 'GET'                     => akcija_priljubljene_get($kontekst),
    $akcija === 'priljubljene' && $metoda === 'POST'                    => akcija_priljubljene_post($vhod, $kontekst),
    $akcija === 'priljubljene' && $metoda === 'DELETE'                  => akcija_priljubljene_delete($vhod, $kontekst),
    $akcija === 'karte' && $metoda === 'GET'                            => akcija_karte(),
    $akcija === 'info'                                                  => akcija_info(),
    default                                                             => odziv_napaka("Neznana akcija '$akcija' za metodo '$metoda'.", 404),
};

header('Content-Type: application/json; charset=utf-8');
echo json_encode($odziv, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
