<?php
/**
 * ============================================================
 * POT: MODULI/Modul_Bridge/embed/mini_sistem.php
 * 📅 VERZIJA: v118 (19.6.2026 02:00)
 * ============================================================
 *
 * 🏛️ NIVO: MODUL (Modul_Bridge/embed)
 *
 * 📰 NAMEN:
 *     Jedro mini sistema – uporabi se, ko ASTRAMENTALICA sistem NE OBSTAJA.
 *     Omogoča popolnoma samostojno delovanje Bridge-a.
 *     PO NOVI STRUKTURI: moduli direktno v MODULI/ (brez kategorij).
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - mini_inicijalizacija(): void
 *     - mini_prijavi_gosta(): void
 *     - mini_prijavi_admina(): void
 *     - mini_pridobi_uporabnika(): array
 *     - mini_moduli_pridobi_vse(): array
 *
 * 📡 ODVISNOSTI:
 *     - mini_konstante.php
 *     - mini_vloge.php
 *     - mini_seja.php
 *     - mini_cache.php
 *     - mini_izhod.php
 *
 * 🚫 PREPOVEDI:
 *     - Brez die(), exit()
 *     - Brez echo, print_r, var_dump
 *
 * 📌 STATUS:
 *     Stabilno
 *
 * 📅 ZGODOVINA:
 *     - v118: popravljena struktura – direktno iskanje modulov v MODULI/*/podatki/manifest.json
 *     - v116: prva implementacija
 *
 * 👤 AVTOR:
 *     AstraMentalica Mojster
 *
 * 🌐 JEZIK:
 *     sl
 *
 * 🏷️ OZNAKE:
 *     modul, bridge, embed, mini, sistem
 * ============================================================
 */
declare(strict_types=1);

// VARNOST – namesto die() uporabimo return (enak vzorec kot index.php)
if (!defined('BRIDGE_VARNOST')) {
    http_response_code(403);
    return;
}

require_once __DIR__ . '/mini_konstante.php';
require_once __DIR__ . '/mini_vloge.php';
require_once __DIR__ . '/mini_seja.php';
require_once __DIR__ . '/mini_cache.php';
require_once __DIR__ . '/mini_izhod.php';

// ============================================================
// INICIALIZACIJA MINI SISTEMA
// ============================================================

function mini_inicijalizacija(): void
{
    mini_seja_zacni();

    // Privzeta vloga, če ni nastavljena
    if (!mini_je_prijavljen()) {
        mini_prijavi_gosta();
    }
}

// ============================================================
// BRIDGE FUNKCIJE ZA MINI SISTEM
// ============================================================

function bridge_inicijalizacija(): void
{
    mini_inicijalizacija();
}

function bridge_prikazi_pregled(): void
{
    $moduli = mini_moduli_pridobi_vse();
    mini_izhod_glava('Pregled modulov');
    include __DIR__ . '/../prikaz/pregled.php';
    mini_izhod_noga();
}

function bridge_prikazi_testnik(): void
{
    $moduli = mini_moduli_pridobi_vse();
    $uporabnik = mini_pridobi_uporabnika();
    mini_izhod_glava('Testnik vlog');
    include __DIR__ . '/../prikaz/testnik.php';
    mini_izhod_noga();
}

function bridge_prikazi_generator(): void
{
    mini_izhod_glava('Generator modulov');
    include __DIR__ . '/../prikaz/generator.php';
    mini_izhod_noga();
}

function bridge_pakiraj_modul(): void
{
    $ime = $_POST['ime'] ?? $_GET['ime'] ?? '';
    if (empty($ime)) {
        mini_izhod_glava('Pakiranje');
        echo '<div class="sporocilo sporocilo-napaka">Ime modula je obvezno.</div>';
        mini_izhod_noga();
        return;
    }

    $rezultat = mini_pakiraj_modul($ime);
    mini_izhod_glava('Pakiranje: ' . htmlspecialchars($ime));
    include __DIR__ . '/../prikaz/pakiranje.php';
    mini_izhod_noga();
}

// ============================================================
// MINI MODULI FUNKCIJE – NOVA STRUKTURA (BREZ KATEGORIJ)
// ============================================================

/**
 * Pridobi vse module – direktno iz MODULI/*/podatki/manifest.json
 * BREZ kategorij (NEBO, ZEMLJA, SIMBOLI so ukinjeni).
 */
function mini_moduli_pridobi_vse(): array
{
    $moduli = [];
    $pot = MINI_MODULI;

    if (!is_dir($pot)) {
        return [];
    }

    // Direktno išči mape v MODULI/ (preskoči Modul_Bridge)
    foreach (glob($pot . '/*', GLOB_ONLYDIR) as $modulPot) {
        $ime = basename($modulPot);
        
        // Preskoči Bridge in skrite mape
        if ($ime === 'Modul_Bridge' || str_starts_with($ime, '.')) {
            continue;
        }

        // NOVA STRUKTURA: podatki/manifest.json
        $manifestPot = $modulPot . '/podatki/manifest.json';
        if (!file_exists($manifestPot)) {
            continue;
        }

        $manifest = json_decode(file_get_contents($manifestPot), true);
        if (!$manifest || !isset($manifest['_id'])) {
            continue;
        }

        // Dodaj pot za prikaz
        $manifest['_pot'] = $modulPot;
        $moduli[] = $manifest;
    }

    return $moduli;
}

function mini_pakiraj_modul(string $ime): array
{
    // Poišči modul
    $pot_modula = null;
    foreach (glob(MINI_MODULI . '/*', GLOB_ONLYDIR) as $pot) {
        if (basename($pot) === $ime) {
            $pot_modula = $pot;
            break;
        }
    }

    if (!$pot_modula) {
        return ['uspeh' => false, 'napaka' => "Modul '$ime' ni najden."];
    }

    // Simulacija pakiranja
    return [
        'uspeh' => true,
        'ime' => $ime,
        'pot' => MINI_MODULI . '/' . $ime . '.zip',
        'velikost' => '0 KB'
    ];
}