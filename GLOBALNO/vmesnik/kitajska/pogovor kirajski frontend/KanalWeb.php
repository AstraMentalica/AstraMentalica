<?php
/**
 * ============================================================
 * POT: GLOBALNO/vmesnik/kitajska/pogovor kirajski frontend/KanalWeb.php
 * 📅 VERZIJA: v115 (14.6.2026 18:00)
 * ============================================================
 *
 * 🏛️ NIVO: ADAPTER
 *
 * 📰 NAMEN:
 *     Spletni kanal – izhod za spletne zahteve.
 *     Tukaj se kliče globalno_prikaz_strani, NE v api.php!
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - ime(): string
 *     - obdelaj(array $zahteva): array
 *     - poslji(array $odziv): void
 *
 * 📡 ODVISNOSTI:
 *     - GLOBALNO/render/render.php (globalno_prikaz_strani)
 *
 * 🚫 PREPOVEDI:
 *     - Brez business logike
 *     - Brez die(), exit()
 *
 * 📌 STATUS:
 *     Stabilno
 *
 * 📅 ZGODOVINA:
 *     - v115: uskladitev s Header Standard v115,
 *             render premaknjen iz api.php sem
 *
 * 👤 AVTOR:
 *     AstraMentalica Mojster
 *
 * 🌐 JEZIK:
 *     sl
 *
 * 🏷️ OZNAKE:
 *     adapter, kanal, splet
 * ============================================================
 */
declare(strict_types=1);

defined('SISTEM_VARNOST') or die('Direkten dostop ni dovoljen.');

// Naloži renderer
$_renderPot = defined('POT_GLOBALNO')
    ? POT_GLOBALNO . '/render/render.php'
    : __DIR__ . '/../../../GLOBALNO/render/render.php';

if (file_exists($_renderPot)) {
    require_once $_renderPot;
}
unset($_renderPot);

class KanalWeb
{
    private string $ime;

    public function __construct()
    {
        $this->ime = 'splet';
    }

    public function ime(): string
    {
        return $this->ime;
    }

    public function obdelaj(array $zahteva): array
    {
        return $zahteva;
    }

    public function poslji(array $odziv): void
    {
        // Preusmeritev
        if (($odziv['tip'] ?? '') === 'preusmeritev') {
            $pot = $odziv['vsebina']['pot'] ?? '?svet=GLOBALNO';
            header('Location: ' . $pot);
            return;
        }

        // Napaka
        if (($odziv['status'] ?? '') === 'napaka') {
            $this->_prikazi_napako($odziv);
            return;
        }

        // HTML prikaz – render je zdaj TUKAJ, ne v api.php!
        if ($odziv['tip'] === 'html') {
            $vsebina = $odziv['vsebina'] ?? [];
            $stran = $vsebina['stran'] ?? 'domov';
            $podatki = $vsebina['podatki'] ?? [];
            
            if (function_exists('globalno_prikaz_strani')) {
                globalno_prikaz_strani($stran, $podatki);
                return;
            }
        }

        // Fallback
        $this->_fallback_html($odziv);
    }

    private function _prikazi_napako(array $odziv): void
    {
        $koda = $odziv['status_koda'] ?? 500;
        $sporocilo = $odziv['sporocilo'] ?? 'Neznana napaka';
        
        http_response_code($koda);
        
        echo '<!DOCTYPE html>';
        echo '<html lang="sl">';
        echo '<head><meta charset="UTF-8"><title>Napaka ' . $koda . '</title>';
        echo '<style>body{font-family:system-ui;background:#0a0a1a;color:#d4c5a9;display:flex;justify-content:center;align-items:center;min-height:100vh}.napaka{text-align:center}.koda{font-size:6rem;color:#e8c84a}.sporocilo{color:#aaa}</style>';
        echo '</head><body>';
        echo '<div class="napaka">';
        echo '<div class="koda">' . $koda . '</div>';
        echo '<h1>Napaka</h1>';
        echo '<p class="sporocilo">' . htmlspecialchars($sporocilo) . '</p>';
        echo '<a href="?svet=GLOBALNO" style="color:#e8c84a">← Nazaj na domov</a>';
        echo '</div></body></html>';
    }

    private function _fallback_html(array $odziv): void
    {
        ?>
        <!DOCTYPE html>
        <html lang="sl">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title><?= IME_APLIKACIJE ?></title>
            <style>
                *{margin:0;padding:0;box-sizing:border-box}
                body{font-family:system-ui;background:#0a0a1a;color:#d4c5a9;line-height:1.6}
                .vsebina{max-width:1200px;margin:0 auto;padding:2rem}
                .gumb{display:inline-block;padding:0.5rem 1rem;background:rgba(232,200,74,0.1);border:1px solid #e8c84a;border-radius:25px;color:#e8c84a;text-decoration:none}
                .gumb:hover{background:#e8c84a;color:#0a0a1a}
            </style>
        </head>
        <body>
            <div class="vsebina">
                <h1><?= IME_APLIKACIJE ?></h1>
                <pre><?= htmlspecialchars(json_encode($odziv['vsebina'] ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) ?></pre>
                <a href="?svet=GLOBALNO" class="gumb">← Domov</a>
            </div>
        </body>
        </html>
        <?php
    }
}