<?php
/**
 * ============================================================
 * MODUL: SVETOVI
 * POT: MODULI/SVETOVI/svetovi_vstop.php
 * ============================================================
 *
 * Namen:
 *     Minimalni sistemski vstop za upravljanje svetov.
 *     Predaja prikaz na GLOBALNO/postavitev/strani ali vrne seznam svetov.
 * ============================================================
 */

declare(strict_types=1);

if (!defined('SISTEM_VARNOST')) {
    http_response_code(403);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['status' => 'napaka', 'sporocilo' => 'Direkten dostop ni dovoljen.'], JSON_UNESCAPED_UNICODE);
    return;
}

function svetovi_vstop(array $zahteva = []): array
{
    $akcija = (string)($zahteva['akcija'] ?? 'pregled');

    if ($akcija === 'seznam') {
        return [
            'status' => 'uspeh',
            'status_koda' => 200,
            'tip' => 'json',
            'vsebina' => [
                'svetovi' => [
                    'GLOBALNO' => ['opis' => 'Javni prikaz in sestavljeni gradniki'],
                    'UPORABNIKI' => ['opis' => 'Prijava, registracija, profil in admin prikazi'],
                    'ASTRA' => ['opis' => 'Nadzorni in posebni svet'],
                    'MODULI' => ['opis' => 'Modulski svetovi in bridge'],
                ],
            ],
        ];
    }

    if ($akcija === 'prijava') {
        return [
            'status' => 'uspeh',
            'status_koda' => 200,
            'tip' => 'html',
            'vsebina' => [
                'stran' => 'uporabniki/prijava',
                'podatki' => [
                    'prijava' => $zahteva['prijava'] ?? [],
                ],
            ],
        ];
    }

    if ($akcija === 'registracija') {
        return [
            'status' => 'uspeh',
            'status_koda' => 200,
            'tip' => 'html',
            'vsebina' => [
                'stran' => 'uporabniki/registracija',
                'podatki' => [
                    'registracija' => $zahteva['registracija'] ?? [],
                ],
            ],
        ];
    }

    if ($akcija === 'admin') {
        return [
            'status' => 'uspeh',
            'status_koda' => 200,
            'tip' => 'html',
            'vsebina' => [
                'stran' => 'uporabniki/admin',
                'podatki' => [
                    'admin' => $zahteva['admin'] ?? [],
                ],
            ],
        ];
    }

    return [
        'status' => 'uspeh',
        'status_koda' => 200,
        'tip' => 'html',
        'vsebina' => [
            'stran' => 'GLOBALNO',
            'podatki' => [
                'demo' => true,
            ],
        ],
    ];
}
