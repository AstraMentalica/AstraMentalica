<?php
/**
 * ============================================================
 * POT: SISTEM/sistem_runtime/knjiznice/log.php
 * ============================================================
 * 
 * 📦 NAMEN:
 *     Logging funkcije s correlation ID
 * 
 * 🔧 FUNKCIJE:
 *     - log_debug(string $sporocilo, array $kontekst = []): void
 *     - log_info(string $sporocilo, array $kontekst = []): void
 *     - log_warning(string $sporocilo, array $kontekst = []): void
 *     - log_error(string $sporocilo, array $kontekst = []): void
 *     - log_critical(string $sporocilo, array $kontekst = []): void
 * 
 * @author ASTRAMENTALICA
 * @version 1.0.0
 * @since FAZA 1
 * ============================================================
 */

use AstraMentalica\Runtime\Jedro\je_debug;
use AstraMentalica\Runtime\Jedro\je_razvoj;

function log_z_id(string $nivo, string $sporocilo, array $kontekst = []): void
{
    $timestamp = date('Y-m-d H:i:s');
    $req_id = $_SERVER['REQUEST_ID'] ?? 'N/A';
    $log = "[$timestamp] [$nivo] [REQ: $req_id] $sporocilo";
    
    if (!empty($kontekst)) {
        $log .= ' ' . json_encode($kontekst, JSON_UNESCAPED_UNICODE);
    }
    
    $log_file = POT_PODATKI . '/sistem/dnevnik/' . date('Y-m-d') . '.log';
    
    $log_dir = dirname($log_file);
    if (!is_dir($log_dir)) {
        mkdir($log_dir, 0755, true);
    }
    
    error_log($log . "\n", 3, $log_file);
}

function log_debug(string $sporocilo, array $kontekst = []): void
{
    if (je_debug() || je_razvoj()) {
        log_z_id('DEBUG', $sporocilo, $kontekst);
    }
}

function log_info(string $sporocilo, array $kontekst = []): void
{
    log_z_id('INFO', $sporocilo, $kontekst);
}

function log_warning(string $sporocilo, array $kontekst = []): void
{
    log_z_id('WARNING', $sporocilo, $kontekst);
}

function log_error(string $sporocilo, array $kontekst = []): void
{
    log_z_id('ERROR', $sporocilo, $kontekst);
}

function log_critical(string $sporocilo, array $kontekst = []): void
{
    log_z_id('CRITICAL', $sporocilo, $kontekst);
}