<?php
/**
 * ============================================================
 * POT: SISTEM/storitve_svetov/ekonomija/inventar_storitev.php
 * 📅 VERZIJA: v100 (28.6.2026)
 * ============================================================
 *
 * 🏛️ NIVO: STORITEV (BUSINESS LAYER)
 *
 * 📰 NAMEN:
 *     Upravljanje uporabniškega inventarja.
 *     Shranjuje in ureja vse predmete: točke, kristale,
 *     rune, relikvije, zvitke, ključe.
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - inventar_pridobi(string $uid): array
 *     - inventar_dodaj(string $uid, string $tip, string $predmet, int $kolicina, string $vir): array
 *     - inventar_porabi(string $uid, string $tip, string $predmet, int $kolicina, string $namen): array
 *     - inventar_ima(string $uid, string $tip, string $predmet, int $kolicina): bool
 *     - tocke_dodaj(string $uid, int $xp, string $vir): array
 *     - tocke_porabi(string $uid, int $xp, string $namen): array
 *
 * 📡 ODVISNOSTI:
 *     - pot.php
 *     - SISTEM/kernel/baze/upravljalec_baz.php  (shramba_*)
 *     - SISTEM/kernel/jedro/07_dogodki.php       (dogodek_sprozi)
 *
 * 🚫 PREPOVEDI:
 *     - Brez __DIR__
 *     - Brez echo, print_r, var_dump
 *     - Brez die(), exit()
 *
 * 📌 STATUS:
 *     Stabilno
 *
 * 📅 ZGODOVINA:
 *     - v100: prva implementacija
 *
 * 👤 AVTOR:
 *     AstraMentalica Mojster
 *
 * 🌐 JEZIK:
 *     sl
 *
 * 🏷️ OZNAKE:
 *     ekonomija, inventar, predmeti, valuta
 * ============================================================
 *
 * TIPI PREDMETOV:
 * ─────────────────────────────────────────────────────────
 *   tocke     → XP (celo število, ne predmet)
 *   kristal   → common | rare | epic | legendary
 *   runa      → algiz | fehu | hagalaz | isa | ... (31 run)
 *   relikvija → common | rare | epic | legendary
 *   zvitek    → zvitek_1 ... zvitek_10 | pergament
 *   kljuc     → kljuc_1 ... kljuc_6 | nebesni_kljuc
 * ─────────────────────────────────────────────────────────
 *
 * SHRAMBA:
 *   Pot: PODATKI/uporabniki/{uid}/inventar.json
 *   Struktura: { tocke: int, predmeti: { tip: { predmet: kolicina } } }
 * ─────────────────────────────────────────────────────────
 */
declare(strict_types=1);

defined('SISTEM_VARNOST') or die('Direkten dostop ni dovoljen.');

// ============================================================
// KONSTANTE
// ============================================================

const INV_TIPI = ['kristal', 'runa', 'relikvija', 'zvitek', 'kljuc'];

const INV_REDKOSTI = ['common', 'rare', 'epic', 'legendary'];

// Koliko XP se zapiše v history (zadnjih N zapisov)
const INV_HISTORY_MAX = 200;

// ============================================================
// JAVNE FUNKCIJE
// ============================================================

/**
 * Vrne celoten inventar uporabnika.
 * Če inventar še ne obstaja, vrne prazen z 0 točkami.
 */
function inventar_pridobi(string $uid): array
{
    $pot  = _inventar_pot($uid);
    $data = shramba_beri_enega($pot, 'inventar');

    if ($data === null) {
        return _inventar_prazen($uid);
    }

    return $data;
}

/**
 * Doda predmet ali točke v inventar.
 *
 * Za točke: inventar_dodaj($uid, 'tocke', 'xp', 50, 'dnevni_login')
 * Za predmet: inventar_dodaj($uid, 'kristal', 'rare', 1, 'dosezek_meditacija')
 */
function inventar_dodaj(string $uid, string $tip, string $predmet, int $kolicina, string $vir): array
{
    if ($kolicina <= 0) {
        return _inv_napaka('Količina mora biti pozitivna.');
    }

    $inv = inventar_pridobi($uid);

    if ($tip === 'tocke') {
        $inv['tocke'] += $kolicina;
        $inv['tocke_skupaj'] += $kolicina;
        _inventar_history($inv, 'dodaj', 'tocke', 'xp', $kolicina, $vir);
    } else {
        if (!in_array($tip, INV_TIPI, true)) {
            return _inv_napaka("Neznan tip predmeta: $tip");
        }
        $inv['predmeti'][$tip][$predmet] = ($inv['predmeti'][$tip][$predmet] ?? 0) + $kolicina;
        _inventar_history($inv, 'dodaj', $tip, $predmet, $kolicina, $vir);
    }

    $inv['posodobljeno'] = time();
    _inventar_shrani($uid, $inv);

    dogodek_sprozi('inventar.dodano', [
        'uid'      => $uid,
        'tip'      => $tip,
        'predmet'  => $predmet,
        'kolicina' => $kolicina,
        'vir'      => $vir,
    ]);

    return ['status' => 'uspeh', 'inventar' => $inv];
}

/**
 * Porabi predmet ali točke iz inventarja.
 * Vrne napako če inventar nima dovolj.
 */
function inventar_porabi(string $uid, string $tip, string $predmet, int $kolicina, string $namen): array
{
    if ($kolicina <= 0) {
        return _inv_napaka('Količina mora biti pozitivna.');
    }

    if (!inventar_ima($uid, $tip, $predmet, $kolicina)) {
        return _inv_napaka("Ni dovolj: $tip/$predmet (zahtevano: $kolicina).", 402);
    }

    $inv = inventar_pridobi($uid);

    if ($tip === 'tocke') {
        $inv['tocke'] -= $kolicina;
        _inventar_history($inv, 'porabi', 'tocke', 'xp', $kolicina, $namen);
    } else {
        $inv['predmeti'][$tip][$predmet] -= $kolicina;
        if ($inv['predmeti'][$tip][$predmet] <= 0) {
            unset($inv['predmeti'][$tip][$predmet]);
        }
        _inventar_history($inv, 'porabi', $tip, $predmet, $kolicina, $namen);
    }

    $inv['posodobljeno'] = time();
    _inventar_shrani($uid, $inv);

    dogodek_sprozi('inventar.porabljeno', [
        'uid'      => $uid,
        'tip'      => $tip,
        'predmet'  => $predmet,
        'kolicina' => $kolicina,
        'namen'    => $namen,
    ]);

    return ['status' => 'uspeh', 'inventar' => $inv];
}

/**
 * Preveri ali inventar vsebuje dovolj predmeta/točk.
 */
function inventar_ima(string $uid, string $tip, string $predmet, int $kolicina = 1): bool
{
    $inv = inventar_pridobi($uid);

    if ($tip === 'tocke') {
        return $inv['tocke'] >= $kolicina;
    }

    return ($inv['predmeti'][$tip][$predmet] ?? 0) >= $kolicina;
}

// ============================================================
// BLIŽNJICE ZA TOČKE (najpogostejša operacija)
// ============================================================

function tocke_dodaj(string $uid, int $xp, string $vir): array
{
    return inventar_dodaj($uid, 'tocke', 'xp', $xp, $vir);
}

function tocke_porabi(string $uid, int $xp, string $namen): array
{
    return inventar_porabi($uid, 'tocke', 'xp', $xp, $namen);
}

// ============================================================
// BLIŽNJICE ZA RELIKVIJE
// ============================================================

function relikvija_dodaj(string $uid, string $redkost, string $vir): array
{
    if (!in_array($redkost, INV_REDKOSTI, true)) {
        return _inv_napaka("Neznan tip relikvije: $redkost");
    }
    return inventar_dodaj($uid, 'relikvija', $redkost, 1, $vir);
}

function relikvije_pridobi(string $uid): array
{
    $inv = inventar_pridobi($uid);
    return $inv['predmeti']['relikvija'] ?? [];
}

// ============================================================
// INTERNE FUNKCIJE
// ============================================================

function _inventar_pot(string $uid): string
{
    return PODATKI_UPORABNIKI . '/' . $uid . '/inventar';
}

function _inventar_prazen(string $uid): array
{
    return [
        'uid'           => $uid,
        'tocke'         => 0,
        'tocke_skupaj'  => 0,  // kumulativno (za stopnje)
        'predmeti'      => [
            'kristal'  => [],
            'runa'     => [],
            'relikvija'=> [],
            'zvitek'   => [],
            'kljuc'    => [],
        ],
        'history'       => [],
        'ustvarjeno'    => time(),
        'posodobljeno'  => time(),
    ];
}

function _inventar_shrani(string $uid, array $inv): void
{
    $pot = _inventar_pot($uid);
    shramba_posodobi($pot, 'inventar', $inv);
}

function _inventar_history(array &$inv, string $akcija, string $tip, string $predmet, int $kolicina, string $kontekst): void
{
    $inv['history'][] = [
        'akcija'   => $akcija,   // dodaj | porabi
        'tip'      => $tip,
        'predmet'  => $predmet,
        'kolicina' => $kolicina,
        'kontekst' => $kontekst,
        'cas'      => time(),
    ];

    // Ohrani samo zadnjih N zapisov
    if (count($inv['history']) > INV_HISTORY_MAX) {
        $inv['history'] = array_slice($inv['history'], -INV_HISTORY_MAX);
    }
}

function _inv_napaka(string $sporocilo, int $koda = 400): array
{
    return [
        'status'      => 'napaka',
        'status_koda' => $koda,
        'sporocilo'   => $sporocilo,
    ];
}
