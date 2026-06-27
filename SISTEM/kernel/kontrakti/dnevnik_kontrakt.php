<?php
/**
 * ============================================================
 * POT: SISTEM/kernel/kontrakti/dnevnik_kontrakt.php
 * 📅 VERZIJA: v114 (9.6.2026 18:00)
 * ============================================================
 *
 * 🏛️ NIVO: SISTEM N2 (kontrakti)
 *
 * 📰 NAMEN:
 *     Kontrakt za dnevnik sistem.
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - razhroscanje(string $sporocilo, array $sobesedilo = []): void
 *     - obvestilo(string $sporocilo, array $sobesedilo = []): void
 *     - opozorilo(string $sporocilo, array $sobesedilo = []): void
 *     - napaka(string $sporocilo, array $sobesedilo = []): void
 *     - kricno(string $sporocilo, array $sobesedilo = []): void
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
 *     kernel, kontrakti, dnevnik
 * ============================================================
 */
declare(strict_types=1);

interface DnevnikKontrakt
{
    public function razhroscanje(string $sporocilo, array $sobesedilo = []): void;
    public function obvestilo(string $sporocilo, array $sobesedilo = []): void;
    public function opozorilo(string $sporocilo, array $sobesedilo = []): void;
    public function napaka(string $sporocilo, array $sobesedilo = []): void;
    public function kricno(string $sporocilo, array $sobesedilo = []): void;
}