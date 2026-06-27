<?php
/**
 * ============================================================
 * POT: ADAPTER/izhod_kanali/KanalApi.php
 * 📅 VERZIJA: v115 (14.6.2026 18:00)
 * ============================================================
 *
 * 🏛️ NIVO: ADAPTER
 *
 * 📰 NAMEN:
 *     API kanal – pretvori zahtevo v JSON odziv.
 *     Nima avtentikacije! To je naloga middleware.
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
 *     - Brez jwt_avtenticiraj() – to spada v middleware
 *
 * 📌 STATUS:
 *     Stabilno
 *
 * 📅 ZGODOVINA:
 *     - v115: uskladitev s Header Standard v115,
 *             odstranjena JWT avtentikacija
 *
 * 👤 AVTOR:
 *     AstraMentalica Mojster
 *
 * 🌐 JEZIK:
 *     sl
 *
 * 🏷️ OZNAKE:
 *     adapter, kanal, api, boundary
 * ============================================================
 */
declare(strict_types=1);

defined('SISTEM_VARNOST') or die('Direkten dostop ni dovoljen.');

class KanalApi
{
    private string $ime;

    public function __construct()
    {
        $this->ime = 'api';
    }

    public function ime(): string
    {
        return $this->ime;
    }

    public function obdelaj(array $zahteva): array
    {
        $zahteva['api_verzija'] = '1.0';
        $zahteva['api_klic']    = true;
        return $zahteva;
    }

    public function poslji(array $odziv): void
    {
        $koda = $odziv['status_koda'] ?? 200;
        http_response_code($koda);
        header('Content-Type: application/json; charset=utf-8');
        header('X-API-Version: ' . ($odziv['api_verzija'] ?? '1.0'));
        
        echo json_encode($odziv, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
}