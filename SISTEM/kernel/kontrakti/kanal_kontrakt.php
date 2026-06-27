<?php
/**
 * ============================================================
 * POT: SISTEM/kernel/kontrakti/kanal_kontrakt.php
 * 📅 VERZIJA: v114 (9.6.2026 18:00)
 * ============================================================
 *
 * 🏛️ NIVO: SISTEM N2 (kontrakti)
 *
 * 📰 NAMEN:
 *     Kontrakt za kanale.
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - obdelaj(array $zahteva): array
 *     - normaliziraj(array $odziv): string
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
 *     kernel, kontrakti, kanal
 * ============================================================
 */
declare(strict_types=1);

interface KanalKontrakt
{
    public function obdelaj(array $zahteva): array;
    public function normaliziraj(array $odziv): string;
    public function ime(): string;
}