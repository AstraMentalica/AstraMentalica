<?php
/**
 * ============================================================
 * POT: SISTEM/kernel/kontrakti/shramba_kontrakt.php
 * 📅 VERZIJA: v114 (9.6.2026 18:00)
 * ============================================================
 *
 * 🏛️ NIVO: SISTEM N2 (kontrakti)
 *
 * 📰 NAMEN:
 *     Kontrakt za shrambo.
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - beri(string $zbirka, array $pogoji = []): array
 *     - beri_enega(string $zbirka, string $id): ?array
 *     - obstaja(string $zbirka, string $id): bool
 *     - prestej(string $zbirka, array $pogoji = []): int
 *     - zapisi(string $zbirka, array $podatki): ?string
 *     - posodobi(string $zbirka, string $id, array $podatki): bool
 *     - zbrisi(string $zbirka, string $id): bool
 *     - transakcija_zacni(): void
 *     - transakcija_potrdi(): void
 *     - transakcija_preklici(): void
 *     - zaklep_pridobi(string $ime, int $casovna_omejitev = 30): bool
 *     - zaklep_spusti(string $ime): void
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
 *     kernel, kontrakti, shramba
 * ============================================================
 */
declare(strict_types=1);

interface ShrambaBranje
{
    public function beri(string $zbirka, array $pogoji = []): array;
    public function beri_enega(string $zbirka, string $id): ?array;
    public function obstaja(string $zbirka, string $id): bool;
    public function prestej(string $zbirka, array $pogoji = []): int;
}

interface ShrambaPisanje
{
    public function zapisi(string $zbirka, array $podatki): ?string;
    public function posodobi(string $zbirka, string $id, array $podatki): bool;
    public function zbrisi(string $zbirka, string $id): bool;
}

interface ShrambaTransakcija
{
    public function transakcija_zacni(): void;
    public function transakcija_potrdi(): void;
    public function transakcija_preklici(): void;
}

interface ShrambaZaklep
{
    public function zaklep_pridobi(string $ime, int $casovna_omejitev = 30): bool;
    public function zaklep_spusti(string $ime): void;
    public function zaklep_je_zaklenjen(string $ime): bool;
}