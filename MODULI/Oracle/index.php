<?php
/**
 * Modul Orakleum - Index stranica
 * Datoteka: index.php
 * Namen: Glavna vstopna točka za Orakleum modul
 */

// Vključi potrebne datoteke
require_once __DIR__ . '/modul_oracle.php';
require_once __DIR__ . '/modul_oracle_funkcije.php';
require_once __DIR__ . '/modul_oracle_jsonbaza.php';

// Preveri ali je zahtevan AJAX klic
$ajax_zahteva = $_POST['ajax_zahteva'] ?? $_GET['ajax_zahteva'] ?? false;

if ($ajax_zahteva) {
    header('Content-Type: application/json');
    
    try {
        $modul = new ModulOrakleum();
        $akcija = $_POST['akcija'] ?? $_GET['akcija'] ?? '';
        $parametri = $_POST['parametri'] ?? $_GET['parametri'] ?? [];
        
        $rezultat = $modul->obdelajZahtevek($akcija, $parametri);
        echo json_encode($rezultat);
        
    } catch (Exception $e) {
        echo json_encode([
            'uspeh' => false,
            'napaka' => 'Napaka pri obdelavi: ' . $e->getMessage()
        ]);
    }
    exit;
}

// Drugače prikaži HTML stran
?>
<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orakleum - Tarot & Orakelji | AstraMentalica</title>
    <style>
        body {
            font-family: 'Georgia', serif;
            background: linear-gradient(135deg, #f5f5dc, #e6d3a3);
            margin: 0;
            padding: 20px;
            min-height: 100vh;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #8B4513;
            padding-bottom: 20px;
        }
        
        .title {
            font-size: 2.5em;
            color: #8B4513;
            margin: 0;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
        }
        
        .subtitle {
            font-size: 1.2em;
            color: #654321;
            margin: 10px 0;
            font-style: italic;
        }
        
        .oracle-section {
            background: #f9f9f9;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
            border-left: 5px solid #8B4513;
        }
        
        .question-area {
            margin: 20px 0;
        }
        
        .question-input {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            font-family: inherit;
        }
        
        .btn {
            background: linear-gradient(135deg, #8B4513, #A0522D);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            margin: 10px 5px;
            transition: all 0.3s ease;
        }
        
        .btn:hover {
            background: linear-gradient(135deg, #A0522D, #8B4513);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        
        .card-result {
            background: white;
            border: 2px solid #8B4513;
            border-radius: 10px;
            padding: 20px;
            margin: 15px 0;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            display: none;
        }
        
        .card-symbol {
            font-size: 3em;
            text-align: center;
            margin-bottom: 10px;
        }
        
        .card-name {
            font-size: 1.5em;
            color: #8B4513;
            text-align: center;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .card-meaning {
            color: #333;
            line-height: 1.6;
            text-align: center;
        }
        
        .loading {
            text-align: center;
            padding: 20px;
            color: #666;
            display: none;
        }
        
        .loading::after {
            content: "...";
            animation: dots 1.5s steps(5, end) infinite;
        }
        
        @keyframes dots {
            0%, 20% { color: rgba(0,0,0,0); text-shadow: .25em 0 0 rgba(0,0,0,0), .5em 0 0 rgba(0,0,0,0); }
            40% { color: #666; text-shadow: .25em 0 0 rgba(0,0,0,0), .5em 0 0 rgba(0,0,0,0); }
            60% { text-shadow: .25em 0 0 #666, .5em 0 0 rgba(0,0,0,0); }
            80%, 100% { text-shadow: .25em 0 0 #666, .5em 0 0 #666; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 class="title">🎭 Orakleum</h1>
            <p class="subtitle">Tarot & Orakelji - Portal mističnih kart</p>
        </div>
        
        <div class="oracle-section">
            <h3>✨ Vprašaj Orakleum</h3>
            <div class="question-area">
                <input type="text" class="question-input" id="vprasanje" 
                       placeholder="Postavite vprašanje za karte..." maxlength="200">
            </div>
            
            <div style="text-align: center;">
                <button class="btn" onclick="vleciEnoKarto()">🎴 Vleci Eno Karto</button>
                <button class="btn" onclick="vleciTriKarte()">🔮 Tri Karte (Preteklost-Sedanjost-Prihodnost)</button>
                <button class="btn" onclick="vleciSestKart()">⭐ Sest Kart (Popolna Analiza)</button>
            </div>
        </div>
        
        <div class="loading" id="loading">
            Karte se odkrivajo
        </div>
        
        <div id="rezultati"></div>
    </div>

    <script>
        function vleciKarto(steviloKart, tipVlecenja) {
            const vprasanje = document.getElementById('vprasanje').value.trim();
            
            // Prikaži loading
            document.getElementById('loading').style.display = 'block';
            document.getElementById('rezultati').innerHTML = '';
            
            const data = {
                akcija: tipVlecenja === 1 ? 'vleci_karto' : 'odpri_orakel',
                parametri: {
                    stevilo: steviloKart,
                    vprasanje: vprasanje,
                    pozicije: tipVlecenja === 3 ? ['preteklost', 'sedanjost', 'prihodnost'] : null
                }
            };
            
            fetch(window.location.href, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'ajax_zahteva=true&' + new URLSearchParams(data)
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById('loading').style.display = 'none';
                prikaziRezultate(data);
            })
            .catch(error => {
                document.getElementById('loading').style.display = 'none';
                prikaziNapako('Napaka pri komunikaciji s orakljem: ' + error);
            });
        }
        
        function vleciEnoKarto() {
            vleciKarto(1, 1);
        }
        
        function vleciTriKarte() {
            vleciKarto(3, 3);
        }
        
        function vleciSestKart() {
            vleciKarto(6, 6);
        }
        
        function prikaziRezultate(odgovor) {
            const rezultatiDiv = document.getElementById('rezultati');
            
            if (!odgovor.uspeh) {
                prikaziNapako(odgovor.napaka);
                return;
            }
            
            let html = '<div class="card-result" style="display: block;">';
            
            if (odgovor.karta) {
                // Ena karta
                html += `
                    <div class="card-symbol">${odgovor.karta.simbol}</div>
                    <div class="card-name">${odgovor.karta.ime}</div>
                    <div class="card-meaning">${odgovor.karta.opis}</div>
                `;
            } else if (odgovor.orakel) {
                // Orakel z več kartami
                html += `<h4 style="text-align: center; color: #8B4513;">Orakel ${odgovor.orakel.tip}</h4>`;
                
                odgovor.orakel.kartice.forEach((karta, index) => {
                    html += `
                        <div style="margin: 20px 0; padding: 15px; background: #f9f9f9; border-radius: 8px;">
                            <strong>Pozicija ${index + 1}:</strong>
                            <div class="card-symbol">${karta.simbol}</div>
                            <div class="card-name">${karta.ime}</div>
                            <div class="card-meaning">${karta.opis}</div>
                        </div>
                    `;
                });
                
                if (odgovor.celotna_interpretacija) {
                    html += `
                        <div style="background: #e6d3a3; padding: 15px; border-radius: 8px; margin-top: 20px;">
                            <strong>Celotna Interpretacija:</strong><br>
                            ${odgovor.celotna_interpretacija.skupaj_pomens}
                        </div>
                    `;
                }
            }
            
            html += '</div>';
            rezultatiDiv.innerHTML = html;
        }
        
        function prikaziNapako(sporocilo) {
            document.getElementById('rezultati').innerHTML = `
                <div class="card-result" style="display: block; border-color: #dc3545; background: #f8d7da;">
                    <div style="color: #721c24; text-align: center;">
                        ❌ ${sporocilo}
                    </div>
                </div>
            `;
        }
    </script>
</body>
</html>