<?php
/**
 * ============================================================
 * POT: ADAPTER/izhod_kanali/KanalFacebook.php
 * 📅 VERZIJA: v115 (14.6.2026 18:00)
 * ============================================================
 *
 * 🏛️ NIVO: ADAPTER
 *
 * 📰 NAMEN:
 *     Facebook kanal – sprejemanje in pošiljanje Facebook sporočil.
 *     Brez business logike.
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - ime(): string
 *     - obdelaj(array $zahteva): array
 *     - poslji(array $odziv): void
 *
 * 📡 ODVISNOSTI:
 *     - (nobene)
 *
 * 🚫 PREPOVEDI:
 *     - Brez business logike
 *
 * 📌 STATUS:
 *     Stabilno
 *
 * 📅 ZGODOVINA:
 *     - v115: uskladitev s Header Standard v115
 *
 * 👤 AVTOR:
 *     AstraMentalica Mojster
 *
 * 🌐 JEZIK:
 *     sl
 *
 * 🏷️ OZNAKE:
 *     adapter, kanal, facebook, boundary
 * ============================================================
 */
declare(strict_types=1);

defined('SISTEM_VARNOST') or die('Direkten dostop ni dovoljen.');

class KanalFacebook
{
    private string $ime;
    private ?string $pageAccessToken = null;

    public function __construct()
    {
        $this->ime = 'facebook';
        $this->pageAccessToken = getenv('FACEBOOK_PAGE_ACCESS_TOKEN') ?: null;
    }

    public function ime(): string
    {
        return $this->ime;
    }

    public function obdelaj(array $zahteva): array
    {
        $vhod = file_get_contents('php://input');
        if ($vhod) {
            $facebookPodatki = json_decode($vhod, true);
            if (is_array($facebookPodatki) && isset($facebookPodatki['entry'][0]['messaging'][0])) {
                $sporocilo = $facebookPodatki['entry'][0]['messaging'][0];
                $zahteva['facebook'] = $sporocilo;
                
                if (isset($sporocilo['sender']['id'])) {
                    $zahteva['parametri']['recipient_id'] = $sporocilo['sender']['id'];
                    $zahteva['parametri']['besedilo'] = $sporocilo['message']['text'] ?? '';
                    $zahteva['parametri']['postback'] = $sporocilo['postback']['payload'] ?? '';
                    $zahteva['parametri']['tip'] = isset($sporocilo['message']) ? 'sporocilo' : 'postback';
                }
            }
        }
        
        return $zahteva;
    }

    public function poslji(array $odziv): void
    {
        $sporocilo = $odziv['sporocilo'] ?? '';
        $vsebina = $odziv['vsebina'] ?? [];
        $recipientId = $vsebina['recipient_id'] ?? null;
        
        if ($recipientId && $sporocilo && $this->pageAccessToken) {
            $this->_posljiSporocilo($recipientId, $sporocilo);
        }
        
        if (!headers_sent()) {
            header('Content-Type: application/json');
        }
        echo json_encode(['status' => 'ok']);
    }

    private function _posljiSporocilo(string $recipientId, string $besedilo, array $opcije = []): void
    {
        $url = "https://graph.facebook.com/v18.0/me/messages?access_token={$this->pageAccessToken}";
        $podatki = array_merge([
            'recipient' => ['id' => $recipientId],
            'message' => ['text' => $besedilo]
        ], $opcije);
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($podatki));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 1);
        curl_exec($ch);
        curl_close($ch);
    }
}