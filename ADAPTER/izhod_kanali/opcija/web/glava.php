<?php
/**
 * ============================================================
 * POT: ADAPTER/kanali/web/glava.php
 * ============================================================
 * 
 * 📦 NAMEN:
 *     HTML glava strani
 * 
 * @author ASTRAMENTALICA
 * @version 1.0.0
 * @since FAZA 4b
 * ============================================================
 */

function kanal_web_glava(string $naslov = 'AstraMentalica'): void
{
    ?>
<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($naslov); ?> - AstraMentalica</title>
    <link rel="stylesheet" href="/GLOBALNO/slog/osnova.css">
</head>
<body>
<div class="postavitev">
    <?php
}