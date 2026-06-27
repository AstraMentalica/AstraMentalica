<?php
declare(strict_types=1);

define('GESLO_HASH', password_hash('astra2026', PASSWORD_DEFAULT));

$ROOT = realpath(__DIR__ . "/..");
if ($ROOT === false) die('ROOT ni določljiv.');

require_once $ROOT . "/pot.php";
require_once $ROOT . "/AI/varnost.php";

session_name('astra_nadzor');
session_start();

$prijavljen = $_SESSION['prijavljen'] ?? false;

// ... (prijava del ostane enak - preskočil sem za krajšino)

if ($prijavljen && isset($_POST['akcija'])) {
    header('Content-Type: application/json');
    $akcija = $_POST['akcija'];

    try {
        switch ($akcija) {
            case 'zazeni_agenta':
                $agent = $_POST['agent'] ?? '';
                
                $datoteke = [
                    'nadzornik'     => POT_ARHITEKTURNI . '/deepseek_nadzornik.php',
                    'nacrtovalec'   => POT_ARHITEKTURNI . '/deepseek_nacrtovalec.php',
                    'arhitekt'      => POT_ARHITEKTURNI . '/deepseek_arhitekt.php',
                    'koder'         => POT_ARHITEKTURNI . '/deepseek_koder.php',
                    'integrator'    => POT_ARHITEKTURNI . '/deepseek_integrator.php',
                    'revizor'       => POT_ARHITEKTURNI . '/deepseek_revizor.php',
                ];
                
                if (!isset($datoteke[$agent])) throw new Exception("Neznan agent: $agent");
                
                ob_start();
                include $datoteke[$agent];
                $izpis = ob_get_clean();
                
                echo json_encode(['uspeh' => true, 'izpis' => $izpis]);
                break;

            // Dodaj kasneje tudi potrjevanje predlogov...
            default:
                throw new Exception("Neznana akcija.");
        }
    } catch (Exception $e) {
        echo json_encode(['uspeh' => false, 'napaka' => $e->getMessage()]);
    }
    exit;
}
?>

<!-- HTML del (skrajšan za zdaj) -->
<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <title>ASTRA — Nadzor</title>
    <!-- stil ostane enak -->
</head>
<body>

<div class="glavna">
    <div class="stranska">
        <button class="gumb" onclick="pokazi('agenti',this)">🤖 Agenti</button>
    </div>

    <div class="vsebina">
        <div id="plosca-agenti" class="plosca">
            <h2>🤖 Agenti</h2>
            
            <button class="gumb-akcija" onclick="zazeni('nadzornik')">👁️‍🗨️ Zaženi Nadzornika</button>
            <button class="gumb-akcija" onclick="zazeni('nacrtovalec')">🧠 Zaženi Načrtovalca</button>
            <button class="gumb-akcija" onclick="zazeni('arhitekt')">🔍 Arhitekt</button>
            <!-- ostali agenti -->
        </div>
    </div>
</div>

<script>
function zazeni(agent) {
    // AJAX klic (enak kot prej)
    console.log("Zaganjam:", agent);
}
</script>

</body>
</html>