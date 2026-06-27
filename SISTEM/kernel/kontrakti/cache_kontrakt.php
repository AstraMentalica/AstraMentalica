<?php
/**
 * ============================================================
 * POT: SISTEM/kernel/kontrakti/cache_kontrakt.php
 * 📅 VERZIJA: v114 (9.6.2026 18:00)
 * ============================================================
 *
 * 🏛️ NIVO: SISTEM N2 (kontrakti)
 *
 * 📰 NAMEN:
 *     Kontrakt za cache sistem.
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - shrani(string $kljuc, $vrednost, int $cas_zivljenja = 3600): bool
 *     - preberi(string $kljuc)
 *     - zbrisi(string $kljuc): bool
 *     - pocisti(): void
 *     - obstaja(string $kljuc): bool
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
 *     kernel, kontrakti, cache
 * ============================================================
 */
declare(strict_types=1);

interface Predpomnilnik
{
    public function shrani(string $kljuc, $vrednost, int $cas_zivljenja = 3600): bool;
    public function preberi(string $kljuc);
    public function zbrisi(string $kljuc): bool;
    public function pocisti(): void;
    public function obstaja(string $kljuc): bool;
}