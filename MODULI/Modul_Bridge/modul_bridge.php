<?php
/**
 * ============================================================
 * POT: MODULI/Modul_Bridge/modul_bridge.php
 * 📅 VERZIJA: v1.0.0 (28.6.2026)
 * ============================================================
 *
 * 🏛️ NIVO: BRIDGE (jedro)
 *
 * 📰 NAMEN:
 *     Edina datoteka, ki jo moduli kličejo preko require_once.
 *     Avtomatsko zazna ali ASTRAMENTALICA sistem obstaja.
 *     Če obstaja → uporabi pravi sistem.
 *     Če ne → uporabi demo/mini način.
 *
 * 🔧 JAVNI RAZRED:
 *     - Modul_Bridge::vloga_preveri(string $zahtevana): bool
 *     - Modul_Bridge::uporabnik_pridobi(): array
 *     - Modul_Bridge::podatki_beri(string $kljuc): mixed
 *     - Modul_Bridge::podatki_pisi(string $kljuc, $vrednost): bool
 *     - Modul_Bridge::modul_klic(string $modul, string $akcija, array $podatki): array
 *     - Modul_Bridge::klic(string $akcija, array $podatki): array
 *
 * 🚫 PREPOVEDI:
 *     - Brez die(), exit()
 *     - Brez echo, print_r, var_dump
 *
 * 📌 STATUS:
 *     Stabilno
 *
 * 👤 AVTOR:
 *     AstraMentalica Mojster
 *
 * 🌐 JEZIK:
 *     sl
 *
 * 🏷️ OZNAKE:
 *     bridge, modul, jedro
 * ============================================================
 */
declare(strict_types=1);

// Prepreči večkratno nalaganje
if (class_exists('Modul_Bridge', false)) {
    return;
}

// ── 1. PREVERI SISTEM ───────────────────────────────────────
$bridgeRoot = __DIR__;
$sistemObstaja = false;

// Poišči pot.php (SIDRO) v različnih lokacijah
$iskanePoti = [
    $bridgeRoot . '/../../pot.php',           // standard: MODULI/ → root
    $bridgeRoot . '/../../../pot.php',        // en nivo globlje
    $_SERVER['DOCUMENT_ROOT'] . '/pot.php',   // koren domene
];

foreach ($iskanePoti as $pot) {
    if (file_exists($pot)) {
        require_once $pot;
        $sistemObstaja = true;
        break;
    }
}

define('BRIDGE_SISTEM_OBSTAJA', $sistemObstaja);

// ── 2. NALOŽI USTREZEN STACK ────────────────────────────────
if ($sistemObstaja) {
    // Pravi sistem – uporabi sistemske funkcije
    require_once $bridgeRoot . '/jedro/sistemske_funkcije.php';
} else {
    // Demo način – uporabi mini sistem
    define('BRIDGE_VARNOST', true);
    require_once $bridgeRoot . '/embed/mini_konstante.php';
    require_once $bridgeRoot . '/embed/mini_vloge.php';
    require_once $bridgeRoot . '/embed/mini_seja.php';
    require_once $bridgeRoot . '/embed/mini_cache.php';
    require_once $bridgeRoot . '/embed/mini_izhod.php';
}

// ── 3. BRIDGE RAZRED ────────────────────────────────────────
/**
 * Modul_Bridge – statični most med moduli in sistemom.
 *
 * Modul kliče Modul_Bridge::metoda() in ne ve,
 * ali dela s pravim sistemom ali demo načinom.
 */
class Modul_Bridge
{
    /**
     * Preveri ali ima uporabnik zahtevano vlogo.
     */
    public static function vloga_preveri(string $zahtevana): bool
    {
        if (defined('BRIDGE_SISTEM_OBSTAJA') && BRIDGE_SISTEM_OBSTAJA && function_exists('pravice_preveri_vlogo')) {
            return pravice_preveri_vlogo(_vloga_v_string_v_int($zahtevana));
        }
        // Demo način – vedno true (admin)
        return true;
    }

    /**
     * Pridobi trenutnega uporabnika.
     */
    public static function uporabnik_pridobi(): array
    {
        if (defined('BRIDGE_SISTEM_OBSTAJA') && BRIDGE_SISTEM_OBSTAJA && function_exists('uporabniki_trenutni')) {
            $upo = uporabniki_trenutni();
            if (is_array($upo)) {
                return $upo;
            }
        }
        // Demo način
        if (function_exists('mini_pridobi_uporabnika')) {
            return mini_pridobi_uporabnika();
        }
        return [
            'id'    => 0,
            'ime'   => 'Demo',
            'vloga' => 100,
        ];
    }

    /**
     * Preberi podatke preko Bridge-a.
     */
    public static function podatki_beri(string $kljuc): mixed
    {
        // Demo način – vrni prazno
        return null;
    }

    /**
     * Shrani podatke preko Bridge-a (z dovoljenjem).
     */
    public static function podatki_pisi(string $kljuc, mixed $vrednost): bool
    {
        // Demo način – ne shranjuj
        return true;
    }

    /**
     * Pokliči drugega modula preko Bridge-a.
     */
    public static function modul_klic(string $modul, string $akcija, array $podatki = []): array
    {
        // V demo načinu vrni prazno
        return ['status' => 'demo', 'sporocilo' => "Demo klic: $modul/$akcija"];
    }

    /**
     * Klic sistema preko mostu.
     */
    public static function klic(string $akcija, array $podatki = []): array
    {
        return self::modul_klic('sistem', $akcija, $podatki);
    }
}

// ── 4. POMOŽNE FUNKCIJE ──────────────────────────────────────

if (!function_exists('_vloga_v_string_v_int')) {
    function _vloga_v_string_v_int(string $vloga): int
    {
        return match (strtoupper($vloga)) {
            'GOST' => 0,
            'S0'   => 10,
            'S1'   => 20,
            'S2'   => 30,
            'S3'   => 40,
            'S4'   => 50,
            'S5'   => 60,
            'ADMIN' => 100,
            default => 0,
        };
    }
}

// ── 5. PODPORA ZA STAR NAČIN (direkten klic) ────────────────
if (!defined('SISTEM_OBSTAJA')) {
    define('SISTEM_OBSTAJA', BRIDGE_SISTEM_OBSTAJA);
}
