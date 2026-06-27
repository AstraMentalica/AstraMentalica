<?php
/**
 * ---------------------------------------------------------
 * POT: MODULI/moduli_setup.php
 * VERZIJA: 1.0.0 (24.6.2026)
 * ---------------------------------------------------------
 * OPIS: Setup skripta za registracijo modulov
 * ---------------------------------------------------------
 */

declare(strict_types=1);

// Preveri ali je sistem dostopen
if (!defined('SISTEM_OBSTAJA')) {
    die('Sistem ni dostopen. Zaženite skripto preko AstraMentalica sistema.');
}

// Samo admin lahko registrira module
if (!function_exists('pravice_preveri_vlogo') || !pravice_preveri_vlogo('S5')) {
    die('Za registracijo modulov potrebujete admin pravice (S5).');
}

// Naloži potrebne funkcije
require_once __DIR__ . '/Modul_Bridge/jedro/sistemske_funkcije.php';

// Seznam modulov za registracijo
$moduli_za_registracijo = [
    'VibraMystica' => [
        'ime' => 'VibraMystica',
        'oznaka' => 'vibramystica',
        'verzija' => '1.0.0',
        'vloga' => 'S0',
        'opis' => 'Zvočna mistika — zvočno zdravljenje, frekvenčna terapija, mantre in binaural beats.',
        'tip' => 'zbiralec',
        'nivo' => 1,
        'kategorija' => 'ZEMLJA',
        'tags' => ['zvok', 'frekvenca', 'mantra', 'binaural'],
        'jeziki' => ['sl']
    ],
    
    'Energetica' => [
        'ime' => 'Energetica',
        'oznaka' => 'energetica',
        'verzija' => '1.0.0',
        'vloga' => 'S0',
        'opis' => 'Interaktivni zemljevid čaker, barve, zvoki in meditacije.',
        'tip' => 'interaktivni',
        'nivo' => 1,
        'kategorija' => 'ZEMLJA',
        'tags' => ['energija', 'čakre', 'barve', 'zvoki'],
        'jeziki' => ['sl']
    ],
    
    'Celestara' => [
        'ime' => 'Celestara',
        'oznaka' => 'celestara',
        'verzija' => '1.0.0',
        'vloga' => 'S0',
        'opis' => 'Nebeški atlas — zvezde, ozvezdja, planetarne ure in kozmični ritmi.',
        'tip' => 'enciklopedija',
        'nivo' => 1,
        'kategorija' => 'NEBESNA',
        'tags' => ['zvezde', 'ozvezdja', 'planeti', 'astronomija'],
        'jeziki' => ['sl']
    ],
    
    'Tarot' => [
        'ime' => 'Tarot',
        'oznaka' => 'tarot',
        'verzija' => '1.0.0',
        'vloga' => 'S0',
        'opis' => 'Tarot vedeževanje in kartologija — 78 kart, številne razlage.',
        'tip' => 'divinacija',
        'nivo' => 1,
        'kategorija' => 'VEDIŽEVANJE',
        'tags' => ['tarot', 'karte', 'vedeževanje', 'orakelj'],
        'jeziki' => ['sl']
    ],
    
    'Runaris' => [
        'ime' => 'Runaris',
        'oznaka' => 'runaris',
        'verzija' => '1.0.0',
        'vloga' => 'S0',
        'opis' => 'Runska mitologija in interpretacija run — Futhark, Elder Futhark.',
        'tip' => 'enciklopedija',
        'nivo' => 1,
        'kategorija' => 'MITOLOGIJA',
        'tags' => ['rune', 'germanska', 'mitologija', 'Futhark'],
        'jeziki' => ['sl']
    ],
    
    'CodexDamiris' => [
        'ime' => 'Codex Damiris',
        'oznaka' => 'codex',
        'verzija' => '1.0.0',
        'vloga' => 'S0',
        'opis' => 'Digitalna knjiga modrosti in duhovnega razvoja — več poglavij, meditacije, rituali.',
        'tip' => 'knjiga',
        'nivo' => 1,
        'kategorija' => 'KNJIGE',
        'tags' => ['knjiga', 'modrost', 'duhovnost', 'meditacija'],
        'jeziki' => ['sl']
    ],
    
    'Oracle' => [
        'ime' => 'Oracle',
        'oznaka' => 'oracle',
        'verzija' => '1.0.0',
        'vloga' => 'S0',
        'opis' => 'Sistemsko vedeževanje in napovedi — integracija z AI.',
        'tip' => 'divinacija',
        'nivo' => 1,
        'kategorija' => 'VEDIŽEVANJE',
        'tags' => ['oracle', 'napovedi', 'AI', 'sistem'],
        'jeziki' => ['sl']
    ],
    
    'Lapidaria' => [
        'ime' => 'Lapidaria',
        'oznaka' => 'lapidaria',
        'verzija' => '1.0.0',
        'vloga' => 'S0',
        'opis' => 'Kristalna enciklopedija in kartice moči kristalov.',
        'tip' => 'enciklopedija',
        'nivo' => 1,
        'kategorija' => 'KRISTALI',
        'tags' => ['kristali', 'kamni', 'energija', 'zdravljenje'],
        'jeziki' => ['sl']
    ],
    
    'Devorum' => [
        'ime' => 'Devorum',
        'oznaka' => 'devorum',
        'verzija' => '1.0.0',
        'vloga' => 'S0',
        'opis' => 'Enciklopedija mitov in arhetipov — svetovne mitologije.',
        'tip' => 'enciklopedija',
        'nivo' => 1,
        'kategorija' => 'MITOLOGIJA',
        'tags' => ['miti', 'arhetipi', 'bogovi', 'legende'],
        'jeziki' => ['sl']
    ],
    
    'Kabbaloria' => [
        'ime' => 'Kabbaloria',
        'oznaka' => 'kabbaloria',
        'verzija' => '1.0.0',
        'vloga' => 'S0',
        'opis' => 'Drevo življenja in sefirotske poti kabalistične modrosti.',
        'tip' => 'mistika',
        'nivo' => 1,
        'kategorija' => 'KABALA',
        'tags' => ['kabala', 'drevo', 'sefiroti', 'misticizem'],
        'jeziki' => ['sl']
    ]
];

// Rezultati
$rezultati = [
    'uspeh' => [],
    'napake' => [],
    'preskoceni' => []
];

// Registriraj vsak modul
foreach ($moduli_za_registracijo as $ime_mape => $manifest) {
    try {
        // Preveri ali modul že obstaja
        if (modul_je_registriran($manifest['oznaka'])) {
            $rezultati['preskoceni'][] = "$ime_mape (že registriran)";
            continue;
        }
        
        // Preveri ali mapa obstaja
        $modul_pot = POT_MODULI . '/' . $ime_mape;
        if (!is_dir($modul_pot)) {
            $rezultati['napake'][] = "$ime_mape (mapa ne obstaja)";
            continue;
        }
        
        // Preveri ali obstaja modul.php
        if (!file_exists($modul_pot . '/modul.php')) {
            $rezultati['napake'][] = "$ime_mape (manjka modul.php)";
            continue;
        }
        
        // Registriraj modul
        if (modul_registriraj($ime_mape, $manifest)) {
            $rezultati['uspeh'][] = $ime_mape;
        } else {
            $rezultati['napake'][] = "$ime_mape (neznana napaka)";
        }
        
    } catch (Exception $e) {
        $rezultati['napake'][] = "$ime_mape ({$e->getMessage()})";
    }
}

// Prikaži rezultate
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Moduli Setup | AstraMentalica</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 2rem;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .container {
            max-width: 800px;
            width: 100%;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
            padding: 2.5rem;
            color: #fff;
        }

        h1 {
            font-size: 2rem;
            margin-bottom: 1.5rem;
            text-align: center;
        }

        .section {
            margin-bottom: 2rem;
        }

        .section h2 {
            font-size: 1.3rem;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .list {
            list-style: none;
            padding: 0;
        }

        .list li {
            padding: 0.8rem 1rem;
            margin-bottom: 0.5rem;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            border-left: 4px solid;
        }

        .list.success li {
            border-left-color: #4caf50;
        }

        .list.error li {
            border-left-color: #f44336;
        }

        .list.warning li {
            border-left-color: #ff9800;
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.1);
            padding: 1.5rem;
            border-radius: 12px;
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .stat-value {
            font-size: 2.5rem;
            font-weight: bold;
            color: #e8c84a;
        }

        .stat-label {
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.8);
            margin-top: 0.5rem;
        }

        .back-link {
            display: inline-block;
            margin-top: 1rem;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            font-size: 0.95rem;
        }

        .back-link:hover {
            color: #fff;
        }

        .icon-success { color: #4caf50; }
        .icon-error { color: #f44336; }
        .icon-warning { color: #ff9800; }
    </style>
</head>
<body>
    <div class="container">
        <h1>📦 Moduli Setup</h1>
        
        <div class="stats">
            <div class="stat-card">
                <div class="stat-value"><?= count($rezultati['uspeh']) ?></div>
                <div class="stat-label">Uspešno registriranih</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?= count($rezultati['napake']) ?></div>
                <div class="stat-label">Napak</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?= count($rezultati['preskoceni']) ?></div>
                <div class="stat-label">Preskočenih</div>
            </div>
        </div>

        <?php if (!empty($rezultati['uspeh'])): ?>
            <div class="section">
                <h2><span class="icon-success">✅</span> Uspešno registrirani</h2>
                <ul class="list success">
                    <?php foreach ($rezultati['uspeh'] as $modul): ?>
                        <li><?= htmlspecialchars($modul) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if (!empty($rezultati['napake'])): ?>
            <div class="section">
                <h2><span class="icon-error">❌</span> Napake</h2>
                <ul class="list error">
                    <?php foreach ($rezultati['napake'] as $napaka): ?>
                        <li><?= htmlspecialchars($napaka) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if (!empty($rezultati['preskoceni'])): ?>
            <div class="section">
                <h2><span class="icon-warning">⚠️</span> Preskočeni</h2>
                <ul class="list warning">
                    <?php foreach ($rezultati['preskoceni'] as $modul): ?>
                        <li><?= htmlspecialchars($modul) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <a href="?svet=UPORABNIKI&pot=moduli" class="back-link">← Nazaj na module</a>
    </div>
</body>
</html>