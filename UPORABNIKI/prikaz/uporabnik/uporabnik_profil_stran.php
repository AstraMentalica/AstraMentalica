<?php
/**
 * ---------------------------------------------------------
 * POT: UPORABNIKI/prikaz/uporabnik/uporabnik_profil_stran.php
 * VERZIJA: 1.0.0 (24.6.2026)
 * ---------------------------------------------------------
 * OPIS: Uporabniški profil s statistiko in aktivnostmi
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

// Naloži statistiko
$statistika = [
    'registriran' => date('d.m.Y H:i', $uporabnik['ustvarjen'] ?? time()),
    'zadnja_prijava' => date('d.m.Y H:i', $uporabnik['zadnja_prijava'] ?? time()),
    'stevilo_prijav' => $uporabnik['stevilo_prijav'] ?? 1,
];

// Naloži uporabnikove nastavitve
$nastavitve = [];
$nastavitveDatoteka = UPORABNIKI . '/' . $uporabnikId . '/nastavitve.json';
if (file_exists($nastavitveDatoteka)) {
    $nastavitve = json_decode(file_get_contents($nastavitveDatoteka), true) ?? [];
}

// Priljubljene frekvence
$priljubljene = $nastavitve['priljubljene_frekvence'] ?? [];
?>

<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Moj profil | AstraMentalica</title>
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
            max-width: 1000px;
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
            text-align: center;
        }

        .profile-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            margin: 0 auto 1.5rem;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.5);
        }

        .glass-header h1 {
            font-size: 2.2rem;
            margin-bottom: 0.5rem;
        }

        .glass-header p {
            color: rgba(255, 255, 255, 0.8);
            font-size: 1rem;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
            padding: 2rem;
            color: #fff;
        }

        .glass-card h2 {
            font-size: 1.4rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .stat-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .stat-item:last-child {
            border-bottom: none;
        }

        .stat-label {
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.95rem;
        }

        .stat-value {
            font-weight: 600;
            font-size: 1rem;
        }

        .action-buttons {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .glass-button {
            padding: 0.9rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 12px;
            color: #fff;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            text-decoration: none;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .glass-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
        }

        .glass-button.secondary {
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .glass-button.secondary:hover {
            background: rgba(255, 255, 255, 0.25);
        }

        .glass-button.danger {
            background: rgba(244, 67, 54, 0.3);
            border: 1px solid rgba(244, 67, 54, 0.5);
        }

        .glass-button.danger:hover {
            background: rgba(244, 67, 54, 0.4);
        }

        .favorites-section {
            margin-top: 2rem;
        }

        .favorites-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }

        .favorite-item {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            padding: 1rem;
            text-align: center;
            transition: all 0.3s ease;
        }

        .favorite-item:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
        }

        .favorite-icon {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        .favorite-name {
            font-size: 0.9rem;
            font-weight: 500;
        }

        .empty-state {
            text-align: center;
            padding: 2rem;
            color: rgba(255, 255, 255, 0.6);
            font-style: italic;
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

        @media (max-width: 768px) {
            body {
                padding: 1rem;
            }
            
            .grid {
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
            <div class="profile-avatar">👤</div>
            <h1><?= htmlspecialchars($uporabnik['ime'] ?? 'Uporabnik') ?></h1>
            <p><?= htmlspecialchars($uporabnik['elektronski_naslov'] ?? '') ?></p>
        </div>

        <div class="grid">
            <!-- Statistika -->
            <div class="glass-card">
                <h2>📊 Statistika</h2>
                <div class="stat-item">
                    <span class="stat-label">Registriran</span>
                    <span class="stat-value"><?= $statistika['registriran'] ?></span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">Zadnja prijava</span>
                    <span class="stat-value"><?= $statistika['zadnja_prijava'] ?></span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">Število prijav</span>
                    <span class="stat-value"><?= $statistika['stevilo_prijav'] ?></span>
                </div>
            </div>

            <!-- Hitre povezave -->
            <div class="glass-card">
                <h2>⚡ Hitre povezave</h2>
                <div class="action-buttons">
                    <a href="?svet=UPORABNIKI&pot=nastavitve" class="glass-button">
                        ⚙️ Nastavitve
                    </a>
                    <a href="?svet=MODULI_CEL" class="glass-button secondary">
                        📦 Moduli
                    </a>
                    <a href="?svet=UPORABNIKI&pot=odjava" class="glass-button danger">
                        🚪 Odjava
                    </a>
                </div>
            </div>
        </div>

        <!-- Priljubljene frekvence -->
        <div class="glass-card favorites-section">
            <h2>⭐ Priljubljene frekvence</h2>
            <?php if (!empty($priljubljene)): ?>
                <div class="favorites-grid">
                    <?php foreach ($priljubljene as $fav): ?>
                        <div class="favorite-item">
                            <div class="favorite-icon">🎵</div>
                            <div class="favorite-name"><?= htmlspecialchars($fav['ime'] ?? 'Frekvenca') ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    Še nimate shranjenih priljubljenih frekvenc.<br>
                    Obiščite <a href="?svet=UPORABNIKI&pot=nastavitve" style="color: #e8c84a;">Nastavitve</a> da jih dodate.
                </div>
            <?php endif; ?>
        </div>

        <a href="?svet=GLOBALNO" class="back-link">← Nazaj na domov</a>
    </div>
</body>
</html>