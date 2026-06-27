<?php
/**
 * ============================================================
 * POT: MODULI/Modul_Bridge/generator/generiraj_stebelno.php
 * 📅 VERZIJA: v118 (19.6.2026 02:00)
 * ============================================================
 *
 * 🏛️ NIVO: BRIDGE / GENERATOR
 *
 * 📰 NAMEN:
 *     Ustvari stebelni modul — popolnoma samostojen.
 *     Deluje brez ASTRAMENTALICA sistema preko embed/mini_sistem.php.
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - generiraj_stebelno(string $ime, string $opis): array
 *
 * 📌 STATUS:
 *     Stabilno
 *
 * 📅 ZGODOVINA:
 *     - v118: popravljena struktura – uporablja NOVO strukturo (brez kategorij)
 *     - v114: stara struktura
 *
 * 👤 AVTOR:
 *     AstraMentalica Mojster
 *
 * 🌐 JEZIK:
 *     sl
 *
 * 🏷️ OZNAKE:
 *     bridge, generator, stebelno
 * ============================================================
 */

declare(strict_types=1);

function generiraj_stebelno(string $ime, string $opis): array {
    if (!preg_match('/^[A-Z][a-zA-Z0-9]+$/', $ime)) {
        return ['uspeh' => false, 'napaka' => 'Ime mora biti v PascalCase (npr. MojModul).'];
    }

    // Stebelni modul gre v svojo mapo (ne v MODULI/)
    $pot_stebelnega = MINI_BRIDGE . '/stebelne/' . $ime . '/';
    $pot_podatki = $pot_stebelnega . 'podatki/';

    if (is_dir($pot_stebelnega)) {
        return ['uspeh' => false, 'napaka' => "Stebelni modul '$ime' že obstaja."];
    }

    // Ustvari mape
    if (!mkdir($pot_stebelnega, 0755, true) && !is_dir($pot_stebelnega)) {
        return ['uspeh' => false, 'napaka' => 'Ne morem ustvariti mape.'];
    }
    if (!mkdir($pot_podatki, 0755, true) && !is_dir($pot_podatki)) {
        return ['uspeh' => false, 'napaka' => 'Ne morem ustvariti mape podatki/.'];
    }

    $datum = date('d.m.Y H:i');
    $id = strtolower($ime);

    // ── podatki/manifest.json ──────────────────────────────
    $manifest = [
        '_id' => $id,
        '_verzija' => '1.0.0',
        'modul' => [
            'id' => $id,
            'ime' => $ime,
            'tip' => 'stebelno',
            'nivo' => 0,
            'verzija' => '1.0.0',
            'aktiviran' => true,
            'vstopna' => 'index.php',
            'opis' => $opis
        ],
        'dostop' => [
            'minimalna_vloga' => 'gost',
            'plan' => 'demo'
        ],
        'ui' => [
            'ima_prikaz' => true,
            'ikona' => '🌱',
            'barva' => '#6ee7b7'
        ],
        'izvajanje' => [
            'tip' => 'ui',
            'cron' => false,
            'api_only' => false
        ],
        'log' => [
            'omogocen' => true,
            'nivo' => 'info'
        ]
    ];
    file_put_contents(
        $pot_podatki . 'manifest.json',
        json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
    );

    // ── podatki/api.json ────────────────────────────────────
    $api = [
        '_id' => $id,
        '_verzija' => '1.0.0',
        'kanali' => ['web'],
        'vstop' => [
            'web' => 'index.php'
        ],
        'javne_metode' => ['domov'],
        'http_poti' => [
            '/' . $id
        ]
    ];
    file_put_contents(
        $pot_podatki . 'api.json',
        json_encode($api, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
    );

    // ── podatki/izhod.json ──────────────────────────────────
    $izhod = [
        '_id' => $id,
        '_verzija' => '1.0.0',
        'modul' => [
            'id' => $id,
            'ime' => $ime,
            'tip' => 'stebelno',
            'nivo' => 0,
            'verzija' => '1.0.0',
            'aktiviran' => true,
            'vstopna' => 'index.php',
            'opis' => $opis
        ],
        'dostop' => [
            'minimalna_vloga' => 'gost',
            'plan' => 'demo'
        ],
        'vhod' => [
            'potrebuje' => [],
            'opcijsko' => [],
            'vir' => 'demo'
        ],
        'izhod' => [
            'format' => 'html',
            'pise_v' => []
        ],
        'odvisnosti' => [
            'bere_iz' => []
        ],
        'ui' => [
            'ima_prikaz' => true,
            'ikona' => '🌱',
            'barva' => '#6ee7b7'
        ],
        'izvajanje' => [
            'tip' => 'ui',
            'cron' => false,
            'api_only' => false
        ],
        'log' => [
            'omogocen' => true,
            'nivo' => 'info'
        ]
    ];
    file_put_contents(
        $pot_podatki . 'izhod.json',
        json_encode($izhod, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
    );

    // ── index.php ──────────────────────────────────────────
    $index = <<<PHP
<?php
/**
 * ============================================================
 * $ime – Stebelni modul
 * Generirano z Modul Bridge ($datum)
 * ============================================================
 *
 * SAMOSTOJNI MODUL – deluje brez ASTRAMENTALICE.
 * Uporablja embed/mini_sistem.php iz Bridge-a.
 * ============================================================
 */

declare(strict_types=1);

// Poišči Bridge
\$bridgePoti = [
    __DIR__ . '/../modul_bridge.php',
    __DIR__ . '/../../modul_bridge.php',
    __DIR__ . '/../../../modul_bridge.php',
];

\$bridgeNajden = false;
foreach (\$bridgePoti as \$pot) {
    if (file_exists(\$pot)) {
        require_once \$pot;
        \$bridgeNajden = true;
        break;
    }
}

if (!\$bridgeNajden) {
    // Če Bridge ni najden, uporabi mini sistem direktno
    require_once __DIR__ . '/../../embed/mini_sistem.php';
}

mini_inicijalizacija();

\$modul = [
    'ime'     => '$ime',
    'id'      => '$id',
    'opis'    => '$opis',
    'verzija' => '1.0.0',
];

mini_izhod_glava(\$modul['ime']);
?>
<div class="kartica">
    <h2>✨ <?= htmlspecialchars(\$modul['ime'], ENT_QUOTES, 'UTF-8') ?></h2>
    <p><?= htmlspecialchars(\$modul['opis'], ENT_QUOTES, 'UTF-8') ?></p>
    <p><strong>Status:</strong> <span class="oznaka oznaka-info">Stebelni modul</span></p>
    <p><strong>ID:</strong> <code><?= \$modul['id'] ?></code></p>
</div>
<?php
mini_izhod_noga();
PHP;

    file_put_contents($pot_stebelnega . 'index.php', $index);

    // ── .htaccess ──────────────────────────────────────────
    file_put_contents($pot_stebelnega . '.htaccess', _stebelno_htaccess());

    // ── .gitkeep ────────────────────────────────────────────
    file_put_contents($pot_podatki . '.gitkeep', '');

    return [
        'uspeh' => true,
        'pot'   => $pot_stebelnega,
        'ime'   => $ime,
    ];
}

function _stebelno_htaccess(): string {
    return <<<'HTX'
# ---------------------------------------------------------
# .htaccess – varnostna zaščita stebelnega modula
# ---------------------------------------------------------

<FilesMatch "\.(php|php8|phtml)$">
    Order Deny,Allow
    Deny from all
</FilesMatch>

<FilesMatch "^(index\.php)$">
    Order Allow,Deny
    Allow from all
</FilesMatch>

<FilesMatch "\.(css|js|jpg|jpeg|png|gif|svg|ico|webp)$">
    Order Allow,Deny
    Allow from all
</FilesMatch>

<FilesMatch "\.(json|yml|yaml|xml|ini|env)$">
    Order Deny,Allow
    Deny from all
</FilesMatch>
HTX;
}