<?php
/**
 * ============================================================
 * POT: ADAPTER/kanali/facebook/kanal_facebook.php
 * ============================================================
 * 
 * @package AstraMentalica\Adapter\Kanali
 * 
 * 📦 NAMEN:
 *     Facebook kanal - Meta API
 * 
 * 🔧 INTERFACE:
 *     - KanalContract
 * 
 * ⚠️ OPOMBA:
 *     Zahteva veljaven Facebook Page Access Token
 * 
 * @author ASTRAMENTALICA
 * @version 1.0.0
 * @since FAZA 4b
 * ============================================================
 */

namespace AstraMentalica\Adapter\Kanali;

use AstraMentalica\Procesi\Protokoli\KanalContract;

class KanalFacebook implements KanalContract
{
    private ?string $dostopniZeton = null;
    private ?string $stranId = null;
    
    public function __construct()
    {
        $this->dostopniZeton = $_ENV['FACEBOOK_DOSTOPNI_ZETON'] ?? null;
        $this->stranId = $_ENV['FACEBOOK_STRAN_ID'] ?? null;
    }
    
    public function ime(): string
    {
        return 'facebook';
    }
    
    public function obdelaj(array $zahteva): array
    {
        if (!$this->jePodprt()) {
            $zahteva['napaka'] = 'Facebook kanal ni nastavljen';
            return $zahteva;
        }
        
        return $zahteva;
    }
    
    public function poslji(array $odziv): void
    {
        if (!$this->jePodprt()) {
            return;
        }
        
        // Napak ne pošiljamo na Facebook
        if ($odziv['status'] === 'napaka') {
            return;
        }
        
        $sporocilo = $odziv['sporocilo'] ?? '';
        $vsebina = $odziv['vsebina'] ?? [];
        
        if (empty($sporocilo) && empty($vsebina)) {
            return;
        }
        
        $fbSporocilo = $sporocilo;
        if (!empty($vsebina) && isset($vsebina['tekst'])) {
            $fbSporocilo = $vsebina['tekst'];
        }
        
        $this->posljiNaFacebook($fbSporocilo);
    }
    
    public function jePodprt(): bool
    {
        return !empty($this->dostopniZeton) && !empty($this->stranId);
    }
    
    private function posljiNaFacebook(string $sporocilo): void
    {
        // Simulacija - v produkciji bi se klical cURL
        if (function_exists('log_info')) {
            log_info("Facebook kanal: pošiljanje sporočila", ['sporocilo' => substr($sporocilo, 0, 100)]);
        }
    }
}