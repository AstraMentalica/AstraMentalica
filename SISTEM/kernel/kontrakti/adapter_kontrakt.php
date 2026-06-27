<?php
/**
 * ============================================================
 * POT: SISTEM/kernel/kontrakti/adapter_kontrakt.php
 * 📅 VERZIJA: v114 (9.6.2026 18:00)
 * ============================================================
 *
 * 🏛️ NIVO: SISTEM N2 (kontrakti)
 *
 * 📰 NAMEN:
 *     Kontrakt za adapter sloj (vmesnik KanalInterface).
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - sprejmi(array $zahteva): array
 *     - poslji(array $odziv): void
 *     - kanal(string $ime): KanalInterface
 *     - obdelaj(array $zahteva): array
 *     - ime(): string
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
 *     kernel, kontrakti, adapter
 * ============================================================
 */
declare(strict_types=1);

interface AdapterMost
{
    public function sprejmi(array $zahteva): array;
    public function poslji(array $odziv): void;
    public function kanal(string $ime): KanalInterface;
}

interface KanalInterface
{
    public function obdelaj(array $zahteva): array;
    public function ime(): string;
}