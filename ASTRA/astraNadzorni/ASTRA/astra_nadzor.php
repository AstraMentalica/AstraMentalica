<?php
declare(strict_types=1);

// ============================================================
// KONFIGURACIJA
// ============================================================
define('GESLO_HASH', password_hash('astra2026', PASSWORD_DEFAULT));

$ROOT = realpath(__DIR__ . "/..");
if ($ROOT === false) {
    die('ROOT ni določljiv.');
}

// ============================================================
// POTI - PRAVA STRUKTURA
// ============================================================
define('POT_ROOT',          $ROOT);
define('POT_AI',            $ROOT . '/AI');
define('POT_SISTEMSKI_AI',  $ROOT . '/AI/sistemskiAI');
define('POT_ARHITEKTURNI',  $ROOT . '/AI/sistemskiAI/arhitekturniAI');
define('POT_STRUKTURNI',    $ROOT . '/AI/sistemskiAI/strukturniAI');
define('POT_NALOGE',        $ROOT . '/AI/sistemskiAI/naloge');
define('POT_POROCILA',      $ROOT . '/AI/sistemskiAI/naloge/porocila');
define('POT_BACKUP',        $ROOT . '/AI/sistemskiAI/naloge/backup');
define('POT_PATCH',         $ROOT . '/AI/sistemskiAI/naloge/patch');
define('POT_DNEVNIK',       $ROOT . '/AI/sistemskiAI/naloge/dnevnik');
define('POT_SPOMIN',        $ROOT . '/AI/sistemskiAI/naloge/spomin');
define('POT_PRAVILA',       $ROOT . '/AI/sistemskiAI/pravila');
define('POT_ENV',           $ROOT . '/PODATKI/sef/.env_dipsi');

// ============================================================
// BRANJE API KLJUČA
// ============================================================
function preberiDeepSeekKljuc(): string {
    if (!file_exists(POT_ENV)) return '';
    $vsebina = file_get_contents(POT_ENV);
    foreach (explode("\n", $vsebina) as $vrstica) {
        $vrstica = trim($vrstica);
        if (strpos($vrstica, 'DEEPSEEK_API_KEY=') === 0) {
            return trim(explode('=', $vrstica, 2)[1]);
        }
    }
    return '';
}

// ============================================================
// SEJA
// ============================================================
session_name('astra_nadzor');
session_start();

$prijavljen = $_SESSION['prijavljen'] ?? false;

if (isset($_GET['odjava'])) {
    session_destroy();
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

$prijavaNapaka = '';
if (!$prijavljen && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['geslo'])) {
    if (password_verify($_POST['geslo'], GESLO_HASH)) {
        $_SESSION['prijavljen'] = true;
        $prijavljen = true;
    } else {
        $prijavaNapaka = 'Napačno geslo.';
        sleep(1);
    }
}

// ============================================================
// AJAX
// ============================================================
if ($prijavljen && isset($_POST['akcija'])) {
    header('Content-Type: application/json; charset=utf-8');
    $akcija = $_POST['akcija'] ?? '';

    try {
        switch ($akcija) {

            // ================================================
            // ZAŽENI AGENTA
            // ================================================
            case 'zazeni_agenta':
                $agent = $_POST['agent'] ?? '';
                
                $datoteke = [
                    'arhitekt'    => POT_ARHITEKTURNI . '/deepseek_arhitekt.php',
                    'koder'       => POT_ARHITEKTURNI . '/deepseek_koder.php',
                    'integrator'  => POT_ARHITEKTURNI . '/deepseek_integrator.php',
                    'revizor'     => POT_ARHITEKTURNI . '/deepseek_revizor.php',
                    'arhitekt_sistem'     => POT_STRUKTURNI . '/arhitekt_sistem.php',
                    'arhitekt_globalno'   => POT_STRUKTURNI . '/arhitekt_globalno.php',
                    'arhitekt_uporabniki' => POT_STRUKTURNI . '/arhitekt_uporabniki.php',
                    'arhitekt_podatki'    => POT_STRUKTURNI . '/arhitekt_podatki.php',
                    'arhitekt_vsebina'    => POT_STRUKTURNI . '/arhitekt_vsebina.php',
                    'arhitekt_moduli'     => POT_STRUKTURNI . '/arhitekt_moduli.php'
                ];
                
                if (!isset($datoteke[$agent])) {
                    throw new Exception("Neznan agent: $agent");
                }
                
                $pot = $datoteke[$agent];
                if (!file_exists($pot)) {
                    throw new Exception("Agent ni najden: " . basename($pot));
                }
                
                ob_start();
                include $pot;
                $izpis = ob_get_clean();
                
                echo json_encode(['uspeh' => true, 'izpis' => $izpis]);
                break;

            // ================================================
            // ZAŽENI CEL CIKEL
            // ================================================
            case 'zazeni_cikel':
                $agenti = ['arhitekt', 'koder', 'integrator', 'revizor'];
                $rezultati = [];
                foreach ($agenti as $a) {
                    $pot = POT_ARHITEKTURNI . '/deepseek_' . $a . '.php';
                    if (!file_exists($pot)) {
                        $rezultati[$a] = "❌ Agent ni nameščen";
                        continue;
                    }
                    ob_start();
                    include $pot;
                    $rezultati[$a] = ob_get_clean();
                }
                echo json_encode(['uspeh' => true, 'rezultati' => $rezultati]);
                break;

            // ================================================
            // POGOVOR Z AGENTOM
            // ================================================
            case 'pogovor':
                $sporocilo = trim($_POST['sporocilo'] ?? '');
                if (empty($sporocilo)) {
                    throw new Exception("Sporočilo je prazno.");
                }
                
                $apiKljuc = preberiDeepSeekKljuc();
                if (empty($apiKljuc)) {
                    throw new Exception("API ključ manjka v PODATKI/sef/.env_dipsi");
                }
                
                $odgovor = pokliciDeepSeek($sporocilo, $apiKljuc);
                echo json_encode(['uspeh' => true, 'odgovor' => $odgovor]);
                break;

            // ================================================
            // PREBERI POROČILA
            // ================================================
            case 'preberi_porocila':
                $porocila = [];
                $tipi = ['arhitekt', 'koder', 'integrator', 'revizor'];
                foreach ($tipi as $tip) {
                    $datoteke = glob(POT_POROCILA . "/{$tip}_*.json");
                    if (!empty($datoteke)) {
                        sort($datoteke);
                        $zadnja = end($datoteke);
                        $porocila[$tip] = json_decode(file_get_contents($zadnja), true);
                    }
                }
                echo json_encode(['uspeh' => true, 'porocila' => $porocila]);
                break;

            // ================================================
            // PREBERI DNEVNIK
            // ================================================
            case 'preberi_dnevnik':
                $datoteka = POT_DNEVNIK . '/lastnik_akcije.log';
                if (!file_exists($datoteka)) {
                    echo json_encode(['uspeh' => true, 'vsebina' => "Dnevnik še ne obstaja."]);
                    break;
                }
                echo json_encode(['uspeh' => true, 'vsebina' => file_get_contents($datoteka)]);
                break;

            default:
                throw new Exception("Neznana akcija.");
        }
    } catch (Exception $e) {
        echo json_encode(['uspeh' => false, 'napaka' => $e->getMessage()]);
    }
    exit;
}

// ============================================================
// FUNKCIJE
// ============================================================
function pokliciDeepSeek(string $sporocilo, string $apiKljuc): string {
    $podatki = [
        'model' => 'deepseek-chat',
        'messages' => [
            ['role' => 'system', 'content' => 'Si AI asistent za AstraMentalica sistem. Odgovarjaj v slovenščini.'],
            ['role' => 'user', 'content' => $sporocilo]
        ],
        'max_tokens' => 1024,
        'temperature' => 0.7
    ];

    $ch = curl_init('https://api.deepseek.com/chat/completions');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $apiKljuc,
        ],
        CURLOPT_POSTFIELDS => json_encode($podatki),
        CURLOPT_TIMEOUT => 30,
    ]);

    $odziv = curl_exec($ch);
    $napaka = curl_error($ch);
    curl_close($ch);

    if ($napaka) return "❌ Napaka: $napaka";
    $json = json_decode($odziv, true);
    return $json['choices'][0]['message']['content'] ?? "❌ Ni odgovora.";
}

// ============================================================
// HTML - PRIJAVA
// ============================================================
if (!$prijavljen): ?>
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><title>ASTRA Nadzor</title>
<style>
body{background:#0d0f14;color:#e8eaf0;display:flex;align-items:center;justify-content:center;height:100vh;font-family:system-ui;}
.box{background:#151820;border:1px solid #2a2f42;border-radius:12px;padding:40px;max-width:360px;width:100%;text-align:center;}
.box h1{color:#4f6ef7;}
.box input{width:100%;padding:10px;background:#1c2030;border:1px solid #2a2f42;border-radius:8px;color:#fff;margin:15px 0;}
.box button{width:100%;padding:10px;background:#4f6ef7;border:none;border-radius:8px;color:#fff;font-weight:bold;cursor:pointer;}
.error{color:#e74c3c;font-size:14px;}
</style>
</head>
<body>
<div class="box">
    <h1>🌌 ASTRA NADZOR</h1>
    <p style="color:#8890a8;">Admin dostop</p>
    <?php if ($prijavaNapaka): ?><div class="error"><?= $prijavaNapaka ?></div><?php endif; ?>
    <form method="POST">
        <input type="password" name="geslo" placeholder="Geslo" autofocus>
        <button type="submit">Vstopi →</button>
    </form>
</div>
</body>
</html>
<?php exit; endif; ?>

<!-- ============================================================ -->
<!-- HTML - NADZORNA PLOŠČA -->
<!-- ============================================================ -->
<!DOCTYPE html>
<html lang="sl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>ASTRA — Nadzor</title>
<style>
*{margin:0;padding:0;box-sizing:border-box;}
body{background:#0d0f14;color:#e8eaf0;font-family:system-ui;font-size:14px;}

.glavna{display:flex;height:100vh;}
.stranska{width:220px;background:#151820;border-right:1px solid #2a2f42;padding:15px 0;overflow-y:auto;}
.stranska .gumb{display:block;width:100%;text-align:left;padding:10px 15px;background:none;border:none;color:#8890a8;cursor:pointer;font-size:14px;}
.stranska .gumb:hover{background:#1c2030;color:#fff;}
.stranska .gumb.aktiven{background:#1e2540;color:#4f6ef7;}
.stranska .oznaka{color:#8890a8;font-size:11px;text-transform:uppercase;padding:10px 15px 5px;}

.vsebina{flex:1;padding:20px;overflow-y:auto;}
.plosca{display:none;}
.plosca.aktivna{display:block;}
.naslov{font-size:20px;font-weight:bold;margin-bottom:15px;}
.kartica{background:#151820;border:1px solid #2a2f42;border-radius:10px;padding:15px;margin-bottom:15px;}
.terminal{background:#080b10;border:1px solid #2a2f42;border-radius:8px;padding:15px;font-family:monospace;font-size:13px;color:#a8c4a8;max-height:400px;overflow-y:auto;white-space:pre-wrap;margin-top:10px;}

.agenti{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:10px;}
.agent{background:#151820;border:1px solid #2a2f42;border-radius:10px;padding:15px;text-align:center;}
.agent .ikona{font-size:30px;}
.agent .ime{font-weight:bold;margin:5px 0;}
.agent .opis{color:#8890a8;font-size:12px;}

.gumb-akcija{background:#4f6ef7;border:none;border-radius:6px;color:#fff;padding:8px 16px;cursor:pointer;font-size:13px;}
.gumb-akcija:hover{background:#7b93ff;}
.gumb-akcija.siv{background:#1c2030;border:1px solid #2a2f42;color:#8890a8;}
.gumb-akcija.siv:hover{background:#2a2f42;}

.pogovor{background:#080b10;border:1px solid #2a2f42;border-radius:10px;height:400px;display:flex;flex-direction:column;}
.pogovor-msgs{flex:1;overflow-y:auto;padding:15px;display:flex;flex-direction:column;gap:10px;}
.pogovor-msgs .lastnik{align-self:flex-end;background:#1e2a4a;padding:8px 12px;border-radius:10px;}
.pogovor-msgs .agent{align-self:flex-start;background:#1c2030;padding:8px 12px;border-radius:10px;}
.pogovor-msgs .sistem{align-self:center;color:#8890a8;font-style:italic;font-size:13px;}

.pogovor-vhod{display:flex;gap:10px;padding:10px;border-top:1px solid #2a2f42;}
.pogovor-vhod input{flex:1;background:#1c2030;border:1px solid #2a2f42;border-radius:8px;color:#fff;padding:10px;outline:none;}
.pogovor-vhod input:focus{border-color:#4f6ef7;}
.pogovor-vhod button{background:#4f6ef7;border:none;border-radius:8px;color:#fff;padding:10px 20px;cursor:pointer;}

.izbira-agenta{display:flex;gap:8px;flex-wrap:wrap;margin-bottom:15px;}
.izbira-agenta button{padding:6px 14px;border-radius:6px;border:1px solid #2a2f42;background:#151820;color:#8890a8;cursor:pointer;font-size:13px;}
.izbira-agenta button.aktiven{border-color:#4f6ef7;background:#1e2540;color:#4f6ef7;}
.izbira-agenta button:hover{border-color:#4f6ef7;color:#fff;}

.statistike{display:grid;grid-template-columns:repeat(4,1fr);gap:10px;margin-bottom:15px;}
.stat{background:#1c2030;border:1px solid #2a2f42;border-radius:8px;padding:12px;text-align:center;}
.stat .st{font-size:28px;font-weight:bold;font-family:monospace;}
.stat .label{color:#8890a8;font-size:11px;text-transform:uppercase;}

@media(max-width:700px){.glavna{flex-direction:column;}.stranska{width:100%;border-right:none;border-bottom:1px solid #2a2f42;}}
</style>
</head>
<body>

<div class="glavna">

    <!-- STRANSKA -->
    <div class="stranska">
        <div class="oznaka">Pregled</div>
        <button class="gumb aktiven" onclick="pokazi('pregled',this)">📊 Stanje</button>
        <button class="gumb" onclick="pokazi('porocila',this)">📋 Poročila</button>
        <div class="oznaka">Agenti</div>
        <button class="gumb" onclick="pokazi('agenti',this)">🤖 Zaženi</button>
        <button class="gumb" onclick="pokazi('pogovor',this)">💬 Pogovor</button>
        <div class="oznaka">Sistem</div>
        <button class="gumb" onclick="pokazi('dnevnik',this)">📜 Dnevnik</button>
        <button class="gumb" onclick="location.href='?odjava'" style="color:#e74c3c;">🚪 Odjava</button>
    </div>

    <!-- VSEBINA -->
    <div class="vsebina">

        <!-- PREGLED -->
        <div id="plosca-pregled" class="plosca aktivna">
            <div class="naslov">📊 Stanje sistema</div>
            <div class="statistike" id="statistike">
                <div class="stat"><div class="st" id="s-kriticnih">-</div><div class="label">Kritičnih</div></div>
                <div class="stat"><div class="st" id="s-visokih">-</div><div class="label">Visokih</div></div>
                <div class="stat"><div class="st" id="s-skupaj">-</div><div class="label">Skupaj napak</div></div>
                <div class="stat"><div class="st" id="s-status">-</div><div class="label">Status</div></div>
            </div>
            <div class="kartica">
                <div style="display:flex;justify-content:space-between;margin-bottom:10px;">
                    <strong>Zadnji cikel</strong>
                    <button class="gumb-akcija siv" onclick="naloziPorocila()">↻ Osveži</button>
                </div>
                <div id="pregled-vsebina">Nalagam...</div>
            </div>
        </div>

        <!-- POROČILA -->
        <div id="plosca-porocila" class="plosca">
            <div class="naslov">📋 Poročila</div>
            <div id="porocila-vsebina">Nalagam...</div>
        </div>

        <!-- AGENTI -->
        <div id="plosca-agenti" class="plosca">
            <div class="naslov">🤖 Agenti</div>
            
            <div style="margin-bottom:15px;">
                <h3 style="color:#8890a8;font-size:13px;margin-bottom:8px;">📌 Splošni agenti</h3>
                <div class="agenti">
                    <div class="agent"><div class="ikona">🔍</div><div class="ime">Arhitekt</div><div class="opis">Pregled sistema</div><button class="gumb-akcija" onclick="zazeni('arhitekt')">▶ Zaženi</button></div>
                    <div class="agent"><div class="ikona">🔧</div><div class="ime">Koder</div><div class="opis">Popravi napake</div><button class="gumb-akcija" onclick="zazeni('koder')">▶ Zaženi</button></div>
                    <div class="agent"><div class="ikona">🧪</div><div class="ime">Integrator</div><div class="opis">Testira sistem</div><button class="gumb-akcija" onclick="zazeni('integrator')">▶ Zaženi</button></div>
                    <div class="agent"><div class="ikona">📋</div><div class="ime">Revizor</div><div class="opis">Skupna ocena</div><button class="gumb-akcija" onclick="zazeni('revizor')">▶ Zaženi</button></div>
                </div>
            </div>

            <div style="margin-bottom:15px;">
                <h3 style="color:#8890a8;font-size:13px;margin-bottom:8px;">📁 Specifični arhitekti</h3>
                <div class="agenti">
                    <div class="agent"><div class="ikona">⚙️</div><div class="ime">Sistem</div><div class="opis">SISTEM/</div><button class="gumb-akcija" onclick="zazeni('arhitekt_sistem')">▶</button></div>
                    <div class="agent"><div class="ikona">🎨</div><div class="ime">Globalno</div><div class="opis">GLOBALNO/</div><button class="gumb-akcija" onclick="zazeni('arhitekt_globalno')">▶</button></div>
                    <div class="agent"><div class="ikona">👤</div><div class="ime">Uporabniki</div><div class="opis">UPORABNIKI/</div><button class="gumb-akcija" onclick="zazeni('arhitekt_uporabniki')">▶</button></div>
                    <div class="agent"><div class="ikona">💾</div><div class="ime">Podatki</div><div class="opis">PODATKI/</div><button class="gumb-akcija" onclick="zazeni('arhitekt_podatki')">▶</button></div>
                    <div class="agent"><div class="ikona">📄</div><div class="ime">Vsebina</div><div class="opis">VSEBINA/</div><button class="gumb-akcija" onclick="zazeni('arhitekt_vsebina')">▶</button></div>
                    <div class="agent"><div class="ikona">🧩</div><div class="ime">Moduli</div><div class="opis">MODULI/</div><button class="gumb-akcija" onclick="zazeni('arhitekt_moduli')">▶</button></div>
                </div>
            </div>

            <div class="kartica">
                <button class="gumb-akcija" onclick="zazeniCikel()" style="width:100%;padding:12px;font-size:16px;">▶▶ Zaženi CEL CIKEL</button>
            </div>

            <div class="terminal" id="terminal-agenti">Pripravljen.</div>
        </div>

        <!-- POGOVOR -->
        <div id="plosca-pogovor" class="plosca">
            <div class="naslov">💬 Pogovor z agentom</div>
            
            <div class="izbira-agenta" id="izbira-agenta">
                <button class="aktiven" data-agent="splosno" onclick="izberiAgenta('splosno',this)">🧠 Splošno</button>
                <button data-agent="arhitekt" onclick="izberiAgenta('arhitekt',this)">🔍 Arhitekt</button>
                <button data-agent="koder" onclick="izberiAgenta('koder',this)">🔧 Koder</button>
                <button data-agent="integrator" onclick="izberiAgenta('integrator',this)">🧪 Integrator</button>
                <button data-agent="revizor" onclick="izberiAgenta('revizor',this)">📋 Revizor</button>
                <button data-agent="sistem" onclick="izberiAgenta('sistem',this)">⚙️ Sistem</button>
                <button data-agent="globalno" onclick="izberiAgenta('globalno',this)">🎨 Globalno</button>
                <button data-agent="uporabniki" onclick="izberiAgenta('uporabniki',this)">👤 Uporabniki</button>
                <button data-agent="moduli" onclick="izberiAgenta('moduli',this)">🧩 Moduli</button>
            </div>

            <div class="pogovor">
                <div class="pogovor-msgs" id="pogovor-msgs">
                    <div class="sistem">Agent pripravljen. Vprašaj kar koli.</div>
                </div>
                <div class="pogovor-vhod">
                    <input type="text" id="pogovor-input" placeholder="Vpiši vprašanje..." onkeydown="if(event.key==='Enter')poslji()">
                    <button onclick="poslji()">Pošlji</button>
                </div>
            </div>
        </div>

        <!-- DNEVNIK -->
        <div id="plosca-dnevnik" class="plosca">
            <div class="naslov">📜 Dnevnik</div>
            <div style="margin-bottom:10px;"><button class="gumb-akcija siv" onclick="naloziDnevnik()">↻ Osveži</button></div>
            <div class="terminal" id="dnevnik-vsebina" style="height:500px;">Nalagam...</div>
        </div>

    </div>
</div>

<script>
// ============================================================
// JAVASCRIPT
// ============================================================

let trenutniAgent = 'splosno';

function pokazi(id, gumb) {
    document.querySelectorAll('.plosca').forEach(p => p.classList.remove('aktivna'));
    document.querySelectorAll('.stranska .gumb').forEach(g => g.classList.remove('aktiven'));
    document.getElementById('plosca-' + id).classList.add('aktivna');
    if (gumb) gumb.classList.add('aktiven');
    if (id === 'pregled' || id === 'porocila') naloziPorocila();
    if (id === 'dnevnik') naloziDnevnik();
}

async function ajax(podatki) {
    const fd = new FormData();
    for (const [k,v] of Object.entries(podatki)) fd.append(k,v);
    const r = await fetch(location.href, {method:'POST',body:fd});
    return r.json();
}

// ============================================================
// POROČILA
// ============================================================
async function naloziPorocila() {
    const r = await ajax({akcija:'preberi_porocila'});
    if (!r.uspeh) return;
    const p = r.porocila;
    
    // Statistike
    const arh = p.arhitekt;
    if (arh) {
        const po = arh.po_vplivu || {};
        document.getElementById('s-kriticnih').textContent = po.kritičen || 0;
        document.getElementById('s-visokih').textContent = po.visok || 0;
        document.getElementById('s-skupaj').textContent = arh.skupaj_napak || 0;
    }
    const rev = p.revizor;
    if (rev) {
        document.getElementById('s-status').textContent = rev.skupna_ocena || '-';
    }
    
    // Pregled
    let html = '';
    const imena = {arhitekt:'🔍 Arhitekt', koder:'🔧 Koder', integrator:'🧪 Integrator', revizor:'📋 Revizor'};
    for (const [key,val] of Object.entries(imena)) {
        const pod = p[key];
        if (!pod) {
            html += `<div style="padding:5px 0;border-bottom:1px solid #2a2f42;">${val} <span style="color:#8890a8;font-size:12px;">ni poročila</span></div>`;
        } else {
            const napak = pod.skupaj_napak ?? pod.skupaj_popravkov ?? '';
            html += `<div style="padding:5px 0;border-bottom:1px solid #2a2f42;">${val} <span style="color:#8890a8;font-size:12px;">${pod.datum || ''}</span> <span style="color:#4f6ef7;">${napak}</span></div>`;
        }
    }
    document.getElementById('pregled-vsebina').innerHTML = html || 'Ni poročil.';
    
    // Poročila - podrobno
    let html2 = '';
    for (const [key,val] of Object.entries(p)) {
        html2 += `<div class="kartica"><strong>${key.toUpperCase()}</strong> <span style="color:#8890a8;font-size:12px;">${val.datum || ''}</span><pre style="background:#080b10;padding:10px;border-radius:6px;margin-top:10px;font-size:12px;overflow:auto;max-height:200px;color:#a8c4a8;">${JSON.stringify(val,null,2)}</pre></div>`;
    }
    document.getElementById('porocila-vsebina').innerHTML = html2 || 'Ni poročil.';
}

// ============================================================
// AGENTI
// ============================================================
async function zazeni(agent) {
    const terminal = document.getElementById('terminal-agenti');
    terminal.textContent = `▶ Zaganjam ${agent}...\n`;
    
    const r = await ajax({akcija:'zazeni_agenta', agent});
    if (r.uspeh) {
        terminal.textContent += r.izpis || '(brez izpisa)';
        terminal.textContent += `\n✅ ${agent} končal.`;
    } else {
        terminal.textContent += `\n❌ Napaka: ${r.napaka}`;
    }
    terminal.scrollTop = terminal.scrollHeight;
    naloziPorocila();
}

async function zazeniCikel() {
    const terminal = document.getElementById('terminal-agenti');
    terminal.textContent = '▶▶ Zaganjam cel cikel...\n';
    
    const r = await ajax({akcija:'zazeni_cikel'});
    if (r.uspeh) {
        for (const [agent, izpis] of Object.entries(r.rezultati)) {
            terminal.textContent += `\n── ${agent} ──\n${izpis || '(brez izpisa)'}`;
        }
        terminal.textContent += '\n✅ Cikel končan.';
    } else {
        terminal.textContent += `\n❌ Napaka: ${r.napaka}`;
    }
    terminal.scrollTop = terminal.scrollHeight;
    naloziPorocila();
}

// ============================================================
// POGOVOR
// ============================================================
function izberiAgenta(agent, gumb) {
    document.querySelectorAll('#izbira-agenta button').forEach(b => b.classList.remove('aktiven'));
    gumb.classList.add('aktiven');
    trenutniAgent = agent;
}

async function poslji() {
    const input = document.getElementById('pogovor-input');
    const msg = input.value.trim();
    if (!msg) return;
    
    const msgs = document.getElementById('pogovor-msgs');
    msgs.innerHTML += `<div class="lastnik">${msg}</div>`;
    input.value = '';
    msgs.scrollTop = msgs.scrollHeight;
    
    const r = await ajax({akcija:'pogovor', sporocilo:msg, agent_tip:trenutniAgent});
    if (r.uspeh) {
        msgs.innerHTML += `<div class="agent">${r.odgovor}</div>`;
    } else {
        msgs.innerHTML += `<div class="sistem">❌ ${r.napaka}</div>`;
    }
    msgs.scrollTop = msgs.scrollHeight;
}

// ============================================================
// DNEVNIK
// ============================================================
async function naloziDnevnik() {
    const r = await ajax({akcija:'preberi_dnevnik'});
    document.getElementById('dnevnik-vsebina').textContent = r.uspeh ? (r.vsebina || 'Prazen.') : '❌ ' + r.napaka;
}

// ============================================================
// ZAGON
// ============================================================
naloziPorocila();
</script>

</body>
</html>