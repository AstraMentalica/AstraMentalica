<?php
declare(strict_types=1);
/**
 * ============================================================
 * POT: SISTEM/administracija/diagnostika/debug.php
 * ============================================================
 * 
 * 📦 NAMEN:
 *     Razhroščevanje - debug orodja
 * 
 * @author ASTRAMENTALICA
 * @version 1.0.0
 * @since FAZA 7
 * ============================================================
 */

function debug_aktiviraj(): void
{
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

function debug_d($data): void
{
    echo '<pre>';
    var_dump($data);
    echo '</pre>';
}

function debug_dd($data): void
{
    debug_d($data);
    exit(0);
}