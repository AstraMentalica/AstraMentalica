<?php
/**
 * ============================================================
 * POT: SISTEM/kanali/obdelava.php
 * 📅 VERZIJA: v116 (18.6.2026 20:25)
 * ============================================================
 * 🏛️ NIVO: SISTEM N2 (KANALI)
 * 📰 NAMEN: Obdelava čakalne vrste – pošiljanje na kanale.
 * 📌 STATUS: Stabilno
 * 👤 AVTOR: AstraMentalica Mojster
 * 🌐 JEZIK: sl
 * ============================================================
 */
declare(strict_types=1);

if (!defined('SISTEM_VARNOST')) {
    http_response_code(403);
    header('Location: /');
    return;
}

function obdelava_izvedi(?string $tip = null): array
{
    $postavke  = vrsta_preberi($tip);
    $rezultati = [];

    foreach ($postavke as $postavka) {
        $kanali = $postavka['kanali'] ?? ['web'];

        foreach ($kanali as $kanal) {
            $rezultati[] = [
                'kanal' => $kanal,
                'id'    => $postavka['id'] ?? 'unknown',
                'uspeh' => _poslji_na_kanal($kanal, $postavka)
            ];
        }
    }

    vrsta_pocisti($tip);

    return $rezultati;
}

function _poslji_na_kanal(string $kanal, array $postavka): bool
{
    $mapa_kanalov = [
        'web'      => POT_ADAPTER . '/izhod_kanali/KanalWeb.php',
        'api'      => POT_ADAPTER . '/izhod_kanali/KanalApi.php',
        'telegram' => POT_ADAPTER . '/izhod_kanali/KanalTelegram.php',
        'facebook' => POT_ADAPTER . '/izhod_kanali/KanalFacebook.php',
        'ai'       => POT_ADAPTER . '/izhod_kanali/KanalAi.php',
        'cli'      => POT_ADAPTER . '/izhod_kanali/KanalCli.php'
    ];

    $pot = $mapa_kanalov[$kanal] ?? null;

    if (!$pot || !file_exists($pot)) {
        return false;
    }

    try {
        require_once $pot;
        $funkcija = 'kanal_' . $kanal . '_poslji';
        if (!function_exists($funkcija)) {
            return false;
        }
        return $funkcija($postavka);
    } catch (Exception $e) {
        error_log('[OBDELAVA] Napaka pri kanalu ' . $kanal . ': ' . $e->getMessage());
        return false;
    }
}
