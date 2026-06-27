<?php
/**
 * ============================================================
 * POT: SISTEM/kernel/kontrakti/rbac_kontrakt.php
 * 📅 VERZIJA: v114 (9.6.2026 18:00)
 * ============================================================
 *
 * 🏛️ NIVO: SISTEM N2 (kontrakti)
 *
 * 📰 NAMEN:
 *     Kontrakt za RBAC sistem.
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - preveri(int $vloga_uporabnika, int $potrebna_vloga): bool
 *     - vloga_v_niz(int $vloga): string
 *     - niz_v_vlogo(string $vloga): int
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
 *     kernel, kontrakti, rbac
 * ============================================================
 */
declare(strict_types=1);

/**
 * RBAC PRAVILA
 * 
 * Cela števila se uporabljajo v podatkih (baza, seja).
 * Nizi se uporabljajo v pravilih (manifest.json, konfiguracija).
 * 
 * | Vrednost (celo) | Ključ (niz) | Dostop |
 * |----------------|-------------|--------|
 * | 0              | "gost"      | Domov, prijava, registracija |
 * | 10             | "S0"        | + Mystaia, profil |
 * | 20             | "S1"        | + Stelaris, Lunaris |
 * | 30             | "S2"        | + Jyotir |
 * | 40             | "S3"        | + Tarot |
 * | 50             | "S4"        | + Synera |
 * | 60             | "S5"        | + ASTRA, vsi moduli |
 * | 100            | "admin"     | Vse |
 */


interface RBACPreverjanje
{
    public function preveri(int $vloga_uporabnika, int $potrebna_vloga): bool;
    public function vloga_v_niz(int $vloga): string;
    public function niz_v_vlogo(string $vloga): int;
}