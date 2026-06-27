<?php
/**
 * ============================================================
 * POT: ADAPTER/kanali/telegram/kanal_telegram.php
 * ============================================================
 * 
 * @package AstraMentalica\Adapter\Kanali
 * 
 * 📦 NAMEN:
 *     Telegram kanal - Bot API integracija
 * 
 * 🔧 INTERFACE:
 *     - KanalContract
 * 
 * ⚠️ OPOMBA:
 *     Zahteva veljaven Telegram Bot Token
 * 
 * @author ASTRAMENTALICA
 * @version 1.0.0
 * @since FAZA 4b
 * ============================================================
 */

namespace AstraMentalica\Adapter\Kanali;

use AstraMentalica\Procesi\Protokoli\KanalContract;

class KanalTelegram implements KanalContract
{
    private string $apiUrl = 'https://api.telegram.org/bot';
    private ?string $botToken = null;
    private ?string $chatId = null;
    
    public function __construct()
    {
        $this->botToken = $_ENV['TELEGRAM_BOT_TOKEN'] ?? null;
        $this->chatId = $_ENV['TELEGRAM_CHAT_ID'] ?? null;
    }
    
    public function ime(): string
    {
        return 'telegram';
    }
    
    public function obdelaj(array $request): array
    {
        // Validacija tokena
        if (!$this->jePodprt()) {
            $request['error'] = 'Telegram kanal ni konfiguriran';
            return $request;
        }
        
        // Preveri ali je to webhook klic iz Telegrama
        if (isset($request['telo']['message'])) {
            $request['telegram_message'] = $request['telo']['message'];
        }
        
        return $request;
    }
    
    public function poslji(array $response): void
    {
        if (!$this->jePodprt()) {
            return;
        }
        
        // Če je napaka, ne pošiljamo na Telegram
        if ($response['status'] === 'napaka') {
            return;
        }
        
        $sporocilo = $response['sporocilo'] ?? '';
        $vsebina = $response['vsebina'] ?? [];
        
        if (empty($sporocilo) && empty($vsebina)) {
            return;
        }
        
        // Pripravi sporočilo za Telegram
        $telegramMessage = $sporocilo;
        if (!empty($vsebina) && isset($vsebina['tekst'])) {
            $telegramMessage = $vsebina['tekst'];
        }
        
        // Pošlji na Telegram
        $this->posljiNaTelegram($telegramMessage);
    }
    
    public function jePodprt(): bool
    {
        return !empty($this->botToken);
    }
    
    private function posljiNaTelegram(string $sporocilo): void
    {
        // Simulacija - v produkciji bi se klical cURL
        // $url = "{$this->apiUrl}{$this->botToken}/sendMessage";
        // $params = ['chat_id' => $this->chatId, 'text' => $sporocilo];
        
        // Zaenkrat samo logiramo
        if (function_exists('log_info')) {
            log_info("Telegram kanal: pošiljanje sporočila", ['sporocilo' => substr($sporocilo, 0, 100)]);
        }
    }
    
    /**
     * Obdelaj webhook klic iz Telegrama
     */
    public function obdelajWebhook(array $request): array
    {
        $message = $request['telo']['message'] ?? null;
        
        if (!$message) {
            return ['status' => 'napaka', 'sporocilo' => 'Ni sporočila'];
        }
        
        $text = $message['text'] ?? '';
        $chatId = $message['chat']['id'] ?? null;
        
        return [
            'status' => 'uspeh',
            'kanal' => 'telegram',
            'vsebina' => [
                'tekst' => $text,
                'chat_id' => $chatId
            ]
        ];
    }
}