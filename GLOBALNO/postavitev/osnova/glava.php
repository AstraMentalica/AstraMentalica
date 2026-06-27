<?php
/**
 * ---------------------------------------------------------
 * POT: GLOBALNO/render/osnova/glava.php
 * v111 (27.5.2026 14:30)
 * ---------------------------------------------------------
 * OPIS: HTML glava – skupni header za vse strani
 * ---------------------------------------------------------
 *
 * ODVISNOSTI:
 * - pot.php
 *
 * UPORABA:
 * - GLOBALNO/render/postavitev/*.php
 *
 * PREPOVEDI:
 * - Brez business logike
 * - Brez direktnih poizvedb v bazo
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

// Podatki prihajajo iz backend-a (SISTEM/storitve_svetov/globalno/)
// $vsebina je podana preko globalno_prikaz_strani()

if (!isset($vsebina)) {
    $vsebina = [];
}

$naslov = $vsebina['naslov'] ?? IME_APLIKACIJE;
$opis = $vsebina['opis'] ?? 'Platforma za duhovni razvoj in raziskovanje';
$jezik = $vsebina['jezik'] ?? 'sl';
$tema = $vsebina['tema'] ?? 'standard';
$cspNonce = $vsebina['csp_nonce'] ?? '';
?>

<!DOCTYPE html>
<html lang="<?= htmlspecialchars($jezik) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="description" content="<?= htmlspecialchars($opis) ?>">
    <meta name="keywords" content="duhovnost, astrologija, tarot, meditacija, razvoj">
    <meta name="author" content="<?= IME_APLIKACIJE ?>">
    <meta name="theme-color" content="#0a0a1a">
    
    <title><?= htmlspecialchars($naslov) ?> | <?= IME_APLIKACIJE ?></title>
    
    <!-- Osnovni CSS -->
    <link rel="stylesheet" href="<?= GLOBALNO ?>/vmesnik/css/jedro/reset.css">
    <link rel="stylesheet" href="<?= GLOBALNO ?>/vmesnik/css/jedro/osnova.css">
    <link rel="stylesheet" href="<?= GLOBALNO ?>/vmesnik/css/gradniki/gumb.css">
    <link rel="stylesheet" href="<?= GLOBALNO ?>/vmesnik/css/gradniki/kartica.css">
    
    <!-- Tema -->
    <link rel="stylesheet" href="<?= GLOBALNO ?>/vmesnik/teme/<?= htmlspecialchars($tema) ?>/slog.css">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?= GLOBALNO ?>/slike/logo/favicon.ico">
    
    <?php if ($cspNonce): ?>
    <meta http-equiv="Content-Security-Policy" content="script-src 'nonce-<?= $cspNonce ?>' 'unsafe-inline' 'unsafe-eval'; style-src 'self' 'unsafe-inline'">
    <?php endif; ?>
</head>
<body class="tema-<?= htmlspecialchars($tema) ?>">