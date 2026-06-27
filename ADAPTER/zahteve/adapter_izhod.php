<?php
/**
 * ============================================================
 * POT: ADAPTER/odzivi/adapter_izhod.php
 * ============================================================
 * 
 * 📦 NAMEN:
 *     Pošiljanje odgovora
 * 
 * @author ASTRAMENTALICA
 * @version 1.0.0
 * @since FAZA 4b
 * ============================================================
 */

function adapter_izhod_poslji(array $odziv, string $kanal = 'web'): void
{
    $status_koda = $odziv['status_koda'] ?? 200;
    http_response_code($status_koda);
    
    switch ($kanal) {
        case 'api':
            header('Content-Type: application/json');
            echo adapter_serializacija_json($odziv);
            break;
        case 'web':
            // HTML se obravnava preko kanal_web
            break;
        default:
            header('Content-Type: application/json');
            echo adapter_serializacija_json($odziv);
    }
}