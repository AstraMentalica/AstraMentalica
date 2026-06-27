<?php
/**
 * ============================================================================
 * PATH: SISTEM/admin/ai_center.php
 * NAMEN: GLAVNA AI NADZORNA PLOŠČA – ZBIRALIŠČE VSEH AI AGENTOV
 * VERZIJA: 2.0.0 (FULL UPGRADE)
 * ZADNJA_SPREMEMBA: 2026-06-15
 * ============================================================================
 */

$varnost = astra_varnost();
$rezultat = '';
$sporocilo = '';
$napaka = '';

// ============================================================================
// POMOŽNE FUNKCIJE
// ============================================================================
function pridobiVsebine() {
    $vsebine = [];
    $basePath = VSEBINA_PATH;
    
    // Cikli
    $cikliPath = $basePath . '/cikli';
    if (is_dir($cikliPath)) {
        $cikli = glob($cikliPath . '/*', GLOB_ONLYDIR);
        foreach ($cikli as $cikel) {
            $ime = basename($cikel);
            $config = file_exists($cikel . '/config.json') ? json_decode(file_get_contents($cikel . '/config.json'), true) : [];
            $naslov = $config['title'] ?? $ime;
            $vsebine[] = ['tip' => 'cikel', 'id' => $ime, 'naslov' => $naslov];
        }
    }
    
    // Codex
    $codexPath = $basePath . '/codex';
    if (is_dir($codexPath)) {
        $codexi = glob($codexPath . '/*.md');
        foreach ($codexi as $codex) {
            $ime = basename($codex, '.md');
            $content = file_get_contents($codex);
            $naslov = $ime;
            if (preg_match('/naslov:\s*"(.+?)"/', $content, $m)) $naslov = $m[1];
            $vsebine[] = ['tip' => 'codex', 'id' => $ime, 'naslov' => $naslov];
        }
    }
    
    // Manifesti
    $manifestiPath = $basePath . '/manifesti';
    if (is_dir($manifestiPath)) {
        $manifesti = glob($manifestiPath . '/*.md');
        foreach ($manifesti as $manifest) {
            $ime = basename($manifest, '.md');
            $content = file_get_contents($manifest);
            $naslov = $ime;
            if (preg_match('/naslov:\s*"(.+?)"/', $content, $m)) $naslov = $m[1];
            $vsebine[] = ['tip' => 'manifest', 'id' => $ime, 'naslov' => $naslov];
        }
    }
    
    return $vsebine;
}

function pridobiPotVsebine($tipVira, $vir) {
    $pot = '';
    if ($tipVira === 'cikel') {
        $pot = VSEBINA_PATH . '/cikli/' . $vir . '/01-spoznavanje.md';
        if (!file_exists($pot)) {
            $files = glob(VSEBINA_PATH . '/cikli/' . $vir . '/*.md');
            $pot = $files[0] ?? '';
        }
    } elseif ($tipVira === 'codex') {
        $pot = VSEBINA_PATH . '/codex/' . $vir . '.md';
    } elseif ($tipVira === 'manifest') {
        $pot = VSEBINA_PATH . '/manifesti/' . $vir . '.md';
    }
    return $pot;
}

function shraniBlog($generirano, $vir) {
    $blogPath = VSEBINA_PATH . '/blog';
    if (!is_dir($blogPath)) mkdir($blogPath, 0755, true);
    
    $naslovClanka = trim(substr($generirano, 0, 60));
    $naslovClanka = preg_replace('/[^a-z0-9_]/i', '_', str_replace(' ', '_', $naslovClanka));
    $filename = $blogPath . '/' . date('Y-m-d') . '_' . $naslovClanka . '.md';
    
    $zapis = "---\n";
    $zapis .= "naslov: \"Blog članek " . date('Y-m-d') . "\"\n";
    $zapis .= "tip: blog\n";
    $zapis .= "vir: $vir\n";
    $zapis .= "datum: " . date('Y-m-d H:i:s') . "\n";
    $zapis .= "---\n\n";
    $zapis .= $generirano;
    
    return file_put_contents($filename, $zapis);
}

function zapisiAILog($tip, $sporocilo) {
    $logFile = PODATKI_PATH . '/ai.log';
    if (!is_dir(dirname($logFile))) mkdir(dirname($logFile), 0755, true);
    file_put_contents($logFile, '[' . date('Y-m-d H:i:s') . "] $tip: $sporocilo\n", FILE_APPEND);
}

function pridobiPravila() {
    $pravila = [];
    $pravilaPath = ROOT_PATH . '/razvoj/pravila';
    if (is_dir($pravilaPath)) {
        $pravilaDatoteke = glob($pravilaPath . '/*.md');
        foreach ($pravilaDatoteke as $dat) {
            $ime = basename($dat);
            $vsebina = file_get_contents($dat);
            $predlog = '';
            if (preg_match('/## Predlog:\s*(.+?)(\n|$)/s', $vsebina, $m)) {
                $predlog = trim($m[1]);
            } elseif (preg_match('/---\n(.*?)\n---/s', $vsebina, $m)) {
                $predlog = substr($m[1], 0, 150);
            } else {
                $predlog = substr($vsebina, 0, 150);
            }
            $pravila[] = [
                'ime' => $ime,
                'predlog' => $predlog,
                'cas' => date('Y-m-d H:i:s', filemtime($dat))
            ];
        }
        rsort($pravila);
    }
    return $pravila;
}

function pridobiAILoge() {
    $aiLogi = [];
    $logPath = PODATKI_PATH . '/ai.log';
    if (file_exists($logPath)) {
        $vrstice = file($logPath);
        $zadnje = array_slice($vrstice, -30);
        foreach (array_reverse($zadnje) as $vrstica) {
            if (preg_match('/\[(.*?)\] (.*?): (.*)/', $vrstica, $m)) {
                $aiLogi[] = ['cas' => $m[1], 'tip' => $m[2], 'sporocilo' => $m[3]];
            } else {
                $aiLogi[] = ['cas' => date('Y-m-d H:i:s'), 'tip' => 'info', 'sporocilo' => trim($vrstica)];
            }
        }
    }
    return $aiLogi;
}

function varenPath($path, $allowedBase) {
    $realPath = realpath($path);
    $realBase = realpath($allowedBase);
    if ($realPath === false || strpos($realPath, $realBase) !== 0) {
        return false;
    }
    return $realPath;
}

// ============================================================================
// PRIDOBI PODATKE PRED OBDELAVO
// ============================================================================
$vsebine = pridobiVsebine();

// Preveri ali AI funkcije sploh obstajajo
$aiAvailable = function_exists('ai_generiraj') && function_exists('ai_analiziraj');

// ============================================================================
// OBDELAVA POST ZAHTEVKOV (podpora za AJAX in normalne zahtevke)
// ============================================================================
$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
          strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!astra_csrf_preveri($_POST['csrf_zeton'] ?? '')) {
        $napaka = 'Neveljaven zahtevek. Osvežite stran in poskusite znova.';
    } else {
        $akcija = $_POST['ai_akcija'] ?? '';
        $vir = trim($_POST['vir'] ?? '');
        $tipVira = $_POST['tip_vira'] ?? 'cikel';
        $prompt = trim($_POST['prompt'] ?? '');
        
        // 1. GENERIRANJE BLOG ČLANKA
        if ($akcija === 'generiraj_blog' && $vir) {
            if (!$aiAvailable) {
                $napaka = 'AI servis ni na voljo. Preverite integracijo API.';
            } else {
                $potVsebine = pridobiPotVsebine($tipVira, $vir);
                
                if ($potVsebine && file_exists($potVsebine)) {
                    $originalnaVsebina = file_get_contents($potVsebine);
                    $originalnaVsebina = preg_replace('/^---\n.*?\n---\n/s', '', $originalnaVsebina);
                    
                    $aiPrompt = "Na podlagi naslednje vsebine ustvari blog članek v slovenskem jeziku. Uporabi privlačen naslov. Članek naj ima uvod, glavni del (3-4 točke) in zaključek.\n\nVsebina:\n" . substr($originalnaVsebina, 0, 4000);
                    $generirano = ai_generiraj($aiPrompt, ['max_tokens' => 2500]);
                    
                    if ($generirano) {
                        $rezultat = $generirano;
                        $sporocilo = 'Blog članek uspešno generiran.';
                        zapisiAILog('generiraj_blog', $vir);
                        
                        if (isset($_POST['shrani'])) {
                            if (shraniBlog($generirano, $vir)) {
                                $sporocilo .= ' Članek shranjen v /blog.';
                            } else {
                                $napaka = 'Članka ni bilo mogoče shraniti. Preverite dovoljenja.';
                            }
                        }
                    } else {
                        $napaka = 'AI ni vrnil odgovora. Preverite konfiguracijo.';
                    }
                } else {
                    $napaka = 'Izbrana vsebina ne obstaja.';
                }
            }
        }
        
        // 2. ANALIZA VSEBINE
        elseif ($akcija === 'analiziraj' && $prompt) {
            if (!$aiAvailable) {
                $napaka = 'AI servis ni na voljo.';
            } else {
                $rezultat = ai_analiziraj($prompt, 'splosno');
                if ($rezultat) {
                    $sporocilo = 'Analiza končana.';
                    zapisiAILog('analiziraj', substr($prompt, 0, 100));
                } else {
                    $napaka = 'Analiza ni uspela.';
                }
            }
        }
        
        // 3. POLJUBEN AI PROMPT
        elseif ($akcija === 'generiraj_prompt' && $prompt) {
            if (!$aiAvailable) {
                $napaka = 'AI servis ni na voljo.';
            } else {
                $rezultat = ai_generiraj($prompt, ['max_tokens' => 1500]);
                if ($rezultat) {
                    $sporocilo = 'Vsebina generirana.';
                    zapisiAILog('generiraj_prompt', substr($prompt, 0, 100));
                } else {
                    $napaka = 'Generiranje ni uspelo.';
                }
            }
        }
        
        // 4. POGON SAMOUČEČEGA AGENTA – VARNI EXEC
        elseif ($akcija === 'pozeni_agenta') {
            $agentPath = realpath(SISTEM_PATH . '/cli/agent.php');
            $allowedBase = realpath(SISTEM_PATH);
            
            if ($agentPath && strpos($agentPath, $allowedBase) === 0 && file_exists($agentPath)) {
                $phpPath = trim(shell_exec('which php'));
                if (empty($phpPath)) $phpPath = 'php';
                
                $cmd = escapeshellcmd($phpPath) . ' ' . escapeshellarg($agentPath) . ' 2>&1';
                $output = [];
                $return = 0;
                exec($cmd, $output, $return);
                
                if ($return === 0) {
                    $sporocilo = 'Agent uspešno požgan.';
                    $rezultat = implode("\n", $output);
                    zapisiAILog('pozeni_agenta', 'Uspešno izveden');
                } else {
                    $napaka = 'Napaka pri pogonu agenta (koda ' . $return . '): ' . implode("\n", $output);
                }
            } else {
                $napaka = 'agent.php ne obstaja ali je nedostopen.';
            }
        }
        
        // 5. BRISANJE PRAVILA – VARNOST
        elseif ($akcija === 'izbrisi_pravilo' && isset($_POST['pravilo'])) {
            $imePravila = basename($_POST['pravilo']);
            $pravilaBase = realpath(ROOT_PATH . '/razvoj/pravila');
            $targetPath = realpath($pravilaBase . '/' . $imePravila);
            
            if ($targetPath && strpos($targetPath, $pravilaBase) === 0 && file_exists($targetPath)) {
                if (unlink($targetPath)) {
                    $sporocilo = 'Pravilo "' . htmlspecialchars($imePravila) . '" izbrisano.';
                    zapisiAILog('izbrisi_pravilo', $imePravila);
                } else {
                    $napaka = 'Pravila ni bilo mogoče izbrisati.';
                }
            } else {
                $napaka = 'Pravilo ne obstaja ali je pot neveljavna.';
            }
        }
        
        else {
            $napaka = 'Manjkajo parametri ali je akcija neznana.';
        }
    }
    
    // Če je AJAX zahtevek, vrnemo samo posodobljene dele
    if ($isAjax) {
        $response = [];
        
        // Zgradimo HTML za posodobitev
        ob_start();
        ?>
        <div class="obvestilo obvestilo-uspesno" style="display: <?= $sporocilo ? 'block' : 'none' ?>">✅ <?= htmlspecialchars($sporocilo) ?></div>
        <div class="obvestilo obvestilo-napaka" style="display: <?= $napaka ? 'block' : 'none' ?>">❌ <?= htmlspecialchars($napaka) ?></div>
        <?php if ($rezultat): ?>
        <div class="rezultat-sekcija">
            <div style="background: white; border-radius: 12px; padding: 1.5rem; margin-bottom: 1.5rem; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                <h2>📄 Rezultat</h2>
                <div class="rezultat"><?= nl2br(htmlspecialchars($rezultat)) ?></div>
            </div>
        </div>
        <?php endif; ?>
        
        <div class="pravila-sekcija">
            <?php $pravila = pridobiPravila(); ?>
            <div style="background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                <h2>📋 Generirana pravila</h2>
                <?php if (empty($pravila)): ?>
                    <p style="color: #666;">Ni generiranih pravil. Poženi samoučečega agenta.</p>
                <?php else: ?>
                    <?php foreach ($pravila as $p): ?>
                    <div class="pravilo-item">
                        <div style="flex:1;">
                            <strong><?= htmlspecialchars($p['ime']) ?></strong><br>
                            <small style="color:#666;"><?= htmlspecialchars(substr($p['predlog'], 0, 80)) ?>...</small>
                        </div>
                        <form method="post" onsubmit="return confirm('Izbriši pravilo <?= htmlspecialchars($p['ime']) ?>?')" style="margin-left: 0.5rem;">
                            <?= astra_csrf_vnos() ?>
                            <input type="hidden" name="ai_akcija" value="izbrisi_pravilo">
                            <input type="hidden" name="pravilo" value="<?= htmlspecialchars($p['ime']) ?>">
                            <button type="submit" class="gumb gumb-danger gumb-small" title="Izbriši">🗑️</button>
                        </form>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="logi-sekcija">
            <?php $aiLogi = pridobiAILoge(); ?>
            <div style="background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                <h2>📜 Zadnji AI dogodki</h2>
                <?php if (empty($aiLogi)): ?>
                    <p style="color: #666;">Ni AI klicev.</p>
                <?php else: ?>
                    <?php foreach ($aiLogi as $log): ?>
                    <div class="log-item">
                        <span style="color:#888;"><?= htmlspecialchars($log['cas']) ?></span>
                        <strong style="color:var(--ai-primary);">[<?= htmlspecialchars($log['tip']) ?>]</strong>
                        <?= htmlspecialchars(substr($log['sporocilo'], 0, 120)) ?>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
        <?php
        $response['html'] = ob_get_clean();
        $response['success'] = empty($napaka);
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
}

// Pridobi podatke za prikaz (samo za normalne zahtevke)
$pravila = pridobiPravila();
$aiLogi = pridobiAILoge();
?>
<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🤖 AI Command Center – Astra</title>
    <link rel="stylesheet" href="<?= ROOT_URL ?>GLOBALNO/slog/osnova.css">
    <link rel="stylesheet" href="<?= ROOT_URL ?>GLOBALNO/slog/postavitev.css">
    <link rel="stylesheet" href="<?= ROOT_URL ?>GLOBALNO/slog/gradniki.css">
    <style>
        :root { 
            --ai-primary: #6366f1; 
            --ai-secondary: #8b5cf6; 
            --ai-dark: #1e1b4b;
            --ai-success: #10b981;
            --ai-danger: #dc2626;
        }
        
        * {
            box-sizing: border-box;
        }
        
        .admin-container { 
            max-width: 1400px; 
            margin: 0 auto; 
            padding: 2rem; 
        }
        
        .admin-nav { 
            display: flex; 
            gap: 0.5rem; 
            flex-wrap: wrap; 
            margin-bottom: 2rem; 
            border-bottom: 2px solid #e2e8f0; 
            padding-bottom: 1rem; 
        }
        
        .admin-nav a { 
            padding: 0.6rem 1.2rem; 
            background: #f1f5f9; 
            border-radius: 10px; 
            text-decoration: none; 
            color: #334155; 
            font-weight: 500;
            transition: all 0.2s ease;
        }
        
        .admin-nav a:hover, 
        .admin-nav a.aktivna { 
            background: var(--ai-primary); 
            color: white; 
            transform: translateY(-2px);
        }
        
        .agent-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(340px, 1fr)); 
            gap: 1.5rem; 
            margin-bottom: 2rem; 
        }
        
        .agent-card { 
            background: white; 
            border-radius: 16px; 
            padding: 1.5rem; 
            box-shadow: 0 4px 12px rgba(0,0,0,0.08); 
            border-top: 4px solid var(--ai-primary); 
            transition: transform 0.2s, box-shadow 0.2s;
        }
        
        .agent-card:hover { 
            transform: translateY(-4px); 
            box-shadow: 0 8px 24px rgba(0,0,0,0.12);
        }
        
        .agent-card h3 { 
            margin: 0 0 0.5rem; 
            display: flex; 
            align-items: center; 
            gap: 0.5rem; 
            font-size: 1.25rem;
            color: var(--ai-dark);
        }
        
        .agent-card .opis { 
            color: #64748b; 
            font-size: 0.85rem; 
            margin-bottom: 1.25rem; 
            line-height: 1.4;
        }
        
        .form-group { 
            margin-bottom: 1rem; 
        }
        
        .form-group label { 
            display: block; 
            margin-bottom: 0.4rem; 
            font-weight: 600; 
            font-size: 0.85rem; 
            color: #334155; 
        }
        
        .form-group select, 
        .form-group textarea, 
        .form-group input { 
            width: 100%; 
            padding: 0.65rem; 
            border: 1.5px solid #e2e8f0; 
            border-radius: 10px; 
            font-family: inherit;
            transition: border-color 0.2s;
        }
        
        .form-group select:focus, 
        .form-group textarea:focus, 
        .form-group input:focus { 
            outline: none; 
            border-color: var(--ai-primary); 
            box-shadow: 0 0 0 3px rgba(99,102,241,0.1);
        }
        
        .vrstica { 
            display: flex; 
            gap: 1rem; 
        }
        
        .vrstica .form-group { 
            flex: 1; 
        }
        
        .checkbox-group { 
            display: flex; 
            align-items: center; 
            gap: 0.5rem; 
            margin: 0.75rem 0;
        }
        
        .checkbox-group input {
            width: auto;
        }
        
        .gumb { 
            padding: 0.65rem 1.25rem; 
            background: var(--ai-primary); 
            color: white; 
            border: none; 
            border-radius: 10px; 
            cursor: pointer; 
            font-weight: 600;
            transition: all 0.2s;
            width: 100%;
        }
        
        .gumb:hover { 
            background: var(--ai-secondary); 
            transform: translateY(-1px);
        }
        
        .gumb-danger { 
            background: var(--ai-danger); 
        }
        
        .gumb-danger:hover { 
            background: #b91c1c; 
        }
        
        .gumb-success { 
            background: var(--ai-success); 
        }
        
        .gumb-success:hover { 
            background: #059669; 
        }
        
        .gumb-small { 
            padding: 0.3rem 0.8rem; 
            font-size: 0.8rem; 
            width: auto;
        }
        
        .rezultat { 
            background: #1e1b4b; 
            color: #e2e8f0; 
            padding: 1.25rem; 
            border-radius: 12px; 
            font-family: 'Courier New', monospace; 
            white-space: pre-wrap; 
            max-height: 500px; 
            overflow-y: auto; 
            font-size: 0.9rem;
            line-height: 1.5;
        }
        
        .obvestilo { 
            padding: 1rem; 
            border-radius: 12px; 
            margin-bottom: 1.5rem; 
            font-weight: 500;
        }
        
        .obvestilo-uspesno { 
            background: #d4edda; 
            border-left: 4px solid var(--ai-success); 
            color: #155724; 
        }
        
        .obvestilo-napaka { 
            background: #f8d7da; 
            border-left: 4px solid var(--ai-danger); 
            color: #721c24; 
        }
        
        .pravilo-item { 
            background: #f8fafc; 
            padding: 0.75rem; 
            margin-bottom: 0.75rem; 
            border-radius: 10px; 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            flex-wrap: wrap; 
            transition: background 0.2s;
        }
        
        .pravilo-item:hover {
            background: #f1f5f9;
        }
        
        .log-item { 
            font-family: monospace; 
            font-size: 0.7rem; 
            padding: 0.4rem 0.5rem; 
            border-bottom: 1px solid #e2e8f0; 
        }
        
        .grid-2 { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); 
            gap: 1.5rem; 
            margin-top: 1.5rem; 
        }
        
        /* AJAX Loading Overlay */
        .ajax-loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.8);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            backdrop-filter: blur(4px);
        }
        
        .loading-spinner {
            background: white;
            padding: 2rem;
            border-radius: 20px;
            text-align: center;
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
        }
        
        .spinner {
            width: 60px;
            height: 60px;
            border: 4px solid #e2e8f0;
            border-top: 4px solid var(--ai-primary);
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 1rem;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .loading-spinner p {
            margin: 0;
            color: var(--ai-dark);
            font-weight: 600;
        }
        
        .loading-spinner small {
            color: #64748b;
            font-size: 0.8rem;
        }
        
        .status-badge {
            display: inline-block;
            padding: 0.2rem 0.5rem;
            background: var(--ai-success);
            color: white;
            border-radius: 20px;
            font-size: 0.7rem;
            margin-left: 0.5rem;
        }
        
        @media (max-width: 768px) { 
            .admin-container { 
                padding: 1rem; 
            } 
            .agent-grid { 
                grid-template-columns: 1fr; 
            } 
            .vrstica { 
                flex-direction: column; 
            }
            .grid-2 {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
<div class="admin-container">
    
    <!-- Navigacija -->
    <div class="admin-nav">
        <a href="?section=dashboard">📊 Dashboard</a>
        <a href="?section=moduli">📦 Moduli</a>
        <a href="?section=uporabniki">👤 Uporabniki</a>
        <a href="?section=vsebina">📄 Vsebina</a>
        <a href="?section=cache">🗑️ Cache</a>
        <a href="?section=logi">📜 Logi</a>
        <a href="?section=nastavitve">⚙️ Nastavitve</a>
        <a href="?section=ai_center" class="aktivna">🤖 AI Center</a>
    </div>
    
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
        <div>
            <h1 style="margin: 0;">🤖 AI Command Center</h1>
            <p style="color: #64748b; margin-top: 0.5rem;">Zbirališče vseh umetnih inteligenc – varno, modularno, pripravljeno na razširitev</p>
        </div>
        <?php if ($aiAvailable): ?>
            <span class="status-badge">✅ AI aktiven</span>
        <?php else: ?>
            <span class="status-badge" style="background: #dc2626;">⚠️ AI nedosegljiv</span>
        <?php endif; ?>
    </div>
    
    <!-- Sporočila -->
    <?php if ($sporocilo): ?>
    <div class="obvestilo obvestilo-uspesno">✅ <?= htmlspecialchars($sporocilo) ?></div>
    <?php endif; ?>
    <?php if ($napaka): ?>
    <div class="obvestilo obvestilo-napaka">❌ <?= htmlspecialchars($napaka) ?></div>
    <?php endif; ?>
    
    <!-- AGENTI -->
    <div class="agent-grid">
        
        <!-- Agent 1: Blog pisec -->
        <div class="agent-card">
            <h3>
                <span>📝</span> Blog pisec
            </h3>
            <div class="opis">Generiraj blog članek iz ciklov, codexa ali manifestov</div>
            <form method="post" data-ajax="true">
                <?= astra_csrf_vnos() ?>
                <input type="hidden" name="ai_akcija" value="generiraj_blog">
                <div class="vrstica">
                    <div class="form-group">
                        <label>Tip vira</label>
                        <select name="tip_vira">
                            <option value="cikel">🔄 Cikel</option>
                            <option value="codex">📚 Codex</option>
                            <option value="manifest">📜 Manifest</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Izberi vir</label>
                        <select name="vir" required>
                            <option value="">-- Izberi --</option>
                            <?php foreach ($vsebine as $v): ?>
                                <option value="<?= htmlspecialchars($v['id']) ?>"><?= ucfirst($v['tip']) ?>: <?= htmlspecialchars($v['naslov']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="checkbox-group">
                    <input type="checkbox" name="shrani" id="shrani" value="1">
                    <label for="shrani">💾 Shrani v /blog</label>
                </div>
                <button type="submit" class="gumb">✍️ Generiraj</button>
            </form>
        </div>
        
        <!-- Agent 2: Analiza vsebine -->
        <div class="agent-card">
            <h3>
                <span>🔍</span> Analiza vsebine
            </h3>
            <div class="opis">AI analiza besedila – sentiment, povzetek, ključne točke</div>
            <form method="post" data-ajax="true">
                <?= astra_csrf_vnos() ?>
                <input type="hidden" name="ai_akcija" value="analiziraj">
                <div class="form-group">
                    <label>Vsebina za analizo</label>
                    <textarea name="prompt" rows="4" placeholder="Prilepi vsebino... (npr. članek, sporočilo, opis)" required></textarea>
                </div>
                <button type="submit" class="gumb">🔍 Analiziraj</button>
            </form>
        </div>
        
        <!-- Agent 3: Poljuben AI prompt -->
        <div class="agent-card">
            <h3>
                <span>💬</span> Poljuben AI prompt
            </h3>
            <div class="opis">Prosto komuniciranje z AI – vprašanja, prevodi, ustvarjanje</div>
            <form method="post" data-ajax="true">
                <?= astra_csrf_vnos() ?>
                <input type="hidden" name="ai_akcija" value="generiraj_prompt">
                <div class="form-group">
                    <label>Prompt</label>
                    <textarea name="prompt" rows="4" placeholder="Napiši kaj želiš... npr. 'Napiši kratek opis AI agentov'" required></textarea>
                </div>
                <button type="submit" class="gumb">💬 Pošlji</button>
            </form>
        </div>
        
        <!-- Agent 4: Samoučeči agent -->
        <div class="agent-card">
            <h3>
                <span>🧠</span> Samoučeči agent
            </h3>
            <div class="opis">Bere sistemske loge in predlaga popravke (poganja se v ozadju)</div>
            <form method="post" onsubmit="return confirm('Poženem samoučečega agenta? To lahko traja nekaj sekund.')">
                <?= astra_csrf_vnos() ?>
                <input type="hidden" name="ai_akcija" value="pozeni_agenta">
                <button type="submit" class="gumb gumb-success">▶️ Poženi agenta</button>
            </form>
        </div>
        
    </div>
    
    <!-- REZULTAT (dinamično posodabljan) -->
    <div class="rezultat-sekcija">
        <?php if ($rezultat): ?>
        <div style="background: white; border-radius: 12px; padding: 1.5rem; margin-bottom: 1.5rem; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
            <h2>📄 Rezultat</h2>
            <div class="rezultat"><?= nl2br(htmlspecialchars($rezultat)) ?></div>
        </div>
        <?php endif; ?>
    </div>
    
    <!-- PRAVILA IN LOGI -->
    <div class="grid-2">
        
        <div class="pravila-sekcija">
            <div style="background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                <h2>📋 Generirana pravila</h2>
                <?php if (empty($pravila)): ?>
                    <p style="color: #666;">Ni generiranih pravil. Poženi samoučečega agenta.</p>
                <?php else: ?>
                    <?php foreach ($pravila as $p): ?>
                    <div class="pravilo-item">
                        <div style="flex:1;">
                            <strong><?= htmlspecialchars($p['ime']) ?></strong><br>
                            <small style="color:#666;"><?= htmlspecialchars(substr($p['predlog'], 0, 80)) ?>...</small>
                        </div>
                        <form method="post" onsubmit="return confirm('Izbriši pravilo <?= htmlspecialchars($p['ime']) ?>?')" style="margin-left: 0.5rem;">
                            <?= astra_csrf_vnos() ?>
                            <input type="hidden" name="ai_akcija" value="izbrisi_pravilo">
                            <input type="hidden" name="pravilo" value="<?= htmlspecialchars($p['ime']) ?>">
                            <button type="submit" class="gumb gumb-danger gumb-small" title="Izbriši">🗑️</button>
                        </form>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="logi-sekcija">
            <div style="background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                <h2>📜 Zadnji AI dogodki</h2>
                <?php if (empty($aiLogi)): ?>
                    <p style="color: #666;">Ni AI klicev.</p>
                <?php else: ?>
                    <?php foreach ($aiLogi as $log): ?>
                    <div class="log-item">
                        <span style="color:#888;"><?= htmlspecialchars($log['cas']) ?></span>
                        <strong style="color:var(--ai-primary);">[<?= htmlspecialchars($log['tip']) ?>]</strong>
                        <?= htmlspecialchars(substr($log['sporocilo'], 0, 120)) ?>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
        
    </div>
    
</div>

<script>
// ============================================================
// AJAX AI KOMANDNI CENTER - BREZ OSVEŽEVANJA STRANI
// ============================================================

document.addEventListener('DOMContentLoaded', function() {
    const forms = document.querySelectorAll('form[data-ajax="true"]');
    
    forms.forEach(form => {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            
            submitBtn.innerHTML = '⏳ Prosim počakaj...';
            submitBtn.disabled = true;
            
            showLoadingOverlay();
            
            try {
                const response = await fetch(window.location.href, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                const data = await response.json();
                
                if (data.html) {
                    // Posodobi vsebino
                    const tempDiv = document.createElement('div');
                    tempDiv.innerHTML = data.html;
                    
                    // Posodobi obvestila
                    const newSuccess = tempDiv.querySelector('.obvestilo-uspesno');
                    const newError = tempDiv.querySelector('.obvestilo-napaka');
                    const newResult = tempDiv.querySelector('.rezultat-sekcija');
                    const newRules = tempDiv.querySelector('.pravila-sekcija');
                    const newLogs = tempDiv.querySelector('.logi-sekcija');
                    
                    // Izbriši stara obvestila
                    document.querySelectorAll('.obvestilo').forEach(el => el.remove());
                    
                    // Dodaj nova obvestila
                    if (newSuccess && newSuccess.innerHTML.trim() !== '<div class="obvestilo obvestilo-uspesno" style="display: none;">✅ </div>') {
                        document.querySelector('.agent-grid').insertAdjacentElement('beforebegin', newSuccess);
                        setTimeout(() => newSuccess.style.opacity = '0', 5000);
                        setTimeout(() => newSuccess.remove(), 5500);
                    }
                    
                    if (newError && newError.innerHTML.trim() !== '<div class="obvestilo obvestilo-napaka" style="display: none;">❌ </div>') {
                        document.querySelector('.agent-grid').insertAdjacentElement('beforebegin', newError);
                        setTimeout(() => newError.style.opacity = '0', 8000);
                        setTimeout(() => newError.remove(), 8500);
                    }
                    
                    // Posodobi rezultat
                    if (newResult) {
                        const oldResult = document.querySelector('.rezultat-sekcija');
                        if (oldResult) oldResult.innerHTML = newResult.innerHTML;
                    }
                    
                    // Posodobi pravila
                    if (newRules) {
                        const oldRules = document.querySelector('.pravila-sekcija');
                        if (oldRules) oldRules.innerHTML = newRules.innerHTML;
                    }
                    
                    // Posodobi loge
                    if (newLogs) {
                        const oldLogs = document.querySelector('.logi-sekcija');
                        if (oldLogs) oldLogs.innerHTML = newLogs.innerHTML;
                    }
                    
                    // Pomakni se na rezultat če obstaja
                    if (newResult && newResult.querySelector('.rezultat') && newResult.querySelector('.rezultat').innerHTML.trim() !== '') {
                        newResult.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                }
            } catch (error) {
                console.error('AJAX napaka:', error);
                showError('Prišlo je do napake pri povezavi. Poskusite ponovno.');
            } finally {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
                hideLoadingOverlay();
            }
        });
    });
    
    function showLoadingOverlay() {
        let overlay = document.querySelector('.ajax-loading-overlay');
        if (!overlay) {
            overlay = document.createElement('div');
            overlay.className = 'ajax-loading-overlay';
            overlay.innerHTML = `
                <div class="loading-spinner">
                    <div class="spinner"></div>
                    <p>🤖 AI obdeluje zahtevo...</p>
                    <small>To lahko traja nekaj sekund</small>
                </div>
            `;
            document.body.appendChild(overlay);
        }
        overlay.style.display = 'flex';
    }
    
    function hideLoadingOverlay() {
        const overlay = document.querySelector('.ajax-loading-overlay');
        if (overlay) overlay.style.display = 'none';
    }
    
    function showError(message) {
        const errorDiv = document.createElement('div');
        errorDiv.className = 'obvestilo obvestilo-napaka';
        errorDiv.innerHTML = `❌ ${message}`;
        document.querySelector('.agent-grid').insertAdjacentElement('beforebegin', errorDiv);
        setTimeout(() => errorDiv.style.opacity = '0', 5000);
        setTimeout(() => errorDiv.remove(), 5500);
    }
});
</script>

</body>
</html>