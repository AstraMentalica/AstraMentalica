<?php
/**
 * ---------------------------------------------------------
 * POT: UPORABNIKI/prikaz/uporabnik/uporabnik_moduli.php
 * VERZIJA: 1.0.0 (24.6.2026)
 * ---------------------------------------------------------
 * OPIS: Prijetno okolje za upravljanje modulov
 * ---------------------------------------------------------
 */

declare(strict_types=1);

// Preveri prijavo
if (!seja_je_prijavljen()) {
    header('Location: ?svet=UPORABNIKI&pot=prijava');
    exit;
}

$uporabnikId = seja_pridobi('uporabnik_id');
$uporabnik = baza_beri_enega('uporabniki', $uporabnikId);

// Naloži uporabnikove moduli
$uporabnikoviModuli = [];
$moduliDatoteka = UPORABNIKI . '/' . $uporabnikId . '/moduli.json';
if (file_exists($moduliDatoteka)) {
    $uporabnikoviModuli = json_decode(file_get_contents($moduliDatoteka), true) ?? [];
}

// Seznam vseh modulov
$vsiModuli = [
    [
        'id' => 'vibramystica',
        'ime' => 'VibraMystica',
        'opis' => 'Zvočna mistika - frekvenčna terapija, mantre, binaural beats',
        'ikona' => '🎵',
        'barva' => '#9c27b0',
        'kategorija' => 'Zvok'
    ],
    [
        'id' => 'energetica',
        'ime' => 'Energetica',
        'opis' => 'Interaktivni zemljevid čaker, barve, zvoki in meditacije',
        'ikona' => '⚡',
        'barva' => '#ff9800',
        'kategorija' => 'Energija'
    ],
    [
        'id' => 'celestara',
        'ime' => 'Celestara',
        'opis' => 'Nebeški atlas - zvezde, ozvezdja, planetarne ure',
        'ikona' => '🌟',
        'barva' => '#2196f3',
        'kategorija' => 'Nebo'
    ],
    [
        'id' => 'tarot',
        'ime' => 'Tarot',
        'opis' => 'Tarot vedeževanje in kartologija',
        'ikona' => '🃏',
        'barva' => '#e91e63',
        'kategorija' => 'Divinacija'
    ],
    [
        'id' => 'runaris',
        'ime' => 'Runaris',
        'opis' => 'Runska mitologija in interpretacija run',
        'ikona' => 'ᚠ',
        'barva' => '#795548',
        'kategorija' => 'Mitologija'
    ],
    [
        'id' => 'codex',
        'ime' => 'Codex Damiris',
        'opis' => 'Digitalna knjiga modrosti in duhovnega razvoja',
        'ikona' => '📖',
        'barva' => '#4caf50',
        'kategorija' => 'Knjige'
    ],
    [
        'id' => 'oracle',
        'ime' => 'Oracle',
        'opis' => 'Sistemsko vedeževanje in napovedi',
        'ikona' => '🔮',
        'barva' => '#9c27b0',
        'kategorija' => 'Divinacija'
    ],
    [
        'id' => 'lapidaria',
        'ime' => 'Lapidaria',
        'opis' => 'Kristalna enciklopedija in kartice moči kristalov',
        'ikona' => '💎',
        'barva' => '#00bcd4',
        'kategorija' => 'Kristali'
    ],
    [
        'id' => 'devorum',
        'ime' => 'Devorum',
        'opis' => 'Enciklopedija mitov in arhetipov',
        'ikona' => '🏛️',
        'barva' => '#ff5722',
        'kategorija' => 'Mitologija'
    ],
    [
        'id' => 'kabbaloria',
        'ime' => 'Kabbaloria',
        'opis' => 'Drevo življenja in sefirotske poti kabalistične modrosti',
        'ikona' => '🌳',
        'barva' => '#3f51b5',
        'kategorija' => 'Kabala'
    ]
];

// Obdelava akcij
$napaka = '';
$uspeh = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $akcija = $_POST['akcija'] ?? '';
    
    if ($akcija === 'aktiviraj_modul') {
        $modulId = $_POST['modul_id'] ?? '';
        
        // Preveri ali modul že obstaja
        $zeObstaja = false;
        foreach ($uporabnikoviModuli as $m) {
            if ($m['id'] === $modulId) {
                $zeObstaja = true;
                break;
            }
        }
        
        if (!$zeObstaja) {
            // Najdi modul v seznamu
            $modul = null;
            foreach ($vsiModuli as $m) {
                if ($m['id'] === $modulId) {
                    $modul = $m;
                    break;
                }
            }
            
            if ($modul) {
                $uporabnikoviModuli[] = [
                    'id' => $modul['id'],
                    'ime' => $modul['ime'],
                    'aktiviran' => true,
                    'aktivirano' => time()
                ];
                
                // Shrani
                file_put_contents($moduliDatoteka, json_encode($uporabnikoviModuli, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
                
                $uspeh = "Modul {$modul['ime']} uspešno aktiviran!";
            }
        }
    }
    
    if ($akcija === 'deaktiviraj_modul') {
        $modulId = $_POST['modul_id'] ?? '';
        
        $uporabnikoviModuli = array_filter($uporabnikoviModuli, function($m) use ($modulId) {
            return $m['id'] !== $modulId;
        });
        
        // Shrani
        file_put_contents($moduliDatoteka, json_encode(array_values($uporabnikoviModuli), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        
        $uspeh = 'Modul deaktiviran.';
    }
}

// Razvrsti module po kategorijah
$moduliPoKategorijah = [];
foreach ($vsiModuli as $modul) {
    $moduliPoKategorijah[$modul['kategorija']][] = $modul;
}
?>

<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Moduli | AstraMentalica</title>
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
            position: relative;
            overflow-x: hidden;
        }

        body::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 1px, transparent 1px);
            background-size: 50px 50px;
            animation: float 20s linear infinite;
        }

        @keyframes float {
            0% { transform: translateY(0deg) rotate(0deg); }
            100% { transform: translateY(-50px) rotate(5deg); }
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            position: relative;
            z-index: 1;
        }

        .glass-header {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
            padding: 2.5rem;
            margin-bottom: 2rem;
            color: #fff;
        }

        .glass-header h1 {
            font-size: 2.2rem;
            margin-bottom: 0.5rem;
        }

        .glass-header p {
            color: rgba(255, 255, 255, 0.8);
            font-size: 1rem;
        }

        .stats-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-top: 1.5rem;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 1.2rem;
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .stat-value {
            font-size: 2rem;
            font-weight: bold;
            color: #e8c84a;
        }

        .stat-label {
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.8);
            margin-top: 0.3rem;
        }

        .category-section {
            margin-bottom: 2.5rem;
        }

        .category-title {
            color: #fff;
            font-size: 1.5rem;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .modules-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1.5rem;
        }

        .module-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
            padding: 1.8rem;
            color: #fff;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .module-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, var(--module-color, #667eea), transparent);
        }

        .module-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 40px 0 rgba(31, 38, 135, 0.5);
        }

        .module-card.active {
            border-color: rgba(102, 126, 234, 0.6);
            box-shadow: 0 8px 32px 0 rgba(102, 126, 234, 0.4);
        }

        .module-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .module-icon {
            width: 60px;
            height: 60px;
            border-radius: 15px;
            background: linear-gradient(135deg, var(--module-color, #667eea), var(--module-color-dark, #764ba2));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .module-info h3 {
            font-size: 1.3rem;
            margin-bottom: 0.2rem;
        }

        .module-info .module-category {
            font-size: 0.85rem;
            color: rgba(255, 255, 255, 0.7);
            background: rgba(255, 255, 255, 0.1);
            padding: 0.2rem 0.6rem;
            border-radius: 20px;
            display: inline-block;
        }

        .module-description {
            color: rgba(255, 255, 255, 0.85);
            font-size: 0.95rem;
            line-height: 1.5;
            margin-bottom: 1.5rem;
            min-height: 3rem;
        }

        .module-status {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1rem;
            font-size: 0.9rem;
        }

        .status-indicator {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #4caf50;
            box-shadow: 0 0 10px #4caf50;
        }

        .status-indicator.inactive {
            background: #f44336;
            box-shadow: 0 0 10px #f44336;
        }

        .module-actions {
            display: flex;
            gap: 0.8rem;
        }

        .module-button {
            flex: 1;
            padding: 0.7rem;
            border: none;
            border-radius: 10px;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
            text-decoration: none;
        }

        .module-button.primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
        }

        .module-button.primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
        }

        .module-button.secondary {
            background: rgba(255, 255, 255, 0.15);
            color: #fff;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .module-button.secondary:hover {
            background: rgba(255, 255, 255, 0.25);
        }

        .module-button.danger {
            background: rgba(244, 67, 54, 0.3);
            color: #fff;
            border: 1px solid rgba(244, 67, 54, 0.5);
        }

        .module-button.danger:hover {
            background: rgba(244, 67, 54, 0.4);
        }

        .glass-error {
            background: rgba(244, 67, 54, 0.2);
            border: 1px solid rgba(244, 67, 54, 0.4);
            border-radius: 12px;
            padding: 0.9rem;
            color: #ff6b6b;
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }

        .glass-success {
            background: rgba(76, 175, 80, 0.2);
            border: 1px solid rgba(76, 175, 80, 0.4);
            border-radius: 12px;
            padding: 0.9rem;
            color: #81c784;
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }

        .back-link {
            display: inline-block;
            margin-top: 2rem;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            font-size: 0.95rem;
            transition: color 0.3s;
        }

        .back-link:hover {
            color: #fff;
        }

        .empty-state {
            text-align: center;
            padding: 3rem;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 20px;
            border: 2px dashed rgba(255, 255, 255, 0.2);
            color: rgba(255, 255, 255, 0.7);
        }

        .empty-state-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
        }

        @media (max-width: 768px) {
            body {
                padding: 1rem;
            }
            
            .modules-grid {
                grid-template-columns: 1fr;
            }
            
            .glass-header h1 {
                font-size: 1.8rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="glass-header">
            <h1>📦 Moji Moduli</h1>
            <p>Upravljaj in raziskuj svoje module</p>
            
            <div class="stats-row">
                <div class="stat-card">
                    <div class="stat-value"><?= count($uporabnikoviModuli) ?></div>
                    <div class="stat-label">Aktivnih modulov</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value"><?= count($vsiModuli) ?></div>
                    <div class="stat-label">Razpoložljivih</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value"><?= count($vsiModuli) - count($uporabnikoviModuli) ?></div>
                    <div class="stat-label">Čaka aktivacijo</div>
                </div>
            </div>
        </div>

        <?php if ($napaka): ?>
            <div class="glass-error"><?= htmlspecialchars($napaka) ?></div>
        <?php endif; ?>

        <?php if ($uspeh): ?>
            <div class="glass-success"><?= htmlspecialchars($uspeh) ?></div>
        <?php endif; ?>

        <?php if (empty($uporabnikoviModuli)): ?>
            <div class="empty-state">
                <div class="empty-state-icon">📦</div>
                <h2>Še nimate aktiviranih modulov</h2>
                <p>Raziščite spodnje module in jih aktivirajte za začetek.</p>
            </div>
        <?php endif; ?>

        <?php foreach ($moduliPoKategorijah as $kategorija => $moduli): ?>
            <div class="category-section">
                <h2 class="category-title">
                    <span>📂</span>
                    <?= htmlspecialchars($kategorija) ?>
                </h2>
                
                <div class="modules-grid">
                    <?php foreach ($moduli as $modul): ?>
                        <?php
                        $jeAktiven = false;
                        foreach ($uporabnikoviModuli as $um) {
                            if ($um['id'] === $modul['id']) {
                                $jeAktiven = true;
                                break;
                            }
                        }
                        ?>
                        
                        <div class="module-card <?= $jeAktiven ? 'active' : '' ?>" 
                             style="--module-color: <?= $modul['barva'] ?>; 
                                    --module-color-dark: <?= $modul['barva'] ?>88;">
                            
                            <div class="module-header">
                                <div class="module-icon">
                                    <?= htmlspecialchars($modul['ikona']) ?>
                                </div>
                                <div class="module-info">
                                    <h3><?= htmlspecialchars($modul['ime']) ?></h3>
                                    <span class="module-category"><?= htmlspecialchars($modul['kategorija']) ?></span>
                                </div>
                            </div>
                            
                            <p class="module-description">
                                <?= htmlspecialchars($modul['opis']) ?>
                            </p>
                            
                            <div class="module-status">
                                <span class="status-indicator <?= $jeAktiven ? '' : 'inactive' ?>"></span>
                                <span><?= $jeAktiven ? 'Aktiven' : 'Neaktiven' ?></span>
                            </div>
                            
                            <div class="module-actions">
                                <?php if ($jeAktiven): ?>
                                    <a href="?svet=MODULI_CEL&modul=<?= $modul['id'] ?>" 
                                       class="module-button primary">
                                        Odpri
                                    </a>
                                    <form method="post" style="flex: 1;">
                                        <input type="hidden" name="akcija" value="deaktiviraj_modul">
                                        <input type="hidden" name="modul_id" value="<?= $modul['id'] ?>">
                                        <button type="submit" class="module-button danger" 
                                                onclick="return confirm('Ali ste prepričani?')">
                                            Deaktiviraj
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <form method="post" style="flex: 1;">
                                        <input type="hidden" name="akcija" value="aktiviraj_modul">
                                        <input type="hidden" name="modul_id" value="<?= $modul['id'] ?>">
                                        <button type="submit" class="module-button secondary">
                                            ✨ Aktiviraj
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>

        <a href="?svet=UPORABNIKI&pot=profil" class="back-link">← Nazaj na profil</a>
    </div>

    <script>
        // Animiraj module ob nalaganju
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.module-card');
            
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                
                setTimeout(() => {
                    card.style.transition = 'all 0.5s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 50);
            });
        });
    </script>
</body>
</html>