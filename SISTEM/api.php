<?php
/**
 * ============================================================
 * POT: SISTEM/api.php
 * 📅 VERZIJA: v119 (23.6.2026 12:00)
 * ============================================================
 *
 * 🏛️ NIVO: SISTEM (N1 – vstop)
 *
 * 📰 NAMEN:
 *     Edina vstopna točka v sistem.
 *     Adapter kliče sistem_izvedi(). Bootstrap in Zaganjalnik
 *     se sprožita tukaj.
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - sistem_izvedi(array $zahteva): array
 *
 * 📡 ODVISNOSTI:
 *     - pot.php
 *     - SISTEM/kernel/zaganjalnik.php prestavim v SISTEM/zaganjalnik.php
 *
 * 🚫 PREPOVEDI:
 *     - Brez die(), exit()
 *     - Brez definiranja konstant
 *     - Brez __DIR__
 *     - Brez HTML izpisa
 *
 * 📌 STATUS:
 *     Stabilno
 *
 * 📅 ZGODOVINA:
 *     - v116: uskladitev s Header Standard v116,
 *             odstranjeni vsi die() in exit()
 *
 * 👤 AVTOR:
 *     AstraMentalica Mojster
 *
 * 🌐 JEZIK:
 *     sl
 *
 * 🏷️ OZNAKE:
 *     sistem, vstop, api
 * ============================================================
 */
declare(strict_types=1);

// VARNOST – namesto die() uporabimo return
if (!defined('SISTEM_VARNOST')) {
    http_response_code(403);
    header('Location: /');
    return;
}

require_once __DIR__ . '/../pot.php';

// ============================================================
// GLAVNA FUNKCIJA – KLIČE JO ADAPTER
// ============================================================

function sistem_izvedi(array $zahteva): array
{
    _sistem_bootstrap();

    $akcija = $zahteva['parametri']['akcija'] ?? $zahteva['vsebina']['akcija'] ?? '';
    $pot    = $zahteva['pot'] ?? '';

    if (empty($akcija)) {
        return _sistem_prikazi_stran($pot, $zahteva);
    }

    return _sistem_api_route($akcija, $zahteva);
}

// ============================================================
// BOOTSTRAP – SAMO ENKRAT
// ============================================================

function _sistem_bootstrap(): void
{
    static $nalozeno = false;
    if ($nalozeno) {
        return;
    }
    $nalozeno = true;

    require_once POT_SISTEM . '/zaganjalnik.php';
    $odziv = zaganjalnik_izvedi(['sistem' => []]);

    if (isset($odziv['status']) && $odziv['status'] === 'napaka') {
        error_log('[SISTEM] Napaka pri zagonu jedra: ' . ($odziv['sporocilo'] ?? ''));
    }
}

// ============================================================
// PRIKAZ STRANI – VRAČA STRUKTURO, NE HTML!
// ============================================================

function _sistem_prikazi_stran(string $pot, array $zahteva): array
{
    $javneStrani      = ['prijava', 'registracija', 'pozabljeno_geslo', 'ponastavi_geslo', 'pogoji', 'zasebnost'];
    $zasebneStrani    = ['peskovnik', 'profil', 'passport', 'nastavitve'];
    $modulneStrani    = ['MODULI', 'modul'];
    $uporabnik        = $zahteva['uporabnik'] ?? null;

    // Google OAuth callback – ?svet=google_callback&code=...
    if ($pot === 'google_callback') {
        return _sistem_google_callback($zahteva);
    }

    // Zaščitene strani – preusmeri na prijavo, če ni prijavljen
    if (in_array($pot, $zasebneStrani, true) && $uporabnik === null) {
        return [
            'status'      => 'uspeh',
            'status_koda' => 302,
            'tip'         => 'preusmeritev',
            'vsebina'     => ['pot' => '?svet=prijava&napaka=prijava_obvezna'],
            'kanal'       => $zahteva['kanal'] ?? 'splet',
        ];
    }

    // Gost — demo dostop do javnih strani
    if ($uporabnik === null) {
        // Zaščitene strani — samo za prijavljene
        if (in_array($pot, $zasebneStrani, true)) {
            return [
                'status'      => 'uspeh',
                'status_koda' => 302,
                'tip'         => 'preusmeritev',
                'vsebina'     => ['pot' => '?svet=prijava&napaka=prijava_obvezna'],
                'kanal'       => $zahteva['kanal'] ?? 'splet',
            ];
        }

        // Gost: prazna pot ali GLOBALNO → domov (demo)
        // Gost: javne strani → Dostopne (prijava, registracija, itd.)
        // Gost: druge javne strani → pokaži kot demo
        $stran = $pot === '' || $pot === '/' || $pot === 'GLOBALNO'
            ? 'GLOBALNO'
            : (in_array($pot, $javneStrani, true) ? $pot : $pot);

        return [
            'status'      => 'uspeh',
            'status_koda' => 200,
            'tip'         => 'html',
            'vsebina'     => [
                'stran'   => $stran,
                'podatki' => [
                    'uporabnik' => null,
                    'parametri' => $zahteva['parametri'] ?? [],
                    'demo'      => true  // označi kot demo način
                ]
            ],
            'kanal'       => $zahteva['kanal'] ?? 'splet'
        ];
    }

    // Prijavljen uporabnik — privzeto na peskovnik
    if (in_array($pot, $javneStrani, true)) {
        // Če je že prijavljen in pride na prijavo/registracijo → na peskovnik
        return [
            'status'      => 'uspeh',
            'status_koda' => 302,
            'tip'         => 'preusmeritev',
            'vsebina'     => ['pot' => '?svet=peskovnik'],
            'kanal'       => $zahteva['kanal'] ?? 'splet',
        ];
    }

    // Prijavljen — dovoljene zasebne strani ali moduli ali default peskovnik
    if (in_array($pot, $modulneStrani, true)) {
        $stran = $pot;
    } else {
        $stran = in_array($pot, $zasebneStrani, true) ? $pot : 'peskovnik';
    }

    return [
        'status'      => 'uspeh',
        'status_koda' => 200,
        'tip'         => 'html',
        'vsebina'     => [
            'stran'   => $stran,
            'podatki' => [
                'uporabnik' => $uporabnik,
                'parametri' => $zahteva['parametri'] ?? []
            ]
        ],
        'kanal'       => $zahteva['kanal'] ?? 'splet'
    ];
}

// ============================================================
// GOOGLE OAuth CALLBACK
// ============================================================

function _sistem_google_callback(array $zahteva): array
{
    $kanal = $zahteva['kanal'] ?? 'splet';
    $koda  = $zahteva['parametri']['code'] ?? '';

    if (empty($koda)) {
        return [
            'status'      => 'uspeh',
            'status_koda' => 302,
            'tip'         => 'preusmeritev',
            'vsebina'     => ['pot' => '?svet=prijava&napaka=google_napaka'],
            'kanal'       => $kanal,
        ];
    }

    $potOAuth        = POT_SISTEM . '/storitve_svetov/uporabniki/uporabnik_google_oauth.php';
    $potRegistracija = POT_SISTEM . '/storitve_svetov/uporabniki/uporabnik_registracija.php';
    $potPrijava      = POT_SISTEM . '/storitve_svetov/uporabniki/uporabnik_prijava.php';

    require_once $potOAuth;
    require_once $potRegistracija;
    require_once $potPrijava;

    $oauth       = google_oauth();
    $googleUp    = $oauth->obdelajPovratek($koda);

    if ($googleUp === null) {
        return [
            'status'      => 'uspeh',
            'status_koda' => 302,
            'tip'         => 'preusmeritev',
            'vsebina'     => ['pot' => '?svet=prijava&napaka=google_napaka'],
            'kanal'       => $kanal,
        ];
    }

    // Registriraj ali prijavi prek Google
    $odziv = uporabniki_registriraj_google($googleUp);

    if (($odziv['status'] ?? '') === 'uspeh' || ($odziv['status_koda'] ?? 0) === 409) {
        // Prijavi v sejo
        $up = uporabniki_po_emailu($googleUp['email']);
        if ($up !== null) {
            seja_prijavi($up['id'], $up['ime'], $up['elektronski_naslov'], (int)($up['vloga'] ?? VLOGA_S0));
        }
        return [
            'status'      => 'uspeh',
            'status_koda' => 302,
            'tip'         => 'preusmeritev',
            'vsebina'     => ['pot' => '?svet=peskovnik'],
            'kanal'       => $kanal,
        ];
    }

    return [
        'status'      => 'uspeh',
        'status_koda' => 302,
        'tip'         => 'preusmeritev',
        'vsebina'     => ['pot' => '?svet=prijava&napaka=google_napaka'],
        'kanal'       => $kanal,
    ];
}

// ============================================================
// API ROUTING
// ============================================================

function _sistem_api_route(string $akcija, array $zahteva): array
{
    $podatki = array_merge($zahteva['parametri'] ?? [], $zahteva['vsebina'] ?? []);
    $kanal   = $zahteva['kanal'] ?? 'splet';

    $uporabnikAkcije = [
        'prijava', 'odjava', 'trenutni_uporabnik',
        'registracija', 'registracija_google',
        'profil_pridobi', 'profil_posodobi', 'profil_geslo',
    ];

    if (in_array($akcija, $uporabnikAkcije, true)) {
        return _sistem_route_uporabniki($akcija, $podatki, $kanal);
    }

    return [
        'status'      => 'napaka',
        'status_koda' => 404,
        'sporocilo'   => 'Neznana akcija: ' . htmlspecialchars($akcija),
        'vsebina'     => [],
        'kanal'       => $kanal,
    ];
}

// ============================================================
// UPORABNIKI – routing za vse akcije
// ============================================================

function _sistem_route_uporabniki(string $akcija, array $podatki, string $kanal): array
{
    $potPrijava      = POT_SISTEM . '/storitve_svetov/uporabniki/uporabnik_prijava.php';
    $potRegistracija = POT_SISTEM . '/storitve_svetov/uporabniki/uporabnik_registracija.php';
    $potProfil       = POT_SISTEM . '/storitve_svetov/uporabniki/uporabnik_profil.php';

    $odziv = match ($akcija) {

        'prijava' => (static function () use ($podatki, $potPrijava, $kanal): array {
            require_once $potPrijava;
            $odziv = uporabniki_prijavi(
                $podatki['email'] ?? '',
                $podatki['geslo'] ?? ''
            );
            // Po uspešni prijavi preusmeri spletni kanal na peskovnik
            if ($kanal === 'splet' && ($odziv['status'] ?? '') === 'uspeh') {
                return [
                    'status'      => 'uspeh',
                    'status_koda' => 302,
                    'tip'         => 'preusmeritev',
                    'vsebina'     => ['pot' => '?svet=peskovnik'],
                    'kanal'       => $kanal,
                ];
            }
            return $odziv;
        })(),

        'odjava' => (static function () use ($potPrijava, $kanal): array {
            require_once $potPrijava;
            uporabniki_odjavi();
            return [
                'status'      => 'uspeh',
                'status_koda' => 302,
                'tip'         => 'preusmeritev',
                'vsebina'     => ['pot' => '?svet=prijava'],
                'kanal'       => $kanal,
            ];
        })(),

        'trenutni_uporabnik' => (static function () use ($potPrijava): array {
            require_once $potPrijava;
            $upo = uporabniki_trenutni();
            return $upo
                ? ['status' => 'uspeh',  'status_koda' => 200, 'vsebina' => $upo, 'sporocilo' => '']
                : ['status' => 'napaka', 'status_koda' => 401, 'vsebina' => [], 'sporocilo' => 'Ni prijavljenega uporabnika.'];
        })(),

        'registracija' => (static function () use ($podatki, $potRegistracija, $kanal): array {
            require_once $potRegistracija;
            $odziv = uporabniki_registriraj($podatki);
            if ($kanal === 'splet' && ($odziv['status'] ?? '') === 'uspeh') {
                return [
                    'status'      => 'uspeh',
                    'status_koda' => 302,
                    'tip'         => 'preusmeritev',
                    'vsebina'     => ['pot' => '?svet=peskovnik'],
                    'kanal'       => $kanal,
                ];
            }
            return $odziv;
        })(),

        'registracija_google' => (static function () use ($podatki, $potRegistracija): array {
            require_once $potRegistracija;
            return uporabniki_registriraj_google($podatki);
        })(),

        'profil_pridobi' => (static function () use ($podatki, $potProfil): array {
            require_once $potProfil;
            return uporabniki_profil_pridobi($podatki['id'] ?? null);
        })(),

        'profil_posodobi' => (static function () use ($podatki, $potProfil): array {
            require_once $potProfil;
            return uporabniki_profil_posodobi($podatki);
        })(),

        'profil_geslo' => (static function () use ($podatki, $potProfil): array {
            require_once $potProfil;
            return uporabniki_profil_spremeni_geslo(
                $podatki['staro_geslo'] ?? '',
                $podatki['novo_geslo']  ?? ''
            );
        })(),

        default => [
            'status'      => 'napaka',
            'status_koda' => 404,
            'sporocilo'   => 'Neznana uporabniška akcija.',
            'vsebina'     => [],
        ],
    };

    $odziv['kanal'] = $kanal;
    return $odziv;
}

