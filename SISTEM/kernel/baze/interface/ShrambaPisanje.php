<?php
/**
 * ---------------------------------------------------------
 * POT: SISTEM/kernel/baze/interface/ShrambaPisanje.php
 * v111 (27.5.2026 05:00)
 * ---------------------------------------------------------
 * OPIS: Vmesnik za pisanje podatkov v shrambo
 * ---------------------------------------------------------
 *
 * ODVISNOSTI:
 * - pot.php
 *
 * UPORABA:
 * - upravljalec_baz.php
 *
 * FUNKCIJE:
 * - zapisi(), posodobi(), zbrisi()
 *
 * PREPOVEDI:
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

interface ShrambaPisanje
{
public function zapisi(string $zbirka, array $podatki): ?string;
public function posodobi(string $zbirka, string $id, array $podatki): bool;
public function zbrisi(string $zbirka, string $id): bool;
}