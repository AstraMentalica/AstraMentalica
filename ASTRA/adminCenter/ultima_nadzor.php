<?php
/**
 * ============================================================
 * ULTIMA NADZOR – AstraMentalica
 * ============================================================
 * Popoln nadzor: pregled, urejanje, predogled, zagon.
 * ============================================================
 */

// Varnost
$dovoljeniIPji = ['127.0.0.1', '::1', '192.168.1.%'];
$skrivniKljuc = $_GET['kljuc'] ?? '';
$masterKljuc = 'astra_master_2026';

$trenutniIP = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
$dovoljen = false;
foreach ($dovoljeniIPji as $ip) {
    if (strpos($ip, '%') !== false) {
        if (preg_match('/^' . str_replace('%', '.*', $ip) . '$/', $trenutniIP)) { $dovoljen = true; break; }
    } elseif ($trenutniIP === $ip) { $dovoljen = true; break; }
}
if (!$dovoljen && $skrivniKljuc !== $masterKljuc) {
    http_response_code(403);
    die('<h1>🔒 DOSTOP ZAVRNJEN</h1><p>Samo pooblaščeni uporabniki.</p>');
}

require_once __DIR__ . '/../../pot.php';

// Definicija vseh map
$KORENSKE_MAPE = [
    'PODATKI' => PODATKI_POT,
    'VSEBINA' => VSEBINA_POT,
    'GLOBALNO' => GLOBALNO_POT,
    'MODULI' => MODULI_POT,
    'ASTRA' => ASTRA_POT,
    'SISTEM' => SISTEM_POT,
    'UPORABNIKI' => UPORABNIKI_POT,
];

// Funkcije za upravljanje datotek
function pridobiStrukturo($pot, $rel = '') {
    if (!is_dir($pot)) return ['mape' => [], 'datoteke' => []];
    $rez = ['mape' => [], 'datoteke' => []];
    $items = scandir($pot);
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') continue;
        $polna = $pot . '/' . $item;
        $relativna = $rel ? $rel . '/' . $item : $item;
        if (is_dir($polna)) {
            $rez['mape'][] = ['ime' => $item, 'pot' => $relativna, 'vsebina' => pridobiStrukturo($polna, $relativna)];
        } else {
            $ext = pathinfo($item, PATHINFO_EXTENSION);
            $rez['datoteke'][] = [
                'ime' => $item, 'pot' => $relativna, 'ext' => $ext,
                'velikost' => filesize($polna),
                'velikost_fmt' => formatirajVelikost(filesize($polna)),
                'zadnja' => date('Y-m-d H:i:s', filemtime($polna))
            ];
        }
    }
    usort($rez['mape'], fn($a,$b) => strcmp($a['ime'], $b['ime']));
    usort($rez['datoteke'], fn($a,$b) => strcmp($a['ime'], $b['ime']));
    return $rez;
}

function formatirajVelikost($b) {
    if ($b < 1024) return $b . ' B';
    if ($b < 1048576) return round($b / 1024, 1) . ' KB';
    return round($b / 1048576, 1) . ' MB';
}

function preberiDatoteko($pot) {
    $abs = KOREN . '/' . ltrim($pot, '/');
    if (strpos($abs, KOREN) !== 0 || !file_exists($abs)) return null;
    return file_get_contents($abs);
}

function shraniDatoteko($pot, $vsebina) {
    $abs = KOREN . '/' . ltrim($pot, '/');
    if (strpos($abs, KOREN) !== 0) return false;
    $mapa = dirname($abs);
    if (!is_dir($mapa)) mkdir($mapa, 0755, true);
    return file_put_contents($abs, $vsebina) !== false;
}

function izbrisiDatoteko($pot) {
    $abs = KOREN . '/' . ltrim($pot, '/');
    if (strpos($abs, KOREN) !== 0 || !file_exists($abs)) return false;
    return unlink($abs);
}

function ustvariMapo($pot, $ime) {
    $abs = KOREN . '/' . ltrim($pot, '/') . '/' . $ime;
    if (strpos($abs, KOREN) !== 0) return false;
    return mkdir($abs, 0755, true);
}

// API klici
$akcija = $_POST['akcija'] ?? $_GET['akcija'] ?? '';
if ($akcija) {
    header('Content-Type: application/json');
    
    if ($akcija === 'pridobi_strukturo') {
        $mapa = $_POST['mapa'] ?? '';
        if (!isset($KORENSKE_MAPE[$mapa])) {
            echo json_encode(['uspeh' => false, 'napaka' => 'Neveljavna mapa']);
            exit;
        }
        echo json_encode(['uspeh' => true, 'podatki' => pridobiStrukturo($KORENSKE_MAPE[$mapa])]);
        exit;
    }
    
    if ($akcija === 'pridobi_vsebino') {
        $pot = $_POST['pot'] ?? '';
        $vsebina = preberiDatoteko($pot);
        echo json_encode(['uspeh' => $vsebina !== null, 'vsebina' => $vsebina ?? '', 'pot' => $pot]);
        exit;
    }
    
    if ($akcija === 'shrani_vsebino') {
        $pot = $_POST['pot'] ?? '';
        $vsebina = $_POST['vsebina'] ?? '';
        $ok = shraniDatoteko($pot, $vsebina);
        echo json_encode(['uspeh' => $ok]);
        exit;
    }
    
    if ($akcija === 'izbrisi') {
        $pot = $_POST['pot'] ?? '';
        $ok = izbrisiDatoteko($pot);
        echo json_encode(['uspeh' => $ok]);
        exit;
    }
    
    if ($akcija === 'ustvari_mapo') {
        $pot = $_POST['pot'] ?? '';
        $ime = $_POST['ime'] ?? '';
        $ok = ustvariMapo($pot, $ime);
        echo json_encode(['uspeh' => $ok]);
        exit;
    }
    
    echo json_encode(['uspeh' => false, 'napaka' => 'Neznana akcija']);
    exit;
}
?>
<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>⚡ ULTIMA NADZOR – AstraMentalica</title>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;600&family=JetBrains+Mono&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            background: #03050a;
            color: #e0d8c8;
            font-family: 'JetBrains Mono', monospace;
            font-size: 12px;
            overflow: hidden;
            height: 100vh;
        }
        
        /* Layout */
        .ultima {
            display: flex;
            height: 100vh;
        }
        
        /* Sidebar */
        .sidebar {
            width: 220px;
            background: #060a14;
            border-right: 1px solid #1a2535;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
            flex-shrink: 0;
        }
        
        .sidebar .logo {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid #1a2535;
            margin-bottom: 15px;
        }
        
        .sidebar .logo h1 {
            font-family: 'Cinzel', serif;
            font-size: 1rem;
            color: #c8a84b;
            letter-spacing: 2px;
        }
        
        .folder-item {
            padding: 8px 15px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s;
            border-left: 3px solid transparent;
        }
        
        .folder-item:hover {
            background: rgba(200,168,75,0.08);
            color: #c8a84b;
        }
        
        .folder-item.aktivno {
            border-left-color: #c8a84b;
            background: rgba(200,168,75,0.12);
            color: #c8a84b;
        }
        
        /* File tree */
        .filetree {
            width: 320px;
            background: #0a0e18;
            border-right: 1px solid #1a2535;
            overflow-y: auto;
            flex-shrink: 0;
        }
        
        .tree-node {
            padding: 4px 8px;
            cursor: pointer;
            user-select: none;
            font-size: 11px;
        }
        
        .tree-node:hover {
            background: rgba(200,168,75,0.08);
        }
        
        .tree-node.selected {
            background: rgba(200,168,75,0.15);
            color: #c8a84b;
        }
        
        .tree-folder {
            font-weight: normal;
            color: #8ba0c0;
        }
        
        .tree-file {
            color: #4a6080;
            padding-left: 20px;
        }
        
        .tree-file.preview {
            color: #c8a84b;
        }
        
        .tree-indent {
            display: inline-block;
            width: 16px;
        }
        
        /* Main panel */
        .main-panel {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        
        /* Tabs */
        .tabs {
            display: flex;
            background: #060a14;
            border-bottom: 1px solid #1a2535;
            overflow-x: auto;
            flex-shrink: 0;
        }
        
        .tab {
            padding: 8px 16px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
            border-right: 1px solid #1a2535;
            font-size: 11px;
            white-space: nowrap;
        }
        
        .tab:hover {
            background: rgba(200,168,75,0.08);
        }
        
        .tab.aktivno {
            background: #0a0e18;
            border-bottom: 2px solid #c8a84b;
            color: #c8a84b;
        }
        
        .tab-close {
            margin-left: 8px;
            color: #4a6080;
            font-size: 14px;
        }
        
        .tab-close:hover {
            color: #ef4444;
        }
        
        /* Content area */
        .content-area {
            flex: 1;
            display: flex;
            overflow: hidden;
        }
        
        .editor-panel {
            flex: 2;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        
        .editor-toolbar {
            padding: 8px 12px;
            background: #0a0e18;
            border-bottom: 1px solid #1a2535;
            display: flex;
            gap: 8px;
            align-items: center;
        }
        
        .editor-toolbar button {
            background: transparent;
            border: 1px solid #1a2535;
            color: #8ba0c0;
            padding: 4px 12px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 11px;
        }
        
        .editor-toolbar button:hover {
            border-color: #c8a84b;
            color: #c8a84b;
        }
        
        .editor {
            flex: 1;
            background: #0a0a14;
            border: none;
            color: #e0d8c8;
            font-family: 'JetBrains Mono', monospace;
            font-size: 11px;
            padding: 15px;
            resize: none;
            outline: none;
            tab-size: 4;
        }
        
        /* Preview panel (mini okno) */
        .preview-panel {
            flex: 1;
            background: #0a0e18;
            border-left: 1px solid #1a2535;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        
        .preview-header {
            padding: 8px 12px;
            background: #060a14;
            border-bottom: 1px solid #1a2535;
            font-size: 10px;
            color: #c8a84b;
            display: flex;
            justify-content: space-between;
        }
        
        .preview-frame {
            flex: 1;
            background: white;
            border: none;
            width: 100%;
        }
        
        .preview-error {
            padding: 20px;
            color: #ef4444;
            text-align: center;
        }
        
        /* Status bar */
        .status-bar {
            padding: 4px 12px;
            background: #060a14;
            border-top: 1px solid #1a2535;
            font-size: 10px;
            color: #4a6080;
            display: flex;
            justify-content: space-between;
            flex-shrink: 0;
        }
        
        /* Scrollbar */
        ::-webkit-scrollbar { width: 4px; height: 4px; }
        ::-webkit-scrollbar-track { background: #060a14; }
        ::-webkit-scrollbar-thumb { background: #c8a84b; border-radius: 2px; }
    </style>
</head>
<body>

<div class="ultima">
    
    <!-- Leva sidebar: izbira sistema -->
    <div class="sidebar">
        <div class="logo">
            <h1>⚡ ULTIMA</h1>
            <p style="font-size: 8px; color: #4a6080;">Vrhovni nadzor</p>
        </div>
        <div id="folderList"></div>
    </div>
    
    <!-- File tree (dinamično) -->
    <div class="filetree" id="fileTree">
        <div style="padding: 20px; text-align: center; color: #4a6080;">Izberi mapo</div>
    </div>
    
    <!-- Glavni panel z tabi -->
    <div class="main-panel">
        <div class="tabs" id="tabs"></div>
        
        <div class="content-area">
            <!-- Editor -->
            <div class="editor-panel" id="editorPanel">
                <div class="editor-toolbar">
                    <button id="saveBtn" disabled>💾 Shrani (Ctrl+S)</button>
                    <button id="runBtn" disabled>▶ Zaženi v predogledu</button>
                    <button id="copyBtn" disabled>📋 Kopiraj pot</button>
                </div>
                <textarea class="editor" id="editor" placeholder="Izberi datoteko za urejanje..." spellcheck="false"></textarea>
            </div>
            
            <!-- Predogled (mini okno) -->
            <div class="preview-panel" id="previewPanel">
                <div class="preview-header">
                    <span>🔍 PREDOGLED</span>
                    <span>
                        <button id="refreshPreview" style="background:none; border:none; color:#c8a84b; cursor:pointer;">🔄</button>
                        <button id="openNewWindow" style="background:none; border:none; color:#c8a84b; cursor:pointer;">⤴</button>
                    </span>
                </div>
                <iframe id="previewFrame" class="preview-frame" srcdoc="<html><body style='background:#0a0e18;color:#8ba0c0;display:flex;align-items:center;justify-content:center;height:100%'><p>Izberi datoteko za predogled</p></body></html>"></iframe>
            </div>
        </div>
        
        <div class="status-bar">
            <span id="statusMsg">Pripravljen</span>
            <span id="statusInfo">📁</span>
        </div>
    </div>
</div>

<script>
// ============================================================
// ULTIMA NADZOR - JavaScript
// ============================================================

let trenutnaMapa = 'PODATKI';
let trenutnaStruktura = null;
let odprtiTabi = [];
let aktivniTab = null;
let trenutnaPot = '';
let autoRefreshInterval = null;

// API klic
async function api(akcija, podatki = {}) {
    const fd = new FormData();
    fd.append('akcija', akcija);
    for (let [k,v] of Object.entries(podatki)) fd.append(k, v);
    const res = await fetch(location.href, { method: 'POST', body: fd });
    return await res.json();
}

// Osveži status
function setStatus(msg, type = 'info') {
    document.getElementById('statusMsg').innerHTML = msg;
    setTimeout(() => { if (document.getElementById('statusMsg').innerHTML === msg) 
        document.getElementById('statusMsg').innerHTML = 'Pripravljen'; }, 3000);
}

// ============================================================
// 1. NALOŽI SEZNAM SISTEMSKIH MAP
// ============================================================
async function naloziKorenskeMape() {
    const res = await api('pridobi_korenske_mape');
    if (!res.uspeh) return;
    
    const container = document.getElementById('folderList');
    container.innerHTML = '';
    res.podatki.forEach(m => {
        const div = document.createElement('div');
        div.className = `folder-item ${trenutnaMapa === m.ime ? 'aktivno' : ''}`;
        div.innerHTML = `<span>📁 ${m.ime}</span><span style="margin-left:auto; font-size:9px; color:#4a6080;">${m.velikost}</span>`;
        div.onclick = () => {
            trenutnaMapa = m.ime;
            naloziStrukturo();
            document.querySelectorAll('.folder-item').forEach(f => f.classList.remove('aktivno'));
            div.classList.add('aktivno');
        };
        container.appendChild(div);
    });
}

// ============================================================
// 2. NALOŽI STRUKTURO IZBRANE MAPE
// ============================================================
async function naloziStrukturo() {
    const res = await api('pridobi_strukturo', { mapa: trenutnaMapa });
    if (!res.uspeh) return;
    
    trenutnaStruktura = res.podatki;
    prikaziStrukturo(trenutnaStruktura, '');
}

function prikaziStrukturo(node, path) {
    const container = document.getElementById('fileTree');
    let html = '';
    
    function renderTree(nodes, level, parentPath) {
        let result = '';
        nodes.mape?.forEach(m => {
            const fullPath = parentPath ? parentPath + '/' + m.ime : m.ime;
            result += `<div class="tree-node tree-folder" onclick="toggleFolder(this, '${fullPath}', event)">
                        <span class="tree-indent" style="width:${level*16}px"></span>
                        <span>📁</span> ${m.ime}
                        <span style="font-size:9px; color:#4a6080; margin-left:8px">📁</span>
                    </div>
                    <div class="tree-children" id="children-${fullPath.replace(/\//g, '_')}" style="display:none">`;
            if (m.vsebina) result += renderTree(m.vsebina, level + 1, fullPath);
            result += `</div>`;
        });
        
        nodes.datoteke?.forEach(f => {
            const fullPath = parentPath ? parentPath + '/' + f.ime : f.ime;
            const isPreview = f.ext === 'php' || f.ext === 'html' || f.ext === 'js' || f.ext === 'css';
            result += `<div class="tree-node tree-file" onclick="odpriDatoteko('${fullPath}', event)" ondblclick="predogledDatoteke('${fullPath}')">
                        <span class="tree-indent" style="width:${level*16}px"></span>
                        <span>${isPreview ? '🔍' : '📄'}</span> ${f.ime}
                        <span style="font-size:8px; color:#4a6080; margin-left:8px">${f.velikost_fmt}</span>
                    </div>`;
        });
        return result;
    }
    
    container.innerHTML = renderTree(node, 0, '');
}

window.toggleFolder = function(el, path, e) {
    e.stopPropagation();
    const childrenDiv = document.getElementById(`children-${path.replace(/\//g, '_')}`);
    if (childrenDiv) {
        const isVisible = childrenDiv.style.display !== 'none';
        childrenDiv.style.display = isVisible ? 'none' : 'block';
        const folderSpan = el.querySelector('span:first-child');
        if (folderSpan) folderSpan.textContent = isVisible ? '📁' : '📂';
    }
};

// ============================================================
// 3. ODPRI DATOTEKO V TAB/UREJEVALNIKU
// ============================================================
async function odpriDatoteko(pot, event) {
    if (event) event.stopPropagation();
    
    // Preveri, če je že odprt
    const obstojec = odprtiTabi.find(t => t.pot === pot);
    if (obstoJec) {
        aktivirajTab(obstojec.id);
        return;
    }
    
    setStatus(`Nalagam ${pot}...`);
    
    const res = await api('pridobi_vsebino', { pot });
    if (!res.uspeh) {
        setStatus(`Napaka pri nalaganju ${pot}`, 'error');
        return;
    }
    
    const id = 'tab_' + Date.now() + '_' + Math.random().toString(36).substr(2, 6);
    const ime = pot.split('/').pop();
    
    odprtiTabi.push({
        id: id,
        pot: pot,
        ime: ime,
        vsebina: res.vsebina,
        spremenjeno: false
    });
    
    dodajTabVmesnik(id, ime, pot);
    if (odprtiTabi.length === 1) aktivirajTab(id);
    
    setStatus(`Odprto: ${pot}`);
}

function dodajTabVmesnik(id, ime, pot) {
    const tabsContainer = document.getElementById('tabs');
    const tab = document.createElement('div');
    tab.className = 'tab';
    tab.id = `tab-${id}`;
    tab.setAttribute('data-id', id);
    tab.setAttribute('data-pot', pot);
    tab.innerHTML = `<span>📄 ${ime}</span><span class="tab-close" onclick="zapriTab('${id}', event)">×</span>`;
    tab.onclick = (e) => { if(e.target !== tab.querySelector('.tab-close')) aktivirajTab(id); };
    tabsContainer.appendChild(tab);
}

function aktivirajTab(id) {
    aktivniTab = id;
    const tabData = odprtiTabi.find(t => t.id === id);
    if (!tabData) return;
    
    // Označi tab
    document.querySelectorAll('.tab').forEach(t => t.classList.remove('aktivno'));
    document.getElementById(`tab-${id}`).classList.add('aktivno');
    
    // Naloži v editor
    const editor = document.getElementById('editor');
    editor.value = tabData.vsebina;
    trenutnaPot = tabData.pot;
    
    // Omogoči gumbe
    document.getElementById('saveBtn').disabled = false;
    document.getElementById('runBtn').disabled = false;
    document.getElementById('copyBtn').disabled = false;
    
    setStatus(`Urejanje: ${tabData.ime}`);
    
    // Samodejno osveži predogled (če je PHP/HTML)
    const ext = tabData.ime.split('.').pop();
    if (ext === 'php' || ext === 'html' || ext === 'htm') {
        predogledDatoteke(tabData.pot);
    } else {
        document.getElementById('previewFrame').srcdoc = `<html><body style='background:#0a0e18;color:#8ba0c0;display:flex;align-items:center;justify-content:center;height:100%'><p>📄 ${tabData.ime}<br>Predogled ni na voljo za to vrsto datoteke.<br><br>Podprti formati: .php, .html, .htm</p></body></html>`;
    }
}

async function zapriTab(id, event) {
    if (event) event.stopPropagation();
    
    const tabData = odprtiTabi.find(t => t.id === id);
    if (tabData && tabData.spremenjeno) {
        if (!confirm(`Datoteka ${tabData.ime} ni shranjena. Zapri vseeno?`)) return;
    }
    
    const index = odprtiTabi.findIndex(t => t.id === id);
    if (index !== -1) odprtiTabi.splice(index, 1);
    
    const tabElement = document.getElementById(`tab-${id}`);
    if (tabElement) tabElement.remove();
    
    if (odprtiTabi.length > 0) {
        const zadnji = odprtiTabi[odprtiTabi.length - 1];
        aktivirajTab(zadnji.id);
    } else {
        aktivniTab = null;
        trenutnaPot = '';
        document.getElementById('editor').value = '';
        document.getElementById('saveBtn').disabled = true;
        document.getElementById('runBtn').disabled = true;
        document.getElementById('copyBtn').disabled = true;
        setStatus('Pripravljen');
    }
}

// ============================================================
// 4. SHRANJEVANJE
// ============================================================
async function shraniTrenutno() {
    if (!aktivniTab) return;
    
    const tabData = odprtiTabi.find(t => t.id === aktivniTab);
    if (!tabData) return;
    
    const novaVsebina = document.getElementById('editor').value;
    const res = await api('shrani_vsebino', { pot: tabData.pot, vsebina: novaVsebina });
    
    if (res.uspeh) {
        tabData.vsebina = novaVsebina;
        tabData.spremenjeno = false;
        setStatus(`Shranjeno: ${tabData.ime}`);
        
        // Osveži predogled
        const ext = tabData.ime.split('.').pop();
        if (ext === 'php' || ext === 'html' || ext === 'htm') {
            predogledDatoteke(tabData.pot);
        }
    } else {
        setStatus('Napaka pri shranjevanju!', 'error');
    }
}

// ============================================================
// 5. PREDOGLED (mini okno)
// ============================================================
async function predogledDatoteke(pot) {
    setStatus(`Predogled: ${pot}`);
    
    const ext = pot.split('.').pop();
    const frame = document.getElementById('previewFrame');
    
    if (ext === 'php') {
        // PHP datoteke - pokaži v iframe (z URL)
        const url = '/' + pot;
        frame.src = url;
    } else if (ext === 'html' || ext === 'htm') {
        // HTML - lahko direktno
        const res = await api('pridobi_vsebino', { pot });
        if (res.uspeh) {
            frame.srcdoc = res.vsebina;
        } else {
            frame.srcdoc = `<html><body style='background:#0a0e18;color:#ef4444;padding:20px'><p>Napaka pri nalaganju: ${pot}</p></body></html>`;
        }
    } else if (ext === 'css' || ext === 'js') {
        frame.srcdoc = `<html><head><style>${ext === 'css' ? 'body{background:#0a0e18;color:#e0d8c8;padding:20px;font-family:monospace} pre{white-space:pre-wrap}' : ''}</style></head><body><pre>${await (await api('pridobi_vsebino', { pot })).vsebina || 'Ni vsebine'}</pre></body></html>`;
    } else {
        frame.srcdoc = `<html><body style='background:#0a0e18;color:#8ba0c0;display:flex;align-items:center;justify-content:center;height:100%'><p>📄 ${pot}<br>Predogled ni na voljo.</p></body></html>`;
    }
}

function osveziPredogled() {
    if (aktivniTab) {
        const tabData = odprtiTabi.find(t => t.id === aktivniTab);
        if (tabData) predogledDatoteke(tabData.pot);
    }
}

function odpriVNovemOknu() {
    if (aktivniTab) {
        const tabData = odprtiTabi.find(t => t.id === aktivniTab);
        if (tabData) window.open('/' + tabData.pot, '_blank');
    }
}

// ============================================================
// 6. DETEKCIJA SPREMEMB V EDITORJU
// ============================================================
function setupEditorWatcher() {
    const editor = document.getElementById('editor');
    editor.addEventListener('input', () => {
        if (aktivniTab) {
            const tabData = odprtiTabi.find(t => t.id === aktivniTab);
            if (tabData && editor.value !== tabData.vsebina) {
                tabData.spremenjeno = true;
                const tabElement = document.getElementById(`tab-${aktivniTab}`);
                if (tabElement && !tabElement.querySelector('.tab-close')?.innerHTML.includes('●')) {
                    tabElement.querySelector('.tab-close').innerHTML = '●';
                }
                setStatus('✏️ Nespremenjeno', 'warning');
            }
        }
    });
}

// ============================================================
// 7. KOPIRANJE POTI
// ============================================================
function kopirajPot() {
    if (trenutnaPot) {
        navigator.clipboard.writeText(trenutnaPot);
        setStatus(`Kopirano: ${trenutnaPot}`);
    }
}

// ============================================================
// 8. TIPKOVNE BLIŽNJICE
// ============================================================
document.addEventListener('keydown', (e) => {
    if (e.ctrlKey && e.key === 's') {
        e.preventDefault();
        shraniTrenutno();
    }
    if (e.ctrlKey && e.key === 'r') {
        e.preventDefault();
        osveziPredogled();
    }
});

// ============================================================
// 9. ZAGON
// ============================================================
async function init() {
    await naloziKorenskeMape();
    await naloziStrukturo();
    setupEditorWatcher();
    
    document.getElementById('saveBtn').onclick = shraniTrenutno;
    document.getElementById('runBtn').onclick = () => { if(aktivniTab) predogledDatoteke(odprtiTabi.find(t=>t.id===aktivniTab)?.pot); };
    document.getElementById('copyBtn').onclick = kopirajPot;
    document.getElementById('refreshPreview').onclick = osveziPredogled;
    document.getElementById('openNewWindow').onclick = odpriVNovemOknu;
    
    setStatus('ULTIMA NADZOR pripravljen');
}

init();
</script>
</body>
</html>