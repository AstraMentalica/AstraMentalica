<?php
/**
 * ---------------------------------------------------------
 * POT: SISTEM/kernel/baze/interface/ShrambaTransakcija.php
 * v111 (27.5.2026 05:00)
 * ---------------------------------------------------------
 * OPIS: Vmesnik za transakcije v shrambi
 * ---------------------------------------------------------
 *
 * ODVISNOSTI:
 * - pot.php
 *
 * UPORABA:
 * - upravljalec_baz.php
 *
 * FUNKCIJE:
 * - transakcija_zacni(), transakcija_potrdi(), transakcija_preklici()
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

interface ShrambaTransakcija
{
public function transakcija_zacni(): void;
public function transakcija_potrdi(): void;
public function transakcija_preklici(): void;
}