<?php
declare(strict_types=1);

/**
 * Generira manifest.webmanifest za PWA module
 * Kitajski trg — mobile-first, standalone, offline-capable
 */

$izhod_dir = __DIR__ . '/izhod';

// Barve po kategorijah za theme_color
$tema_barve = [
    'NEBO'          => '#0f172a',
    'PROSTOR'       => '#052e16',
    'DIVINACIJA'    => '#1e1b4b',
    'MITOLOGIJA'    => '#431407',
    'PRAKSE'        => '#0c0a09',
    'NARAVA'        => '#14532d',
    'ZDRAVJE'       => '#450a0a',
    'PROFESIONALNO' => '#0f172a',
];

foreach (glob("$izhod_dir/*/podatki/manifest.json") as $manifest_pot) {
    $m = json_decode(file_get_contents($manifest_pot), true);

    // Samo PWA moduli
    if (!($m['ui']['pwa'] ?? false)) continue;

    $id       = $m['_id'];
    $ime      = $m['modul']['ime'];
    $ime_izv  = $m['modul']['ime_izvirno'] ?? '';
    $ikona    = $m['ui']['ikona'];
    $barva    = $m['ui']['barva'];
    $kat      = $m['ui']['kategorija'];
    $opis     = $m['modul']['opis'];
    $jeziki   = $m['ui']['jeziki'];
    $tema     = $tema_barve[$kat] ?? '#0f172a';
    $dir      = dirname($manifest_pot);

    // Primarna smer (zh -> LTR, ja -> LTR)
    $primarni_jezik = in_array('zh', $jeziki) ? 'zh' : (in_array('ja', $jeziki) ? 'ja' : 'en');

    // Kratek opis za display (max 80 znakov)
    $kratek_opis = mb_substr(explode('.', $opis)[0], 0, 80);

    $pwa = [
        'name'             => "$ime · $ime_izv",
        'short_name'       => $ime,
        'description'      => $kratek_opis,
        'start_url'        => "/$id/",
        'scope'            => "/$id/",
        'display'          => 'standalone',
        'orientation'      => 'portrait',
        'theme_color'      => $tema,
        'background_color' => '#0a0a0f',
        'lang'             => $primarni_jezik,
        'dir'              => 'ltr',
        'categories'       => ['lifestyle', 'education', 'spirituality'],

        'icons' => [
            [
                'src'     => "/assets/ikone/$id/icon-72.png",
                'sizes'   => '72x72',
                'type'    => 'image/png',
                'purpose' => 'any',
            ],
            [
                'src'     => "/assets/ikone/$id/icon-96.png",
                'sizes'   => '96x96',
                'type'    => 'image/png',
                'purpose' => 'any',
            ],
            [
                'src'     => "/assets/ikone/$id/icon-144.png",
                'sizes'   => '144x144',
                'type'    => 'image/png',
                'purpose' => 'any',
            ],
            [
                'src'     => "/assets/ikone/$id/icon-192.png",
                'sizes'   => '192x192',
                'type'    => 'image/png',
                'purpose' => 'any maskable',
            ],
            [
                'src'     => "/assets/ikone/$id/icon-512.png",
                'sizes'   => '512x512',
                'type'    => 'image/png',
                'purpose' => 'any maskable',
            ],
        ],

        'screenshots' => [
            [
                'src'          => "/assets/screenshots/$id/mobile-1.jpg",
                'sizes'        => '390x844',
                'type'         => 'image/jpeg',
                'form_factor'  => 'narrow',
                'label'        => "$ime — Glavna stran",
            ],
            [
                'src'          => "/assets/screenshots/$id/mobile-2.jpg",
                'sizes'        => '390x844',
                'type'         => 'image/jpeg',
                'form_factor'  => 'narrow',
                'label'        => "$ime — $kratek_opis",
            ],
        ],

        'shortcuts' => array_slice(
            array_map(fn($pot) => [
                'name' => ucfirst(str_replace(['/', "-", '_'], ['', ' ', ' '], ltrim(explode('/', $pot)[2] ?? $pot, '/'))),
                'url'  => $pot,
                'icons' => [['src' => "/assets/ikone/$id/icon-96.png", 'sizes' => '96x96']],
            ],
            // Vzami max 4 poti za shortcuts
            array_slice($m['ui']['jeziki'] ?? [], 0, 0) // placeholder — pravi se generira spodaj
        ), 0, 0),

        'prefer_related_applications' => false,

        'related_applications' => [],

        // WeChat mini-program integracija (za kitajski trg)
        'astra_pwa' => [
            'offline_capable'   => true,
            'cache_strategy'    => 'stale-while-revalidate',
            'wechat_compatible' => in_array('zh', $jeziki) || in_array('zh-TW', $jeziki),
            'alipay_compatible' => in_array('zh', $jeziki),
            'push_notifications' => false,
            'install_prompt'    => true,
        ],
    ];

    // Dodaj prave shortcuts iz HTTP poti v manifest modula
    $api_pot = dirname($manifest_pot) . '/api.json';
    if (file_exists($api_pot)) {
        $api = json_decode(file_get_contents($api_pot), true);
        $http_poti = $api['http_poti'] ?? [];
        $shortcuts = [];
        foreach (array_slice($http_poti, 0, 4) as $pot) {
            $deli = explode('/', trim($pot, '/'));
            $naziv = ucfirst(str_replace(['-', '_'], ' ', end($deli)));
            $shortcuts[] = [
                'name'  => $naziv,
                'url'   => $pot,
                'icons' => [['src' => "/assets/ikone/$id/icon-96.png", 'sizes' => '96x96']],
            ];
        }
        $pwa['shortcuts'] = $shortcuts;
    }

    $izhodna_pot = "$dir/manifest.webmanifest";
    file_put_contents($izhodna_pot, json_encode($pwa, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "\n");
    echo "✔ PWA: $ime ($id) · $ime_izv\n";
}

$skupaj = count(glob("$izhod_dir/*/podatki/manifest.webmanifest"));
echo "\nPWA manifestov: $skupaj\n";
