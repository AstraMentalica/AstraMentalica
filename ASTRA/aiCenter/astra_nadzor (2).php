<?php
declare(strict_types=1);

require_once __DIR__ . "/../pot.php";
require_once __DIR__ . "/../AI/varnost.php";

define('GESLO_HASH', password_hash('astra2026', PASSWORD_DEFAULT));

session_name('astra_nadzor');
session_start();

$prijavljen = $_SESSION['prijavljen'] ?? false;

// Prijava (ostane enaka)
if (!$prijavljen && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['geslo'])) {
    if (password_verify($_POST['geslo'], GESLO_HASH)) {
        $_SESSION['prijavljen'] = true;
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
}

if ($prijavljen && isset($_POST['akcija'])) {
    header('Content-Type: application/json');
    $akcija = $_POST['akcija'];

    try {
        switch ($akcija) {
            case 'zazeni_agenta':
                $agent = $_POST['agent'] ?? '';
                $pot = match($agent) {
                    'nadzornik'   => POT_ARHITEKTURNI . '/deepseek_nadzornik.php',
                    'nacrtovalec' => POT_ARHITEKTURNI . '/deepseek_nacrtovalec.php',
                    'arhitekt'    => POT_ARHITEKTURNI . '/deepseek_arhitekt.php',
                    default       => throw new Exception("Neznan agent"),
                };

                ob_start();
                include $pot;
                $izpis = ob_get_clean();

                echo json_encode(['uspeh' => true, 'izpis' => $izpis]);
                break;

            case 'pregled_nepotrjeno':
                $mape = glob(POT_NEPOTrJENO . "/*", GLOB_ONLYDIR);
                $rez = [];
                foreach ($mape as $m) {
                    $rez[] = basename($m);
                }
                echo json_encode(['uspeh' => true, 'mape' => $rez]);
                break;

            default:
                throw new Exception("Neznana akcija");
        }
    } catch (Exception $e) {
        echo json_encode(['uspeh' => false, 'napaka' => $e->getMessage()]);
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <title>ASTRA — Nadzor v2.3</title>
    <style>
        body { background:#0a0a1a; color:#d4c5a9; font-family:monospace; padding:20px; }
        .gumb { padding:10px 15px; margin:5px; background:#4f6ef7; color:white; border:none; border-radius:6px; cursor:pointer; }
    </style>
</head>
<body>
    <h1>🌌 ASTRA NADZOR v2.3</h1>

    <button class="gumb" onclick="zazeni('nadzornik')">👁️‍🗨️ Zaženi Nadzornika</button>
    <button class="gumb" onclick="zazeni('nacrtovalec')">🧠 Zaženi Načrtovalca</button>
    <button class="gumb" onclick="zazeni('arhitekt')">🔍 Zaženi Arhitekta</button>
    <button class="gumb" onclick="pregledNepotrjeno()">📋 Preglej Nepotrjeno</button>

    <div id="terminal" style="background:#000; padding:15px; margin-top:20px; min-height:300px; white-space:pre-wrap;"></div>

<script>
function zazeni(agent) {
    const t = document.getElementById('terminal');
    t.innerHTML += `▶ Zaganjam ${agent}...\n`;
    
    fetch('', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `akcija=zazeni_agenta&agent=${agent}`
    })
    .then(r => r.json())
    .then(data => {
        t.innerHTML += data.uspeh ? data.izpis : '❌ ' + data.napaka;
        t.scrollTop = t.scrollHeight;
    });
}

function pregledNepotrjeno() {
    const t = document.getElementById('terminal');
    t.innerHTML += "📂 Pregledujem nepotrjeno/...\n";
    // tukaj lahko kasneje dodaš več
}
</script>
</body>
</html>