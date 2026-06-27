<?php
/**
 * ============================================================
 * POT: ADAPTER/kanali/ai/kanal_ai.php
 * ============================================================
 * 
 * @package AstraMentalica\Adapter\Kanali
 * 
 * 📦 NAMEN:
 *     AI kanal - za umetno inteligenco (stroji)
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

class KanalAi implements KanalContract
{
    private array $podprtiFormati = ['json', 'tekst'];
    
    public function ime(): string
    {
        return 'ai';
    }
    
    public function obdelaj(array $zahteva): array
    {
        $format = $zahteva['format'] ?? 'json';
        
        if (!in_array($format, $this->podprtiFormati)) {
            $format = 'json';
        }
        
        $zahteva['ai_format'] = $format;
        
        return $zahteva;
    }
    
    public function poslji(array $odziv): void
    {
        $statusKoda = $odziv['status_koda'] ?? 200;
        http_response_code($statusKoda);
        
        $format = $odziv['ai_format'] ?? 'json';
        
        if ($format === 'tekst') {
            header('Content-Type: text/plain; charset=utf-8');
            echo $odziv['sporocilo'] ?? '';
        } else {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($odziv, JSON_UNESCAPED_UNICODE);
        }
    }
    
    public function jePodprt(): bool
    {
        return true;
    }
}