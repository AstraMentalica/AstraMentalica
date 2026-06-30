<?php
/**
 * ============================================================
 * POT: MODULI/SVETOVI/prikaz_sveta.php
 * VERZIJA: 1.0.0 (24.6.2026)
 * ============================================================
 *
 * 🏛️ NIVO: MODULI / SVETOVI
 *
 * 📰 NAMEN:
 *     Predloga za prikaz elementalnega sveta z glassmorphism UI
 *
 * 👤 AVTOR:
 *     AstraMentalica Mojster
 * ============================================================
 */

// $podatki mora biti definiran iz svetovi_handler.php
if (!isset($podatki)) {
    die('Neposreden dostop ni dovoljen.');
}
?>
<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($podatki['ime']) ?> | AstraMentalica</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: <?= $podatki['ozadje'] ?>;
            min-height: 100vh;
            padding: 2rem;
            position: relative;
            overflow-x: hidden;
        }

        /* Lebdeči elementi */
        body::before {
            content: '';
            position: fixed;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.08) 1px, transparent 1px);
            background-size: 40px 40px;
            animation: plavaj 30s linear infinite;
            pointer-events: none;
        }

        @keyframes plavaj {
            0% { transform: translate(0, 0) rotate(0deg); }
            100% { transform: translate(-50px, -50px) rotate(5deg); }
        }

        .kontejner {
            max-width: 1400px;
            margin: 0 auto;
            position: relative;
            z-index: 1;
        }

        /* Glava sveta */
        .glava {
            background: rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border-radius: 24px;
            border: 1px solid rgba(255, 255, 255, 0.25);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
            padding: 3rem;
            margin-bottom: 2rem;
            color: #fff;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .glava::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: sij 4s ease-in-out infinite alternate;
        }

        @keyframes sij {
            0% { transform: translate(0, 0); opacity: 0.5; }
            100% { transform: translate(-30px, 30px); opacity: 1; }
        }

        .svet-simbol {
            font-size: 5rem;
            margin-bottom: 1rem;
            display: block;
            animation: utripanje 3s ease-in-out infinite;
        }

        @keyframes utripanje {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }

        .glava h1 {
            font-size: 2.8rem;
            margin-bottom: 0.8rem;
            text-shadow: 0 2px 10px rgba(0,0,0,0.3);
        }

        .glava p {
            font-size: 1.1rem;
            opacity: 0.9;
            max-width: 600px;
            margin: 0 auto 1.5rem;
            line-height: 1.6;
        }

        /* Atributi */
        .atributi {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 0.6rem;
            margin-top: 1rem;
        }

        .atribut {
            background: rgba(255, 255, 255, 0.15);
            padding: 0.4rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
            border: 1px solid rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(5px);
        }

        /* Navigacija med svetovi */
        .nav-svetovi {
            display: flex;
            justify-content: center;
            gap: 0.8rem;
            margin-bottom: 2rem;
            flex-wrap: wrap;
        }

        .nav-svet {
            padding: 0.6rem 1.2rem;
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            background: rgba(255, 255, 255, 0.08);
            color: #fff;
            text-decoration: none;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            backdrop-filter: blur(5px);
        }

        .nav-svet:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
        }

        .nav-svet.aktiven {
            background: rgba(255, 255, 255, 0.25);
            border-color: rgba(255, 255, 255, 0.5);
            font-weight: 600;
        }

        /* Mreža modulov */
        .moduli-naslov {
            color: #fff;
            font-size: 1.5rem;
            margin-bottom: 1.2rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .moduli-mreza {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 1.2rem;
            margin-bottom: 2rem;
        }

        .modul-kartica {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-radius: 16px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 1.5rem;
            color: #fff;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .modul-kartica:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 40px 0 rgba(31, 38, 135, 0.5);
            background: rgba(255, 255, 255, 0.15);
        }

        .modul-ikona {
            font-size: 2rem;
            margin-bottom: 0.8rem;
        }

        .modul-kartica h3 {
            font-size: 1.1rem;
            margin-bottom: 0.4rem;
        }

        .modul-kartica p {
            font-size: 0.85rem;
            opacity: 0.8;
            line-height: 1.4;
        }

        /* Energijski trak */
        .energijski-trak {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            border: 1px solid rgba(255, 255, 255, 0.15);
            padding: 1.5rem 2rem;
            margin-bottom: 2rem;
            color: #fff;
        }

        .energijski-trak h3 {
            font-size: 1.1rem;
            margin-bottom: 0.8rem;
            opacity: 0.9;
        }

        .trak-vrstica {
            height: 8px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 4px;
            overflow: hidden;
        }

        .trak-polnilo {
            height: 100%;
            background: <?= $podatki['barva'] ?>;
            border-radius: 4px;
            width: 65%;
            animation: polni 2s ease-out;
            box-shadow: 0 0 15px <?= $podatki['barva'] ?>80;
        }

        @keyframes polni {
            0% { width: 0%; }
            100% { width: 65%; }
        }

        /* Povratna povezava */
        .povratek {
            display: inline-block;
            margin-top: 1rem;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            font-size: 0.95rem;
            transition: color 0.3s;
        }

        .povratek:hover { color: #fff; }

        @media (max-width: 768px) {
            body { padding: 1rem; }
            .glava { padding: 2rem; }
            .glava h1 { font-size: 2rem; }
            .svet-simbol { font-size: 3.5rem; }
            .moduli-mreza { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="kontejner">
        <!-- Navigacija med svetovi -->
        <div class="nav-svetovi">
            <?php
            $vsiSvetovi = svetovi_pridobi_vse();
            foreach ($vsiSvetovi as $id => $svet):
                $aktiven = $id === $podatki['id'];
            ?>
                <a href="?svet=<?= $id ?>" class="nav-svet <?= $aktiven ? 'aktiven' : '' ?>">
                    <?= $svet['simbol'] ?> <?= $svet['ime'] ?>
                </a>
            <?php endforeach; ?>
        </div>

        <!-- Glava sveta -->
        <div class="glava">
            <span class="svet-simbol"><?= $podatki['ikona'] ?></span>
            <h1><?= htmlspecialchars($podatki['ime']) ?></h1>
            <p><?= htmlspecialchars($podatki['opis']) ?></p>
            
            <div class="atributi">
                <?php foreach ($podatki['atributi'] as $atribut): ?>
                    <span class="atribut"><?= htmlspecialchars($atribut) ?></span>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Energijski trak -->
        <div class="energijski-trak">
            <h3>⚡ Energijska raven sveta</h3>
            <div class="trak-vrstica">
                <div class="trak-polnilo"></div>
            </div>
        </div>

        <!-- Moduli v svetu -->
        <h2 class="moduli-naslov">📦 Moduli v tem svetu</h2>
        <div class="moduli-mreza">
            <?php
            $moduli = svetovi_pridobi_module($podatki['id']);
            $vsiModuli = [];
            if (file_exists(__DIR__ . '/../moduli_elementi.php')) {
                require_once __DIR__ . '/../moduli_elementi.php';
                $vsiModuli = $moduli_po_elementih[$podatki['id']] ?? [];
            }
            
            foreach ($moduli as $imeModula):
                $info = $vsiModuli[$imeModula] ?? [
                    'ime' => $imeModula,
                    'opis' => 'Modul v tem svetu',
                    'ikona' => '📦',
                    'barva' => $podatki['barva']
                ];
            ?>
                <div class="modul-kartica" style="--modul-barv: <?= $info['barva'] ?? $podatki['barva'] ?>">
                    <div class="modul-ikona"><?= $info['ikona'] ?? '📦' ?></div>
                    <h3><?= htmlspecialchars($info['ime'] ?? $imeModula) ?></h3>
                    <p><?= htmlspecialchars($info['opis'] ?? '') ?></p>
                </div>
            <?php endforeach; ?>
        </div>

        <a href="?svet=UPORABNIKI&pot=profil" class="povratek">← Nazaj na profil</a>
    </div>
</body>
</html>