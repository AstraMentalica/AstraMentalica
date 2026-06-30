<?php
/**
 * ============================================================
 * POT: SISTEM/storitve_svetov/ekonomija/tocke_storitev.php
 * 📅 VERZIJA: v100 (28.6.2026)
 * ============================================================
 *
 * 🏛️ NIVO: STORITEV (BUSINESS LAYER)
 *
 * 📰 NAMEN:
 *     Pravila dodeljevanja točk (XP) in avtomatsko
 *     napredovanje uporabnika med stopnjami.
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - tocke_za_akcijo(string $uid, string $akcija, array $kontekst): array
 *     - tocke_preveri_napredovanje(string $uid): array
 *     - tocke_lestvica(): array
 *     - tocke_meja_stopnje(int $vloga): int
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
 * AKCIJE IN XP:
 * ─────────────────────────────────────────────────────────
 *   dnevni_login          +10   (1x/dan)
 *   modul_zakljucen       +25   (vsak modul 1x)
 *   modul_napredni        +100  (S3+ moduli)
 *   vpogled_zapisan       +15   (dnevnik, max 3x/dan)
 *   profil_dopolnjen      +50   (1x ob prvem dopolnitvi)
 *   povabilo_sprejeto     +100  (ko povabljenec postane S1)
 *   zvitek_napisan        +20   (vsak zvitek)
 *   cakre_posodobljene    +10   (1x/teden)
 *   relikvija_pridobljena +200  (bonus za epic/legendary)
 *   registracija          +50   (1x ob aktivaciji)
 * ─────────────────────────────────────────────────────────
 *
 * MEJE STOPENJ (tocke_skupaj – nikoli ne padejo):
 *   S0 →  S1:       500
 *   S1 →  S2:     2 000
 *   S2 →  S3:     7 000
 *   S3 →  S4:    20 000
 *   S4 →  S5:    60 000
 * ─────────────────────────────────────────────────────────
 */
declare(strict_types=1);

defined('SISTEM_VARNOST') or die('Direkten dostop ni dovoljen.');

// ============================================================
// TABELA AKCIJ  (akcija => [xp, limit_na_dan|teden|enkrat])
// ============================================================

const TOCKE_AKCIJE = [
    'registracija'          => ['xp' => 50,  'limit' => 'enkrat'],
    'dnevni_login'          => ['xp' => 10,  'limit' => 'dan'],
    'modul_zakljucen'       => ['xp' => 25,  'limit' => 'modul'],   // 1x per modul ID
    'modul_napredni'        => ['xp' => 100, 'limit' => 'modul'],
    'vpogled_zapisan'       => ['xp' => 15,  'limit' => '3/dan'],
    'zvitek_napisan'        => ['xp' => 20,  'limit' => 'brez'],
    'profil_dopolnjen'      => ['xp' => 50,  'limit' => 'enkrat'],
    'cakre_posodobljene'    => ['xp' => 10,  'limit' => 'teden'],
    'povabilo_sprejeto'     => ['xp' => 100, 'limit' => 'brez'],
    'relikvija_epic'        => ['xp' => 200, 'limit' => 'brez'],
    'relikvija_legendary'   => ['xp' => 500, 'limit' => 'brez'],
];

// Meje točk za napredovanje (tocke_skupaj)
const TOCKE_MEJE = [
    VLOGA_S0 => 0,
    VLOGA_S1 => 500,
    VLOGA_S2 => 2000,
    VLOGA_S3 => 7000,
    VLOGA_S4 => 20000,
    VLOGA_S5 => 60000,
];

// Predmeti ki se podarijo ob napredovanju
const TOCKE_NAPREDOVANJE_DARILA = [
    VLOGA_S1 => [['tip' => 'runa',     'predmet' => 'nakljucna', 'kolicina' => 3]],
    VLOGA_S2 => [['tip' => 'kristal',  'predmet' => 'rare',      'kolicina' => 1]],
    VLOGA_S3 => [['tip' => 'zvitek',   'predmet' => 'zvitek_1',  'kolicina' => 1],
                 ['tip' => 'kljuc',    'predmet' => 'kljuc_1',   'kolicina' => 1]],
    VLOGA_S4 => [['tip' => 'relikvija','predmet' => 'common',    'kolicina' => 1]],
    VLOGA_S5 => [['tip' => 'relikvija','predmet' => 'epic',      'kolicina' => 1],
                 ['tip' => 'kristal',  'predmet' => 'legendary', 'kolicina' => 1]],
];

// ============================================================
// JAVNE FUNKCIJE
// ============================================================

/**
 * Zapiše XP za akcijo, ob upoštevanju dnevnih/tedenskih limitov.
 * Vrne koliko XP je bilo dejansko dodano (0 če limit dosežen).
 */
function tocke_za_akcijo(string $uid, string $akcija, array $kontekst = []): array
{
    if (!isset(TOCKE_AKCIJE[$akcija])) {
        return _tocke_napaka("Neznana akcija: $akcija");
    }

    $def = TOCKE_AKCIJE[$akcija];

    // Preveri limit
    if (!_tocke_limit_dovoljen($uid, $akcija, $def['limit'], $kontekst)) {
        return ['status' => 'preskoceno', 'xp' => 0, 'razlog' => 'limit_dosezen'];
    }

    // Dodaj XP
    $rezultat = tocke_dodaj($uid, $def['xp'], $akcija);
    if ($rezultat['status'] !== 'uspeh') {
        return $rezultat;
    }

    // Zabeleži limit
    _tocke_limit_zabeleži($uid, $akcija, $def['limit'], $kontekst);

    // Preveri napredovanje
    $napredovanje = tocke_preveri_napredovanje($uid);

    return [
        'status'       => 'uspeh',
        'xp'           => $def['xp'],
        'akcija'       => $akcija,
        'napredovanje' => $napredovanje['napredoval'] ?? false,
        'nova_vloga'   => $napredovanje['nova_vloga'] ?? null,
    ];
}

/**
 * Preveri ali je uporabnik zbral dovolj točk za naslednjo stopnjo.
 * Če da – posodobi vlogo in podari darila.
 */
function tocke_preveri_napredovanje(string $uid): array
{
    $inv        = inventar_pridobi($uid);
    $skupaj     = $inv['tocke_skupaj'];
    $uporabnik  = shramba_beri_enega(PODATKI_UPORABNIKI . '/' . $uid, 'profil');

    if ($uporabnik === null) {
        return ['napredoval' => false];
    }

    $trenutna = (int)($uporabnik['vloga'] ?? VLOGA_S0);

    // Poišči naslednjo stopnjo
    $naslednja = null;
    foreach (TOCKE_MEJE as $vloga => $meja) {
        if ($vloga > $trenutna && $skupaj >= $meja) {
            $naslednja = $vloga;
        }
    }

    if ($naslednja === null) {
        return ['napredoval' => false];
    }

    // Posodobi vlogo
    shramba_posodobi(PODATKI_UPORABNIKI . '/' . $uid, 'profil', [
        ...$uporabnik,
        'vloga'              => $naslednja,
        'vloga_posodobljeno' => time(),
    ]);

    // Podari predmete ob napredovanju
    $darila = TOCKE_NAPREDOVANJE_DARILA[$naslednja] ?? [];
    foreach ($darila as $dar) {
        inventar_dodaj($uid, $dar['tip'], $dar['predmet'], $dar['kolicina'], 'napredovanje_' . $naslednja);
    }

    // Sproži dogodek
    dogodek_sprozi('uporabnik.napredoval', [
        'uid'       => $uid,
        'iz_vloge'  => $trenutna,
        'na_vlogo'  => $naslednja,
        'darila'    => $darila,
    ]);

    return [
        'napredoval' => true,
        'iz_vloge'   => $trenutna,
        'nova_vloga' => $naslednja,
        'darila'     => $darila,
    ];
}

/**
 * Vrne XP mejo za dano stopnjo.
 */
function tocke_meja_stopnje(int $vloga): int
{
    return TOCKE_MEJE[$vloga] ?? 0;
}

/**
 * Lestvica – top N uporabnikov po tocke_skupaj.
 */
function tocke_lestvica(int $limit = 10): array
{
    $vsi = shramba_beri(PODATKI_UPORABNIKI, []);

    usort($vsi, fn($a, $b) => ($b['tocke_skupaj'] ?? 0) <=> ($a['tocke_skupaj'] ?? 0));

    return array_slice(array_map(fn($u) => [
        'uid'          => $u['id'] ?? '?',
        'ime'          => $u['ime_prikazno'] ?? 'Neznan',
        'tocke_skupaj' => $u['tocke_skupaj'] ?? 0,
        'vloga'        => $u['vloga'] ?? VLOGA_GOST,
    ], $vsi), 0, $limit);
}

// ============================================================
// INTERNE – LIMIT LOGIKA
// ============================================================

function _tocke_limit_dovoljen(string $uid, string $akcija, string $limit, array $kontekst): bool
{
    if ($limit === 'brez') {
        return true;
    }

    $key  = _tocke_limit_kljuc($uid, $akcija, $limit, $kontekst);
    $data = shramba_beri_enega(PODATKI_CACHE . '/tocke_limiti', $key);

    if ($data === null) {
        return true;
    }

    return match($limit) {
        'enkrat'  => false,
        'dan'     => ($data['datum'] ?? '') !== date('Y-m-d'),
        'teden'   => ($data['teden']  ?? '') !== date('Y-W'),
        '3/dan'   => ($data['datum']  ?? '') !== date('Y-m-d') || ($data['stevec'] ?? 0) < 3,
        'modul'   => false,  // modul ID je del ključa – če obstaja, je bil že dodan
        default   => true,
    };
}

function _tocke_limit_zabeleži(string $uid, string $akcija, string $limit, array $kontekst): void
{
    if ($limit === 'brez') {
        return;
    }

    $key  = _tocke_limit_kljuc($uid, $akcija, $limit, $kontekst);
    $data = shramba_beri_enega(PODATKI_CACHE . '/tocke_limiti', $key) ?? [];

    $nova = match($limit) {
        'enkrat' => ['zabelezenoOb' => time()],
        'dan'    => ['datum' => date('Y-m-d')],
        'teden'  => ['teden' => date('Y-W')],
        '3/dan'  => [
            'datum'   => date('Y-m-d'),
            'stevec'  => ($data['datum'] ?? '') === date('Y-m-d') ? ($data['stevec'] ?? 0) + 1 : 1,
        ],
        'modul'  => ['zabelezenoOb' => time()],
        default  => [],
    };

    shramba_zapisi(PODATKI_CACHE . '/tocke_limiti', ['id' => $key, ...$nova]);
}

function _tocke_limit_kljuc(string $uid, string $akcija, string $limit, array $kontekst): string
{
    $suffix = $limit === 'modul' ? ('_' . ($kontekst['modul_id'] ?? 'brez')) : '';
    return $uid . '_' . $akcija . $suffix;
}

function _tocke_napaka(string $sporocilo): array
{
    return ['status' => 'napaka', 'status_koda' => 400, 'sporocilo' => $sporocilo];
}
