<?php
/**
 * ============================================================
 * POT: SISTEM/storitve_svetov/ekonomija/trznica_storitev.php
 * 📅 VERZIJA: v100 (28.6.2026)
 * ============================================================
 *
 * 🏛️ NIVO: STORITEV (BUSINESS LAYER)
 *
 * 📰 NAMEN:
 *     Tržnica – tri vrste transakcij:
 *
 *     A) NAKUP    → točke za predmet/postavitev (sistem → user)
 *     B) MENJAVA  → predmet za predmet (user ↔ user)
 *     C) KATALOG  → kaj je na voljo za kupiti
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - trznica_katalog(int $vloga): array
 *     - trznica_kupi(string $uid, string $artikel_id): array
 *     - trznica_ponudi(string $uid, array $ponudba, array $zahteva): array
 *     - trznica_sprejmi(string $uid, string $ponudba_id): array
 *     - trznica_preklic(string $uid, string $ponudba_id): array
 *     - trznica_ponudbe_aktivne(string $uid): array
 *
 * 📡 ODVISNOSTI:
 *     - ekonomija/inventar_storitev.php
 *     - SISTEM/kernel/jedro/07_dogodki.php
 *     - SISTEM/kernel/baze/upravljalec_baz.php
 *
 * 🚫 PREPOVEDI:
 *     - Brez __DIR__, echo, die()
 *
 * 📌 STATUS: Stabilno
 * 📅 ZGODOVINA:
 *     - v100: prva implementacija
 * 👤 AVTOR: AstraMentalica Mojster
 * ============================================================
 *
 * KATALOG ARTIKLOV – struktura:
 * ─────────────────────────────────────────────────────────
 *   id          → unikaten ključ artikla
 *   ime         → prikazno ime
 *   tip         → 'postavitev' | 'predmet' | 'gradnik'
 *   vsebina     → kaj dobi kupec (tip/predmet/kolicina)
 *   cena_tip    → 'tocke' | 'kristal' | 'runa' | ...
 *   cena        → količina valute
 *   vloga_min   → minimalna stopnja za nakup
 *   enkratno    → bool (samo 1x kupljivo)
 * ─────────────────────────────────────────────────────────
 *
 * MENJAVA – potek:
 *   1. user_A pokliče trznica_ponudi() → ustvari ponudbo
 *   2. user_B vidi ponudbo v trznica_ponudbe_aktivne()
 *   3. user_B pokliče trznica_sprejmi() → atomska menjava
 *   4. Oba dobita predmete, ponudba se zapre
 * ─────────────────────────────────────────────────────────
 */
declare(strict_types=1);

defined('SISTEM_VARNOST') or die('Direkten dostop ni dovoljen.');

// Ponudba poteče po 7 dneh
const TRZNICA_PONUDBA_TTL = 7 * 24 * 3600;

// ============================================================
// KATALOG
// ============================================================

function trznica_katalog_definicija(): array
{
    return [

        // ── TEME ───────────────────────────────────────────
        'tema_avrora' => [
            'ime'       => 'Tema Avrora',
            'tip'       => 'postavitev',
            'vsebina'   => ['tip' => 'postavitev', 'predmet' => 'avrora'],
            'cena_tip'  => 'tocke',
            'cena'      => 300,
            'vloga_min' => VLOGA_S1,
            'enkratno'  => true,
        ],
        'tema_oganj' => [
            'ime'       => 'Tema Oganj',
            'tip'       => 'postavitev',
            'vsebina'   => ['tip' => 'postavitev', 'predmet' => 'oganj'],
            'cena_tip'  => 'tocke',
            'cena'      => 300,
            'vloga_min' => VLOGA_S1,
            'enkratno'  => true,
        ],
        'tema_mystic' => [
            'ime'       => 'Tema Mystic',
            'tip'       => 'postavitev',
            'vsebina'   => ['tip' => 'postavitev', 'predmet' => 'mystic'],
            'cena_tip'  => 'kristal',
            'cena'      => 1,           // 1 rare kristal
            'cena_redkost' => 'rare',
            'vloga_min' => VLOGA_S2,
            'enkratno'  => true,
        ],

        // ── GRADNIKI ────────────────────────────────────────
        'gradnik_delci' => [
            'ime'       => 'Delci miške (efekt)',
            'tip'       => 'gradnik',
            'vsebina'   => ['tip' => 'gradnik', 'predmet' => 'delci_miske'],
            'cena_tip'  => 'tocke',
            'cena'      => 150,
            'vloga_min' => VLOGA_S1,
            'enkratno'  => true,
        ],
        'gradnik_portal' => [
            'ime'       => 'Portalni vstop',
            'tip'       => 'gradnik',
            'vsebina'   => ['tip' => 'gradnik', 'predmet' => 'portal_vstop'],
            'cena_tip'  => 'runa',
            'cena'      => 3,
            'vloga_min' => VLOGA_S2,
            'enkratno'  => true,
        ],
        'gradnik_krogla' => [
            'ime'       => 'Preročiška krogla',
            'tip'       => 'gradnik',
            'vsebina'   => ['tip' => 'gradnik', 'predmet' => 'preroska_krogla'],
            'cena_tip'  => 'relikvija',
            'cena'      => 1,
            'cena_redkost' => 'rare',
            'vloga_min' => VLOGA_S4,
            'enkratno'  => true,
        ],

        // ── PREDMETI ────────────────────────────────────────
        'kljuc_posebni' => [
            'ime'       => 'Ključ posebnih vsebin',
            'tip'       => 'predmet',
            'vsebina'   => ['tip' => 'kljuc', 'predmet' => 'kljuc_3', 'kolicina' => 1],
            'cena_tip'  => 'tocke',
            'cena'      => 500,
            'vloga_min' => VLOGA_S2,
            'enkratno'  => false,        // kupljivo večkrat
        ],
        'kristal_rare' => [
            'ime'       => 'Rare kristal',
            'tip'       => 'predmet',
            'vsebina'   => ['tip' => 'kristal', 'predmet' => 'rare', 'kolicina' => 1],
            'cena_tip'  => 'runa',
            'cena'      => 5,
            'vloga_min' => VLOGA_S1,
            'enkratno'  => false,
        ],
        'zvitek_blank' => [
            'ime'       => 'Prazen zvitek',
            'tip'       => 'predmet',
            'vsebina'   => ['tip' => 'zvitek', 'predmet' => 'zvitek_1', 'kolicina' => 1],
            'cena_tip'  => 'tocke',
            'cena'      => 80,
            'vloga_min' => VLOGA_S0,
            'enkratno'  => false,
        ],

        // ── POSEBNO – S5 ────────────────────────────────────
        'kozmos_3d_unlock' => [
            'ime'       => 'Kozmos 3D (prezgodaj)',
            'opis'      => 'Odklenj kozmos 3D pred S5 napredovanjem',
            'tip'       => 'gradnik',
            'vsebina'   => ['tip' => 'gradnik', 'predmet' => 'kozmos_3d'],
            'cena_tip'  => 'relikvija',
            'cena'      => 1,
            'cena_redkost' => 'epic',
            'vloga_min' => VLOGA_S4,
            'enkratno'  => true,
        ],
    ];
}

// ============================================================
// JAVNE FUNKCIJE
// ============================================================

/**
 * Vrne katalog artiklov dostopnih za dano vlogo.
 * Označi že kupljene artikle za danega uporabnika.
 */
function trznica_katalog(int $vloga, ?string $uid = null): array
{
    $katalog  = trznica_katalog_definicija();
    $kupljeni = $uid ? _trznica_kupljeni($uid) : [];

    $rezultat = [];
    foreach ($katalog as $id => $artikel) {
        if ($vloga < $artikel['vloga_min']) {
            continue;
        }
        $rezultat[$id] = [
            ...$artikel,
            'id'       => $id,
            'kupljeno' => in_array($id, $kupljeni, true),
        ];
    }

    return $rezultat;
}

/**
 * Kupi artikel iz kataloga (sistem → user).
 * Atomsko: preveri inventar, odštej ceno, dodaj vsebino.
 */
function trznica_kupi(string $uid, string $artikel_id): array
{
    $katalog = trznica_katalog_definicija();

    if (!isset($katalog[$artikel_id])) {
        return _trznica_napaka("Artikel ne obstaja: $artikel_id");
    }

    $artikel = $katalog[$artikel_id];

    // Preveri vlogo
    $uporabnik = shramba_beri_enega(PODATKI_UPORABNIKI . '/' . $uid, 'profil');
    $vloga     = (int)($uporabnik['vloga'] ?? VLOGA_GOST);

    if ($vloga < $artikel['vloga_min']) {
        return _trznica_napaka('Premajhna stopnja za ta artikel.', 403);
    }

    // Preveri enkratnost
    if ($artikel['enkratno'] && in_array($artikel_id, _trznica_kupljeni($uid), true)) {
        return _trznica_napaka('Ta artikel si že kupil.', 409);
    }

    // Preveri in odštej ceno
    $cenaTip    = $artikel['cena_tip'];
    $cenaPredm  = ($cenaTip === 'tocke') ? 'xp' : ($artikel['cena_redkost'] ?? $cenaTip);
    $cenaKol    = $artikel['cena'];

    $odbitek = inventar_porabi($uid, $cenaTip, $cenaPredm, $cenaKol, 'trznica_nakup_' . $artikel_id);
    if ($odbitek['status'] !== 'uspeh') {
        return $odbitek;
    }

    // Dodaj vsebino
    $v = $artikel['vsebina'];
    inventar_dodaj($uid, $v['tip'], $v['predmet'], $v['kolicina'] ?? 1, 'trznica_nakup');

    // Zabeleži nakup
    _trznica_zabeleži_nakup($uid, $artikel_id);

    // Transakcija v history
    _trznica_transakcija_zapisi([
        'tip'        => 'nakup',
        'kupec_uid'  => $uid,
        'artikel_id' => $artikel_id,
        'cena_tip'   => $cenaTip,
        'cena'       => $cenaKol,
        'cas'        => time(),
    ]);

    dogodek_sprozi('trznica.kupljeno', ['uid' => $uid, 'artikel_id' => $artikel_id]);

    return ['status' => 'uspeh', 'artikel' => $artikel_id, 'vsebina' => $v];
}

/**
 * Ustvari ponudbo za menjavo med uporabniki.
 *
 * $ponudba = ['tip' => 'kristal', 'predmet' => 'rare', 'kolicina' => 1]
 * $zahteva = ['tip' => 'runa', 'predmet' => 'algiz', 'kolicina' => 2]
 */
function trznica_ponudi(string $uid, array $ponudba, array $zahteva): array
{
    // Preveri da ponudnik ima predmet
    if (!inventar_ima($uid, $ponudba['tip'], $ponudba['predmet'], $ponudba['kolicina'] ?? 1)) {
        return _trznica_napaka('Nimaš dovolj predmetov za ponudbo.');
    }

    // Začasno rezerviraj predmet (odštej iz inventarja – v escrow)
    $rezerv = inventar_porabi($uid, $ponudba['tip'], $ponudba['predmet'], $ponudba['kolicina'] ?? 1, 'trznica_escrow');
    if ($rezerv['status'] !== 'uspeh') {
        return $rezerv;
    }

    $id = uniqid('ponudba_', true);

    $zapis = [
        'id'           => $id,
        'ponudnik_uid' => $uid,
        'ponudba'      => $ponudba,
        'zahteva'      => $zahteva,
        'status'       => 'aktivna',
        'ustvarjeno'   => time(),
        'poteče'       => time() + TRZNICA_PONUDBA_TTL,
    ];

    shramba_zapisi(PODATKI_REGISTRI . '/trznica_ponudbe', $zapis);

    dogodek_sprozi('trznica.ponudba_ustvarjena', ['ponudba_id' => $id, 'uid' => $uid]);

    return ['status' => 'uspeh', 'ponudba_id' => $id];
}

/**
 * Sprejme aktivno ponudbo – atomska menjava med dvema uporabnikoma.
 */
function trznica_sprejmi(string $uid, string $ponudba_id): array
{
    $ponudba = shramba_beri_enega(PODATKI_REGISTRI . '/trznica_ponudbe', $ponudba_id);

    if ($ponudba === null || $ponudba['status'] !== 'aktivna') {
        return _trznica_napaka('Ponudba ne obstaja ali ni več aktivna.', 404);
    }

    if ($ponudba['ponudnik_uid'] === $uid) {
        return _trznica_napaka('Ne moreš sprejeti lastne ponudbe.', 400);
    }

    if (time() > $ponudba['poteče']) {
        _trznica_ponudba_zapri($ponudba_id, 'potekla');
        return _trznica_napaka('Ponudba je potekla.', 410);
    }

    $z = $ponudba['zahteva'];

    // Preveri da sprejemnik ima zahtevano
    if (!inventar_ima($uid, $z['tip'], $z['predmet'], $z['kolicina'] ?? 1)) {
        return _trznica_napaka('Nimaš zahtevanega predmeta za menjavo.');
    }

    // Sprejemnik odda zahtevano
    $odbitek = inventar_porabi($uid, $z['tip'], $z['predmet'], $z['kolicina'] ?? 1, 'trznica_menjava');
    if ($odbitek['status'] !== 'uspeh') {
        return $odbitek;
    }

    // Sprejemnik dobi ponujeno (iz escrow)
    $p = $ponudba['ponudba'];
    inventar_dodaj($uid, $p['tip'], $p['predmet'], $p['kolicina'] ?? 1, 'trznica_menjava_prejeto');

    // Ponudnik dobi zahtevano
    inventar_dodaj($ponudba['ponudnik_uid'], $z['tip'], $z['predmet'], $z['kolicina'] ?? 1, 'trznica_menjava_prejeto');

    // Zapri ponudbo
    _trznica_ponudba_zapri($ponudba_id, 'zakljucena', $uid);

    // Transakcija
    _trznica_transakcija_zapisi([
        'tip'          => 'menjava',
        'ponudnik_uid' => $ponudba['ponudnik_uid'],
        'sprejemnik_uid' => $uid,
        'ponudba_id'   => $ponudba_id,
        'cas'          => time(),
    ]);

    dogodek_sprozi('trznica.menjava_zakljucena', [
        'ponudba_id'     => $ponudba_id,
        'ponudnik_uid'   => $ponudba['ponudnik_uid'],
        'sprejemnik_uid' => $uid,
    ]);

    return ['status' => 'uspeh', 'prejeto' => $p, 'oddano' => $z];
}

/**
 * Prekliče lastno ponudbo in vrne predmet v inventar.
 */
function trznica_preklic(string $uid, string $ponudba_id): array
{
    $ponudba = shramba_beri_enega(PODATKI_REGISTRI . '/trznica_ponudbe', $ponudba_id);

    if ($ponudba === null || $ponudba['ponudnik_uid'] !== $uid) {
        return _trznica_napaka('Ponudba ne obstaja ali ni tvoja.', 403);
    }

    if ($ponudba['status'] !== 'aktivna') {
        return _trznica_napaka('Ponudba ni več aktivna.');
    }

    // Vrni predmet iz escrow nazaj v inventar
    $p = $ponudba['ponudba'];
    inventar_dodaj($uid, $p['tip'], $p['predmet'], $p['kolicina'] ?? 1, 'trznica_preklic_vrnitev');

    _trznica_ponudba_zapri($ponudba_id, 'preklicana');

    return ['status' => 'uspeh'];
}

/**
 * Vrne vse aktivne ponudbe na tržnici (ne lastne uporabnikove).
 */
function trznica_ponudbe_aktivne(?string $uid = null): array
{
    $vse = shramba_beri(PODATKI_REGISTRI . '/trznica_ponudbe', []);

    $zdaj = time();
    return array_values(array_filter($vse, function($p) use ($uid, $zdaj) {
        if ($p['status'] !== 'aktivna') return false;
        if ($zdaj > $p['poteče']) return false;
        if ($uid !== null && $p['ponudnik_uid'] === $uid) return false;
        return true;
    }));
}

// ============================================================
// INTERNE
// ============================================================

function _trznica_kupljeni(string $uid): array
{
    $data = shramba_beri_enega(PODATKI_REGISTRI . '/trznica_nakupi', $uid);
    return $data['artikli'] ?? [];
}

function _trznica_zabeleži_nakup(string $uid, string $artikel_id): void
{
    $data    = shramba_beri_enega(PODATKI_REGISTRI . '/trznica_nakupi', $uid) ?? ['id' => $uid, 'artikli' => []];
    $data['artikli'][] = $artikel_id;
    shramba_zapisi(PODATKI_REGISTRI . '/trznica_nakupi', $data);
}

function _trznica_ponudba_zapri(string $id, string $status, ?string $sprejemnik = null): void
{
    $ponudba = shramba_beri_enega(PODATKI_REGISTRI . '/trznica_ponudbe', $id);
    if ($ponudba === null) return;

    shramba_posodobi(PODATKI_REGISTRI . '/trznica_ponudbe', $id, [
        ...$ponudba,
        'status'      => $status,
        'zakljuceno'  => time(),
        'sprejemnik_uid' => $sprejemnik,
    ]);
}

function _trznica_transakcija_zapisi(array $data): void
{
    $data['id'] = uniqid('trx_', true);
    shramba_zapisi(PODATKI_REGISTRI . '/trznica_transakcije', $data);
}

function _trznica_napaka(string $sporocilo, int $koda = 400): array
{
    return ['status' => 'napaka', 'status_koda' => $koda, 'sporocilo' => $sporocilo];
}
