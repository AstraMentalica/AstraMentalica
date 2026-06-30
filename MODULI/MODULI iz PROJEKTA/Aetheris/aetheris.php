<?php
/**
 * AETHERIS.PHP - GLAVNI CONTROLLER IN API SISTEM
 * Kombinacija frontend controllerja in API vmesnika
 */

// Vključi vse potrebne datoteke
require_once 'AetherisJedro.php';
require_once 'AetherisFunkcije.php';
require_once 'AI_Aetheris.php';

// Nastavi začetne headerje
header('Content-Type: text/html; charset=utf-8');

// Inicializiraj AI sistem
$ai_sistem = new AI_Aetheris();

// Preveri ali je to API klic ali prikaz strani
$je_api_klic = isset($_POST['akcija']) || isset($_GET['akcija']);

if ($je_api_klic) {
    // API MODE - vrni JSON odgovor
    obdelajApiZahtevek();
} else {
    // FRONTEND MODE - prikaži osnovno stran
    prikaziOsnovnoStran();
}

/**
 * Obdelaj API zahtevek
 */
function obdelajApiZahtevek() {
    global $ai_sistem;
    
    // Preklopi na JSON output
    header('Content-Type: application/json; charset=utf-8');
    
    $akcija = $_POST['akcija'] ?? $_GET['akcija'] ?? '';
    $odgovor = [];
    
    try {
        switch ($akcija) {
            case 'pridobi_osnovne_podatke':
                $odgovor = aetherisGenerirajUspeh('Osnovni podatki uspešno pridobljeni', [
                    'uporabniki' => AetherisJedro::pridobiVseUporabnike(),
                    'tematski_sklopi' => AetherisJedro::pridobiVseTematskeSklope(),
                    'statistika' => AetherisJedro::pridobiStatistiko(),
                    'ai_statistika' => $ai_sistem->pridobiStatistiko()
                ]);
                break;
                
            case 'vprasaj_ai':
                $vprasanje = aetherisSanitizirajVnos($_POST['vprasanje'] ?? '');
                $uporabnik = aetherisSanitizirajVnos($_POST['uporabnik'] ?? '');
                
                if (empty($vprasanje) || empty($uporabnik)) {
                    $odgovor = aetherisGenerirajNapako('Manjkata vprašanje in uporabnik');
                } else {
                    $ai_odgovor = $ai_sistem->obdelajVprasanje($vprasanje, $uporabnik);
                    $odgovor = aetherisGenerirajUspeh('AI je uspešno obdelal vprašanje', [
                        'odgovor' => $ai_odgovor,
                        'zgodovina' => count($ai_sistem->pridobiZgodovino())
                    ]);
                }
                break;
                
            case 'pridobi_teme':
                $sklop_id = $_POST['sklop_id'] ?? $_GET['sklop_id'] ?? null;
                $teme = AetherisJedro::pridobiVseTeme($sklop_id);
                $odgovor = aetherisGenerirajUspeh('Teme uspešno pridobljene', [
                    'teme' => $teme,
                    'sklop_id' => $sklop_id
                ]);
                break;
                
            case 'pridobi_statistiko':
                $odgovor = aetherisGenerirajUspeh('Statistika uspešno pridobljena', [
                    'statistika' => AetherisJedro::pridobiStatistiko(),
                    'ai_statistika' => $ai_sistem->pridobiStatistiko()
                ]);
                break;
                
            case 'testiraj_dostop':
                $uporabnik = aetherisSanitizirajVnos($_POST['uporabnik'] ?? $_GET['uporabnik'] ?? '');
                $raven = intval($_POST['raven'] ?? $_GET['raven'] ?? 0);
                
                if (empty($uporabnik)) {
                    $odgovor = aetherisGenerirajNapako('Manjka uporabnik');
                } else {
                    $dostop = aetherisPreveriDostop($uporabnik, $raven);
                    $odgovor = aetherisGenerirajUspeh('Dostop uspešno preverjen', [
                        'dostop' => $dostop,
                        'uporabnik' => $uporabnik,
                        'zahtevana_raven' => $raven
                    ]);
                }
                break;
                
            case 'ustvari_temo':
                $naslov = aetherisSanitizirajVnos($_POST['naslov'] ?? '');
                $vsebina = aetherisSanitizirajVnos($_POST['vsebina'] ?? '');
                $uporabnik = aetherisSanitizirajVnos($_POST['uporabnik'] ?? '');
                $sklop = intval($_POST['sklop'] ?? 1);
                
                $napake = aetherisValidirajTemo($naslov, $vsebina);
                
                if (!empty($napake)) {
                    $odgovor = aetherisGenerirajNapako('Napake pri validaciji: ' . implode(', ', $napake));
                } else {
                    $nova_tema_id = AetherisJedro::ustvariTemo($naslov, $vsebina, $uporabnik, $sklop);
                    aetherisLogirajDejavnost("Ustvarjena nova tema: $naslov", $uporabnik);
                    
                    $odgovor = aetherisGenerirajUspeh('Tema uspešno ustvarjena', [
                        'tema_id' => $nova_tema_id,
                        'naslov' => $naslov,
                        'avtor' => $uporabnik
                    ]);
                }
                break;
                
            case 'pridobi_ai_zgodovino':
                $stevilo = min(intval($_POST['stevilo'] ?? $_GET['stevilo'] ?? 10), 50);
                $zgodovina = $ai_sistem->pridobiZgodovino($stevilo);
                $odgovor = aetherisGenerirajUspeh('Zgodovina AI pridobljena', [
                    'zgodovina' => $zgodovina,
                    'stevilo_vnosov' => count($zgodovina)
                ]);
                break;
                
            default:
                $odgovor = aetherisGenerirajNapako('Neznana akcija. Razpoložljive akcije: pridobi_osnovne_podatke, vprasaj_ai, pridobi_teme, pridobi_statistiko, testiraj_dostop, ustvari_temo, pridobi_ai_zgodovino');
        }
        
    } catch (Exception $e) {
        $odgovor = aetherisGenerirajNapako('Sistemska napaka: ' . $e->getMessage());
        aetherisLogirajDejavnost("API napaka: " . $e->getMessage(), 'sistem');
    }
    
    echo json_encode($odgovor, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

/**
 * Prikaži osnovno frontend stran
 */
function prikaziOsnovnoStran() {
    global $ai_sistem;
    
    // Pridobi podatke za prikaz
    $uporabniki = AetherisJedro::pridobiVseUporabnike();
    $sklopi = AetherisJedro::pridobiVseTematskeSklope();
    $statistika = AetherisJedro::pridobiStatistiko();
    $ai_statistika = $ai_sistem->pridobiStatistiko();
    
    ?>
    <!DOCTYPE html>
    <html lang="sl">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Aetheris Controller - Sistem za Upravljanje</title>
        <style>
            * { margin: 0; padding: 0; box-sizing: border-box; }
            body { 
                font-family: 'Consolas', 'Monaco', monospace;
                background: #0f0f23;
                color: #00ff00;
                line-height: 1.6;
                padding: 20px;
            }
            .container { max-width: 1200px; margin: 0 auto; }
            .header { 
                text-align: center; 
                padding: 20px 0; 
                border-bottom: 2px solid #00ff00;
                margin-bottom: 30px;
            }
            .logo { 
                font-size: 2.5em; 
                color: #00ff00;
                text-shadow: 0 0 10px #00ff00;
                margin-bottom: 10px;
            }
            .panel { 
                background: #1a1a2e; 
                border: 1px solid #00ff00;
                border-radius: 5px;
                padding: 20px;
                margin-bottom: 20px;
            }
            .panel h2 { 
                color: #00ff00; 
                margin-bottom: 15px;
                border-bottom: 1px solid #00ff00;
                padding-bottom: 5px;
            }
            .grid { 
                display: grid; 
                grid-template-columns: 1fr 1fr; 
                gap: 20px; 
            }
            .button {
                background: #00ff00;
                color: #0f0f23;
                border: none;
                padding: 10px 15px;
                margin: 5px;
                border-radius: 3px;
                cursor: pointer;
                font-family: inherit;
                font-weight: bold;
            }
            .button:hover { background: #00cc00; }
            .input { 
                width: 100%; 
                padding: 8px; 
                margin: 5px 0; 
                background: #0f0f23;
                border: 1px solid #00ff00;
                color: #00ff00;
                border-radius: 3px;
            }
            .output {
                background: #000;
                border: 1px solid #00ff00;
                padding: 15px;
                margin-top: 10px;
                border-radius: 3px;
                white-space: pre-wrap;
                font-family: inherit;
                max-height: 400px;
                overflow-y: auto;
            }
            .status { 
                padding: 10px; 
                margin: 10px 0; 
                border-radius: 3px;
                border-left: 4px solid #00ff00;
            }
            .status.success { background: #1a2e1a; border-color: #00ff00; }
            .status.error { background: #2e1a1a; border-color: #ff0000; }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <div class="logo">AETHERIS CONTROLLER</div>
                <div>Sistem za upravljanje foruma - API + Frontend</div>
            </div>

            <div class="grid">
                <!-- Levi stolpec -->
                <div>
                    <div class="panel">
                        <h2>🎯 Hitre Akcije</h2>
                        <button class="button" onclick="klicApi('pridobi_osnovne_podatke')">📊 Osnovni Podatki</button>
                        <button class="button" onclick="klicApi('pridobi_statistiko')">📈 Statistika</button>
                        <button class="button" onclick="klicApi('pridobi_teme')">📚 Vse Teme</button>
                        <button class="button" onclick="klicApi('pridobi_ai_zgodovino')">🧠 AI Zgodovina</button>
                    </div>

                    <div class="panel">
                        <h2>🔮 AI Orakelj</h2>
                        <select class="input" id="aiUporabnik">
                            <?php foreach ($uporabniki as $kljuc => $uporabnik): ?>
                                <option value="<?= $kljuc ?>"><?= $uporabnik['ime'] ?> (Raven <?= $uporabnik['raven_dostopa'] ?>)</option>
                            <?php endforeach; ?>
                        </select>
                        <textarea class="input" id="aiVprasanje" placeholder="Vnesi vprašanje za AI..." rows="3"></textarea>
                        <button class="button" onclick="vprasajAI()">🎯 Pošlji AI</button>
                        <div id="aiRezultat" class="output" style="display: none;"></div>
                    </div>
                </div>

                <!-- Desni stolpec -->
                <div>
                    <div class="panel">
                        <h2>📝 Nova Tema</h2>
                        <input class="input" type="text" id="temaNaslov" placeholder="Naslov teme">
                        <textarea class="input" id="temaVsebina" placeholder="Vsebina teme..." rows="3"></textarea>
                        <select class="input" id="temaSklop">
                            <?php foreach ($sklopi as $sklop): ?>
                                <option value="<?= $sklop['id'] ?>"><?= $sklop['ikona'] ?> <?= $sklop['naslov'] ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button class="button" onclick="ustvariTemo()">➕ Ustvari Tema</button>
                    </div>

                    <div class="panel">
                        <h2>🔍 Test Dostopa</h2>
                        <select class="input" id="testUporabnik">
                            <?php foreach ($uporabniki as $kljuc => $uporabnik): ?>
                                <option value="<?= $kljuc ?>"><?= $uporabnik['ime'] ?></option>
                            <?php endforeach; ?>
                        </select>
                        <select class="input" id="testRaven">
                            <option value="0">Raven 0 (Gost)</option>
                            <option value="1">Raven 1 (Registriran)</option>
                            <option value="2">Raven 2 (Napredni)</option>
                            <option value="3">Raven 3 (Upravitelj)</option>
                        </select>
                        <button class="button" onclick="testirajDostop()">🔐 Testiraj Dostop</button>
                    </div>
                </div>
            </div>

            <!-- Prikaz rezultatov -->
            <div class="panel">
                <h2>📄 Rezultati API Klicev</h2>
                <div id="apiRezultat" class="output">
                    Rezultati API klicev se bodo prikazali tukaj...
                </div>
            </div>

            <!-- Statusna informacija -->
            <div class="panel">
                <h2>ℹ️ Sistem Informacija</h2>
                <div class="status success">
                    <strong>Jedro:</strong> <?= count($uporabniki) ?> uporabnikov, <?= count($sklopi) ?> sklopov, <?= $statistika['stevilo_tem'] ?> tem<br>
                    <strong>AI:</strong> <?= $ai_statistika['stevilo_interakcij'] ?> interakcij, Model: <?= $ai_statistika['uporabljeni_model'] ?><br>
                    <strong>Zadnja aktivnost:</strong> <?= $statistika['zadnja_aktivnost'] ?>
                </div>
            </div>
        </div>

        <script>
            async function klicApi(akcija, dodatniPodatki = {}) {
                try {
                    const podatki = new URLSearchParams({
                        akcija: akcija,
                        ...dodatniPodatki
                    });

                    const odziv = await fetch('aetheris.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: podatki
                    });

                    const rezultat = await odziv.json();
                    prikaziRezultat(rezultat);
                    
                } catch (napaka) {
                    prikaziRezultat({
                        status: 'napaka',
                        sporocilo: 'Omrežna napaka: ' + napaka.message
                    });
                }
            }

            function prikaziRezultat(rezultat) {
                const element = document.getElementById('apiRezultat');
                element.textContent = JSON.stringify(rezultat, null, 2);
                element.style.display = 'block';
            }

            function vprasajAI() {
                const vprasanje = document.getElementById('aiVprasanje').value;
                const uporabnik = document.getElementById('aiUporabnik').value;
                
                if (!vprasanje.trim()) {
                    alert('Vnesi vprašanje!');
                    return;
                }
                
                klicApi('vprasaj_ai', {
                    vprasanje: vprasanje,
                    uporabnik: uporabnik
                });
            }

            function ustvariTemo() {
                const naslov = document.getElementById('temaNaslov').value;
                const vsebina = document.getElementById('temaVsebina').value;
                const uporabnik = document.getElementById('aiUporabnik').value;
                const sklop = document.getElementById('temaSklop').value;
                
                if (!naslov.trim() || !vsebina.trim()) {
                    alert('Vnesi naslov in vsebino!');
                    return;
                }
                
                klicApi('ustvari_temo', {
                    naslov: naslov,
                    vsebina: vsebina,
                    uporabnik: uporabnik,
                    sklop: sklop
                });
            }

            function testirajDostop() {
                const uporabnik = document.getElementById('testUporabnik').value;
                const raven = document.getElementById('testRaven').value;
                
                klicApi('testiraj_dostop', {
                    uporabnik: uporabnik,
                    raven: raven
                });
            }

            // Samodejno naloži osnovne podatke ob zagonu
            document.addEventListener('DOMContentLoaded', function() {
                klicApi('pridobi_osnovne_podatke');
            });
        </script>
    </body>
    </html>
    <?php
}
?>