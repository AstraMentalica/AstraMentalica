<?php
/**
 * ---------------------------------------------------------
 * POT: ASTRA/auth/odjava.php
 * v111 (27.5.2026 17:00)
 * ---------------------------------------------------------
 * OPIS: Admin odjava
 * ---------------------------------------------------------
 *
 * ODVISNOSTI:
 * - pot.php
 *
 * UPORABA:
 * - Odjava iz ASTRA sveta
 *
 * PREPOVEDI:
 * - Brez business logike
 *
 * STATUS: Stabilno
 *
 * ZGODOVINA:
 * - v111: FAZA 20+ – implementacija
 *
 * ---------------------------------------------------------
 * AVTOR: AstraMentalica Mojster
 * ---------------------------------------------------------
 */

declare(strict_types=1);

session_start();
session_destroy();

header('Location: ?svet=ASTRA&pot=prijava');
exit;