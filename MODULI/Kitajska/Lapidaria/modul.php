<?php
/**
 * ============================================================
 * MODUL: Lapidaria
 * POT: MODULI/Lapidaria/modul.php
 * 📅 VERZIJA: v1.0.0 (24.6.2026)
 * ============================================================
 *
 * 🏛️ NIVO: MODUL
 *
 * 📰 NAMEN:
 *     Standardna vstopna točka za Lapidaria modul
 *     Zagotovi kompatibilnost z Modul_Bridge
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - modul_lapidaria_akcija(string $akcija, array $podatki): array
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
 *     modul, lapidaria
 * ============================================================
 */

declare(strict_types=1);

// Varnost
if (!defined('BRIDGE_VARNOST') && !defined('SISTEM_VARNOST')) {
    die('Direktni dostop je prepovedan');
}

// Vključi potrebne datoteke
require_once __DIR__ . '/modul_lapidaria_funkcije.php';
require_once __DIR__ . '/modul_lapidaria_pravila.php';
require_once __DIR__ . '/modul_lapidaria_jsonbaza.php';

// Inicializiraj modul
$modul_lapidaria = new ModulLapidaria();

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
function modul_lapidaria_akcija(string $akcija, array $podatki = []): array {
    global $modul_lapidaria;
    
    // Preveri dostop
    if (!Modul_Bridge::vloga_preveri('S0')) {
        return odziv_napaka('Dostop zavrnjen', 403);
    }
    
    // Izvrši akcijo
    $rezultat = $modul_lapidaria->obdelajZahtevek($akcija, $podatki);
    
    if (isset($rezultat['uspeh']) && $rezultat['uspeh']) {
        return odziv_uspeh($rezultat['vsebina'] ?? $rezultat, $rezultat['sporocilo'] ?? 'Uspeh');
    } else {
        return odziv_napaka($rezultat['napaka'] ?? 'Neznana napaka', 400);
    }
}

// ============================
// AKCIJE
// ============================

function _modul_lapidaria_info(array $podatki): array {
    global $modul_lapidaria;
    
    $podatki_modula = $modul_lapidaria->pridobiOsnovnePodatke();
    
    return odziv_uspeh([
        'ime' => $podatki_modula['ime'],
        'id' => 'lapidaria',
        'verzija' => $podatki_modula['razlicica'],
        'opis' => $podatki_modula['opis'],
        'avtor' => $podatki_modula['avtor']
    ], 'Informacije o modulu');
}

function _modul_lapidaria_domov(array $podatki): array {
    global $modul_lapidaria;
    
    $vsebina = $modul_lapidaria->pridobiVsebino($podatki);
    
    return odziv_uspeh([
        'naslov' => $vsebina['naslov'] ?? 'Lapidaria',
        'vsebina' => $vsebina['vsebina'] ?? ''
    ], 'Domov');
}

// ── ČE SE KLIČE DIREKTNO (brez Bridge-a) ──────────────────
if (basename($_SERVER['SCRIPT_FILENAME'] ?? '') === 'modul.php' && !defined('SISTEM_OBSTAJA')) {
    $akcija = $_REQUEST['akcija'] ?? 'domov';
    $podatki = $_REQUEST;
    $odziv = modul_lapidaria_akcija($akcija, $podatki);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($odziv, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}