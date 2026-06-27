<?php
/**
 * ============================================================
 * POT: SISTEM/kernel/kontrakti/odziv_kontrakt.php
 * 📅 VERZIJA: v114 (9.6.2026 18:00)
 * ============================================================
 *
 * 🏛️ NIVO: SISTEM N2 (kontrakti)
 *
 * 📰 NAMEN:
 *     Kontrakt za odzive.
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - ustvari(array $podatki): array
 *     - preveri(array $odziv): bool
 *     - napaka(string $sporocilo, int $koda = 500): array
 *     - uspeh(array $vsebina, string $sporocilo = ''): array
 *     - opozorilo(string $sporocilo, array $vsebina = []): array
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
 *     kernel, kontrakti, odziv
 * ============================================================
 */
declare(strict_types=1);

abstract class OdzivPogodba
{
    abstract public static function ustvari(array $podatki): array;
    abstract public static function preveri(array $odziv): bool;
    abstract public static function napaka(string $sporocilo, int $koda = 500): array;
    abstract public static function uspeh(array $vsebina, string $sporocilo = ''): array;
    abstract public static function opozorilo(string $sporocilo, array $vsebina = []): array;
}