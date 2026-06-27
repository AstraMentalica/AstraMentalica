<?php
/**
 * ============================================================
 * POT: ADAPTER/zahteve/adapter_parser.php
 * 📅 VERZIJA: v114 (9.6.2026 18:00)
 * ============================================================
 *
 * 🏛️ NIVO: ADAPTER
 *
 * 📰 NAMEN:
 *     Razčlenjevanje vhodnih podatkov različnih formatov.
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - adapter_parse_json(string $vhod): ?array
 *     - adapter_parse_xml(string $vhod): ?array
 *     - adapter_parse_form_data(string $vhod): array
 *     - adapter_parse_query_string(string $vhod): array
 *     - adapter_parse_content_type(?string $contentType): string
 *     - adapter_parse_vhod(AdapterZahteva $zahteva): ?array
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
 *     adapter, zahteve, parser
 * ============================================================
 */
declare(strict_types=1);

function adapter_parse_json(string $vhod): ?array
{
    if (empty($vhod)) {
        return null;
    }
    
    /*
     * ⚠️ STAR KOD (v111) – NE DELUJE, ker json_parse ne obstaja:
     * 
     * try {
     *     $podatki = json_parse($vhod, true);
     *     return is_array($podatki) ? $podatki : null;
     * } catch (RuntimeException $e) {
     *     return null;
     * }
     */
    
    // ✅ NOV KOD (v112) – uporablja vgrajeni json_decode
    $podatki = json_decode($vhod, true);
    return is_array($podatki) ? $podatki : null;
}

function adapter_parse_xml(string $vhod): ?array
{
    if (empty($vhod)) {
        return null;
    }
    
    $xml = simplexml_load_string($vhod);
    if ($xml === false) {
        return null;
    }
    
    return json_decode(json_encode($xml), true);
}

function adapter_parse_form_data(string $vhod): array
{
    $podatki = [];
    parse_str($vhod, $podatki);
    return $podatki;
}

function adapter_parse_query_string(string $vhod): array
{
    $podatki = [];
    parse_str($vhod, $podatki);
    return $podatki;
}

function adapter_parse_content_type(?string $contentType): string
{
    if ($contentType === null) {
        return 'form';
    }
    
    $contentType = strtolower($contentType);
    
    if (strpos($contentType, 'application/json') !== false) {
        return 'json';
    }
    
    if (strpos($contentType, 'application/xml') !== false || strpos($contentType, 'text/xml') !== false) {
        return 'xml';
    }
    
    if (strpos($contentType, 'application/x-www-form-urlencoded') !== false) {
        return 'form';
    }
    
    if (strpos($contentType, 'multipart/form-data') !== false) {
        return 'multipart';
    }
    
    return 'form';
}

function adapter_parse_vhod(AdapterZahteva $zahteva): ?array
{
    $vhod = file_get_contents('php://input');
    if (empty($vhod)) {
        return null;
    }
    
    $contentType = adapter_glava_pridobi($zahteva, 'Content-Type');
    $tip = adapter_parse_content_type($contentType);
    
    return match ($tip) {
        'json' => adapter_parse_json($vhod),
        'xml' => adapter_parse_xml($vhod),
        'form' => adapter_parse_form_data($vhod),
        default => null
    };
}