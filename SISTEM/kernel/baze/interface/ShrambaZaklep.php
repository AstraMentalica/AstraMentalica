<?php
/**
 * ---------------------------------------------------------
 * POT: SISTEM/kernel/baze/interface/ShrambaZaklep.php
 * v111 (27.5.2026 05:00)
 * ---------------------------------------------------------
 * OPIS: Vmesnik za zaklepe v shrambi
 * ---------------------------------------------------------
 *
 * ODVISNOSTI:
 * - pot.php
 *
 * UPORABA:
 * - upravljalec_baz.php
 *
 * FUNKCIJE:
 * - zaklep_pridobi(), zaklep_spusti(), zaklep_je_zaklenjen()
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

interface ShrambaZaklep
{
public function zaklep_pridobi(string $ime, int $časovna_omejitev = 30): bool;
public function zaklep_spusti(string $ime): void;
public function zaklep_je_zaklenjen(string $ime): bool;
}