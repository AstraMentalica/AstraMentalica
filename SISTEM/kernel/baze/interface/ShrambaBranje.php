<?php
/**
 * ---------------------------------------------------------
 * POT: SISTEM/kernel/baze/interface/ShrambaBranje.php
 * v111 (27.5.2026 05:00)
 * ---------------------------------------------------------
 * OPIS: Vmesnik za branje podatkov iz shrambe
 * ---------------------------------------------------------
 *
 * ODVISNOSTI:
 * - pot.php
 *
 * UPORABA:
 * - upravljalec_baz.php
 *
 * FUNKCIJE:
 * - beri(), beri_enega(), obstaja(), prestej()
 *
 * PREPOVEDI:
 * - Brez pisanja podatkov
 * - Brez echo, print_r, var_dump
 *
 * STATUS: Stabilno
 *
 * ZGODOVINA:
 * - v111: FAZA 0 – definicija
 * - v111: FAZA 2 – implementacija
 *
 * ---------------------------------------------------------
 * AVTOR: AstraMentalica Mojster
 * ---------------------------------------------------------
 */

declare(strict_types=1);

interface ShrambaBranje
{
public function beri(string $zbirka, array $pogoji = []): array;
public function beri_enega(string $zbirka, string $id): ?array;
public function obstaja(string $zbirka, string $id): bool;
public function prestej(string $zbirka, array $pogoji = []): int;
}