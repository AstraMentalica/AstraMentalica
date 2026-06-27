<?php
/**
 * ============================================================
 * POT: ADAPTER/zahteve/adapter_glave.php
 * 📅 VERZIJA: v114 (9.6.2026 18:00)
 * ============================================================
 *
 * 🏛️ NIVO: ADAPTER
 *
 * 📰 NAMEN:
 *     Upravljanje HTTP glav zahteve.
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - adapter_glava_pridobi(AdapterZahteva $zahteva, string $ime, ?string $privzeto =...): ?string
 *     - adapter_glava_obstaja(AdapterZahteva $zahteva, string $ime): bool
 *     - adapter_glave_vse(AdapterZahteva $zahteva): array
 *     - adapter_glava_avtorizacija(AdapterZahteva $zahteva): ?string
 *     - adapter_glava_bearer_token(AdapterZahteva $zahteva): ?string
 *
 * 📡 ODVISNOSTI:
 *     - (nobene)
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
 *     - v114: uskladitev s Header Standard v114
 *
 * 👤 AVTOR:
 *     AstraMentalica Mojster
 *
 * 🌐 JEZIK:
 *     sl
 *
 * 🏷️ OZNAKE:
 *     adapter, zahteve, glave
 * ============================================================
 */
declare(strict_types=1);

function adapter_glava_pridobi(AdapterZahteva $zahteva, string $ime, ?string $privzeto = null): ?string
{
    $glave = $zahteva->glave();
    
    // Poskusi z tocno tem imenom
    if (isset($glave[$ime])) {
        return $glave[$ime];
    }
    
    // Poskusi z malimi crkami
    $imeMale = strtolower($ime);
    foreach ($glave as $kljuc => $vrednost) {
        if (strtolower($kljuc) === $imeMale) {
            return $vrednost;
        }
    }
    
    return $privzeto;
}

function adapter_glava_obstaja(AdapterZahteva $zahteva, string $ime): bool
{
    return adapter_glava_pridobi($zahteva, $ime) !== null;
}

function adapter_glave_vse(AdapterZahteva $zahteva): array
{
    return $zahteva->glave();
}

function adapter_glava_avtorizacija(AdapterZahteva $zahteva): ?string
{
    // Poskusi z Authorization glavo
    $auth = adapter_glava_pridobi($zahteva, 'Authorization');
    if ($auth !== null) {
        return $auth;
    }
    
    // Poskusi z X-API-Key
    $apiKey = adapter_glava_pridobi($zahteva, 'X-API-Key');
    if ($apiKey !== null) {
        return 'Bearer ' . $apiKey;
    }
    
    return null;
}

function adapter_glava_bearer_token(AdapterZahteva $zahteva): ?string
{
    $auth = adapter_glava_avtorizacija($zahteva);
    if ($auth !== null && preg_match('/Bearer\s+(.+)/i', $auth, $ujemanja)) {
        return $ujemanja[1];
    }
    
    return null;
}