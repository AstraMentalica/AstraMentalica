<?php
/**
 * ============================================================
 * POT: ADAPTER/izhod_kanali/KanalCli.php
 * 📅 VERZIJA: v115 (14.6.2026 18:00)
 * ============================================================
 *
 * 🏛️ NIVO: ADAPTER
 *
 * 📰 NAMEN:
 *     CLI kanal – izhod za ukazno vrstico.
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
 *     adapter, kanal, cli, boundary
 * ============================================================
 */
declare(strict_types=1);

defined('SISTEM_VARNOST') or die('Direkten dostop ni dovoljen.');

class KanalCli
{
    private string $ime;
    private array $barve = [
        'uspeh' => "\033[32m",
        'napaka' => "\033[31m",
        'opozorilo' => "\033[33m",
        'info' => "\033[36m",
        'reset' => "\033[0m"
    ];

    public function __construct()
    {
        $this->ime = 'cli';
    }

    public function ime(): string
    {
        return $this->ime;
    }

    public function obdelaj(array $zahteva): array
    {
        global $argv;
        
        $parametri = [];
        if (isset($argv)) {
            for ($i = 1; $i < count($argv); $i++) {
                $arg = $argv[$i];
                if (strpos($arg, '=') !== false) {
                    [$kljuc, $vrednost] = explode('=', $arg, 2);
                    $parametri[ltrim($kljuc, '-')] = $vrednost;
                } elseif (strpos($arg, '--') === 0) {
                    $parametri[ltrim($arg, '-')] = true;
                } elseif (strpos($arg, '-') === 0) {
                    $parametri[ltrim($arg, '-')] = true;
                } else {
                    $parametri['akcija'] = $arg;
                }
            }
        }
        
        $zahteva['parametri'] = array_merge($zahteva['parametri'], $parametri);
        
        if (empty($zahteva['parametri']['akcija'])) {
            $zahteva['parametri']['akcija'] = 'pomoc';
        }
        
        return $zahteva;
    }

    public function poslji(array $odziv): void
    {
        $status = $odziv['status'] ?? 'uspeh';
        $sporocilo = $odziv['sporocilo'] ?? '';
        $vsebina = $odziv['vsebina'] ?? [];
        
        $barva = $this->barve[$status] ?? $this->barve['info'];
        $reset = $this->barve['reset'];
        
        echo $barva . "[" . strtoupper($status) . "]" . $reset . " " . $sporocilo . "\n";
        
        if (!empty($vsebina)) {
            echo $this->barve['info'] . print_r($vsebina, true) . $reset . "\n";
        }
    }
}