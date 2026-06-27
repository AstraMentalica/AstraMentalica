<?php
/**
 * ============================================================
 * MODUL: Devorum
 * POT: MODULI/Devorum/modul.php
 * 📅 VERZIJA: v1.0.0 (24.6.2026)
 * ============================================================
 *
 * 🏛️ NIVO: MODUL
 *
 * 📰 NAMEN:
 *     Standardna vstopna točka za Devorum modul
 *     Zagotovi kompatibilnost z Modul_Bridge
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - modul_devorum_akcija(string $akcija, array $podatki): array
 *
 * 📡 ODVISNOSTI:
 *     - Modul_Bridge
 *
 * 🚫 PREPOVEDI:
 *     - Brez direktnih klicev v SISTEM/
 *
 * 📌 STATUS:
 *     Pripravljen
 *
 * 👤 AVTOR:
 *     AstraMentalica Mojster
 *
 * 🌐 JEZIK:
 *     sl
 *
 * 🏷️ OZNAKE:
 *     modul, devorum
 * ============================================================
 */

declare(strict_types=1);

// Varnost
if (!defined('BRIDGE_VARNOST') && !defined('SISTEM_VARNOST')) {
    die('Direktni dostop je prepovedan');
}

// Vključi potrebne datoteke
require_once __DIR__ . '/modul_devorum_funkcije.php';
require_once __DIR__ . '/modul_devorum_pravila.php';
require_once __DIR__ . '/modul_devorum_jsonbaza.php';

// Inicializiraj modul
$modul_devorum = new ModulDevorum();

// ============================
// VSTOPNA TOČKA MODULA
// ============================

/**
 * Glavna funkcija modula — kliče jo SISTEM preko Modul_Bridge.
 *
 * @param string $akcija   Kaj modul mora narediti
 * @param array  $podatki  Parametri za akcijo
 * @return array           Rezultat (vedno array, nikoli HTML)
 */
function modul_devorum_akcija(string $akcija, array $podatki = []): array {
    global $modul_devorum;
    
    // Preveri dostop
    if (!Modul_Bridge::vloga_preveri('S0')) {
        return odziv_napaka('Dostop zavrnjen', 403);
    }
    
    // Izvrši akcijo
    $rezultat = $modul_devorum->obdelajZahtevek($akcija, $podatki);
    
    if (isset($rezultat['uspeh']) && $rezultat['uspeh']) {
        return odziv_uspeh($rezultat['vsebina'] ?? $rezultat, $rezultat['sporocilo'] ?? 'Uspeh');
    } else {
        return odziv_napaka($rezultat['napaka'] ?? 'Neznana napaka', 400);
    }
}

// ============================
// AKCIJE
// ============================

function _modul_devorum_info(array $podatki): array {
    global $modul_devorum;
    
    $podatki_modula = $modul_devorum->pridobiOsnovnePodatke();
    
    return odziv_uspeh([
        'ime' => $podatki_modula['ime'],
        'id' => 'devorum',
        'verzija' => $podatki_modula['razlicica'],
        'opis' => $podatki_modula['opis'],
        'avtor' => $podatki_modula['avtor']
    ], 'Informacije o modulu');
}

function _modul_devorum_domov(array $podatki): array {
    global $modul_devorum;
    
    $vsebina = $modul_devorum->pridobiVsebino($podatki);
    
    return odziv_uspeh([
        'naslov' => $vsebina['naslov'] ?? 'Devorum',
        'vsebina' => $vsebina['vsebina'] ?? ''
    ], 'Domov');
}

// ── ČE SE KLIČE DIREKTNO (brez Bridge-a) ──────────────────
if (basename($_SERVER['SCRIPT_FILENAME'] ?? '') === 'modul.php' && !defined('SISTEM_OBSTAJA')) {
    $akcija = $_REQUEST['akcija'] ?? 'domov';
    $podatki = $_REQUEST;
    $odziv = modul_devorum_akcija($akcija, $podatki);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($odziv, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}