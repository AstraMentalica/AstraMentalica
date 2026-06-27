<?php
/**
 * ============================================================
 * POT: SISTEM/kernel/kontrakti/jedro_kontrakt.php
 * 📅 VERZIJA: v114 (9.6.2026 18:00)
 * ============================================================
 *
 * 🏛️ NIVO: SISTEM N2 (kontrakti)
 *
 * 📰 NAMEN:
 *     Jedro kontrakt (ZaganjalnikKontrakt itd).
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - zagon(): void
 *     - faze(): array
 *     - je_dovoljen(string $svet): bool
 *     - vpisi_svet(string $svet, array $dovoljenja): void
 *     - vpisi(string $ime, callable $tovarna, string $zivljenjski_...): void
 *     - pridobi(string $ime): object
 *     - ima(string $ime): bool
 *     - poslusaj(string $dogodek, callable $poslusalec, int $prednost...): void
 *     - sprozi(string $dogodek, array $podatki = []): void
 *     - dodaj(string $ime, callable $kavelj, int $prednost = 10): void
 *     - izvedi(string $ime, array $parametri = []): array
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
 *     kernel, kontrakti, jedro
 * ============================================================
 */
declare(strict_types=1);

interface ZaganjalnikKontrakt
{
    public function zagon(): void;
    public function faze(): array;
}

interface UpravljalecSvetovKontrakt
{
    public function je_dovoljen(string $svet): bool;
    public function vpisi_svet(string $svet, array $dovoljenja): void;
}

interface PonudnikContainer
{
    public function vpisi(string $ime, callable $tovarna, string $zivljenjski_krog = 'posameznik'): void;
    public function pridobi(string $ime): object;
    public function ima(string $ime): bool;
}

interface DogodekBus
{
    public function poslusaj(string $dogodek, callable $poslusalec, int $prednost = 10): void;
    public function sprozi(string $dogodek, array $podatki = []): void;
}

interface KaveljSistem
{
    public function dodaj(string $ime, callable $kavelj, int $prednost = 10): void;
    public function izvedi(string $ime, array $parametri = []): array;
}