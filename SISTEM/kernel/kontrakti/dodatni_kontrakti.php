<?php
/**
 * ============================================================
 * POT: SISTEM/kernel/kontrakti/dodatni_kontrakti.php
 * 📅 VERZIJA: v114 (9.6.2026 18:00)
 * ============================================================
 *
 * 🏛️ NIVO: SISTEM N2 (kontrakti)
 *
 * 📰 NAMEN:
 *     Dodatni kontrakti sistema.
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - poslji(string $uporabnikId, string $sporocilo, array $opcij...): bool
 *     - preberi(string $uporabnikId, string $notifikacijaId): bool
 *     - vse(string $uporabnikId, int $limit = 20): array
 *     - neprebrane(string $uporabnikId): int
 *     - zabelezi(string $dogodek, array $podatki = []): void
 *     - statistika(string $dogodek, int $od = null, int $do = null): array
 *     - trendi(string $dogodek, int $dni = 30): array
 *     - izvozi(string $format = 'json'): string
 *     - nastaviNaslov(string $naslov): void
 *     - nastaviOpis(string $opis): void
 *     - nastaviKljucneBesede(array $kljucneBesede): void
 *     - generirajMeta(): string
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
 *     kernel, kontrakti
 * ============================================================
 */
declare(strict_types=1);

// ============================================================
// 1. NOTIFIKACIJE
// ============================================================

interface NotifikacijaInterface
{
public function poslji(string $uporabnikId, string $sporocilo, array $opcije = []): bool;
public function preberi(string $uporabnikId, string $notifikacijaId): bool;
public function vse(string $uporabnikId, int $limit = 20): array;
public function neprebrane(string $uporabnikId): int;
}

// ============================================================
// 2. ANALITIKA
// ============================================================

interface AnalitikaInterface
{
public function zabelezi(string $dogodek, array $podatki = []): void;
public function statistika(string $dogodek, int $od = null, int $do = null): array;
public function trendi(string $dogodek, int $dni = 30): array;
public function izvozi(string $format = 'json'): string;
}

// ============================================================
// 3. SEO
// ============================================================

interface SeoInterface
{
public function nastaviNaslov(string $naslov): void;
public function nastaviOpis(string $opis): void;
public function nastaviKljucneBesede(array $kljucneBesede): void;
public function generirajMeta(): string;
public function generirajSitemap(): string;
}

// ============================================================
// 4. SOCIALNA OMREŽJA
// ============================================================

interface SocialInterface
{
public function objavi(string $omrezje, string $sporocilo, array $mediji = []): array;
public function preveriDostop(string $omrezje): bool;
public function povezi(string $omrezje, array $podatki): bool;
public function prekiniPovezavo(string $omrezje): bool;
}

// ============================================================
// 5. GDPR
// ============================================================

interface GdprInterface
{
public function soglasje(string $uporabnikId, string $tip, bool $soglasje): void;
public function preveriSoglasje(string $uporabnikId, string $tip): bool;
public function izvoziPodatke(string $uporabnikId): array;
public function izbrisiPodatke(string $uporabnikId): bool;
}

// ============================================================
// 6. PLAČILA
// ============================================================

interface PlaciloInterface
{
public function ustvariPlacilo(float $znesek, string $valuta, array $podatki): array;
public function preveriStatus(string $placiloId): array;
public function preklici(string $placiloId): bool;
public function povratnaSredstva(string $placiloId, float $znesek = null): bool;
}