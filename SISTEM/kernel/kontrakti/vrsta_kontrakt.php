<?php
/**
 * ============================================================
 * POT: SISTEM/kernel/kontrakti/vrsta_kontrakt.php
 * 📅 VERZIJA: v114 (9.6.2026 18:00)
 * ============================================================
 *
 * 🏛️ NIVO: SISTEM N2 (kontrakti)
 *
 * 📰 NAMEN:
 *     Kontrakt za vrsto.
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - dodaj(array $paket, string $vrsta = 'obicajna_prednost'): bool
 *     - vzemi(string $vrsta = 'obicajna_prednost'): ?array
 *     - stevilo(string $vrsta = 'obicajna_prednost'): int
 *     - potrdi(string $vrsta, string $id): bool
 *     - ponovno_poskusi(array $paket, int $zakasnitev = 5): bool
 *     - mrtvo(string $vrsta, array $paket, string $razlog): void
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
 *     kernel, kontrakti, vrsta
 * ============================================================
 */
declare(strict_types=1);

/**
 * VRSTE ČAKALNE VRSTE
 * 
 * | Ime vrste            | Opis                           |
 * |----------------------|--------------------------------|
 * | sprotno              | Neposredno, čim prej           |
 * | visoka_prednost      | Visoka prednost                |
 * | običajna_prednost    | Običajna prednost (privzeto)   |
 * | nizka_prednost       | Nizka prednost                 |
 * | elektronska_posta    | Elektronska pošta              |
 * | umetna_inteligenca   | Poizvedbe do umetne inteligence|
 * | časovnik             | Časovno načrtovane naloge      |
 * | obvestila            | Obvestila uporabnikom          |
 * | mrtvo                | Mrtva pisma (neuspešni paketi) |
 */

interface CakalnaVrstaKontrakt
{
    public function dodaj(array $paket, string $vrsta = 'obicajna_prednost'): bool;
    public function vzemi(string $vrsta = 'obicajna_prednost'): ?array;
    public function stevilo(string $vrsta = 'obicajna_prednost'): int;
    public function potrdi(string $vrsta, string $id): bool;
    public function ponovno_poskusi(array $paket, int $zakasnitev = 5): bool;
    public function mrtvo(string $vrsta, array $paket, string $razlog): void;
}