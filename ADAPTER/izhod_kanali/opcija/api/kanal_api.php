<?php
/**
 * ============================================================
 * POT: ADAPTER/kanali/api/kanal_api.php
 * ============================================================
 * 
 * @package AstraMentalica\Adapter\Kanali
 * 
 * 📦 NAMEN:
 *     API kanal - JSON odzivi
 * 
 * 🔧 INTERFACE:
 *     - KanalContract
 * 
 * @author ASTRAMENTALICA
 * @version 1.0.0
 * @since FAZA 4b
 * ============================================================
 */

namespace AstraMentalica\Adapter\Kanali;

use AstraMentalica\Procesi\Protokoli\KanalContract;

class KanalApi implements KanalContract
{
    public function ime(): string
    {
        return 'api';
    }
    
    public function obdelaj(array $request): array
    {
        // API zahteva je že normalizirana
        return $request;
    }
    
    public function poslji(array $response): void
    {
        $statusKoda = $response['status_koda'] ?? 200;
        http_response_code($statusKoda);
        
        // CORS za API
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
        header('Content-Type: application/json; charset=utf-8');
        
        // Odstrani morebitne HTML značke iz sporočil
        if (isset($response['sporocilo'])) {
            $response['sporocilo'] = strip_tags($response['sporocilo']);
        }
        
        echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
    
    public function jePodprt(): bool
    {
        return true;
    }
}