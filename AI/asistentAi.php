<?php
/**
 * UNIVERZALNA AI SKRIPTA - WEB VMESNIK
 * Rečeš ji kar hočeš, ona naredi!
 */

require_once '../includes/ai_deepseek_json.php';

$ai = new DeepSeekAI_JSON();
$result = null;
$input = $_POST['prompt'] ?? '';
$agent = $_POST['agent'] ?? 'bloger';
$api_key_id = $_POST['api_key_id'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $input) {
    $response = $ai->generiraj($input, $agent, $api_key_id);
    if ($response['success']) {
        $result = $response['content'];
    } else {
        $error = $response['error'];
    }
}
?>
<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <title>🤖 Univerzalni AI Asistent</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            color: white;
            font-size: 2.5rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
        }
        .header p {
            color: rgba(255,255,255,0.9);
            margin-top: 10px;
        }
        .card {
            background: white;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        .input-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            color: #333;
        }
        select, textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            font-family: inherit;
            font-size: 1rem;
            transition: border-color 0.2s;
        }
        select:focus, textarea:focus {
            outline: none;
            border-color: #667eea;
        }
        textarea {
            resize: vertical;
            min-height: 120px;
        }
        .button-group {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        button {
            padding: 12px 24px;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 1rem;
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .btn-secondary {
            background: #f0f0f0;
            color: #333;
        }
        .btn-secondary:hover {
            background: #e0e0e0;
        }
        .result {
            background: #1a1a2e;
            color: #e2e8f0;
            padding: 20px;
            border-radius: 12px;
            font-family: 'Courier New', monospace;
            white-space: pre-wrap;
            max-height: 500px;
            overflow-y: auto;
            font-size: 0.9rem;
            line-height: 1.5;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 12px;
            margin-bottom: 20px;
            border-left: 4px solid #dc2626;
        }
        .suggestions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 15px;
        }
        .suggestion {
            background: #f0f0f0;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.85rem;
            cursor: pointer;
            transition: background 0.2s;
        }
        .suggestion:hover {
            background: #e0e0e0;
        }
        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid #f3f3f3;
            border-top: 3px solid #667eea;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-left: 10px;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        @media (max-width: 768px) {
            .header h1 { font-size: 1.8rem; }
            .card { padding: 20px; }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>🤖 Univerzalni AI Asistent</h1>
        <p>Reči mi, kaj naj naredim - pišem kodo, popravljam, analiziram, prevajam, ustvarjam CRUD...</p>
    </div>
    
    <div class="card">
        <form method="post" id="aiForm">
            <div class="input-group">
                <label>🎭 Izberi agenta</label>
                <select name="agent">
                    <option value="bloger">📝 Blog pisec - za članke in vsebine</option>
                    <option value="analitik">🔍 Analitik - za analize in preglede</option>
                    <option value="kreativec">🎨 Kreativec - za kreativno pisanje</option>
                    <option value="urejevalec">✏️ Urejevalec - za lektoriranje</option>
                </select>
            </div>
            
            <div class="input-group">
                <label>💬 Kaj naj naredim?</label>
                <textarea name="prompt" placeholder="Primeri:
• Napiši PHP funkcijo za validacijo emaila
• Popravi to kodo: function test() { return 'hello' }
• Ustvari CRUD za tabelo uporabniki
• Analiziraj ta JSON in mi povej, kaj je narobe
• Prevedi v slovenščino: Artificial Intelligence is amazing
• Debugiraj napako: undefined variable $user
• Napiši mi SQL za tabelo products z vsemi polji" required><?= htmlspecialchars($input) ?></textarea>
            </div>
            
            <div class="suggestions">
                <div class="suggestion" onclick="setPrompt('Napiši PHP funkcijo za pozdrav uporabnika')">👋 PHP pozdrav</div>
                <div class="suggestion" onclick="setPrompt('Ustvari CRUD za tabelo products')">🗄️ CRUD generator</div>
                <div class="suggestion" onclick="setPrompt('Popravi to kodo: <?php echo str_repeat(\" \", 0); ?>')">🔧 Popravi kodo</div>
                <div class="suggestion" onclick="setPrompt('Analiziraj ta JSON in mi povej strukturo')">📊 Analiziraj JSON</div>
                <div class="suggestion" onclick="setPrompt('Napiši mi responsive HTML/CSS za kontaktni obrazec')">🎨 HTML/CSS</div>
                <div class="suggestion" onclick="setPrompt('Prevedi v angleščino: Kako si?')">🌐 Prevajalnik</div>
            </div>
            
            <div class="button-group">
                <button type="submit" class="btn-primary" id="submitBtn">✨ Izvedi</button>
                <button type="button" class="btn-secondary" onclick="clearForm()">🗑️ Počisti</button>
            </div>
        </form>
    </div>
    
    <?php if (isset($error)): ?>
        <div class="error">❌ <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    
    <?php if ($result): ?>
    <div class="card">
        <h2 style="margin-bottom: 15px;">📄 Rezultat</h2>
        <div class="result"><?= nl2br(htmlspecialchars($result)) ?></div>
        
        <div class="button-group" style="margin-top: 20px;">
            <button class="btn-secondary" onclick="copyResult()">📋 Kopiraj</button>
            <button class="btn-secondary" onclick="saveResult()">💾 Shrani</button>
        </div>
    </div>
    <?php endif; ?>
    
    <div class="card">
        <h2 style="margin-bottom: 15px;">📖 Ideje za uporabo</h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px;">
            <div style="background: #f8f9fa; padding: 12px; border-radius: 10px;">
                <strong>📝 Pisanje kode</strong>
                <small>Napiši mi PHP razred za uporabnika</small>
            </div>
            <div style="background: #f8f9fa; padding: 12px; border-radius: 10px;">
                <strong>🔧 Popravljanje</strong>
                <small>Popravi to kodo: [tvoja koda]</small>
            </div>
            <div style="background: #f8f9fa; padding: 12px; border-radius: 10px;">
                <strong>📊 Analiza</strong>
                <small>Analiziraj ta log in najdi napake</small>
            </div>
            <div style="background: #f8f9fa; padding: 12px; border-radius: 10px;">
                <strong>🗄️ CRUD generator</strong>
                <small>Ustvari CRUD za tabelo naročila</small>
            </div>
            <div style="background: #f8f9fa; padding: 12px; border-radius: 10px;">
                <strong>🌐 Prevajanje</strong>
                <small>Prevedi v nemščino: Hello world</small>
            </div>
            <div style="background: #f8f9fa; padding: 12px; border-radius: 10px;">
                <strong>🐛 Debug</strong>
                <small>Zakaj dobim napako "undefined index"?</small>
            </div>
        </div>
    </div>
</div>

<script>
function setPrompt(text) {
    document.querySelector('textarea[name="prompt"]').value = text;
}

function clearForm() {
    document.querySelector('textarea[name="prompt"]').value = '';
}

function copyResult() {
    const result = document.querySelector('.result');
    if (result) {
        const text = result.innerText;
        navigator.clipboard.writeText(text).then(() => {
            alert('Kopirano v odložišče!');
        });
    }
}

function saveResult() {
    const result = document.querySelector('.result');
    if (result) {
        const text = result.innerText;
        const blob = new Blob([text], {type: 'text/plain'});
        const a = document.createElement('a');
        const url = URL.createObjectURL(blob);
        a.href = url;
        a.download = 'ai_result_' + new Date().toISOString().slice(0,19).replace(/:/g, '-') + '.txt';
        a.click();
        URL.revokeObjectURL(url);
    }
}

// Loading animation na submit
document.getElementById('aiForm').addEventListener('submit', function() {
    const btn = document.getElementById('submitBtn');
    btn.innerHTML = '⏳ Obdelujem... <span class="loading"></span>';
    btn.disabled = true;
});
</script>
</body>
</html>