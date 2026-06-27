<?php
/**
 * ============================================================
 * POT: ADAPTER/adapter.php
 * 📅 VERZIJA: v114 (9.6.2026 18:00)
 * ============================================================
 *
 * 🏛️ NIVO: ADAPTER
 *
 * 📰 NAMEN:
 *     Glavni adapter – prevajalnik med zunanjim svetom in sistemom.
 *     Normalizira zahteve in usmeri v SISTEM/api.php.
 *     Ne vsebuje poslovne logike. Ne zaganja bootstrapa.
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - adapter_zagon(): void
 *     - adapter_normaliziraj_zahtevo(): array
 *     - adapter_poslji_odziv(array $odziv): void
 *     - adapter_doloci_kanal(): string
 *     - adapter_registriraj_kanal(string $ime, object $kanal): void
 *     - adapter_pridobi_kanal(string $ime): ?object
 *
 * 📡 ODVISNOSTI:
 *     - pot.php
 *     - SISTEM/api.php
 *     - ADAPTER/izhod_kanali/*.php
 *
 * 🤝 SOODVISNOSTI:
 *     - ADAPTER/middleware/cors.php
 *     - ADAPTER/middleware/auth.php
 *     - ADAPTER/middleware/omejevalnik.php
 *     - ADAPTER/middleware/ip_blacklist.php
 *
 * ⚡ UPORABA:
 *     - Kliče se iz index.php ali api.php (root)
 *
 * 🚫 PREPOVEDI:
 *     - Brez business logike
 *     - Brez __DIR__
 *     - Brez bootstrap_izvedi() ali lifecycle skript
 *     - Brez avtentikacije (to je naloga middleware-a)
 *
 * 📌 STATUS:
 *     Stabilno
 *
 * 📅 ZGODOVINA:
 *     - v114: POPRAVEK – odstranjen klic bootstrap_izvedi() in
 *             RUNTIME/lifecycle/bootstrap.php; adapter ne pozna
 *             runtime lifecycle-a. Zagon sistema je izključno
 *             naloga Zaganjalnika v jedru.
 *             (Gemini review – arhitekturna napaka v bootstrap toku)
 *     - v113: dodan bootstrap_izvedi() klic (zdaj odstranjeno)
 *     - v112: prva implementacija
 *
 * 👤 AVTOR:
 *     AstraMentalica Mojster
 *
 * 🌐 JEZIK:
 *     sl
 *
 * 🏷️ OZNAKE:
 *     adapter, boundary, glavni
 * ============================================================
 */

declare(strict_types=1);

defined('SISTEM_VARNOST') or die('Direkten dostop ni dovoljen.');

// ============================================================
// GLOBALNE SPREMENLJIVKE
// ============================================================
$GLOBALS['ADAPTER_KANALI']          = [];
$GLOBALS['ADAPTER_ZACETEK']         = null;
$GLOBALS['ADAPTER_FORCIRANI_KANAL'] = $GLOBALS['ADAPTER_FORCIRANI_KANAL'] ?? null;

// ============================================================
// REGISTRACIJA KANALOV
// ============================================================

function adapter_registriraj_kanal(string $ime, object $kanal): void
{
    $GLOBALS['ADAPTER_KANALI'][$ime] = $kanal;
}

function adapter_pridobi_kanal(string $ime): ?object
{
    return $GLOBALS['ADAPTER_KANALI'][$ime] ?? null;
}

// ============================================================
// DOLOČITEV KANALA
// ============================================================

function adapter_doloci_kanal(): string
{
    if (isset($GLOBALS['ADAPTER_FORCIRANI_KANAL'])) {
        return $GLOBALS['ADAPTER_FORCIRANI_KANAL'];
    }

    // CLI / Cron – brez potrebe po mapi vhod_zasebno/
    if (PHP_SAPI === 'cli') {
        return 'cli';
    }

    $uri = $_SERVER['REQUEST_URI'] ?? '';

    // AI kanal: glava X-AI-AGENT ali /ai/ v URI
    if (isset($_SERVER['HTTP_X_AI_AGENT']) || str_contains($uri, '/ai/')) {
        return 'ai';
    }

    // API kanal: /api/ v URI
    if (str_contains($uri, '/api/')) {
        return 'api';
    }

    // Webhook kanali
    if (str_contains($uri, 'telegram')) {
        return 'telegram';
    }
    if (str_contains($uri, 'facebook')) {
        return 'facebook';
    }

    return 'splet';
}

// ============================================================
// NORMALIZACIJA ZAHTEVE
// ============================================================

function adapter_normaliziraj_zahtevo(): array
{
    $kanalIme = adapter_doloci_kanal();
    $metoda   = $_SERVER['REQUEST_METHOD'] ?? 'GET';
    $uri      = $_SERVER['REQUEST_URI'] ?? '/';

    // CLI normalizacija
    if ($kanalIme === 'cli') {
        global $argv;
        return [
            'id_zahteve' => uniqid('req_', true),
            'pot'        => $argv[1] ?? 'pomoc',
            'metoda'     => 'CLI',
            'parametri'  => array_slice($argv ?? [], 2),
            'vsebina'    => null,
            'glave'      => [],
            'ip'         => 'cli',
            'kanal'      => 'cli',
            'cas_prejema' => microtime(true),
        ];
    }

    // Parametri iz GET (razen 'svet')
    $parametri = [];
    foreach ($_GET as $kljuc => $vrednost) {
        if ($kljuc !== 'svet') {
            $parametri[$kljuc] = $vrednost;
        }
    }
    $pot = $_GET['svet'] ?? parse_url($uri, PHP_URL_PATH) ?? '/';

    // Telo zahteve
    $vsebina = null;
    if (in_array($metoda, ['POST', 'PUT', 'PATCH', 'DELETE'])) {
        $vhod = file_get_contents('php://input');
        if ($vhod) {
            $json = json_decode($vhod, true);
            if (is_array($json)) {
                $vsebina = $json;
            } else {
                $parametri = array_merge($parametri, $_POST);
            }
        } else {
            $parametri = array_merge($parametri, $_POST);
        }
    }

    // HTTP glave
    $glave = [];
    foreach ($_SERVER as $kljuc => $vrednost) {
        if (str_starts_with($kljuc, 'HTTP_')) {
            $imeGlave = str_replace(
                ' ', '-',
                ucwords(strtolower(str_replace('_', ' ', substr($kljuc, 5))))
            );
            $glave[$imeGlave] = $vrednost;
        }
    }

    // IP naslov
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
    } elseif (!empty($_SERVER['HTTP_X_REAL_IP'])) {
        $ip = $_SERVER['HTTP_X_REAL_IP'];
    }

    return [
        'id_zahteve'  => uniqid('req_', true),
        'pot'         => $pot,
        'metoda'      => $metoda,
        'parametri'   => $parametri,
        'vsebina'     => $vsebina,
        'glave'       => $glave,
        'ip'          => $ip,
        'kanal'       => $kanalIme,
        'cas_prejema' => microtime(true),
    ];
}

// ============================================================
// POŠILJANJE ODZIVA
// ============================================================

function adapter_poslji_odziv(array $odziv): void
{
    $kanalIme = $odziv['kanal'] ?? adapter_doloci_kanal();
    $kanal    = adapter_pridobi_kanal($kanalIme);

    if (isset($GLOBALS['ADAPTER_ZACETEK'])) {
        $odziv['cas_odziva'] = round(
            (microtime(true) - $GLOBALS['ADAPTER_ZACETEK']) * 1000, 2
        );
    }

    if ($kanal !== null) {
        try {
            $kanal->poslji($odziv);
        } catch (Throwable $e) {
            error_log('[ADAPTER] Pošiljanje odziva napaka (' . $kanalIme . '): ' . $e->getMessage());
            if (!headers_sent()) {
                header('Content-Type: application/json');
            }
            echo json_encode([
                'status'      => 'napaka',
                'status_koda' => 500,
                'sporocilo'   => 'Napaka pri pošiljanju odziva',
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }
    } else {
        if (!headers_sent()) {
            header('Content-Type: application/json');
        }
        echo json_encode($odziv, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
}

// ============================================================
// GLAVNI ZAGON
// ============================================================

function adapter_zagon(): void
{
    $GLOBALS['ADAPTER_ZACETEK'] = microtime(true);

    // Middleware sloj (v pravem vrstnem redu)
    // CORS mora biti prvi
    if (file_exists(POT_ADAPTER . '/middleware/cors.php')) {
        require_once POT_ADAPTER . '/middleware/cors.php';
    }

    // IP blacklist
    if (file_exists(POT_ADAPTER . '/middleware/ip_blacklist.php')) {
        require_once POT_ADAPTER . '/middleware/ip_blacklist.php';
    }

    // Rate limiting
    if (file_exists(POT_ADAPTER . '/middleware/omejevalnik.php')) {
        require_once POT_ADAPTER . '/middleware/omejevalnik.php';
    }

    // Dnevnik zahtev
    if (file_exists(POT_ADAPTER . '/middleware/dnevnik.php')) {
        require_once POT_ADAPTER . '/middleware/dnevnik.php';
    }

    // Registracija kanalov
    require_once POT_ADAPTER . '/izhod_kanali/KanalWeb.php';
    require_once POT_ADAPTER . '/izhod_kanali/KanalApi.php';
    require_once POT_ADAPTER . '/izhod_kanali/KanalTelegram.php';
    require_once POT_ADAPTER . '/izhod_kanali/KanalFacebook.php';
    require_once POT_ADAPTER . '/izhod_kanali/KanalAi.php';
    require_once POT_ADAPTER . '/izhod_kanali/KanalCli.php';

    adapter_registriraj_kanal('splet',    new KanalWeb());
    adapter_registriraj_kanal('api',      new KanalApi());
    adapter_registriraj_kanal('telegram', new KanalTelegram());
    adapter_registriraj_kanal('facebook', new KanalFacebook());
    adapter_registriraj_kanal('ai',       new KanalAi());
    adapter_registriraj_kanal('cli',      new KanalCli());

    // Normalizacija zahteve
    $zahteva = adapter_normaliziraj_zahtevo();

    // Obdelava s strani kanala (npr. Telegram/Facebook parsanje telesa)
    $kanalIme = $zahteva['kanal'];
    $kanal    = adapter_pridobi_kanal($kanalIme);
    if ($kanal !== null && method_exists($kanal, 'obdelaj')) {
        try {
            $zahteva = $kanal->obdelaj($zahteva);
        } catch (Throwable $e) {
            error_log('[ADAPTER] Kanal ' . $kanalIme . ' napaka pri obdelavi: ' . $e->getMessage());
        }
    }

    // ============================================================
    // EDINI VSTOP V SISTEM – preko SISTEM/api.php
    // Bootstrap in Zaganjalnik se sprožita znotraj sistem_izvedi()
    // ============================================================
    require_once POT_SISTEM . '/api.php';
    $odziv = sistem_izvedi($zahteva);

    adapter_poslji_odziv($odziv);
}
