<?php
/**
 * ============================================================
 * POT: ADAPTER/izhod_kanali/KanalAi.php
 * 📅 VERZIJA: v115 (14.6.2026 18:00)
 * ============================================================
 *
 * 🏛️ NIVO: ADAPTER
 *
 * 📰 NAMEN:
 *     AI kanal – normalizacija AI zahtev.
 *     Nima klicanja DeepSeek API! To je naloga storitve.
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
 *     - Brez curl_init() in neposrednih HTTP klicev
 *
 * 📌 STATUS:
 *     Stabilno
 *
 * 📅 ZGODOVINA:
 *     - v115: uskladitev s Header Standard v115,
 *             odstranjeni cURL klici v DeepSeek
 *
 * 👤 AVTOR:
 *     AstraMentalica Mojster
 *
 * 🌐 JEZIK:
 *     sl
 *
 * 🏷️ OZNAKE:
 *     adapter, kanal, ai, boundary
 * ============================================================
 */
declare(strict_types=1);

defined('SISTEM_VARNOST') or die('Direkten dostop ni dovoljen.');

class KanalAi
{
    private string $ime;

    public function __construct()
    {
        $this->ime = 'ai';
    }

    public function ime(): string
    {
        return $this->ime;
    }

    public function obdelaj(array $zahteva): array
    {
        $zahteva['ai_klic'] = true;
        return $zahteva;
    }

    public function poslji(array $odziv): void
    {
        if (!headers_sent()) {
            header('Content-Type: application/json; charset=utf-8');
        }
        echo json_encode($odziv, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
}