<?php
declare(strict_types=1);
/* ============================================================
 * POT: SISTEM/kernel/env_loader.php
 * NAMEN: Nalagalnik .env datotek
 * ============================================================
 * v111
 * ============================================================
 *
 * Prebere .env datoteke in nastavi spremenljivke okolja.
 * Enostaven format: KLJUČ=VREDNOST (en par na vrstico).
 * Vrstice z # so komentarji, prazne se preskočijo.
 * 
 * Uporaba:
 *   env_nalozi(PODATKI_ENV . '/.env_sistem');
 *   $vrednost = env_pridobi('APP_DEBUG', false);
 * 
 *  📡 ODVISNOSTI:
 *     - 
 *
 *  🚫 PREPOVEDI: Brez echo, direktnih poti, $_GET/$_POS
 *     - 
 * 
 * ============================================================
 * 
 */

defined('SISTEM_VARNOST') or die('Direkten dostop ni dovoljen.');

/**
 * Naloži .env datoteko in nastavi spremenljivke v $_ENV.
 * Ne prepisuje že nastavljenih spremenljivk.
 * 
 * @param string $datoteka  pot do .env datoteke
 */
function env_nalozi(string $datoteka): void {
    if (!file_exists($datoteka)) return;
    
    $vrstice = file($datoteka, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    
    foreach ($vrstice as $vrstica) {
        $vrstica = trim($vrstica);
        
        // Preskoči komentarje in prazne vrstice
        if ($vrstica === '' || str_starts_with($vrstica, '#')) continue;
        
        // Potrebujemo znak =
        if (!str_contains($vrstica, '=')) continue;
        
        // Razdeli na ključ in vrednost
        [$kljuc, $vrednost] = explode('=', $vrstica, 2);
        $kljuc    = trim($kljuc);
        $vrednost = trim($vrednost, " \t\"'");  // Odstrani narekovaje
        
        if ($kljuc === '') continue;
        
        // Ne prepisuj že nastavljenih
        if (!array_key_exists($kljuc, $_ENV)) {
            $_ENV[$kljuc] = $vrednost;
            putenv("$kljuc=$vrednost");
        }
    }
}

/**
 * Pridobi vrednost spremenljivke okolja.
 * Najprej išče v $_ENV, nato v getenv(), nazadnje vrne privzeto.
 * 
 * @param string $kljuc     ime spremenljivke
 * @param mixed  $privzeto  privzeta vrednost, če ne obstaja
 * @return mixed
 */
function env_pridobi(string $kljuc, $privzeto = null) {
    return $_ENV[$kljuc] ?? getenv($kljuc) ?: $privzeto;
}

// ----------------------------------------------------------
// AVTOMATSKO NALOŽI VSE .env DATOTEKE IZ POT_SEF
// ----------------------------------------------------------
$sefPot = defined('POT_SEF') ? POT_SEF : (defined('PODATKI_ENV') ? PODATKI_ENV : '');
if ($sefPot !== '') {
    foreach ([
        $sefPot . '/.env',
        $sefPot . '/.env_api',
        $sefPot . '/.env_baza',
    ] as $envDatoteka) {
        env_nalozi($envDatoteka);
    }
}