<?php
/**
 * ---------------------------------------------------------
 * POT: ASTRA/auth/prijava.php
 * v111 (27.5.2026 17:00)
 * ---------------------------------------------------------
 * OPIS: Admin prijava – samostojna prijava za ASTRA svet
 * ---------------------------------------------------------
 *
 * ODVISNOSTI:
 * - pot.php
 * - SISTEM/kernel/jedro/04_seja.php
 *
 * UPORABA:
 * - ASTRA vstopna točka
 *
 * PREPOVEDI:
 * - Brez business logike
 *
 * STATUS: Stabilno
 *
 * ZGODOVINA:
 * - v111: FAZA 20+ – implementacija
 *
 * ---------------------------------------------------------
 * AVTOR: AstraMentalica Mojster
 * ---------------------------------------------------------
 */

declare(strict_types=1);

session_start();

$napaka = '';
$uspeh = '';

// Preveri ali je že prijavljen
if (isset($_SESSION['astra_uporabnik']) && $_SESSION['astra_uporabnik']['vloga'] >= VLOGA_ADMIN) {
    header('Location: ?svet=ASTRA&pot=nadzorna_plosca');
    exit;
}

// Obdelava prijave
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $uporabniskoIme = $_POST['uporabnisko_ime'] ?? '';
    $geslo = $_POST['geslo'] ?? '';
    
    // Preveri admin poverilnice (v produkciji preko baze)
    $adminIme = getenv('ASTRA_ADMIN_UPORABNISKO_IME') ?: 'admin';
    $adminGeslo = getenv('ASTRA_ADMIN_GESLO') ?: 'admin2024';
    
    if ($uporabniskoIme === $adminIme && password_verify($geslo, password_hash($adminGeslo, PASSWORD_DEFAULT))) {
        $_SESSION['astra_uporabnik'] = [
            'id' => 1,
            'ime' => 'Administrator',
            'vloga' => VLOGA_ADMIN,
            'prijavljen_od' => time()
        ];
        
        header('Location: ?svet=ASTRA&pot=nadzorna_plosca');
        exit;
    } else {
        $napaka = 'Napačno uporabniško ime ali geslo.';
    }
}
?>
<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prijava v ASTRA | <?= IME_APLIKACIJE ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: system-ui, sans-serif;
            background: linear-gradient(135deg, #0a0a1a 0%, #1a1a2e 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #d4c5a9;
        }
        .astra-prijava {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 20px;
            padding: 2rem;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }
        .astra-prijava h1 {
            text-align: center;
            color: #e8c84a;
            margin-bottom: 0.5rem;
        }
        .astra-prijava .podnaslov {
            text-align: center;
            color: #888;
            margin-bottom: 2rem;
            font-size: 0.9rem;
        }
        .skupina {
            margin-bottom: 1rem;
        }
        label {
            display: block;
            margin-bottom: 0.5rem;
            color: #aaa;
        }
        input {
            width: 100%;
            padding: 0.75rem;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid #2a2a4a;
            border-radius: 8px;
            color: #d4c5a9;
            font-size: 1rem;
        }
        input:focus {
            outline: none;
            border-color: #e8c84a;
        }
        .napaka {
            background: rgba(244, 67, 54, 0.2);
            border-left: 3px solid #f44336;
            padding: 0.75rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            color: #f44336;
        }
        .gumb {
            width: 100%;
            padding: 0.75rem;
            background: #e8c84a;
            border: none;
            border-radius: 8px;
            color: #0a0a1a;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s;
        }
        .gumb:hover {
            background: #ffd700;
        }
        .nazaj {
            text-align: center;
            margin-top: 1rem;
        }
        .nazaj a {
            color: #888;
            text-decoration: none;
            font-size: 0.9rem;
        }
        .nazaj a:hover {
            color: #e8c84a;
        }
    </style>
</head>
<body>
    <div class="astra-prijava">
        <h1>🔐 ASTRA</h1>
        <div class="podnaslov">Admin dostop</div>
        
        <?php if ($napaka): ?>
            <div class="napaka"><?= htmlspecialchars($napaka) ?></div>
        <?php endif; ?>
        
        <form method="post">
            <div class="skupina">
                <label>Uporabniško ime</label>
                <input type="text" name="uporabnisko_ime" required>
            </div>
            <div class="skupina">
                <label>Geslo</label>
                <input type="password" name="geslo" required>
            </div>
            <button type="submit" class="gumb">Prijava</button>
        </form>
        
        <div class="nazaj">
            <a href="?svet=GLOBALNO">← Nazaj na domov</a>
        </div>
    </div>
</body>
</html>