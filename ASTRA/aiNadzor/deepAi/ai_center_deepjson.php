<?php
/**
 * DeepSeek AI Center - JSON verzija (brez baze!)
 */

require_once '../includes/ai_deepseek_json.php';

$ai = new DeepSeekAI_JSON();
$sporocilo = '';
$napaka = '';
$rezultat = null;

// Obdelava POST zahtevkov
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $akcija = $_POST['akcija'] ?? '';
    
    // 1. Navaden pogovor
    if ($akcija === 'pogovor' && isset($_POST['prompt'])) {
        $result = $ai->generiraj(
            $_POST['prompt'], 
            $_POST['agent_id'] ?? 'bloger',
            $_POST['api_key_id'] ?? null
        );
        
        if ($result['success']) {
            $sporocilo = 'Odgovor generiran z ' . $result['api_key'];
            $rezultat = $result['content'];
        } else {
            $napaka = $result['error'];
        }
    }
    
    // 2. Pogovor med agentoma
    elseif ($akcija === 'pogovor_med_agenti') {
        $result = $ai->pogovorMedAgenti(
            $_POST['agent1'],
            $_POST['agent2'],
            $_POST['tema'],
            (int)$_POST['izmjen'],
            $_POST['api_key_id'] ?? null
        );
        
        if ($result['success']) {
            $sporocilo = 'Pogovor med agentoma končan. Shranjen kot: ' . $result['pogovor_id'];
            $rezultat = $result;
        } else {
            $napaka = $result['error'];
        }
    }
    
    // 3. Skupinsko reševanje
    elseif ($akcija === 'skupinsko') {
        $agenti = explode(',', $_POST['agenti']);
        $rezultat = $ai->skupinskoResevanje($_POST['problem'], $agenti, $_POST['api_key_id'] ?? null);
        $sporocilo = 'Skupinsko reševanje končano';
    }
    
    // 4. Dodajanje API ključa
    elseif ($akcija === 'dodaj_api') {
        if ($ai->addApiKey($_POST['ime'], $_POST['api_key'], $_POST['model'])) {
            $sporocilo = 'API ključ dodan';
        } else {
            $napaka = 'Napaka pri dodajanju';
        }
    }
    
    // 5. Brisanje zgodovine
    elseif ($akcija === 'izbrisi_zgodovino' && isset($_POST['session_id'])) {
        if ($ai->deleteSession($_POST['session_id'])) {
            $sporocilo = 'Zgodovina izbrisana';
        }
    }
    
    // 6. Brisanje pogovora
    elseif ($akcija === 'izbrisi_pogovor' && isset($_POST['pogovor_id'])) {
        if ($ai->deleteConversation($_POST['pogovor_id'])) {
            $sporocilo = 'Pogovor izbrisan';
        }
    }
    
    // 7. Izvoz vseh podatkov
    elseif ($akcija === 'izvozi') {
        $file = $ai->exportAllData();
        $sporocilo = 'Podatki izvoženi v: ' . basename($file);
    }
}

// Pridobi podatke za prikaz
$agenti = $ai->getAllAgents();
$apiKljuci = $ai->getApiStats();
$zgodovina = $ai->getHistory(null, null, 50);
$sesije = $ai->getAllSessions();
$pogovori = $ai->getSavedConversations();
?>
<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <title>🤖 DeepSeek AI Center – JSON Edition</title>
    <style>
        * { box-sizing: border-box; }
        body { 
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f5f7fb;
            margin: 0;
            padding: 20px;
        }
        .container { max-width: 1400px; margin: 0 auto; }
        
        .header { margin-bottom: 30px; }
        .header h1 { margin: 0; color: #1a1a2e; }
        .header p { color: #666; margin-top: 5px; }
        
        .grid-2 { display: grid; grid-template-columns: repeat(auto-fit, minmax(450px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .grid-3 { display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 20px; }
        
        .card {
            background: white;
            border-radius: 16px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            border: 1px solid #e0e0e0;
        }
        .card h2 { margin: 0 0 15px 0; font-size: 1.3rem; display: flex; align-items: center; gap: 8px; }
        .card h3 { margin: 0 0 10px 0; font-size: 1rem; color: #555; }
        
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: 600; font-size: 0.85rem; color: #333; }
        .form-group select, .form-group input, .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 10px;
            font-family: inherit;
        }
        .form-group textarea { resize: vertical; }
        
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.2s;
        }
        .btn-primary { background: #0066cc; color: white; }
        .btn-primary:hover { background: #0052a3; transform: translateY(-1px); }
        .btn-success { background: #10b981; color: white; }
        .btn-danger { background: #dc2626; color: white; }
        .btn-warning { background: #f59e0b; color: white; }
        .btn-sm { padding: 5px 12px; font-size: 0.8rem; }
        
        .result-box {
            background: #1a1a2e;
            color: #e2e8f0;
            padding: 15px;
            border-radius: 12px;
            font-family: monospace;
            white-space: pre-wrap;
            max-height: 400px;
            overflow-y: auto;
            font-size: 0.85rem;
        }
        
        .history-item {
            border-bottom: 1px solid #eee;
            padding: 10px;
            margin-bottom: 10px;
        }
        .history-message { margin-left: 20px; padding: 8px; border-radius: 8px; }
        .history-user { background: #e3f2fd; }
        .history-assistant { background: #f5f5f5; }
        .history-agent { font-size: 0.7rem; color: #666; margin-bottom: 4px; }
        
        .conversation-item {
            background: #f8f9fa;
            padding: 12px;
            margin-bottom: 10px;
            border-radius: 10px;
            border-left: 3px solid #10b981;
        }
        
        .obvestilo {
            padding: 12px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .obvestilo-uspesno { background: #d4edda; border-left: 4px solid #10b981; color: #155724; }
        .obvestilo-napaka { background: #f8d7da; border-left: 4px solid #dc2626; color: #721c24; }
        
        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
        }
        .badge-active { background: #10b981; color: white; }
        .badge-inactive { background: #dc2626; color: white; }
        
        .json-info {
            background: #eef2ff;
            padding: 10px;
            border-radius: 8px;
            font-size: 0.8rem;
            margin-bottom: 15px;
        }
        
        @media (max-width: 768px) {
            .grid-2, .grid-3 { grid-template-columns: 1fr; }
            body { padding: 10px; }
        }
    </style>
</head>
<body>
<div class="container">
    
    <div class="header">
        <h1>🤖 DeepSeek AI Center <span style="font-size: 0.8rem; background: #10b981; color: white; padding: 2px 10px; border-radius: 20px;">JSON Edition</span></h1>
        <p>📁 Vse shranjeno v JSON datoteke | Brez baze | Enostavno za backup | Multi-Agent | Več API ključev</p>
    </div>
    
    <div class="json-info">
        💾 Podatki se shranjujejo v: <code><?= PODATKI_PATH ?>/ai/</code><br>
        📁 Zgodovina: <code>zgodovina/[session_id].json</code> | 💬 Pogovori med agenti: <code>pogovori/*.json</code> | 🔑 API ključi: <code>api_kljuci/keys.json</code>
    </div>
    
    <?php if ($sporocilo): ?>
    <div class="obvestilo obvestilo-uspesno">✅ <?= htmlspecialchars($sporocilo) ?></div>
    <?php endif; ?>
    <?php if ($napaka): ?>
    <div class="obvestilo obvestilo-napaka">❌ <?= htmlspecialchars($napaka) ?></div>
    <?php endif; ?>
    
    <!-- GLAVNI AGENTI -->
    <div class="grid-2">
        
        <!-- Kartica 1: Pogovor z agentom -->
        <div class="card">
            <h2>💬 Pogovor z AI agentom</h2>
            <form method="post">
                <input type="hidden" name="akcija" value="pogovor">
                <div class="form-group">
                    <label>Izberi agenta</label>
                    <select name="agent_id">
                        <?php foreach ($agenti as $id => $a): ?>
                            <?php if ($a['aktivno']): ?>
                            <option value="<?= $id ?>"><?= $a['ime'] ?> - <?= $a['opis'] ?></option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>API ključ</label>
                    <select name="api_key_id">
                        <option value="">-- Privzeti --</option>
                        <?php foreach ($apiKljuci as $k): ?>
                            <option value="<?= $k['id'] ?>"><?= $k['ime'] ?> (<?= $k['model'] ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Tvoje vprašanje / prompt</label>
                    <textarea name="prompt" rows="4" placeholder="Npr. Napiši kratek blog članek o AI..." required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">✍️ Pošlji</button>
            </form>
        </div>
        
        <!-- Kartica 2: Pogovor med agentoma -->
        <div class="card">
            <h2>🎭 Pogovor med agentoma</h2>
            <form method="post">
                <input type="hidden" name="akcija" value="pogovor_med_agenti">
                <div class="form-group">
                    <label>Agent 1</label>
                    <select name="agent1">
                        <?php foreach ($agenti as $id => $a): ?>
                            <?php if ($a['aktivno']): ?>
                            <option value="<?= $id ?>"><?= $a['ime'] ?></option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Agent 2</label>
                    <select name="agent2">
                        <?php foreach ($agenti as $id => $a): ?>
                            <?php if ($a['aktivno']): ?>
                            <option value="<?= $id ?>"><?= $a['ime'] ?></option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Tema pogovora</label>
                    <input type="text" name="tema" placeholder="Kaj naj se pogovarjata?" required>
                </div>
                <div class="form-group">
                    <label>Število izmenjav</label>
                    <select name="izmjen">
                        <option value="2">2 izmenjavi (vsak enkrat)</option>
                        <option value="4">4 izmenjave</option>
                        <option value="6">6 izmenjav</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-success">🎭 Začni pogovor</button>
            </form>
        </div>
        
    </div>
    
    <!-- SKUPINSKO REŠEVANJE + API KLJUČI -->
    <div class="grid-2">
        
        <div class="card">
            <h2>👥 Skupinsko reševanje problema</h2>
            <form method="post">
                <input type="hidden" name="akcija" value="skupinsko">
                <div class="form-group">
                    <label>Problem / vprašanje</label>
                    <textarea name="problem" rows="3" placeholder="Kaj želiš rešiti?" required></textarea>
                </div>
                <div class="form-group">
                    <label>Agenti (loči z vejico)</label>
                    <input type="text" name="agenti" value="bloger,analitik,kreativec" placeholder="bloger,analitik,kreativec">
                </div>
                <button type="submit" class="btn btn-primary">👥 Reši skupinsko</button>
            </form>
        </div>
        
        <div class="card">
            <h2>🔑 API ključi (DeepSeek)</h2>
            <h3>Obstoječi ključi</h3>
            <?php foreach ($apiKljuci as $k): ?>
                <div style="background: #f5f5f5; padding: 8px; margin-bottom: 8px; border-radius: 8px; display: flex; justify-content: space-between;">
                    <div>
                        <strong><?= htmlspecialchars($k['ime']) ?></strong><br>
                        <small><?= $k['model'] ?> | Poraba: <?= number_format($k['porabljeno_danes']) ?> tokenov</small>
                    </div>
                    <span class="badge <?= $k['aktivno'] ? 'badge-active' : 'badge-inactive' ?>">
                        <?= $k['aktivno'] ? 'Aktiven' : 'Neaktiven' ?>
                    </span>
                </div>
            <?php endforeach; ?>
            
            <h3 style="margin-top: 20px;">➕ Dodaj nov DeepSeek ključ</h3>
            <form method="post">
                <input type="hidden" name="akcija" value="dodaj_api">
                <div class="form-group">
                    <input type="text" name="ime" placeholder="Ime (npr. DeepSeek Glavni)" required>
                </div>
                <div class="form-group">
                    <input type="text" name="api_key" placeholder="DeepSeek API ključ" required>
                </div>
                <div class="form-group">
                    <select name="model">
                        <option value="deepseek-chat">deepseek-chat</option>
                        <option value="deepseek-coder">deepseek-coder</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary btn-sm">➕ Dodaj ključ</button>
            </form>
        </div>
        
    </div>
    
    <!-- REZULTATI -->
    <?php if ($rezultat): ?>
    <div class="card" style="margin-bottom: 30px;">
        <h2>📄 Rezultat</h2>
        
        <?php if (is_string($rezultat)): ?>
            <div class="result-box"><?= nl2br(htmlspecialchars($rezultat)) ?></div>
            
        <?php elseif (isset($rezultat['zgodovina'])): ?>
            <h3>💬 Pogovor med agentoma</h3>
            <div style="background: #f0f0f0; padding: 15px; border-radius: 12px;">
                <p><strong>Tema:</strong> <?= htmlspecialchars($rezultat['tema']) ?></p>
                <p><strong>ID pogovora:</strong> <code><?= $rezultat['pogovor_id'] ?></code></p>
                <?php foreach ($rezultat['zgodovina'] as $izmenjava): ?>
                    <div style="margin: 15px 0; padding: 10px; background: white; border-radius: 10px;">
                        <span class="badge" style="background: #0066cc; color: white;"><?= htmlspecialchars($izmenjava['ime_agenta']) ?></span>
                        <div style="margin-top: 8px;"><?= nl2br(htmlspecialchars($izmenjava['odgovor'])) ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
            
        <?php elseif (is_array($rezultat)): ?>
            <h3>👥 Skupinski odgovori</h3>
            <?php foreach ($rezultat as $key => $r): ?>
                <?php if ($key === 'zdruzeno'): ?>
                    <div style="margin-top: 20px; padding: 15px; background: #1a1a2e; color: white; border-radius: 12px;">
                        <strong>📋 Združeno mnenje:</strong>
                        <div style="margin-top: 10px;"><?= nl2br(htmlspecialchars($r)) ?></div>
                    </div>
                <?php elseif (is_array($r) && isset($r['ime'])): ?>
                    <div style="margin: 10px 0; padding: 10px; background: #f5f5f5; border-radius: 8px;">
                        <strong><?= htmlspecialchars($r['ime']) ?>:</strong>
                        <div><?= nl2br(htmlspecialchars($r['odgovor'])) ?></div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <?php endif; ?>
    
    <!-- SHRANJENI POGOVORI MED AGENTI -->
    <?php if (!empty($pogovori)): ?>
    <div class="card" style="margin-bottom: 30px;">
        <h2>💾 Shranjeni pogovori med agenti</h2>
        <div class="grid-3">
            <?php foreach (array_slice($pogovori, 0, 6) as $p): ?>
                <div class="conversation-item">
                    <div style="display: flex; justify-content: space-between;">
                        <strong>📌 <?= htmlspecialchars(substr($p['tema'], 0, 50)) ?>...</strong>
                        <form method="post" style="display: inline;">
                            <input type="hidden" name="akcija" value="izbrisi_pogovor">
                            <input type="hidden" name="pogovor_id" value="<?= $p['id'] ?>">
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Izbriši pogovor?')">🗑️</button>
                        </form>
                    </div>
                    <small>Agenti: <?= implode(' vs ', $p['agenti']) ?> | <?= count($p['zgodovina']) ?> izmenjav</small>
                    <div style="font-size: 0.75rem; color: #666; margin-top: 5px;"><?= $p['created_at'] ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- ZGODOVINA POGOVOROV -->
    <div class="card">
        <h2>📜 Zgodovina pogovorov</h2>
        
        <div style="display: flex; gap: 10px; margin-bottom: 20px; flex-wrap: wrap; justify-content: space-between;">
            <div>
                <?php foreach ($sesije as $s): ?>
                    <form method="post" style="display: inline;">
                        <input type="hidden" name="akcija" value="izbrisi_zgodovino">
                        <input type="hidden" name="session_id" value="<?= htmlspecialchars($s['session_id']) ?>">
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Izbriši zgodovino te seje?')">
                            🗑️ <?= substr($s['session_id'], 0, 20) ?>... (<?= $s['sporocil'] ?> sporočil)
                        </button>
                    </form>
                <?php endforeach; ?>
            </div>
            <div>
                <form method="post">
                    <input type="hidden" name="akcija" value="izvozi">
                    <button type="submit" class="btn btn-warning btn-sm">📥 Izvozi vse podatke (JSON)</button>
                </form>
            </div>
        </div>
        
        <?php if (empty($zgodovina)): ?>
            <p>Ni zgodovine. Začni pogovor z agentom.</p>
        <?php else: ?>
            <?php foreach (array_slice($zgodovina, -20) as $msg): ?>
                <div class="history-item">
                    <div class="history-agent">
                        <strong>Agent:</strong> <?= htmlspecialchars($msg['agent_id']) ?> | 
                        <strong>Čas:</strong> <?= $msg['cas'] ?>
                    </div>
                    <div class="history-message history-<?= $msg['vloga'] ?>">
                        <?= nl2br(htmlspecialchars(substr($msg['vsebina'], 0, 300))) ?>
                        <?php if (strlen($msg['vsebina']) > 300): ?>...<?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    
</div>
</body>
</html>