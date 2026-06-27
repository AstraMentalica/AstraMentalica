<?php
/**
 * ============================================================
 * POT: ADAPTER/zahteve/adapter_sanitizacija.php
 * 📅 VERZIJA: v114 (9.6.2026 18:00)
 * ============================================================
 *
 * 🏛️ NIVO: ADAPTER
 *
 * 📰 NAMEN:
 *     Sanitizacija parametrov zahteve.
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - adapter_sanitiziraj_zahtevo(AdapterZahteva $zahteva): AdapterZahteva
 *     - adapter_sanitiziraj_vrednost($vrednost)
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
 *     adapter, zahteve, sanitizacija
 * ============================================================
 */
declare(strict_types=1);

function adapter_sanitiziraj_zahtevo(AdapterZahteva $zahteva): AdapterZahteva
{
    $parametri = $zahteva->parametri();
    $sanitizirani = [];
    
    foreach ($parametri as $kljuc => $vrednost) {
        $sanitizirani[$kljuc] = adapter_sanitiziraj_vrednost($vrednost);
    }
    
    $vsebina = $zahteva->vsebina();
    if ($vsebina !== null) {
        $sanitiziranaVsebina = [];
        foreach ($vsebina as $kljuc => $vrednost) {
            $sanitiziranaVsebina[$kljuc] = adapter_sanitiziraj_vrednost($vrednost);
        }
        $vsebina = $sanitiziranaVsebina;
    }
    
    return new AdapterZahteva(array_merge($zahteva->toArray(), [
        'parametri' => $sanitizirani,
        'vsebina' => $vsebina
    ]));
}

function adapter_sanitiziraj_vrednost($vrednost)
{
    if (is_string($vrednost)) {
        // Odstrani HTML tag-e
        $cist = strip_tags($vrednost);
        // Escape HTML entities
        $cist = htmlspecialchars($cist, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        return $cist;
    }
    
    if (is_array($vrednost)) {
        $rezultat = [];
        foreach ($vrednost as $kljuc => $v) {
            $rezultat[$kljuc] = adapter_sanitiziraj_vrednost($v);
        }
        return $rezultat;
    }
    
    if (is_int($vrednost) || is_float($vrednost)) {
        return $vrednost;
    }
    
    if (is_bool($vrednost)) {
        return $vrednost;
    }
    
    return null;
}