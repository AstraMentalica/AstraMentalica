<?php
/**
 * ============================================================
 * POT: ADAPTER/odzivi/adapter_statusi.php
 * 📅 VERZIJA: v114 (9.6.2026 18:00)
 * ============================================================
 *
 * 🏛️ NIVO: ADAPTER
 *
 * 📰 NAMEN:
 *     Upravljanje statusnih kod.
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - adapter_status_nastavi(int $koda, ?string $sporocilo = null): void
 *     - adapter_status_pridobi(): ?array
 *     - adapter_status_je_uspeh(): bool
 *     - adapter_status_je_napaka(): bool
 *     - adapter_status_je_odjemalec_napaka(): bool
 *     - adapter_status_je_streznik_napaka(): bool
 *     - adapter_status_pretvori_v_niz(int $koda): string
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
 *     adapter, odzivi, statusi
 * ============================================================
 */
declare(strict_types=1);

$GLOBALS['ADAPTER_STATUS'] = null;

function adapter_status_nastavi(int $koda, ?string $sporocilo = null): void
{
    $GLOBALS['ADAPTER_STATUS'] = [
        'koda' => $koda,
        'sporocilo' => $sporocilo,
        'cas' => time()
    ];
    
    if (!headers_sent()) {
        http_response_code($koda);
    }
}

function adapter_status_pridobi(): ?array
{
    return $GLOBALS['ADAPTER_STATUS'];
}

function adapter_status_je_uspeh(): bool
{
    $status = adapter_status_pridobi();
    if ($status === null) {
        return true;
    }
    
    $koda = $status['koda'];
    return $koda >= 200 && $koda < 300;
}

function adapter_status_je_napaka(): bool
{
    return !adapter_status_je_uspeh();
}

function adapter_status_je_odjemalec_napaka(): bool
{
    $status = adapter_status_pridobi();
    if ($status === null) {
        return false;
    }
    
    $koda = $status['koda'];
    return $koda >= 400 && $koda < 500;
}

function adapter_status_je_streznik_napaka(): bool
{
    $status = adapter_status_pridobi();
    if ($status === null) {
        return false;
    }
    
    $koda = $status['koda'];
    return $koda >= 500 && $koda < 600;
}

function adapter_status_pretvori_v_niz(int $koda): string
{
    $statusi = [
        200 => 'OK',
        201 => 'Created',
        204 => 'No Content',
        301 => 'Moved Permanently',
        302 => 'Found',
        304 => 'Not Modified',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        409 => 'Conflict',
        422 => 'Unprocessable Entity',
        429 => 'Too Many Requests',
        500 => 'Internal Server Error',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable'
    ];
    
    return $statusi[$koda] ?? 'Unknown';
}