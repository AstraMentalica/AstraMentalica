<?php
/**
 * ============================================================
 * POT: SISTEM/kanali/priprava.php
 * 📅 VERZIJA: v116 (18.6.2026 20:25)
 * ============================================================
 * 🏛️ NIVO: SISTEM N2 (KANALI)
 * 📰 NAMEN: Priprava izhoda v standardiziran format.
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

function izhod_pripravi(array $data): array
{
    return [
        'tip'     => $data['tip']     ?? 'generic',
        'kanali'  => $data['kanali']  ?? ['web'],
        'vsebina' => $data['vsebina'] ?? [],
        'meta'    => $data['meta']    ?? [
            'cas'    => time(),
            'sistem' => SISTEM_VERZIJA
        ]
    ];
}
