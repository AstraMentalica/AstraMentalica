<?php
/**
 * ============================================================
 * POT: SISTEM/kernel/jedro/01_upravljalec_svetov.php
 * VERZIJA: v117 (30.6.2026)
 * ============================================================
 * NAMEN: Upravljalec svetov – generira whiteliste glede na vlogo
 *        prijavljenega uporabnika (RBAC) iz JSON konfiguracij.
 * ODVISNOSTI: pot.php (VLOGA_* konstante, PODATKI_REGISTRI)
 * ZGODOVINA:
 *   - v117: Data-driven prek JSON; odstranjeni hardcodirani whitelisti
 *   - v116: hardcodirani RBAC whitelisti (umaknjeno)
 *   - v115: samo svetovi, brez RBAC
 * ============================================================
 */
declare(strict_types=1);
defined('SISTEM_VARNOST') or die('Direkten dostop ni dovoljen.');

// ===== JAVNI VMESNIK =====

function svetovi_dovoljeni(int $vloga): array
{
    return _wf(_wl('whitelist_svetovi.json'), $vloga);
}

function moduli_dovoljeni(int $vloga): array
{
    $reg = _mr(); $dov = [];
    foreach ($reg as $key => $mod) {
        if ($vloga >= ($mod['vloga_min'] ?? 100) && ($mod['aktiviran'] ?? false)) {
            $dov[$key] = $mod['ime'] ?? $key;
        }
    }
    return $dov;
}

function gradniki_dovoljeni(int $vloga): array
{
    return _wf(_wl('whitelist_gradniki.json'), $vloga);
}

function svetovi_je_dovoljen(string $svet, int $vloga): bool
{
    return in_array($svet, svetovi_dovoljeni($vloga), true);
}

function upravljalec_svetov_izvedi(array $zaht): array
{
    $vloga = VLOGA_GOST;
    if (session_status() === PHP_SESSION_ACTIVE) {
        $vloga = (int)($_SESSION['uporabnik_vloga'] ?? VLOGA_GOST);
    }
    $ov = _ov($vloga);
    $zaht['sistem']['whitelist_svetovi']  = $ov['svetovi'] ?? svetovi_dovoljeni($vloga);
    $zaht['sistem']['whitelist_moduli']   = $ov['moduli'] ?? moduli_dovoljeni($vloga);
    $zaht['sistem']['whitelist_gradniki'] = $ov['gradniki'] ?? gradniki_dovoljeni($vloga);
    $zaht['sistem']['whitelist_vloga']    = $vloga;
    $zaht['sistem']['whitelist_generiran'] = time();
    return $zaht;
}

// ===== INTERNO =====

function _wl(string $file): array
{
    $pot = defined('PODATKI_REGISTRI') ? PODATKI_REGISTRI . '/whitelist/' . $file : '';
    return ($pot && file_exists($pot)) ? (json_decode(@file_get_contents($pot), true) ?? []) : [];
}

function _wf(array $wlist, int $vloga): array
{
    $res = [];
    foreach ($wlist as $e) {
        if ($vloga >= ($e['vloga_min'] ?? 0)) $res[] = $e['kljuc'] ?? '';
    }
    return array_values(array_filter($res));
}

function _mr(): array
{
    $pot = defined('PODATKI_REGISTRI') ? PODATKI_REGISTRI . '/moduli_register.json' : '';
    return ($pot && file_exists($pot)) ? (json_decode(@file_get_contents($pot), true) ?? []) : [];
}

function _ov(int $vloga): array
{
    $pot = defined('PODATKI_REGISTRI') ? PODATKI_REGISTRI . '/whitelist/override/vloga_' . $vloga . '.json' : '';
    return ($pot && file_exists($pot)) ? (json_decode(@file_get_contents($pot), true) ?? []) : [];
}
