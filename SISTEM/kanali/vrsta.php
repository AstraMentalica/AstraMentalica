<?php
/**
 * ============================================================
 * POT: SISTEM/kanali/vrsta.php
 * 📅 VERZIJA: v116 (18.6.2026 20:25)
 * ============================================================
 * 🏛️ NIVO: SISTEM N2 (KANALI)
 * 📰 NAMEN: Upravljanje čakalne vrste za izhod.
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

function vrsta_dodaj(array $item): bool
{
    $vrsta = baza_beri('sistem', 'vrsta/izhod');

    if (!isset($vrsta['postavke'])) {
        $vrsta = ['postavke' => []];
    }

    $item['id']  = uniqid('vrsta_', true);
    $item['cas'] = time();

    $vrsta['postavke'][] = $item;

    return baza_pisi('sistem', 'vrsta/izhod', $vrsta);
}

function vrsta_preberi(?string $tip = null): array
{
    $vrsta = baza_beri('sistem', 'vrsta/izhod');

    if (empty($vrsta['postavke'])) {
        return [];
    }

    if ($tip === null) {
        return $vrsta['postavke'];
    }

    return array_values(array_filter($vrsta['postavke'], function($item) use ($tip) {
        return ($item['tip'] ?? '') === $tip;
    }));
}

function vrsta_stevilo(?string $tip = null): int
{
    return count(vrsta_preberi($tip));
}

function vrsta_pocisti(?string $tip = null): bool
{
    if ($tip === null) {
        return baza_pisi('sistem', 'vrsta/izhod', ['postavke' => []]);
    }

    $vrsta = baza_beri('sistem', 'vrsta/izhod');

    if (empty($vrsta['postavke'])) {
        return true;
    }

    $vrsta['postavke'] = array_values(array_filter($vrsta['postavke'], function($item) use ($tip) {
        return ($item['tip'] ?? '') !== $tip;
    }));

    return baza_pisi('sistem', 'vrsta/izhod', $vrsta);
}
