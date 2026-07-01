<?php
/**
 * ============================================================
 * POT: GLOBALNO/postavitev/strani/admin/ai_agent_starter.php
 * 📅 VERZIJA: v1.0 (18.6.2026)
 * ============================================================
 *
 * 🏛️ NIVO: ASTRA (ti)
 *
 * 📰 NAMEN:
 *     Nadzorna plošča za AI agente. Zagon, pogovor, naloge.
 *     API ključi se berejo iz PODATKI/sef/.env_dipsi
 *
 * 👤 AVTOR: AI / DeepSeek
 * 🌐 JEZIK: sl
 * ============================================================
 */

declare(strict_types=1);

// ============================================================
// ROOT + POTE
// ============================================================
$ROOT = realpath(__DIR__ . '/..');
if ($ROOT === false) die('ROOT ni določljiv.');

define('POT_ROOT', $ROOT);
define('POT_AI', $ROOT . '/AI');
define('POT_POROCILA', $ROOT . '/AI/sistemskiAI/naloge/porocila');
define('POT_ENV', $ROOT . '/PODATKI/sef/.env_dipsi');
define('POT_AGENTI', $ROOT . '/AI');

// ============================================================
// API KLJUČI – BRANJE IN PISANJE
// ============================================================

function preberiKljuce(): array {
    if (!file_exists(POT_ENV)) return [];
    $vsebina = file_get_contents(POT_ENV);
    $kljuci = [];
    foreach (explode("\n", $vsebina) as $vrstica) {
        $vrstica = trim($vrstica);
        if (empty($vrstica) || strpos($vrstica, '=') === false) continue;
        [$ime, $vrednost] = explode('=', $vrstica, 2);
        $kljuci[trim($ime)] = trim($vrednost);
    }
    return $kljuci;
}

function zapisiKljuce(array $kljuci): void {
    $vsebina = '';
    foreach ($kljuci as $ime => $vrednost) {
        $vsebina .= $ime . '=' . $vrednost . "\n";
    }
    file_put_contents(POT_ENV, $vsebina);
}

// ============================================================
// SESIJA + ZAŠČITA
// ============================================================
if (session_status() === PHP_SESSION_NONE) {
    session_name('ASTRA_SID');
    session_start();
}

// Poenostavljena zaščita – samo ti
$dovoljen = $_SESSION['vloga_int'] ?? 0;
if ($dovoljen < 60) {
    // Če ni prijavljen, pokaži preprosto prijavo
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['geslo'])) {
        $hash = password_hash('astra2026', PASSWORD_DEFAULT);
        if (password_verify($_POST['geslo'], $hash)) {
            $_SESSION['vloga_int'] = 60;
            $_SESSION['uporabnik_id'] = 1;
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit;
        }
        $napaka = 'Napačno geslo.';
    }
    ?>
    <!DOCTYPE html>
    <html>
    <head><meta charset="UTF-8"><title>ASTRA – AI Starter</title>
    <style>body{background:#0a0a1a;color:#d4c5a9;display:flex;align-items:center;justify-content:center;height:100vh;font-family:monospace;}
    .box{background:#151820;padding:40px;border-radius:12px;border:1px solid #2a2f42;max-width:360px;width:100%;}
    input{width:100%;padding:10px;background:#0a0e18;border:1px solid #2a2f42;color:#d4c5a9;border-radius:6px;margin:10px 0;}
    button{width:100%;padding:10px;background:#4f6ef7;border:none;border-radius:6px;color:#fff;font-weight:bold;cursor:pointer;}
    .error{color:#e74c3c;font-size:13px;}</style>
    </head>
    <body>
    <div class="box">
        <h2 style="color:#e8c84a;">🔮 ASTRA AI</h2>
        <p style="color:#8890a8;font-size:14px;">Vpiši geslo za dostop</p>
        <?php if (isset($napaka)) echo '<p class="error">'.$napaka.'</p>'; ?>
        <form method="POST">
            <input type="password" name="geslo" placeholder="Geslo" autofocus>
            <button type="submit">Vstopi</button>
        </form>
    </div>
    </body>
    </html>
    <?php
    exit;
}

// ============================================================
// AJAX AKCIJE
// ============================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['akcija'])) {
    header('Content-Type: application/json; charset=utf-8');
    $akcija = $_POST['akcija'];

    try {
        switch ($akcija) {

            // ---- 1. ZAŽENI AGENTA ----
            case 'zazeni_agenta':
                $agent = $_POST['agent'] ?? '';

                // Popravljeno: poti so AI/sistemskiAI/arhitekturniAI/ in AI/sistemskiAI/strukturniAI/
                $dovoljeni = [
                    // Osnovni DeepSeek agenti (arhitekturniAI/)
                    'arhitekt'   => 'sistemskiAI/arhitekturniAI/deepseek_arhitekt.php',
                    'koder'      => 'sistemskiAI/arhitekturniAI/deepseek_koder.php',
                    'integrator' => 'sistemskiAI/arhitekturniAI/deepseek_integrator.php',
                    'revizor'    => 'sistemskiAI/arhitekturniAI/deepseek_revizor.php',
                    // Strukturni agenti (strukturniAI/)
                    'arhitekt_sistem'     => 'sistemskiAI/strukturniAI/arhitekt_sistem.php',
                    'arhitekt_globalno'   => 'sistemskiAI/strukturniAI/arhitekt_globalno.php',
                    'arhitekt_uporabniki' => 'sistemskiAI/strukturniAI/arhitekt_uporabniki.php',
                    'arhitekt_podatki'    => 'sistemskiAI/strukturniAI/arhitekt_podatki.php',
                    'arhitekt_vsebina'    => 'sistemskiAI/strukturniAI/arhitekt_vsebina.php',
                    'arhitekt_moduli'     => 'sistemskiAI/strukturniAI/arhitekt_moduli.php',
                ];

                if (!isset($dovoljeni[$agent])) throw new Exception("Neznan agent: $agent");
                $datoteka = POT_AI . '/' . $dovoljeni[$agent];
                if (!file_exists($datoteka)) throw new Exception("Agent ni nameščen: " . $dovoljeni[$agent]);

                ob_start();
                include $datoteka;
                $izpis = ob_get_clean();
                echo json_encode(['uspeh' => true, 'izpis' => $izpis]);
                break;

            // ---- 2. ZAŽENI CEL CIKEL (do konca) ----
            case 'zazeni_cikel':
                $agenti = ['arhitekt', 'koder', 'integrator', 'revizor'];
                $rezultati = [];
                $poti = [
                    'arhitekt'   => 'sistemskiAI/arhitekturniAI/deepseek_arhitekt.php',
                    'koder'      => 'sistemskiAI/arhitekturniAI/deepseek_koder.php',
                    'integrator' => 'sistemskiAI/arhitekturniAI/deepseek_integrator.php',
                    'revizor'    => 'sistemskiAI/arhitekturniAI/deepseek_revizor.php',
                ];
                foreach ($agenti as $a) {
                    $datoteka = POT_AI . '/' . $poti[$a];
                    if (!file_exists($datoteka)) {
                        $rezultati[$a] = '❌ Agent ni nameščen.';
                        continue;
                    }
                    ob_start();
                    include $datoteka;
                    $rezultati[$a] = ob_get_clean();
                }
                echo json_encode(['uspeh' => true, 'rezultati' => $rezultati]);
                break;

            // ---- 3. POGOVOR Z AGENTOM ----
            case 'pogovor':
                $sporocilo = trim($_POST['sporocilo'] ?? '');
                if (empty($sporocilo)) throw new Exception("Sporočilo je prazno.");

                $kljuci = preberiKljuce();
                $apiKljuc = $kljuci['DEEPSEEK_API_KEY'] ?? '';

                $odgovor = pokliciDeepSeek($sporocilo, $apiKljuc);
                echo json_encode(['uspeh' => true, 'odgovor' => $odgovor]);
                break;

            // ---- 4. DODAJ API KLJUČ ----
            case 'dodaj_kljuc':
                $ime = trim($_POST['ime'] ?? '');
                $vrednost = trim($_POST['vrednost'] ?? '');
                if (empty($ime) || empty($vrednost)) throw new Exception("Ime in vrednost sta obvezna.");
                $kljuci = preberiKljuce();
                $kljuci[$ime] = $vrednost;
                zapisiKljuce($kljuci);
                echo json_encode(['uspeh' => true, 'sporocilo' => "Ključ $ime shranjen."]);
                break;

            // ---- 5. IZBRIŠI API KLJUČ ----
            case 'izbrisi_kljuc':
                $ime = trim($_POST['ime'] ?? '');
                if (empty($ime)) throw new Exception("Ime je obvezno.");
                $kljuci = preberiKljuce();
                unset($kljuci[$ime]);
                zapisiKljuce($kljuci);
                echo json_encode(['uspeh' => true, 'sporocilo' => "Ključ $ime izbrisan."]);
                break;

            // ---- 6. PREBERI POROČILA ----
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

            default:
                throw new Exception("Neznana akcija.");
        }
    } catch (Exception $e) {
        echo json_encode(['uspeh' => false, 'napaka' => $e->getMessage()]);
    }
    exit;
}

// ============================================================
// POMOŽNE FUNKCIJE
// ============================================================

function pokliciDeepSeek(string $sporocilo, string $apiKljuc): string {
    if (empty($apiKljuc)) {
        return "⚠️ API ključ manjka. Dodaj ga v `PODATKI/sef/.env_dipsi` kot `DEEPSEEK_API_KEY=tvoj_kljuc`";
    }

    $sistemski = "Si AI asistent za AstraMentalico. Pomagaš lastniku sistema. Odgovarjaj jedrnato, v slovenščini. Poznaš arhitekturo: ADAPTER, SISTEM, GLOBALNO, UPORABNIKI, MODULI, PODATKI, VSEBINA, AI, ASTRA.";

    $ch = curl_init('https://api.deepseek.com/v1/chat/completions');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $apiKljuc,
        ],
        CURLOPT_POSTFIELDS => json_encode([
            'model' => 'deepseek-chat',
            'messages' => [
                ['role' => 'system', 'content' => $sistemski],
                ['role' => 'user', 'content' => $sporocilo],
            ],
            'max_tokens' => 1024,
            'temperature' => 0.7,
        ]),
        CURLOPT_TIMEOUT => 30,
    ]);

    $odziv = curl_exec($ch);
    $napaka = curl_error($ch);
    curl_close($ch);

    if ($napaka) return "❌ Napaka povezave: $napaka";

    $json = json_decode($odziv, true);
    return $json['choices'][0]['message']['content'] ?? "❌ Ni odgovora.";
}

// ============================================================
// HTML – VSEBNINA
// ============================================================
$kljuci = preberiKljuce();
$imaKljuc = isset($kljuci['DEEPSEEK_API_KEY']);
?>
<!DOCTYPE html>
<html lang="sl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>ASTRA – AI Agent Starter</title>
<style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
        background: #0a0a1a;
        color: #d4c5a9;
        font-family: 'Courier New', monospace;
        padding: 20px;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        gap: 16px;
    }
    .container { max-width: 1200px; margin: 0 auto; width: 100%; }

    /* Header */
    .header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 16px 20px;
        background: #0f1420;
        border: 1px solid #1a2535;
        border-radius: 10px;
    }
    .header h1 { color: #e8c84a; font-size: 20px; font-weight: 400; }
    .header h1 span { color: #4f6ef7; }
    .header .status { font-size: 12px; color: #4a6080; }

    /* Grid */
    .grid {
        display: grid;
        grid-template-columns: 320px 1fr;
        gap: 16px;
    }

    /* Cards */
    .card {
        background: #0f1420;
        border: 1px solid #1a2535;
        border-radius: 10px;
        padding: 16px;
    }
    .card-title {
        color: #8890a8;
        font-size: 10px;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 12px;
    }

    /* Agent list */
    .agent-btn {
        display: flex;
        align-items: center;
        gap: 10px;
        width: 100%;
        padding: 10px 12px;
        background: transparent;
        border: 1px solid #1a2535;
        border-radius: 6px;
        color: #d4c5a9;
        cursor: pointer;
        margin-bottom: 6px;
        transition: 0.2s;
        font-family: inherit;
        font-size: 13px;
    }
    .agent-btn:hover {
        background: rgba(79, 110, 247, 0.1);
        border-color: #4f6ef7;
    }
    .agent-btn .icon { font-size: 18px; }
    .agent-btn .badge {
        margin-left: auto;
        font-size: 10px;
        color: #4a6080;
    }

    /* Buttons */
    .btn-primary {
        background: #4f6ef7;
        border: none;
        color: #fff;
        padding: 10px 20px;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 600;
        font-family: inherit;
        transition: 0.2s;
        width: 100%;
    }
    .btn-primary:hover { background: #7b93ff; }
    .btn-primary:disabled { opacity: 0.5; cursor: not-allowed; }

    .btn-secondary {
        background: transparent;
        border: 1px solid #1a2535;
        color: #d4c5a9;
        padding: 8px 16px;
        border-radius: 6px;
        cursor: pointer;
        font-family: inherit;
        transition: 0.2s;
    }
    .btn-secondary:hover { border-color: #4f6ef7; }

    /* Terminal */
    .terminal {
        background: #05080f;
        border: 1px solid #1a2535;
        border-radius: 6px;
        padding: 12px;
        font-size: 11px;
        font-family: 'Courier New', monospace;
        color: #a8c4a8;
        max-height: 300px;
        overflow-y: auto;
        white-space: pre-wrap;
        margin-top: 10px;
    }

    /* Chat */
    .chat-box {
        height: 200px;
        overflow-y: auto;
        border: 1px solid #1a2535;
        border-radius: 6px;
        padding: 10px;
        margin-bottom: 10px;
        background: #05080f;
    }
    .chat-msg { margin-bottom: 6px; font-size: 13px; }
    .chat-msg.user { color: #e8c84a; }
    .chat-msg.agent { color: #4f6ef7; }

    .chat-input-row {
        display: flex;
        gap: 8px;
    }
    .chat-input-row input {
        flex: 1;
        background: #0a0e18;
        border: 1px solid #1a2535;
        border-radius: 6px;
        padding: 8px 12px;
        color: #d4c5a9;
        font-family: inherit;
        outline: none;
    }
    .chat-input-row input:focus { border-color: #4f6ef7; }

    /* API keys */
    .key-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 6px 0;
        border-bottom: 1px solid #1a2535;
        font-size: 12px;
    }
    .key-row .name { color: #e8c84a; }
    .key-row .value { color: #4a6080; font-size: 11px; }
    .key-row .del { color: #e74c3c; cursor: pointer; }
    .key-row .del:hover { color: #ff6b6b; }

    .add-key-row {
        display: flex;
        gap: 6px;
        margin-top: 8px;
    }
    .add-key-row input {
        flex: 1;
        background: #0a0e18;
        border: 1px solid #1a2535;
        border-radius: 4px;
        padding: 6px 10px;
        color: #d4c5a9;
        font-family: inherit;
        font-size: 12px;
        outline: none;
    }
    .add-key-row input:focus { border-color: #4f6ef7; }
    .add-key-row button { background: #4f6ef7; border: none; color: #fff; padding: 6px 12px; border-radius: 4px; cursor: pointer; }

    /* Responsive */
    @media (max-width: 768px) {
        .grid { grid-template-columns: 1fr; }
    }
</style>
</head>
<body>

<div class="container">

    <!-- Header -->
    <div class="header">
        <h1>🔮 ASTRA <span>AI</span> Starter</h1>
        <div class="status">
            <?php if ($imaKljuc): ?>
                <span style="color:#2ecc71;">●</span> API ključ aktiven
            <?php else: ?>
                <span style="color:#e74c3c;">●</span> API ključ manjka
            <?php endif; ?>
        </div>
    </div>

    <div class="grid">

        <!-- LEFT: Agenti + Ključi -->
        <div>

            <!-- Agenti -->
            <div class="card">
                <div class="card-title">🤖 Agenti</div>
                <button class="agent-btn" onclick="zazeniAgenta('arhitekt')">
                    <span class="icon">🔍</span> Arhitekt
                    <span class="badge">pregled</span>
                </button>
                <button class="agent-btn" onclick="zazeniAgenta('koder')">
                    <span class="icon">🔧</span> Koder
                    <span class="badge">popravi</span>
                </button>
                <button class="agent-btn" onclick="zazeniAgenta('integrator')">
                    <span class="icon">🧪</span> Integrator
                    <span class="badge">test</span>
                </button>
                <button class="agent-btn" onclick="zazeniAgenta('revizor')">
                    <span class="icon">📋</span> Revizor
                    <span class="badge">oceni</span>
                </button>
                <div style="margin-top:10px;">
                    <button class="btn-primary" onclick="zazeniCikel()">▶▶ Zaženi CEL CIKEL</button>
                </div>
            </div>
<div style="margin-top:12px; border-top:1px solid #1a2535; padding-top:12px;">
    <div class="card-title">📁 Specifični arhitekti</div>
    <button class="agent-btn" onclick="zazeniAgenta('arhitekt_sistem')">
        <span class="icon">⚙️</span> Sistem
        <span class="badge">SISTEM/</span>
    </button>
    <button class="agent-btn" onclick="zazeniAgenta('arhitekt_globalno')">
        <span class="icon">🎨</span> Globalno
        <span class="badge">GLOBALNO/</span>
    </button>
    <button class="agent-btn" onclick="zazeniAgenta('arhitekt_uporabniki')">
        <span class="icon">👤</span> Uporabniki
        <span class="badge">UPORABNIKI/</span>
    </button>
    <button class="agent-btn" onclick="zazeniAgenta('arhitekt_podatki')">
        <span class="icon">💾</span> Podatki
        <span class="badge">PODATKI/</span>
    </button>
    <button class="agent-btn" onclick="zazeniAgenta('arhitekt_vsebina')">
        <span class="icon">📄</span> Vsebina
        <span class="badge">VSEBINA/</span>
    </button>
    <button class="agent-btn" onclick="zazeniAgenta('arhitekt_moduli')">
        <span class="icon">🧩</span> Moduli
        <span class="badge">MODULI/</span>
    </button>
</div>
            <!-- API Ključi -->
            <div class="card" style="margin-top:12px;">
                <div class="card-title">🔑 API Ključi (PODATKI/sef/.env_dipsi)</div>
                <div id="key-list">
                    <?php foreach ($kljuci as $ime => $vrednost): ?>
                    <div class="key-row">
                        <span class="name"><?= htmlspecialchars($ime) ?></span>
                        <span class="value"><?= substr($vrednost, 0, 12) . '...' ?></span>
                        <span class="del" onclick="izbrisiKljuc('<?= htmlspecialchars($ime) ?>')">🗑</span>
                    </div>
                    <?php endforeach; ?>
                    <?php if (empty($kljuci)): ?>
                    <div style="color:#4a6080;font-size:12px;">Ni shranjenih ključev.</div>
                    <?php endif; ?>
                </div>
                <div class="add-key-row">
                    <input type="text" id="key-name" placeholder="IME (npr. DEEPSEEK_API_KEY)" style="flex:1.5;">
                    <input type="text" id="key-value" placeholder="vrednost" style="flex:2;">
                    <button onclick="dodajKljuc()">+</button>
                </div>
            </div>

        </div>

        <!-- RIGHT: Terminal + Chat -->
        <div>

            <!-- Terminal -->
            <div class="card">
                <div class="card-title">📟 Terminal</div>
                <div class="terminal" id="terminal">Pripravljen. Klikni agenta za zagon.</div>
            </div>

            <!-- Pogovor -->
            <div class="card" style="margin-top:12px;">
                <div class="card-title">💬 Pogovor z agentom</div>
                <div class="chat-box" id="chat-box">
                    <div class="chat-msg agent">👋 Agent pripravljen. Vprašaj kar koli.</div>
                </div>
                <div class="chat-input-row">
                    <input type="text" id="chat-input" placeholder="Napiši sporočilo..." onkeydown="if(event.key==='Enter') posljiSporocilo();">
                    <button class="btn-secondary" onclick="posljiSporocilo()">Pošlji</button>
                </div>
            </div>

        </div>

    </div>

</div>

<script>
// ============================================================
// AJAX POMOŽNE
// ============================================================
async function api(akcija, podatki = {}) {
    const fd = new FormData();
    fd.append('akcija', akcija);
    for (const [k, v] of Object.entries(podatki)) fd.append(k, v);
    const r = await fetch(location.href, { method: 'POST', body: fd });
    return r.json();
}

function terminal(msg, append = true) {
    const el = document.getElementById('terminal');
    if (append) {
        el.textContent += msg + '\n';
    } else {
        el.textContent = msg + '\n';
    }
    el.scrollTop = el.scrollHeight;
}

// ============================================================
// AGENTI
// ============================================================
async function zazeniAgenta(agent) {
    terminal('▶ Zaganjam ' + agent + '...');
    const r = await api('zazeni_agenta', { agent });
    if (r.uspeh) {
        terminal(r.izpis || '(brez izpisa)');
        terminal('✅ ' + agent + ' končal.');
    } else {
        terminal('❌ Napaka: ' + r.napaka);
    }
}

async function zazeniCikel() {
    terminal('▶▶ Zaganjam CEL CIKEL...');
    const r = await api('zazeni_cikel');
    if (r.uspeh) {
        for (const [agent, izpis] of Object.entries(r.rezultati)) {
            terminal('── ' + agent + ' ──');
            terminal(izpis || '(brez izpisa)');
        }
        terminal('✅ Cel cikel končan.');
    } else {
        terminal('❌ Napaka: ' + r.napaka);
    }
}

// ============================================================
// POGOVOR
// ============================================================
async function posljiSporocilo() {
    const input = document.getElementById('chat-input');
    const msg = input.value.trim();
    if (!msg) return;

    const box = document.getElementById('chat-box');
    box.innerHTML += '<div class="chat-msg user">🧑 ' + msg + '</div>';
    input.value = '';
    box.scrollTop = box.scrollHeight;

    const r = await api('pogovor', { sporocilo: msg });
    if (r.uspeh) {
        box.innerHTML += '<div class="chat-msg agent">🤖 ' + r.odgovor + '</div>';
    } else {
        box.innerHTML += '<div class="chat-msg agent">❌ ' + r.napaka + '</div>';
    }
    box.scrollTop = box.scrollHeight;
}

// ============================================================
// API KLJUČI
// ============================================================
async function dodajKljuc() {
    const name = document.getElementById('key-name').value.trim();
    const value = document.getElementById('key-value').value.trim();
    if (!name || !value) return alert('Vpiši ime in vrednost.');

    const r = await api('dodaj_kljuc', { ime: name, vrednost: value });
    if (r.uspeh) {
        location.reload();
    } else {
        alert(r.napaka);
    }
}

async function izbrisiKljuc(ime) {
    if (!confirm('Izbriši ključ ' + ime + '?')) return;
    const r = await api('izbrisi_kljuc', { ime });
    if (r.uspeh) location.reload();
    else alert(r.napaka);
}

// ============================================================
// POROČILA (samodejno)
// ============================================================
async function naloziPorocila() {
    const r = await api('preberi_porocila');
    if (r.uspeh) {
        const p = r.porocila;
        const st = Object.keys(p).length;
        if (st > 0) terminal('📄 ' + st + ' poročil na voljo.', false);
    }
}
naloziPorocila();
</script>

</body>
</html>