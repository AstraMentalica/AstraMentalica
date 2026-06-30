<?php
/**
 * ============================================================
 * POT: SISTEM/storitve_svetov/entitete/entiteta_storitev.php
 * 📅 VERZIJA: v100 (28.6.2026)
 * ============================================================
 *
 * 🏛️ NIVO: STORITEV (BUSINESS LAYER)
 *
 * 📰 NAMEN:
 *     Dostop do kanoničnega registra varuhov, živali in
 *     relikvij. Bere kanonični_varuhi.json enkrat in
 *     kešira v memory za čas zahtevka.
 *
 *     Kanonični JSON = združen modri + zeleni:
 *       - 31 varuhov (polna zavest + glas + tip)
 *       - 40 živali (modri 31 + zeleni 9)
 *       - 20 relikvij (vezane na varuha)
 *       - 8 arhetipov
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - entiteta_varuh(string $id): ?array
 *     - entiteta_varuhi_vse(): array
 *     - entiteta_varuhi_za_vlogo(int $vloga): array
 *     - entiteta_varuh_priporocen(string $uid): array
 *     - entiteta_zival(string $id): ?array
 *     - entiteta_relikvija(string $id): ?array
 *     - entiteta_relikvije_varuha(string $varuh_id): array
 *     - entiteta_arhetip(string $id): ?array
 *     - entiteta_arhetip_varuhi(string $arhetip_id): array
 *     - entiteta_odkleniti(string $uid, string $varuh_id): array
 *     - entiteta_odklenjeni(string $uid): array
 *
 * 📡 ODVISNOSTI:
 *     - pot.php (POT_ENTITETE)
 *     - pot_vmesnik.php (POT_ENTITETE_VARUHI)
 *     - ekonomija/inventar_storitev.php
 *     - SISTEM/kernel/jedro/07_dogodki.php
 *
 * 🚫 PREPOVEDI:
 *     - Brez __DIR__, echo, die()
 *
 * 📌 STATUS: Stabilno
 * 📅 ZGODOVINA:
 *     - v100: prva implementacija – bere kanonični_varuhi.json
 * 👤 AVTOR: AstraMentalica Mojster
 * ============================================================
 *
 * VARUH ODKLEPANJE – pravila:
 * ─────────────────────────────────────────────────────────
 *   stellarion    → vedno_odklenjen = true (štartni varuh)
 *   ostali        → tocke_za_odkritje točk Z varuhom
 *                   ali nakup z relikvijo/kristalom v tržnici
 *   S0–S1         → dostop do 3 varuhov max
 *   S2–S3         → do 8 varuhov
 *   S4–S5         → vsi 31
 * ─────────────────────────────────────────────────────────
 */
declare(strict_types=1);

defined('SISTEM_VARNOST') or die('Direkten dostop ni dovoljen.');

// Memory cache za čas zahtevka
$_ENTITETA_CACHE = null;

// Max varuhov po vlogi
const ENTITETA_MAX_VARUHOV = [
    VLOGA_GOST  => 1,
    VLOGA_S0    => 1,
    VLOGA_S1    => 3,
    VLOGA_S2    => 8,
    VLOGA_S3    => 15,
    VLOGA_S4    => 25,
    VLOGA_S5    => 31,
    VLOGA_ADMIN => 31,
];

// ============================================================
// INTERNI LOADER – enkrat na zahtevek
// ============================================================

function _entiteta_data(): array
{
    global $_ENTITETA_CACHE;
    if ($_ENTITETA_CACHE !== null) {
        return $_ENTITETA_CACHE;
    }

    $pot = defined('POT_ENTITETE_VARUHI')
        ? POT_ENTITETE_VARUHI
        : POT_ENTITETE . '/kanonični_varuhi.json';

    if (!file_exists($pot)) {
        $_ENTITETA_CACHE = ['varuhi' => [], 'zivali' => [], 'relikvije' => [], 'arhetipi' => []];
        error_log("[ENTITETA] Manjka kanonični JSON: $pot");
        return $_ENTITETA_CACHE;
    }

    $raw = file_get_contents($pot);
    $data = json_decode($raw, true);

    if (!$data || json_last_error() !== JSON_ERROR_NONE) {
        $_ENTITETA_CACHE = ['varuhi' => [], 'zivali' => [], 'relikvije' => [], 'arhetipi' => []];
        error_log('[ENTITETA] Napaka pri branju JSON: ' . json_last_error_msg());
        return $_ENTITETA_CACHE;
    }

    // Zgradi relikvije lookup po id (so znotraj varuha kot array)
    $relikvije = [];
    foreach ($data['varuhi'] ?? [] as $vid => $v) {
        foreach ($v['relikvije'] ?? [] as $r) {
            $relikvije[$r['id']] = [
                ...$r,
                'varuh_id' => $vid,
                'varuh_ime' => $v['ime'],
            ];
        }
    }
    $data['relikvije'] = $relikvije;

    $_ENTITETA_CACHE = $data;
    return $_ENTITETA_CACHE;
}

// ============================================================
// VARUHI
// ============================================================

/**
 * Vrne enega varuha po ID ali null.
 */
function entiteta_varuh(string $id): ?array
{
    return _entiteta_data()['varuhi'][$id] ?? null;
}

/**
 * Vrne vse varuhe (31).
 */
function entiteta_varuhi_vse(): array
{
    return _entiteta_data()['varuhi'];
}

/**
 * Vrne varuhe dostopne za dano vlogo (po max limitu).
 * Vedno vključi stellarion (vedno_odklenjen).
 * Ostale razvrsti po tocke_za_odkritje.
 */
function entiteta_varuhi_za_vlogo(int $vloga): array
{
    $vsi = entiteta_varuhi_vse();
    $max = _entiteta_max_varuhov($vloga);

    // Vedno odklenjen gre naprej
    $vedno   = array_filter($vsi, fn($v) => $v['vedno_odklenjen']);
    $ostali  = array_filter($vsi, fn($v) => !$v['vedno_odklenjen']);

    // Razvrsti po tocke_za_odkritje (lažje naprej)
    uasort($ostali, fn($a, $b) => $a['tocke_za_odkritje'] <=> $b['tocke_za_odkritje']);

    $rezultat = $vedno;
    $preostalo = $max - count($vedno);
    foreach ($ostali as $id => $v) {
        if ($preostalo <= 0) break;
        $rezultat[$id] = $v;
        $preostalo--;
    }

    return $rezultat;
}

/**
 * Priporoči varuha za uporabnika glede na arhetipsko pot.
 * Vrne do 3 priporočene.
 */
function entiteta_varuh_priporocen(string $uid): array
{
    $profil   = shramba_beri_enega(PODATKI_UPORABNIKI . '/' . $uid, 'profil');
    $arhetip  = $profil['arhetipska_pot'] ?? null;
    $odklenjeni = array_keys(entiteta_odklenjeni($uid));

    if ($arhetip) {
        $arh = entiteta_arhetip($arhetip);
        $priporoceni = $arh['priporoceni_varuhi'] ?? [];
    } else {
        // Brez arhetipa – priporoči po elementih
        $priporoceni = ['stellarion', 'lunara', 'terra'];
    }

    // Filtriraj – samo tisti ki niso že odklenjeni
    $novi = array_filter($priporoceni, fn($id) => !in_array($id, $odklenjeni, true));

    return array_map(
        fn($id) => entiteta_varuh($id),
        array_slice(array_values($novi), 0, 3)
    );
}

// ============================================================
// ŽIVALI
// ============================================================

function entiteta_zival(string $id): ?array
{
    return _entiteta_data()['zivali'][$id] ?? null;
}

function entiteta_zivali_vse(): array
{
    return _entiteta_data()['zivali'];
}

/**
 * Žival ki pripada varuhu.
 */
function entiteta_zival_varuha(string $varuh_id): ?array
{
    $v = entiteta_varuh($varuh_id);
    if (!$v) return null;
    return entiteta_zival($v['magicna_zival_id']);
}

// ============================================================
// RELIKVIJE
// ============================================================

function entiteta_relikvija(string $id): ?array
{
    return _entiteta_data()['relikvije'][$id] ?? null;
}

/**
 * Vse relikvije določenega varuha.
 */
function entiteta_relikvije_varuha(string $varuh_id): array
{
    $v = entiteta_varuh($varuh_id);
    return $v['relikvije'] ?? [];
}

/**
 * Preveri ali ima uporabnik relikvijo.
 */
function entiteta_ima_relikvijo(string $uid, string $relikvija_id): bool
{
    $inv = inventar_pridobi($uid);
    return isset($inv['predmeti']['relikvija'][$relikvija_id]);
}

// ============================================================
// ARHETIPI
// ============================================================

function entiteta_arhetip(string $id): ?array
{
    return _entiteta_data()['arhetipi'][$id] ?? null;
}

function entiteta_arhetipi_vse(): array
{
    return _entiteta_data()['arhetipi'];
}

/**
 * Vrne varuhe priporočene za arhetip.
 */
function entiteta_arhetip_varuhi(string $arhetip_id): array
{
    $arh = entiteta_arhetip($arhetip_id);
    if (!$arh) return [];

    return array_filter(
        array_map(fn($id) => entiteta_varuh($id), $arh['priporoceni_varuhi'] ?? []),
        fn($v) => $v !== null
    );
}

// ============================================================
// ODKLEPANJE VARUHOV
// ============================================================

/**
 * Odklenj varuha za uporabnika.
 * Preveri točke ali prisilno (admin/nagrada).
 */
function entiteta_odkleniti(string $uid, string $varuh_id, bool $prisiljeno = false): array
{
    $varuh = entiteta_varuh($varuh_id);
    if (!$varuh) {
        return ['status' => 'napaka', 'sporocilo' => "Varuh ne obstaja: $varuh_id"];
    }

    // Preveri ali je že odklenjen
    $odklenjeni = entiteta_odklenjeni($uid);
    if (isset($odklenjeni[$varuh_id])) {
        return ['status' => 'preskoceno', 'sporocilo' => 'Varuh je že odklenjen.'];
    }

    // Preveri pogoj (razen prisiljeno ali vedno_odklenjen)
    if (!$prisiljeno && !$varuh['vedno_odklenjen']) {
        $potrebno = $varuh['tocke_za_odkritje'];
        if ($potrebno > 0 && !inventar_ima($uid, 'tocke', 'xp', $potrebno)) {
            return [
                'status'   => 'napaka',
                'sporocilo'=> "Premalo točk. Potrebno: $potrebno",
                'potrebno' => $potrebno,
            ];
        }
        // Odštej točke če so potrebne
        if ($potrebno > 0) {
            inventar_porabi($uid, 'tocke', 'xp', $potrebno, 'odklepanje_varuha_' . $varuh_id);
        }
    }

    // Shrani odklenjenega varuha
    $pot  = PODATKI_UPORABNIKI . '/' . $uid . '/varuhi';
    $data = shramba_beri_enega($pot, 'odklenjeni') ?? ['id' => 'odklenjeni', 'seznam' => []];
    $data['seznam'][$varuh_id] = [
        'odklenjen' => time(),
        'aktiven'   => false,
    ];
    shramba_zapisi($pot, $data);

    // Dodaj nagrado v inventar
    $nagrada = $varuh['nagrada_za_odkritje'] ?? '';
    inventar_dodaj($uid, 'kristal', 'common', 1, 'odklepanje_varuha_' . $varuh_id);

    // XP bonus
    if ($varuh['xp_bonus'] > 0) {
        tocke_dodaj($uid, $varuh['xp_bonus'], 'odklepanje_varuha');
    }

    dogodek_sprozi('varuh.odklenjen', [
        'uid'       => $uid,
        'varuh_id'  => $varuh_id,
        'nagrada'   => $nagrada,
    ]);

    return [
        'status'   => 'uspeh',
        'varuh'    => $varuh,
        'nagrada'  => $nagrada,
        'xp'       => $varuh['xp_bonus'],
    ];
}

/**
 * Vrne seznam odklenjenjih varuhov za uporabnika.
 * Vedno vključi stellarion.
 */
function entiteta_odklenjeni(string $uid): array
{
    $pot  = PODATKI_UPORABNIKI . '/' . $uid . '/varuhi';
    $data = shramba_beri_enega($pot, 'odklenjeni') ?? ['seznam' => []];
    $seznam = $data['seznam'];

    // Stellarion je vedno odklenjen
    if (!isset($seznam['stellarion'])) {
        $seznam['stellarion'] = ['odklenjen' => 0, 'aktiven' => true];
    }

    return $seznam;
}

/**
 * Nastavi aktivnega varuha za uporabnika.
 */
function entiteta_nastavi_aktivnega(string $uid, string $varuh_id): array
{
    $odklenjeni = entiteta_odklenjeni($uid);
    if (!isset($odklenjeni[$varuh_id])) {
        return ['status' => 'napaka', 'sporocilo' => 'Varuh ni odklenjen.'];
    }

    $pot  = PODATKI_UPORABNIKI . '/' . $uid . '/varuhi';
    $data = shramba_beri_enega($pot, 'odklenjeni') ?? ['id' => 'odklenjeni', 'seznam' => []];

    // Deaktiviraj vse, aktiviraj izbranega
    foreach ($data['seznam'] as $id => &$v) {
        $v['aktiven'] = ($id === $varuh_id);
    }
    shramba_zapisi($pot, $data);

    return ['status' => 'uspeh', 'aktiven' => $varuh_id];
}

/**
 * Vrne aktivnega varuha za uporabnika (stellarion če ni drugega).
 */
function entiteta_aktivni_varuh(string $uid): array
{
    $odklenjeni = entiteta_odklenjeni($uid);
    foreach ($odklenjeni as $id => $meta) {
        if ($meta['aktiven']) {
            return entiteta_varuh($id) ?? entiteta_varuh('stellarion');
        }
    }
    return entiteta_varuh('stellarion');
}

// ============================================================
// INTERNE
// ============================================================

function _entiteta_max_varuhov(int $vloga): int
{
    foreach (array_reverse(ENTITETA_MAX_VARUHOV, true) as $min => $max) {
        if ($vloga >= $min) return $max;
    }
    return 1;
}
