<?php
/**
 * ============================================================
 * POT: SISTEM/kernel/kontrakti/usmerjevalnik_kontrakt.php
 * 📅 VERZIJA: v114 (9.6.2026 18:00)
 * ============================================================
 *
 * 🏛️ NIVO: SISTEM N2 (kontrakti)
 *
 * 📰 NAMEN:
 *     Kontrakt za usmerjevalnik.
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - dodaj_pot(string $pot, callable $obdelovalnik, array $metode =...): void
 *     - usmeri(string $pot, string $metoda = 'DOBI'): array
 *     - generiraj_pot(string $ime, array $parametri = []): string
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
 *     kernel, kontrakti, usmerjevalnik
 * ============================================================
 */
declare(strict_types=1);

interface Usmerjevalnik
{
    public function dodaj_pot(string $pot, callable $obdelovalnik, array $metode = ['DOBI']): void;
    public function usmeri(string $pot, string $metoda = 'DOBI'): array;
    public function generiraj_pot(string $ime, array $parametri = []): string;
}