<?php
/**
 * ============================================================
 * POT: ADAPTER/izhod_kanali/KanalTelegram.php
 * 📅 VERZIJA: v115 (14.6.2026 18:00)
 * ============================================================
 *
 * 🏛️ NIVO: ADAPTER
 *
 * 📰 NAMEN:
 *     Telegram kanal – sprejemanje in pošiljanje Telegram sporočil.
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
 *     - Brez die(), exit()
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
 *     adapter, kanal, telegram, boundary
 * ============================================================
 */
declare(strict_types=1);

defined('SISTEM_VARNOST') or die('Direkten dostop ni dovoljen.');

class KanalTelegram
{
    private string $ime;
    private ?string $botToken = null;

    public function __construct()
    {
        $this->ime = 'telegram';
        $this->botToken = getenv('TELEGRAM_BOT_TOKEN') ?: null;
    }

    public function ime(): string
    {
        return $this->ime;
    }

    public function obdelaj(array $zahteva): array
    {
        $vhod = file_get_contents('php://input');
        if ($vhod) {
            $telegramPodatki = json_decode($vhod, true);
            if (is_array($telegramPodatki)) {
                $zahteva['telegram'] = $telegramPodatki;
                
                if (isset($telegramPodatki['message'])) {
                    $sporocilo = $telegramPodatki['message'];
                    $zahteva['parametri']['chat_id'] = $sporocilo['chat']['id'] ?? null;
                    $zahteva['parametri']['besedilo'] = $sporocilo['text'] ?? '';
                    $zahteva['parametri']['uporabnik_id'] = $sporocilo['from']['id'] ?? null;
                    $zahteva['parametri']['tip'] = 'sporocilo';
                }
                
                if (isset($telegramPodatki['callback_query'])) {
                    $callback = $telegramPodatki['callback_query'];
                    $zahteva['parametri']['callback_id'] = $callback['id'] ?? null;
                    $zahteva['parametri']['callback_data'] = $callback['data'] ?? '';
                    $zahteva['parametri']['chat_id'] = $callback['message']['chat']['id'] ?? null;
                    $zahteva['parametri']['tip'] = 'callback';
                }
            }
        }
        
        return $zahteva;
    }

    public function poslji(array $odziv): void
    {
        $sporocilo = $odziv['sporocilo'] ?? '';
        $vsebina = $odziv['vsebina'] ?? [];
        $chatId = $vsebina['chat_id'] ?? null;
        
        if ($chatId && $sporocilo && $this->botToken) {
            $this->_posljiSporocilo((int)$chatId, $sporocilo);
        }
        
        if (!headers_sent()) {
            header('Content-Type: application/json');
        }
        echo json_encode(['ok' => true]);
    }

    private function _posljiSporocilo(int $chatId, string $besedilo, array $opcije = []): void
    {
        $url = "https://api.telegram.org/bot{$this->botToken}/sendMessage";
        $podatki = array_merge([
            'chat_id' => $chatId,
            'text' => $besedilo,
            'parse_mode' => 'HTML'
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