<?php
/**
 * ============================================================
 * POT: SISTEM/kernel/kontrakti/modul_kontrakt.php
 * 📅 VERZIJA: v114 (9.6.2026 18:00)
 * ============================================================
 *
 * 🏛️ NIVO: SISTEM N2 (kontrakti)
 *
 * 📰 NAMEN:
 *     Kontrakt za module.
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - izvedi(array $zahteva): array
 *     - razglas(): array
 *     - ime(): string
 *     - različica(): string
 *     - kategorija(): string
 *     - naloži(string $ime): ModulInterface
 *     - vpisi(string $ime, array $razglas): bool
 *     - je_nalozen(string $ime): bool
 *     - aktiviraj(string $ime): bool
 *     - deaktiviraj(string $ime): bool
 *     - odstrani(string $ime): bool
 *     - vsiModuli(): array
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
 *     kernel, kontrakti, modul
 * ============================================================
 */
declare(strict_types=1);

interface ModulInterface
{
    public function izvedi(array $zahteva): array;
    public function razglas(): array;
    public function ime(): string;
    public function različica(): string;
    public function kategorija(): string;
}

interface ModulPeskovnik
{
    public function naloži(string $ime): ModulInterface;
    public function vpisi(string $ime, array $razglas): bool;
    public function je_nalozen(string $ime): bool;
    public function aktiviraj(string $ime): bool;
    public function deaktiviraj(string $ime): bool;
    public function odstrani(string $ime): bool;
    public function vsiModuli(): array;
}