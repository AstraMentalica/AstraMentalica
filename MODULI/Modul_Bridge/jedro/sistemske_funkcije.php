<?php
/**
 * ============================================================
 * POT: MODULI/Modul_Bridge/jedro/sistemske_funkcije.php
 * 📅 VERZIJA: v118 (19.6.2026 02:00)
 * ============================================================
 *
 * 🏛️ NIVO: BRIDGE / JEDRO
 *
 * 📰 NAMEN:
 *     Bridge funkcije za pravi ASTRAMENTALICA sistem.
 *     Uporablja se SAMO ko SISTEM_OBSTAJA === true.
 *     Kliče sistemske ekvivalente namesto mini_ funkcij.
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - bridge_inicijalizacija(): void
 *     - bridge_prikazi_pregled(): void
 *     - bridge_prikazi_testnik(): void
 *     - bridge_prikazi_generator(): void
 *     - bridge_pakiraj_modul(): void
 *
 * 📡 ODVISNOSTI:
 *     - SISTEM/kernel/jedro/04_seja.php
 *     - SISTEM/kernel/jedro/05_pravice.php
 *     - SISTEM/storitve_svetov/moduli/
 *
 * 🚫 PREPOVEDI:
 *     - Brez echo, print_r, var_dump
 *     - Brez die(), exit()
 *     - Brez direktnih poti (uporabi konstante!)
 *
 * 📌 STATUS:
 *     Stabilno
 *
 * 📅 ZGODOVINA:
 *     - v118: popravljena struktura – direktno iskanje modulov v MODULI/*/podatki/manifest.json
 *     - v114: stara struktura (s kategorijami)
 *
 * 👤 AVTOR:
 *     AstraMentalica Mojster
 *
 * 🌐 JEZIK:
 *     sl
 *
 * 🏷️ OZNAKE:
 *     bridge, jedro, sistemske_funkcije
 * ============================================================
 */

declare(strict_types=1);

defined('SISTEM_VARNOST') or die('Direkten dostop ni dovoljen.');

// ── INICIALIZACIJA ──────────────────────────────────────────

function bridge_inicijalizacija(): void {
    if (function_exists('seja_zacni')) {
        seja_zacni();
    }
}

// ── PREGLED MODULOV ─────────────────────────────────────────

function bridge_prikazi_pregled(): void {
    // Pridobi module iz sistemskega storitve_svetov sloja
    $pot_storitve = defined('POT_STORITVE') ? POT_STORITVE : null;

    if ($pot_storitve && file_exists($pot_storitve . '/moduli/moduli_pregled.php')) {
        require_once $pot_storitve . '/moduli/moduli_pregled.php';
        $moduli = function_exists('moduli_pridobi_vse') ? moduli_pridobi_vse() : [];
    } else {
        $moduli = _bridge_moduli_iz_map();
    }

    include __DIR__ . '/../prikaz/pregled.php';
}

// ── TESTNIK VLOG ────────────────────────────────────────────

function bridge_prikazi_testnik(): void {
    // Preklop vloge za testiranje (samo v bridge kontekstu)
    if (isset($_GET['vloga']) && function_exists('pravice_preveri_vlogo')) {
        $vloga = (int)$_GET['vloga'];
        if (isset($_SESSION)) {
            $_SESSION['_bridge_test_vloga'] = $vloga;
        }
    }

    $moduli = _bridge_moduli_iz_map();
    include __DIR__ . '/../prikaz/testnik.php';
}

// ── GENERATOR ───────────────────────────────────────────────

function bridge_prikazi_generator(): void {
    $sporocilo = null;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $ime  = trim($_POST['ime'] ?? '');
        $opis = trim($_POST['opis'] ?? '');

        if ($ime !== '' && $opis !== '') {
            require_once __DIR__ . '/../generator/generiraj_modul.php';
            $sporocilo = generiraj_modul($ime, $opis);
        }
    }

    include __DIR__ . '/../prikaz/generator.php';
}

// ── PAKIRANJE ───────────────────────────────────────────────

function bridge_pakiraj_modul(): void {
    require_once __DIR__ . '/../orkestrator/pakirnik.php';
    $ime = $_GET['modul'] ?? '';
    $rezultat = pakirnik_ustvari_zip($ime);
    include __DIR__ . '/../prikaz/pakiranje.php';
}

// ── ZASEBNI POMOČNIKI ───────────────────────────────────────

/**
 * Pridobi module direktno iz map strukture (fallback).
 * NOVA STRUKTURA: MODULI/*/podatki/manifest.json (brez kategorij)
 */
function _bridge_moduli_iz_map(): array {
    $pot_moduli = defined('POT_MODULI') ? POT_MODULI : __DIR__ . '/../../';
    $moduli     = [];

    if (!is_dir($pot_moduli)) {
        return [];
    }

    foreach (glob($pot_moduli . '/*', GLOB_ONLYDIR) as $mod_pot) {
        $ime = basename($mod_pot);
        
        // Preskoči Modul_Bridge in skrite mape
        if ($ime === 'Modul_Bridge' || str_starts_with($ime, '.')) {
            continue;
        }

        // NOVA STRUKTURA: podatki/manifest.json
        $manifest_pot = $mod_pot . '/podatki/manifest.json';
        if (!file_exists($manifest_pot)) {
            continue;
        }

        $manifest = json_decode(file_get_contents($manifest_pot), true);
        if (!$manifest || !isset($manifest['_id'])) {
            continue;
        }

        $moduli[] = [
            'pot'        => $mod_pot,
            'kategorija' => 'splošno', // kategorije so ukinjene
            'manifest'   => $manifest,
        ];
    }

    return $moduli;
}