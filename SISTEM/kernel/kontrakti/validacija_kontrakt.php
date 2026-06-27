<?php
/**
 * ============================================================
 * POT: SISTEM/kernel/kontrakti/validacija_kontrakt.php
 * 📅 VERZIJA: v114 (9.6.2026 18:00)
 * ============================================================
 *
 * 🏛️ NIVO: SISTEM N2 (kontrakti)
 *
 * 📰 NAMEN:
 *     Kontrakt za validacijo.
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - preveri(array $podatki, array $predloga): array
 *     - preveriOdziv(array $odziv, string $razlicica = '1.0'): bool
 *     - preveriZahtevo(array $zahteva, string $razlicica = '1.0'): bool
 *     - preveriRazglas(array $razglas, string $razlicica = '2.1'): array
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
 *     kernel, kontrakti, validacija
 * ============================================================
 */
declare(strict_types=1);

interface ValidacijaInterface
{
    public function preveri(array $podatki, array $predloga): array;
    public function preveriOdziv(array $odziv, string $razlicica = '1.0'): bool;
    public function preveriZahtevo(array $zahteva, string $razlicica = '1.0'): bool;
    public function preveriRazglas(array $razglas, string $razlicica = '2.1'): array;
}