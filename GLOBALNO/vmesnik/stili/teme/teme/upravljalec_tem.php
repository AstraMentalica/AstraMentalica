<?php
/**
 * ============================================================
 * POT: GLOBALNO/vmesnik/teme/upravljalec_tem.php
 * 📅 VERZIJA: v114 (9.6.2026 18:00)
 * ============================================================
 *
 * 🏛️ NIVO: GLOBALNO (vmesnik)
 *
 * 📰 NAMEN:
 *     Upravljalec tem in jezikov.
 *     Bere register, naloži CSS, vrne prevedene nize.
 *     Shranjuje izbiro v nastavitve.json uporabnika.
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - tema_pridobi_trenutno(): string
 *     - tema_nastavi(string $id): bool
 *     - tema_pridobi_vse(): array
 *     - tema_html_atribut(): string
 *     - tema_css_link(): string
 *     - jezik_pridobi_trenutnega(): string
 *     - jezik_nastavi(string $id): bool
 *     - jezik_pridobi_vse(): array
 *     - t(string $kljuc, array $zamenjave): string
 *     - nastavitve_shrani(array $nastavitve): bool
 *     - nastavitve_preberi(): array
 *
 * 📡 ODVISNOSTI:
 *     - pot.php (POT_GLOBALNO, POT_UPORABNIKI)
 *     - GLOBALNO/vmesnik/teme/register.json
 *     - GLOBALNO/vmesnik/jeziki/register.json
 *     - GLOBALNO/vmesnik/jeziki/{id}.json
 *
 * 🚫 PREPOVEDI:
 *     - Brez echo, HTML
 *     - Brez direktnih SQL klicev
 *     - Brez session_start()
 *
 * 📌 STATUS:
 *     Stabilno
 *
 * 📅 ZGODOVINA:
 *     - v114: implementacija – teme, jeziki, t(), nastavitve.json
 *
 * 👤 AVTOR:
 *     AstraMentalica Mojster
 *
 * 🌐 JEZIK:
 *     sl
 *
 * 🏷️ OZNAKE:
 *     globalno, vmesnik, tema, jezik, lokalizacija
 * ============================================================
 */

declare(strict_types=1);

defined('SISTEM_VARNOST') or die('Direkten dostop ni dovoljen.');

// ============================================================
// KONSTANTE POT
// ============================================================

define('POT_TEME',   defined('POT_GLOBALNO') ? POT_GLOBALNO . '/vmesnik/teme'   : __DIR__);
define('POT_JEZIKI', defined('POT_GLOBALNO') ? POT_GLOBALNO . '/vmesnik/jeziki'  : dirname(__DIR__) . '/jeziki');

// ============================================================
// TEME
// ============================================================

function tema_register(): array
{
    static $register = null;
    if ($register === null) {
        $datoteka = POT_TEME . '/register.json';
        $register = file_exists($datoteka)
            ? (json_decode(file_get_contents($datoteka), true) ?? [])
            : [];
    }
    return $register;
}

function tema_pridobi_vse(): array
{
    return tema_register()['teme'] ?? [];
}

function tema_pridobi_trenutno(): string
{
    // 1. Seja
    if (isset($_SESSION['nastavitve']['tema'])) {
        return $_SESSION['nastavitve']['tema'];
    }
    // 2. nastavitve.json uporabnika
    $nast = nastavitve_preberi();
    if (!empty($nast['tema'])) {
        return $nast['tema'];
    }
    // 3. Privzeta iz registra
    return tema_register()['privzeta'] ?? 'temna';
}

function tema_nastavi(string $id): bool
{
    $vse = tema_pridobi_vse();
    if (!isset($vse[$id])) {
        return false;
    }
    $_SESSION['nastavitve']['tema'] = $id;
    return nastavitve_shrani(['tema' => $id]);
}

function tema_html_atribut(): string
{
    return htmlspecialchars(tema_pridobi_trenutno());
}

function tema_css_linki(): string
{
    $bazaUrl = '';
    $tema    = tema_pridobi_trenutno();
    $izhod   = '';

    // Spremenljivke (osnova)
    $izhod .= '<link rel="stylesheet" href="' . $bazaUrl . '/GLOBALNO/vmesnik/css/spremenljivke.css">' . "\n";
    // Osnova
    $izhod .= '<link rel="stylesheet" href="' . $bazaUrl . '/GLOBALNO/vmesnik/css/osnova.css">' . "\n";
    // Vse teme – preloader (brskalnik jih predpomni)
    foreach (array_keys(tema_pridobi_vse()) as $tId) {
        $izhod .= '<link rel="stylesheet" href="' . $bazaUrl . '/GLOBALNO/vmesnik/teme/' . $tId . '.css">' . "\n";
    }

    return $izhod;
}

// ============================================================
// JEZIKI
// ============================================================

function jezik_register(): array
{
    static $register = null;
    if ($register === null) {
        $datoteka = POT_JEZIKI . '/register.json';
        $register = file_exists($datoteka)
            ? (json_decode(file_get_contents($datoteka), true) ?? [])
            : [];
    }
    return $register;
}

function jezik_pridobi_vse(): array
{
    return jezik_register()['jeziki'] ?? [];
}

function jezik_pridobi_trenutnega(): string
{
    if (isset($_SESSION['nastavitve']['jezik'])) {
        return $_SESSION['nastavitve']['jezik'];
    }
    $nast = nastavitve_preberi();
    if (!empty($nast['jezik'])) {
        return $nast['jezik'];
    }
    return jezik_register()['privzeti'] ?? 'sl';
}

function jezik_nastavi(string $id): bool
{
    $vsi = jezik_pridobi_vse();
    if (!isset($vsi[$id]) || !($vsi[$id]['dostopen'] ?? false)) {
        return false;
    }
    $_SESSION['nastavitve']['jezik'] = $id;
    return nastavitve_shrani(['jezik' => $id]);
}

function jezik_naloži(string $id): array
{
    static $predpomnilnik = [];
    if (isset($predpomnilnik[$id])) {
        return $predpomnilnik[$id];
    }
    $datoteka = POT_JEZIKI . '/' . $id . '.json';
    if (!file_exists($datoteka)) {
        // Fallback na slovenščino
        $datoteka = POT_JEZIKI . '/sl.json';
    }
    $predpomnilnik[$id] = file_exists($datoteka)
        ? (json_decode(file_get_contents($datoteka), true) ?? [])
        : [];
    return $predpomnilnik[$id];
}

/**
 * Prevod niza. Ključ: 'sekcija.kljuc' ali 'sekcija.podsek.kljuc'.
 * Zamenjave: ['n' => 5] → "{n}" postane "5".
 *
 * Primer: t('prijava.gumb') → "Prijava"
 *         t('cas.pred_min', ['n' => 3]) → "pred 3 min"
 */
function t(string $kljuc, array $zamenjave = []): string
{
    $jezikId = jezik_pridobi_trenutnega();
    $nizi    = jezik_naloži($jezikId);

    $deli    = explode('.', $kljuc);
    $vrednost = $nizi;

    foreach ($deli as $del) {
        if (!is_array($vrednost) || !isset($vrednost[$del])) {
            // Ni prevoda – vrni ključ kot fallback
            return $kljuc;
        }
        $vrednost = $vrednost[$del];
    }

    if (!is_string($vrednost)) {
        return $kljuc;
    }

    // Zamenjave {n} → vrednost
    foreach ($zamenjave as $kl => $vr) {
        $vrednost = str_replace('{' . $kl . '}', (string)$vr, $vrednost);
    }

    return $vrednost;
}

// ============================================================
// NASTAVITVE UPORABNIKA (nastavitve.json)
// ============================================================

function nastavitve_preberi(): array
{
    $pot = _nastavitve_pot();
    if ($pot === null || !file_exists($pot)) {
        return [];
    }
    return json_decode(file_get_contents($pot), true) ?? [];
}

function nastavitve_shrani(array $nove): bool
{
    $pot = _nastavitve_pot();
    if ($pot === null) {
        // Ni prijavljenega uporabnika – shrani samo v sejo
        foreach ($nove as $k => $v) {
            $_SESSION['nastavitve'][$k] = $v;
        }
        return true;
    }

    $obstoječe = nastavitve_preberi();
    $združene  = array_merge($obstoječe, $nove, ['_posodobljeno' => date('Y-m-d H:i:s')]);

    $mapa = dirname($pot);
    if (!is_dir($mapa)) {
        mkdir($mapa, 0755, true);
    }

    return file_put_contents($pot, json_encode($združene, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) !== false;
}

function _nastavitve_pot(): ?string
{
    $userId = $_SESSION['uporabnik']['id'] ?? null;
    if (!$userId || !defined('POT_UPORABNIKI')) {
        return null;
    }
    return POT_UPORABNIKI . '/' . $userId . '/nastavitve.json';
}
