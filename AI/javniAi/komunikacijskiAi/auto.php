<?php
/**
 * avto.php – AVTOMATIZIRAN PREGLED IN POPRAVKI
 * ============================================================
 * 1. Klikneš "Preglej vse" → prebere vse datoteke iz izbrane mape
 * 2. Pošlje v DeepSeek API (Revizor)
 * 3. Dobiš seznam napak
 * 4. Klikneš "Popravi" → Koder predlaga popravke
 * 5. Klikneš "Shrani" → popravki se zapišejo v datoteke
 * ============================================================
 */
$ROOT = __DIR__;
$API_KEY = 'sk-da7741c3f9c04ce69c64075d8ead0bca';

// ── OBDELAVA POST ──────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json; charset=utf-8');
    $akcija = $_POST['akcija'] ?? '';
    
    try {
        switch ($akcija) {
            case 'preberi':
                $mapa = $_POST['mapa'] ?? '';
                $pot = $ROOT . '/' . ltrim($mapa, '/');
                if (!is_dir($pot)) throw new Exception("Mapa '$mapa' ne obstaja.");
                
                $datoteke = [];
                $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($pot, RecursiveDirectoryIterator::SKIP_DOTS));
                foreach ($it as $f) {
                    if ($f->isFile() && $f->getExtension() === 'php') {
                        $rel = str_replace($ROOT . '/', '', $f->getPathname());
                        $datoteke[] = ['pot' => $rel, 'vsebina' => file_get_contents($f->getPathname())];
                    }
                }
                echo json_encode(['uspeh' => true, 'stevilo' => count($datoteke), 'datoteke' => $datoteke]);
                exit;
                
            case 'analiziraj':
                $datoteke = json_decode($_POST['datoteke'] ?? '[]', true);
                if (empty($datoteke)) throw new Exception("Ni datotek.");
                
                $sporocilo = "Preglej naslednje datoteke in naštej vse napake (sintaksa, varnost, arhitektura):\n\n";
                foreach ($datoteke as $d) {
                    $sporocilo .= "=== " . $d['pot'] . " ===\n" . substr($d['vsebina'], 0, 800) . "\n\n";
                }
                
                $odg = pokliciAPI("Ti si REVIZOR. Naštej napake v datotekah. Odgovori v slovenščini.", $sporocilo);
                echo json_encode(['uspeh' => true, 'odgovor' => $odg]);
                exit;
                
            case 'popravi':
                $analiza = $_POST['analiza'] ?? '';
                $datoteke = json_decode($_POST['datoteke'] ?? '[]', true);
                if (empty($analiza)) throw new Exception("Ni analize.");
                
                $sistem = "Ti si KODER. Na podlagi analize predlagaj konkretne popravke. Vrni JSON: [{\"pot\":\"SISTEM/Baza.php\",\"popravek\":\"<?php ...\"}]";
                $sporocilo = "Analiza:\n" . $analiza . "\n\nDatoteke:\n";
                foreach ($datoteke as $d) {
                    $sporocilo .= "=== " . $d['pot'] . " ===\n" . substr($d['vsebina'], 0, 500) . "\n";
                }
                $odg = pokliciAPI($sistem, $sporocilo);
                
                // Poskusi parsati JSON iz odgovora
                $json = json_decode($odg, true);
                if (!$json) {
                    preg_match('/\[[\s\S]*\]/', $odg, $matches);
                    if (!empty($matches)) {
                        $json = json_decode($matches[0], true);
                    }
                }
                if (!$json) throw new Exception("Koder ni vrnil veljavnega JSON.");
                
                echo json_encode(['uspeh' => true, 'popravki' => $json]);
                exit;
                
            case 'shrani':
                $popravki = json_decode($_POST['popravki'] ?? '[]', true);
                if (empty($popravki)) throw new Exception("Ni popravkov.");
                
                $rezultat = [];
                foreach ($popravki as $p) {
                    $pot = $ROOT . '/' . ltrim($p['pot'], '/');
                    if (!file_exists($pot)) {
                        $rezultat[] = ['pot' => $p['pot'], 'status' => '⚠️ Datoteka ne obstaja'];
                        continue;
                    }
                    // Varnostna kopija
                    copy($pot, $pot . '.bak');
                    file_put_contents($pot, $p['popravek']);
                    $rezultat[] = ['pot' => $p['pot'], 'status' => '✅ Shranjeno'];
                }
                echo json_encode(['uspeh' => true, 'rezultat' => $rezultat]);
                exit;
                
            default:
                throw new Exception("Neznana akcija");
        }
    } catch (Exception $e) {
        echo json_encode(['uspeh' => false, 'napaka' => $e->getMessage()]);
    }
    exit;
}

// ── FUNKCIJA ZA API KLIC ──────────────────────────────────────
function pokliciAPI(string $sistem, string $sporocilo): string {
    global $API_KEY;
    $ch = curl_init('https://api.deepseek.com/v1/chat/completions');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode([
            'model' => 'deepseek-chat',
            'temperature' => 0.3,
            'messages' => [
                ['role' => 'system', 'content' => $sistem],
                ['role' => 'user', 'content' => $sporocilo]
            ]
        ]),
        CURLOPT_HTTPHEADER => ['Content-Type: application/json', 'Authorization: Bearer ' . $API_KEY],
        CURLOPT_TIMEOUT => 60,
    ]);
    $r = curl_exec($ch);
    $info = curl_getinfo($ch);
    curl_close($ch);
    if ($info['http_code'] !== 200) {
        throw new Exception("HTTP " . $info['http_code']);
    }
    $d = json_decode($r, true);
    return $d['choices'][0]['message']['content'] ?? '';
}
?>
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><title>Avto pregled</title>
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{background:#0a0a1a;color:#d4c5a9;font-family:monospace;padding:20px}
.box{max-width:1100px;margin:auto;background:#0f1420;padding:24px;border-radius:12px;border:1px solid #1a2535}
h1{color:#e8c84a;font-size:20px;margin-bottom:4px}
.sub{color:#6b7494;font-size:13px;margin-bottom:16px}
.gumbi{display:flex;gap:10px;flex-wrap:wrap;margin-bottom:14px}
.gumb{padding:10px 20px;border:none;border-radius:6px;cursor:pointer;font-family:monospace;font-size:13px}
.gumb-p{background:#4f6ef7;color:#fff}.gumb-p:hover{background:#3d5de6}
.gumb-g{background:#27ae60;color:#fff}.gumb-g:hover{background:#1e8449}
.gumb-r{background:#c0392b;color:#fff}.gumb-r:hover{background:#a93226}
.gumb-s{background:#1a2535;color:#d4c5a9;border:1px solid #252a3d}.gumb-s:hover{border-color:#4f6ef7}
.gumb:disabled{opacity:0.4;cursor:not-allowed}
.row{display:flex;gap:10px;margin-bottom:12px;flex-wrap:wrap}
.row input,.row select{background:#0a0d14;border:1px solid #252a3d;border-radius:6px;padding:8px 12px;color:#d4c5a9;font-family:monospace;font-size:13px;flex:1}
#izpis{background:#05080f;border:1px solid #1a2535;border-radius:6px;padding:14px;max-height:500px;overflow-y:auto;font-size:12px;white-space:pre-wrap;line-height:1.6;font-family:monospace}
.napaka{color:#e74c3c}.ok{color:#27ae60}
</style>
</head>
<body>
<div class="box">
<h1>🤖 Avto pregled in popravki</h1>
<div class="sub">1. Izberi mapo → 2. Klikni "Preglej" → 3. Klikni "Popravi" → 4. Klikni "Shrani"</div>

<div class="row">
    <input type="text" id="mapa" value="RAZVOJ" placeholder="Ime mape (npr. SISTEM, GLOBALNO, RAZVOJ)">
    <button class="gumb gumb-p" onclick="preglej()">📋 Preglej</button>
    <button class="gumb gumb-g" id="popravi-gumb" disabled onclick="popravi()">✏️ Popravi</button>
    <button class="gumb gumb-r" id="shrani-gumb" disabled onclick="shrani()">💾 Shrani</button>
</div>

<div id="status" style="font-size:12px;color:#6b7494;margin-bottom:10px;">Pripravljen.</div>
<div id="izpis"></div>
</div>

<script>
let trenutneDatoteke = [];
let analiza = '';
let popravki = [];

async function preglej() {
    const mapa = document.getElementById('mapa').value.trim() || '';
    document.getElementById('status').textContent = '⏳ Berem datoteke...';
    document.getElementById('izpis').textContent = '⏳ Berem datoteke iz ' + (mapa || 'korena') + '...';
    
    try {
        // 1. Preberi datoteke
        const fd1 = new FormData();
        fd1.append('akcija', 'preberi');
        fd1.append('mapa', mapa);
        const r1 = await fetch(location.href, {method:'POST', body:fd1});
        const d1 = await r1.json();
        if (!d1.uspeh) throw new Error(d1.napaka);
        
        trenutneDatoteke = d1.datoteke;
        document.getElementById('izpis').textContent = `📂 Prebranih ${d1.stevilo} datotek.\n\n⏳ Pošiljam v analizo...`;
        document.getElementById('status').textContent = `📂 ${d1.stevilo} datotek, analiziram...`;
        
        // 2. Analiziraj
        const fd2 = new FormData();
        fd2.append('akcija', 'analiziraj');
        fd2.append('datoteke', JSON.stringify(trenutneDatoteke));
        const r2 = await fetch(location.href, {method:'POST', body:fd2});
        const d2 = await r2.json();
        if (!d2.uspeh) throw new Error(d2.napaka);
        
        analiza = d2.odgovor;
        document.getElementById('izpis').textContent = `📋 REVIZOR:\n\n${analiza}`;
        document.getElementById('status').textContent = '✅ Analiza končana. Klikni "Popravi".';
        document.getElementById('popravi-gumb').disabled = false;
        document.getElementById('shrani-gumb').disabled = true;
        popravki = [];
        
    } catch(e) {
        document.getElementById('izpis').textContent = '❌ ' + e.message;
        document.getElementById('status').textContent = '❌ Napaka';
    }
}

async function popravi() {
    if (!analiza || trenutneDatoteke.length === 0) {
        alert('Najprej izvedi pregled.');
        return;
    }
    document.getElementById('status').textContent = '⏳ Koder pripravlja popravke...';
    document.getElementById('izpis').textContent = '⏳ Koder pripravlja popravke...';
    
    try {
        const fd = new FormData();
        fd.append('akcija', 'popravi');
        fd.append('analiza', analiza);
        fd.append('datoteke', JSON.stringify(trenutneDatoteke));
        const r = await fetch(location.href, {method:'POST', body:fd});
        const d = await r.json();
        if (!d.uspeh) throw new Error(d.napaka);
        
        popravki = d.popravki;
        let izpis = '✏️ KODER PREDLAGA:\n\n';
        if (Array.isArray(popravki)) {
            popravki.forEach(p => {
                izpis += `=== ${p.pot} ===\n${p.popravek}\n\n`;
            });
        } else {
            izpis += JSON.stringify(popravki, null, 2);
        }
        document.getElementById('izpis').textContent = izpis;
        document.getElementById('status').textContent = '✅ Popravki pripravljeni. Klikni "Shrani".';
        document.getElementById('shrani-gumb').disabled = false;
        
    } catch(e) {
        document.getElementById('izpis').textContent = '❌ ' + e.message;
        document.getElementById('status').textContent = '❌ Napaka';
    }
}

async function shrani() {
    if (!popravki || popravki.length === 0) {
        alert('Ni popravkov za shranjevanje.');
        return;
    }
    document.getElementById('status').textContent = '⏳ Shranjujem popravke...';
    document.getElementById('izpis').textContent = '⏳ Shranjujem...';
    
    try {
        const fd = new FormData();
        fd.append('akcija', 'shrani');
        fd.append('popravki', JSON.stringify(popravki));
        const r = await fetch(location.href, {method:'POST', body:fd});
        const d = await r.json();
        if (!d.uspeh) throw new Error(d.napaka);
        
        let izpis = '💾 SHRANJENO:\n\n';
        d.rezultat.forEach(r => {
            izpis += `${r.status} ${r.pot}\n`;
        });
        document.getElementById('izpis').textContent = izpis;
        document.getElementById('status').textContent = '✅ Vse shranjeno. (Varnostne kopije .bak)';
        document.getElementById('shrani-gumb').disabled = true;
        
    } catch(e) {
        document.getElementById('izpis').textContent = '❌ ' + e.message;
        document.getElementById('status').textContent = '❌ Napaka';
    }
}
</script>
</body>
</html>