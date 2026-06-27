<?php
/**
 * ============================================================
 * POT: ADAPTER/odzivi/adapter_odziv.php
 * 📅 VERZIJA: v114 (9.6.2026 18:00)
 * ============================================================
 *
 * 🏛️ NIVO: ADAPTER
 *
 * 📰 NAMEN:
 *     Osnovni odziv objekt.
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - adapter_odziv_ustvari(array $podatki): array
 *     - adapter_odziv_napaka(string $sporocilo, int $koda = 500): array
 *     - adapter_odziv_uspeh(array $vsebina, string $sporocilo = ''): array
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
 *     adapter, odzivi
 * ============================================================
 */
declare(strict_types=1);

function adapter_odziv_ustvari(array $podatki): array
{
    return [
        'id_zahteve' => $podatki['id_zahteve'] ?? uniqid(),
        'api_verzija' => '1.0',
        'status' => $podatki['status'] ?? 'uspeh',
        'status_koda' => $podatki['status_koda'] ?? 200,
        'sporocilo' => $podatki['sporocilo'] ?? '',
        'tip' => $podatki['tip'] ?? 'domov',
        'kanal' => $podatki['kanal'] ?? 'web',
        'vsebina' => $podatki['vsebina'] ?? [],
        'napake' => $podatki['napake'] ?? [],
        'meta' => $podatki['meta'] ?? ['cas' => time()],
        'cas_odziva' => $podatki['cas_odziva'] ?? 0
    ];
}

function adapter_odziv_napaka(string $sporocilo, int $koda = 500): array
{
    return adapter_odziv_ustvari([
        'status' => 'napaka',
        'status_koda' => $koda,
        'sporocilo' => $sporocilo
    ]);
}

function adapter_odziv_uspeh(array $vsebina, string $sporocilo = ''): array
{
    return adapter_odziv_ustvari([
        'status' => 'uspeh',
        'vsebina' => $vsebina,
        'sporocilo' => $sporocilo
    ]);
}