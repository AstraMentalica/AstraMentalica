<?php
/**
 * ============================================================
 * POT: ADAPTER/odzivi/adapter_napake.php
 * 📅 VERZIJA: v114 (9.6.2026 18:00)
 * ============================================================
 *
 * 🏛️ NIVO: ADAPTER
 *
 * 📰 NAMEN:
 *     Obdelava napak po kanalih.
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - adapter_napaka_zabelezi(Throwable $izjema, string $kanal): void
 *     - adapter_napaka_obdelaj(Throwable $izjema, string $kanal, array $originalniOdziv ...): array
 *     - adapter_napaka_kanal_je_kritičen(string $kanal): bool
 *     - adapter_napaka_ustvari_odziv(Throwable $izjema, string $kanal): array
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
 *     adapter, odzivi, napake
 * ============================================================
 */
declare(strict_types=1);

function adapter_napaka_zabelezi(Throwable $izjema, string $kanal): void
{
    $podatki = [
        'kanal' => $kanal,
        'datoteka' => $izjema->getFile(),
        'vrstica' => $izjema->getLine(),
        'sled' => $izjema->getTraceAsString()
    ];
    
    if (function_exists('dnevnik_napaka')) {
        dnevnik_napaka($izjema->getMessage(), $podatki);
    } else {
        error_log("[NAPAKA][$kanal] " . $izjema->getMessage() . " v " . $izjema->getFile() . ":" . $izjema->getLine());
    }
}

function adapter_napaka_obdelaj(Throwable $izjema, string $kanal, array $originalniOdziv = []): array
{
    adapter_napaka_zabelezi($izjema, $kanal);
    
    if (!empty($originalniOdziv)) {
        if (!isset($originalniOdziv['opozorila'])) {
            $originalniOdziv['opozorila'] = [];
        }
        $originalniOdziv['opozorila'][] = [
            'kanal' => $kanal,
            'napaka' => $izjema->getMessage(),
            'cas' => time()
        ];
        return $originalniOdziv;
    }
    
    return [
        'status' => 'napaka',
        'status_koda' => 500,
        'sporocilo' => 'Napaka pri obdelavi zahteve na kanalu: ' . $kanal . ' - ' . $izjema->getMessage(),
        'vsebina' => [],
        'napake' => [['kanal' => $kanal, 'sporocilo' => $izjema->getMessage()]]
    ];
}

function adapter_napaka_kanal_je_kritičen(string $kanal): bool
{
    $kriticniKanali = ['api', 'splet'];
    return in_array($kanal, $kriticniKanali);
}

function adapter_napaka_ustvari_odziv(Throwable $izjema, string $kanal): array
{
    $odziv = [
        'status' => 'napaka',
        'status_koda' => 500,
        'sporocilo' => $izjema->getMessage(),
        'vsebina' => [],
        'napake' => [['kanal' => $kanal, 'sporocilo' => $izjema->getMessage()]]
    ];
    
    if (RAZVOJNI_NACIN) {
        $odziv['napake'][] = [
            'datoteka' => $izjema->getFile(),
            'vrstica' => $izjema->getLine(),
            'sled' => explode("\n", $izjema->getTraceAsString())
        ];
    }
    
    return $odziv;
}