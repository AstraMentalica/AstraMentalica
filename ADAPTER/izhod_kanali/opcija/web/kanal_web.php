<?php
/**
 * ============================================================
 * POT: ADAPTER/kanali/web/kanal_web.php
 * ============================================================
 * 
 * @package AstraMentalica\Adapter\Kanali
 * 
 * 📦 NAMEN:
 *     Spletni kanal - HTML odzivi
 * 
 * 🔧 INTERFACE:
 *     - KanalContract
 * 
 * 🔧 FUNKCIJE:
 *     - ime(): string
 *     - obdelaj(array $request): array
 *     - poslji(array $response): void
 *     - jePodprt(): bool
 * 
 * @author ASTRAMENTALICA
 * @version 1.0.0
 * @since FAZA 4b
 * ============================================================
 */

namespace AstraMentalica\Adapter\Kanali;

use AstraMentalica\Procesi\Protokoli\KanalContract;

class KanalWeb implements KanalContract
{
    private string $postavitev = 'osnovno';
    private array $podprtiTipi = ['domov', 'uporabniki', 'modul', 'astra', 'nastavitve'];
    
    public function ime(): string
    {
        return 'web';
    }
    
    public function obdelaj(array $request): array
    {
        // Normalizacija zahteve za web kanal
        $svet = $request['svet'] ?? '';
        $rel = $request['rel'] ?? null;
        
        return [
            'id' => $request['id'] ?? uniqid(),
            'kanal' => 'web',
            'svet' => $svet,
            'rel' => $rel,
            'parametri' => $request['parametri'] ?? [],
            'glave' => $request['glave'] ?? [],
            'telo' => $request['telo'] ?? null,
            'cas' => $request['cas'] ?? time()
        ];
    }
    
    public function poslji(array $response): void
    {
        // Nastavi HTTP status kodo
        $statusKoda = $response['status_koda'] ?? 200;
        http_response_code($statusKoda);
        
        // Nastavi vsebinski tip
        header('Content-Type: text/html; charset=utf-8');
        
        // Če je napaka, prikaži preprosto sporočilo
        if ($response['status'] === 'napaka') {
            $this->posljiNapako($response);
            return;
        }
        
        // Glede na tip odziva izberi ustrezno postavitev
        $tip = $response['tip'] ?? 'domov';
        
        // Tukaj bi se klical GLOBALNO/ render layer
        // Zaenkrat preprost izpis
        echo $this->renderHtml($response);
    }
    
    public function jePodprt(): bool
    {
        return true;
    }
    
    private function posljiNapako(array $response): void
    {
        $sporocilo = $response['sporocilo'] ?? 'Neznana napaka';
        $statusKoda = $response['status_koda'] ?? 500;
        
        echo "<!DOCTYPE html>
        <html lang='sl'>
        <head>
            <meta charset='UTF-8'>
            <title>Napaka - AstraMentalica</title>
            <style>
                body { font-family: sans-serif; text-align: center; padding: 50px; }
                .napaka { color: #721c24; background: #f8d7da; border: 1px solid #f5c6cb; padding: 20px; border-radius: 5px; }
                h1 { color: #721c24; }
            </style>
        </head>
        <body>
            <div class='napaka'>
                <h1>Napaka {$statusKoda}</h1>
                <p>" . htmlspecialchars($sporocilo) . "</p>
                <a href='?svet='>Nazaj na domov</a>
            </div>
        </body>
        </html>";
    }
    
    private function renderHtml(array $response): string
    {
        $vsebina = $response['vsebina'] ?? [];
        $sporocilo = $response['sporocilo'] ?? '';
        
        $html = "<!DOCTYPE html>
        <html lang='sl'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>AstraMentalica</title>
            <link rel='stylesheet' href='/GLOBALNO/slog/osnova.css'>
        </head>
        <body>
            <div class='postavitev'>
                <div class='glavna'>
                    <div class='kartica'>";
        
        if (!empty($sporocilo)) {
            $html .= "<div class='obvestilo'>" . htmlspecialchars($sporocilo) . "</div>";
        }
        
        if (isset($vsebina['sporocilo'])) {
            $html .= "<p>" . htmlspecialchars($vsebina['sporocilo']) . "</p>";
        }
        
        $html .= "        </div>
                </div>
            </div>
            <script src='/GLOBALNO/skripte/api.js'></script>
        </body>
        </html>";
        
        return $html;
    }
}