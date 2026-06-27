<?php
/**
 * ============================================================
 * ASTRA/astra_nadzor.php — Nadzorna plošča
 * VERZIJA: v116 (18.6.2026)
 * ============================================================
 * VSTOPNA TOČKA: Vključi pot.php + pot_ai.php
 * Lastnik tukaj:
 *   - Zažene agente
 *   - Pregleda predloge (nepotrjeno/)
 *   - Potrdi ali zavrne predloge
 *   - Prebere poročila in dnevnik
 * ============================================================
 */

declare(strict_types=1);

define('ASTRA_VSTOP', true);
define('AI_VSTOP', true);

$root = realpath(__DIR__ . '/..');
if ($root === false) die('ROOT ni določljiv.');

require_once $root . '/pot.php';
require_once POT_AI . '/pot_ai.php';
require_once POT_AI . '/varnost.php';

// ============================================================
// PRIJAVA — enostavna geslo zaščita
// ============================================================
session_name('astra_nadzor');
session_start();

// Geslo nastavi v config.php ali tukaj
$GESLO = (function() use ($root): string {
    $cfg = $root . '/AI/config.php';
    if (file_exists($cfg)) {
        $c = include $cfg;
        return $c['astra_geslo'] ?? 'astra2026';
    }
    return 'astra2026';
})();

if (isset($_POST['geslo'])) {
    if ($_POST['geslo'] === $GESLO) {
        $_SESSION['prijavljen'] = true;
    } else {
        $napakaPrijave = 'Napačno geslo.';
    }
}

if (isset($_GET['odjava'])) {
    session_destroy();
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

$prijavljen = $_SESSION['prijavljen'] ?? false;

// ============================================================
// AJAX AKCIJE (JSON odgovori)
// ============================================================
if ($prijavljen && isset($_POST['akcija'])) {
    header('Content-Type: application/json; charset=utf-8');

    $akcija = $_POST['akcija'];

    try {
        switch ($akcija) {

            // ── Zaženi enega agenta ──────────────────────────
            case 'zazeni_agenta':
                $agent = preg_replace('/[^a-z_]/', '', $_POST['agent'] ?? '');
                $datoteke = [
                    'nadzornik'   => POT_AI_ARHITEKTURNI . '/deepseek_nadzornik.php',
                    'nacrtovalec' => POT_AI_ARHITEKTURNI . '/deepseek_nacrtovalec.php',
                    'arhitekt'    => POT_AI_ARHITEKTURNI . '/deepseek_arhitekt.php',
                    'koder'       => POT_AI_ARHITEKTURNI . '/deepseek_koder.php',
                    'integrator'  => POT_AI_ARHITEKTURNI . '/deepseek_integrator.php',
                    'revizor'     => POT_AI_ARHITEKTURNI . '/deepseek_revizor.php',
                ];
                if (!isset($datoteke[$agent])) throw new Exception("Neznan agent: $agent");

                ob_start();
                include $datoteke[$agent];
                $izpis = ob_get_clean();
                echo json_encode(['uspeh' => true, 'izpis' => $izpis]);
                break;

            // ── Zaženi vse agente po vrsti ───────────────────
            case 'zazeni_cikel':
                $cikel = ['nadzornik', 'nacrtovalec', 'arhitekt', 'koder', 'integrator', 'revizor'];
                $rezultati = [];
                foreach ($cikel as $agent) {
                    $datoteka = POT_AI_ARHITEKTURNI . "/deepseek_{$agent}.php";
                    ob_start();
                    include $datoteka;
                    $rezultati[$agent] = ob_get_clean();
                }
                echo json_encode(['uspeh' => true, 'rezultati' => $rezultati]);
                break;

            // ── Seznam predlogov ─────────────────────────────
            case 'seznam_predlogov':
                $predlogi = [];
                if (is_dir(POT_AI_NEPOTRJENO)) {
                    foreach (glob(POT_AI_NEPOTRJENO . '/*/predlog.json') as $f) {
                        $p = json_decode(file_get_contents($f), true);
                        if ($p) $predlogi[] = $p;
                    }
                }
                echo json_encode(['uspeh' => true, 'predlogi' => $predlogi]);
                break;

            // ── Potrdi predlog ───────────────────────────────
            case 'potrdi_predlog':
                $id = preg_replace('/[^a-zA-Z0-9_]/', '', $_POST['id'] ?? '');
                $predlogDir  = POT_AI_NEPOTRJENO . '/' . $id;
                $predlogFile = $predlogDir . '/predlog.json';

                if (!file_exists($predlogFile)) throw new Exception("Predlog $id ne obstaja.");

                $predlog = json_decode(file_get_contents($predlogFile), true);
                $ciljna  = ROOT . '/' . ltrim($predlog['ciljna_pot'], '/');
                $vsebina = file_get_contents($predlogDir . '/vsebina.txt');

                // Varnostni preverek pred zapisom
                $dostop = ai_preveri_dostop($ciljna);
                if ($dostop === 'blokirano') throw new Exception("Pot ni dovoljena: " . $predlog['ciljna_pot']);

                $dir = dirname($ciljna);
                if (!is_dir($dir)) mkdir($dir, 0755, true);
                file_put_contents($ciljna, $vsebina);

                $predlog['status']   = 'potrjeno';
                $predlog['potrjeno'] = date('Y-m-d H:i:s');
                file_put_contents($predlogFile, json_encode($predlog, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

                ai_log("POTRJENO", "lastnik", $ciljna, "predlog $id potrjen");
                echo json_encode(['uspeh' => true, 'sporocilo' => "Predlog $id potrjen in datoteka zapisana."]);
                break;

            // ── Zavrni predlog ───────────────────────────────
            case 'zavrni_predlog':
                $id = preg_replace('/[^a-zA-Z0-9_]/', '', $_POST['id'] ?? '');
                $predlogDir  = POT_AI_NEPOTRJENO . '/' . $id;
                $predlogFile = $predlogDir . '/predlog.json';

                if (!file_exists($predlogFile)) throw new Exception("Predlog $id ne obstaja.");

                $predlog = json_decode(file_get_contents($predlogFile), true);
                $predlog['status']   = 'zavrnjeno';
                $predlog['zavrnjeno'] = date('Y-m-d H:i:s');
                $predlog['razlog']   = htmlspecialchars($_POST['razlog'] ?? '');
                file_put_contents($predlogFile, json_encode($predlog, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

                // Premakni v karanteno
                $karantena = POT_AI_KARANTENA . '/' . $id;
                if (!is_dir(dirname($karantena))) mkdir(dirname($karantena), 0755, true);
                rename($predlogDir, $karantena);

                ai_log("ZAVRNJENO", "lastnik", $predlogDir, "predlog $id zavrnjen");
                echo json_encode(['uspeh' => true, 'sporocilo' => "Predlog $id zavrnjen → karantena."]);
                break;

            // ── Poročila ─────────────────────────────────────
            case 'preberi_porocila':
                $porocila = [];
                if (is_dir(POT_AI_POROCILA)) {
                    $files = array_reverse(glob(POT_AI_POROCILA . '/*.json'));
                    foreach (array_slice($files, 0, 20) as $f) {
                        $p = json_decode(file_get_contents($f), true);
                        if ($p) $porocila[] = $p;
                    }
                }
                echo json_encode(['uspeh' => true, 'porocila' => $porocila]);
                break;

            // ── Dnevnik ──────────────────────────────────────
            case 'preberi_dnevnik':
                $log = POT_AI_NALOGE . '/ai_log.txt';
                $vsebina = file_exists($log) ? file_get_contents($log) : '(prazen)';
                // Zadnjih 100 vrstic
                $vrstice = array_slice(explode("\n", trim($vsebina)), -100);
                echo json_encode(['uspeh' => true, 'vsebina' => implode("\n", $vrstice)]);
                break;

            // ── Ustvari nalogo za agenta ─────────────────────
            case 'nova_naloga':
                $agent  = preg_replace('/[^a-z_]/', '', $_POST['agent'] ?? 'koder');
                $naslov = htmlspecialchars($_POST['naslov'] ?? 'Nova naloga');
                $opis   = htmlspecialchars($_POST['opis'] ?? '');

                $nalogaDir = POT_AI_NALOGE . '/' . $agent;
                if (!is_dir($nalogaDir)) mkdir($nalogaDir, 0755, true);

                $id = date('Ymd_His') . '_' . substr(md5($naslov), 0, 6);
                $naloga = [
                    'id'      => $id,
                    'naslov'  => $naslov,
                    'opis'    => $opis,
                    'status'  => 'odprta',
                    'ustvari' => date('Y-m-d H:i:s'),
                ];
                file_put_contents($nalogaDir . '/' . $id . '.json', json_encode($naloga, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
                ai_log("NALOGA", "lastnik", $nalogaDir, "nova naloga: $naslov");
                echo json_encode(['uspeh' => true, 'id' => $id]);
                break;

            default:
                throw new Exception("Neznana akcija: $akcija");
        }
    } catch (Throwable $e) {
        echo json_encode(['uspeh' => false, 'napaka' => $e->getMessage()]);
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="sl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>ASTRA — Nadzorna plošča v116</title>
<style>
  :root {
    --bg:      #0a0d14;
    --bg2:     #111520;
    --bg3:     #181d2e;
    --rob:     #252a3d;
    --tekst:   #c8cfe8;
    --muted:   #6b7494;
    --modra:   #4f6ef7;
    --zelena:  #3ecf8e;
    --rdeca:   #f75f5f;
    --rumena:  #f7c94f;
    --font:    'Segoe UI', system-ui, sans-serif;
    --mono:    'Cascadia Code', 'Fira Code', monospace;
  }

  * { box-sizing: border-box; margin: 0; padding: 0; }
  body { background: var(--bg); color: var(--tekst); font-family: var(--font); min-height: 100vh; }

  /* ── PRIJAVA ── */
  .prijava-ovojnica {
    display: flex; align-items: center; justify-content: center; min-height: 100vh;
  }
  .prijava-kartica {
    background: var(--bg2); border: 1px solid var(--rob); border-radius: 12px;
    padding: 40px; width: 360px; text-align: center;
  }
  .prijava-kartica h1 { font-size: 24px; color: var(--modra); margin-bottom: 8px; }
  .prijava-kartica p  { color: var(--muted); margin-bottom: 24px; font-size: 13px; }

  /* ── LAYOUT ── */
  .okvir { display: flex; height: 100vh; overflow: hidden; }
  .stranska { width: 220px; background: var(--bg2); border-right: 1px solid var(--rob); padding: 20px 0; flex-shrink: 0; display: flex; flex-direction: column; }
  .stranska h2 { padding: 0 20px 16px; font-size: 13px; text-transform: uppercase; letter-spacing: 1px; color: var(--muted); }
  .vsebina { flex: 1; overflow-y: auto; padding: 24px; }

  /* ── NAVIGACIJA ── */
  .nav-gumb {
    display: block; width: 100%; padding: 10px 20px; background: none; border: none;
    color: var(--muted); cursor: pointer; text-align: left; font-size: 14px; transition: all .15s;
    border-left: 3px solid transparent;
  }
  .nav-gumb:hover { color: var(--tekst); background: var(--bg3); }
  .nav-gumb.aktiven { color: var(--modra); border-left-color: var(--modra); background: var(--bg3); }
  .nav-sep { border: none; border-top: 1px solid var(--rob); margin: 8px 0; }

  .stranska-dno { margin-top: auto; padding: 16px 20px; }
  .verzija-badge { font-size: 11px; color: var(--muted); }

  /* ── PLOŠČE ── */
  .plosca { display: none; }
  .plosca.aktiven { display: block; }
  .plosca h2 { font-size: 20px; margin-bottom: 20px; }

  /* ── KARTICE ── */
  .kartica {
    background: var(--bg2); border: 1px solid var(--rob); border-radius: 10px;
    padding: 16px; margin-bottom: 16px;
  }
  .kartica h3 { font-size: 14px; margin-bottom: 8px; }

  /* ── STATISTIKE ── */
  .stat-mreza { display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 12px; margin-bottom: 24px; }
  .stat-kartica { background: var(--bg3); border: 1px solid var(--rob); border-radius: 8px; padding: 16px; text-align: center; }
  .stat-kartica .stevilka { font-size: 32px; font-weight: 700; color: var(--modra); }
  .stat-kartica .oznaka { font-size: 11px; color: var(--muted); margin-top: 4px; text-transform: uppercase; letter-spacing: .5px; }

  /* ── AGENTI ── */
  .agenti-mreza { display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 12px; margin-bottom: 24px; }
  .agent-kartica {
    background: var(--bg3); border: 1px solid var(--rob); border-radius: 10px;
    padding: 16px; text-align: center;
  }
  .agent-kartica .ikona { font-size: 28px; margin-bottom: 8px; }
  .agent-kartica h3 { font-size: 14px; margin-bottom: 4px; }
  .agent-kartica p { font-size: 12px; color: var(--muted); margin-bottom: 12px; }

  /* ── GUMBI ── */
  .gumb {
    display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px;
    border-radius: 6px; border: none; cursor: pointer; font-size: 13px;
    font-family: var(--font); transition: all .15s; text-decoration: none;
  }
  .gumb-primarni { background: var(--modra); color: #fff; }
  .gumb-primarni:hover { background: #3d5de6; }
  .gumb-zeleni { background: var(--zelena); color: #0a150f; }
  .gumb-zeleni:hover { background: #2db874; }
  .gumb-rdeci { background: var(--rdeca); color: #fff; }
  .gumb-rdeci:hover { background: #e04a4a; }
  .gumb-sekundarni { background: var(--bg3); color: var(--tekst); border: 1px solid var(--rob); }
  .gumb-sekundarni:hover { border-color: var(--modra); }
  .gumb:disabled { opacity: .5; cursor: not-allowed; }

  /* ── TERMINAL ── */
  .terminal {
    background: #060912; border: 1px solid var(--rob); border-radius: 8px;
    padding: 16px; font-family: var(--mono); font-size: 12px; color: #8af28a;
    min-height: 120px; max-height: 300px; overflow-y: auto; white-space: pre-wrap;
    margin-top: 16px;
  }

  /* ── PREDLOGI ── */
  .predlog-kartica {
    background: var(--bg2); border: 1px solid var(--rob); border-radius: 10px; padding: 16px; margin-bottom: 12px;
  }
  .predlog-glava { display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px; }
  .predlog-id { font-family: var(--mono); font-size: 11px; color: var(--muted); }
  .status-znacka {
    padding: 2px 8px; border-radius: 4px; font-size: 11px; font-weight: 600;
  }
  .status-čaka { background: #2a2000; color: var(--rumena); }
  .status-odobreno { background: #001a0f; color: var(--zelena); }
  .status-zavrnjeno { background: #1a0000; color: var(--rdeca); }
  .status-potrebna_popravka { background: #1a1000; color: var(--rumena); }
  .predlog-pot { font-family: var(--mono); font-size: 12px; color: var(--modra); margin: 4px 0; }
  .predlog-opis { font-size: 13px; color: var(--muted); }
  .predlog-akcije { display: flex; gap: 8px; margin-top: 12px; }

  /* ── FORMA ── */
  .forma-polje { margin-bottom: 14px; }
  .forma-polje label { display: block; font-size: 12px; color: var(--muted); margin-bottom: 6px; }
  .forma-polje input, .forma-polje textarea, .forma-polje select {
    width: 100%; background: var(--bg3); border: 1px solid var(--rob); border-radius: 6px;
    padding: 8px 12px; color: var(--tekst); font-family: var(--font); font-size: 13px;
  }
  .forma-polje input:focus, .forma-polje textarea:focus {
    outline: none; border-color: var(--modra);
  }
  .forma-polje textarea { resize: vertical; min-height: 80px; }

  /* ── POROČILA ── */
  .porocilo-vrstica {
    display: flex; align-items: center; gap: 12px; padding: 8px 0;
    border-bottom: 1px solid var(--rob); font-size: 13px;
  }
  .porocilo-agent { color: var(--modra); font-weight: 600; min-width: 90px; }
  .porocilo-cas { color: var(--muted); font-size: 12px; min-width: 140px; }
  .porocilo-stanje { color: var(--zelena); }

  /* ── DNEVNIK ── */
  .dnevnik { font-family: var(--mono); font-size: 11px; background: #060912; border: 1px solid var(--rob); border-radius: 8px; padding: 16px; max-height: 500px; overflow-y: auto; white-space: pre; color: #a8d4a8; }

  /* ── NALAGANJE ── */
  .nalaganje { display: inline-block; width: 14px; height: 14px; border: 2px solid var(--rob); border-top-color: var(--modra); border-radius: 50%; animation: vrti 0.6s linear infinite; }
  @keyframes vrti { to { transform: rotate(360deg); } }

  /* ── SPOROCILO ── */
  .sporocilo { padding: 10px 14px; border-radius: 6px; font-size: 13px; margin-bottom: 12px; }
  .sporocilo-napaka { background: #1a0000; border: 1px solid var(--rdeca); color: var(--rdeca); }
  .sporocilo-uspeh  { background: #001a0f; border: 1px solid var(--zelena); color: var(--zelena); }
</style>
</head>
<body>

<?php if (!$prijavljen): ?>
<!-- ════════════════════════════════════════════════════════════
     PRIJAVA
════════════════════════════════════════════════════════════ -->
<div class="prijava-ovojnica">
  <div class="prijava-kartica">
    <h1>🌌 ASTRA</h1>
    <p>Nadzorna plošča — <?= SISTEM_VERZIJA ?></p>
    <?php if (isset($napakaPrijave)): ?>
      <div class="sporocilo sporocilo-napaka"><?= htmlspecialchars($napakaPrijave) ?></div>
    <?php endif; ?>
    <form method="POST">
      <div class="forma-polje">
        <input type="password" name="geslo" placeholder="Geslo" autofocus style="text-align:center;">
      </div>
      <button type="submit" class="gumb gumb-primarni" style="width:100%">Vstopi</button>
    </form>
  </div>
</div>

<?php else: ?>
<!-- ════════════════════════════════════════════════════════════
     NADZORNA PLOŠČA
════════════════════════════════════════════════════════════ -->
<div class="okvir">

  <!-- Stranska navigacija -->
  <nav class="stranska">
    <h2>🌌 ASTRA</h2>

    <button class="nav-gumb aktiven" onclick="pokazi('pregled', this)">📊 Pregled</button>
    <button class="nav-gumb" onclick="pokazi('agenti', this)">🤖 Agenti</button>
    <button class="nav-gumb" onclick="pokazi('predlogi', this); naloziPredloge()">📋 Predlogi</button>
    <button class="nav-gumb" onclick="pokazi('nova_naloga', this)">➕ Nova naloga</button>
    <hr class="nav-sep">
    <button class="nav-gumb" onclick="pokazi('porocila', this); naloziPorocila()">📈 Poročila</button>
    <button class="nav-gumb" onclick="pokazi('dnevnik', this); naloziDnevnik()">📜 Dnevnik</button>

    <div class="stranska-dno">
      <div class="verzija-badge">v116 · AstraMentalica</div>
      <a href="?odjava" class="gumb gumb-sekundarni" style="margin-top:8px;width:100%;justify-content:center;font-size:12px;">Odjava</a>
    </div>
  </nav>

  <!-- Vsebina -->
  <main class="vsebina">

    <!-- ── PREGLED ── -->
    <div id="plosca-pregled" class="plosca aktiven">
      <h2>📊 Pregled sistema</h2>
      <div class="stat-mreza" id="statistike">
        <div class="stat-kartica"><div class="stevilka" id="stat-predlogi">—</div><div class="oznaka">Predlogi (čaka)</div></div>
        <div class="stat-kartica"><div class="stevilka" id="stat-porocila">—</div><div class="oznaka">Poročila danes</div></div>
        <div class="stat-kartica"><div class="stevilka" id="stat-naloge">—</div><div class="oznaka">Odprte naloge</div></div>
      </div>

      <div class="kartica">
        <h3>⚡ Hitri zagon</h3>
        <p style="color:var(--muted);font-size:13px;margin-bottom:12px;">Zaženi cel AI cikel (Nadzornik → Koder → Revizor)</p>
        <button class="gumb gumb-zeleni" onclick="zazeniCikel()">▶▶ Zaženi cikel</button>
      </div>
      <div class="terminal" id="terminal-pregled">Pripravljeno. Pritisni "Zaženi cikel" za začetek.</div>
    </div>

    <!-- ── AGENTI ── -->
    <div id="plosca-agenti" class="plosca">
      <h2>🤖 AI Agenti</h2>
      <div class="agenti-mreza">
        <div class="agent-kartica">
          <div class="ikona">👁️‍🗨️</div>
          <h3>Nadzornik</h3>
          <p>Koordinira naloge, pregleda predloge, piše dnevno poročilo</p>
          <button class="gumb gumb-primarni" style="width:100%" onclick="zazeni('nadzornik')">Zaženi</button>
        </div>
        <div class="agent-kartica">
          <div class="ikona">🧠</div>
          <h3>Načrtovalec</h3>
          <p>Analizira zahteve, razdeli delo na podnaloge za Koderja</p>
          <button class="gumb gumb-primarni" style="width:100%" onclick="zazeni('nacrtovalec')">Zaženi</button>
        </div>
        <div class="agent-kartica">
          <div class="ikona">🏛️</div>
          <h3>Arhitekt</h3>
          <p>Skenira projekt, zaznava kršitve arhitekturnih pravil</p>
          <button class="gumb gumb-primarni" style="width:100%" onclick="zazeni('arhitekt')">Zaženi</button>
        </div>
        <div class="agent-kartica">
          <div class="ikona">💻</div>
          <h3>Koder</h3>
          <p>Piše in popravlja PHP/JS/CSS kodo po načrtu</p>
          <button class="gumb gumb-primarni" style="width:100%" onclick="zazeni('koder')">Zaženi</button>
        </div>
        <div class="agent-kartica">
          <div class="ikona">🔗</div>
          <h3>Integrator</h3>
          <p>Integrira odobrene predloge, preverja konflikte</p>
          <button class="gumb gumb-primarni" style="width:100%" onclick="zazeni('integrator')">Zaženi</button>
        </div>
        <div class="agent-kartica">
          <div class="ikona">🔎</div>
          <h3>Revizor</h3>
          <p>Varnostni pregled vseh predlogov kode</p>
          <button class="gumb gumb-primarni" style="width:100%" onclick="zazeni('revizor')">Zaženi</button>
        </div>
      </div>
      <div class="terminal" id="terminal-agenti">Čakam na ukaz...</div>
    </div>

    <!-- ── PREDLOGI ── -->
    <div id="plosca-predlogi" class="plosca">
      <h2>📋 Predlogi za potrditev</h2>
      <div id="predlogi-vsebina"><div class="nalaganje"></div></div>
    </div>

    <!-- ── NOVA NALOGA ── -->
    <div id="plosca-nova_naloga" class="plosca">
      <h2>➕ Nova naloga za agenta</h2>
      <div class="kartica" style="max-width:500px">
        <div class="forma-polje">
          <label>Agent</label>
          <select id="naloga-agent">
            <option value="nadzornik">👁️‍🗨️ Nadzornik</option>
            <option value="nacrtovalec">🧠 Načrtovalec</option>
            <option value="arhitekt">🏛️ Arhitekt</option>
            <option value="koder" selected>💻 Koder</option>
            <option value="integrator">🔗 Integrator</option>
            <option value="revizor">🔎 Revizor</option>
          </select>
        </div>
        <div class="forma-polje">
          <label>Naslov naloge</label>
          <input type="text" id="naloga-naslov" placeholder="npr. Popravi validacijo v ADAPTER/vnos.php">
        </div>
        <div class="forma-polje">
          <label>Podroben opis</label>
          <textarea id="naloga-opis" placeholder="Opiši točno, kaj naj agent naredi..."></textarea>
        </div>
        <button class="gumb gumb-zeleni" onclick="dodajNalogo()">➕ Dodaj nalogo</button>
        <div id="naloga-sporocilo" style="margin-top:12px"></div>
      </div>
    </div>

    <!-- ── POROČILA ── -->
    <div id="plosca-porocila" class="plosca">
      <h2>📈 Poročila agentov</h2>
      <div id="porocila-vsebina"><div class="nalaganje"></div></div>
    </div>

    <!-- ── DNEVNIK ── -->
    <div id="plosca-dnevnik" class="plosca">
      <h2>📜 Sistemski dnevnik</h2>
      <button class="gumb gumb-sekundarni" onclick="naloziDnevnik()" style="margin-bottom:12px">🔄 Osveži</button>
      <div class="dnevnik" id="dnevnik-vsebina">Nalagam...</div>
    </div>

  </main>
</div>

<script>
// ── NAVIGACIJA ──────────────────────────────────────────────
function pokazi(id, gumb) {
    document.querySelectorAll('.plosca').forEach(p => p.classList.remove('aktiven'));
    document.querySelectorAll('.nav-gumb').forEach(g => g.classList.remove('aktiven'));
    document.getElementById('plosca-' + id).classList.add('aktiven');
    if (gumb) gumb.classList.add('aktiven');
}

// ── AJAX ─────────────────────────────────────────────────────
async function ajax(podatki) {
    const fd = new FormData();
    for (const [k, v] of Object.entries(podatki)) fd.append(k, v);
    const r = await fetch(location.href, { method: 'POST', body: fd });
    return r.json();
}

// ── AGENTI ───────────────────────────────────────────────────
async function zazeni(agent) {
    const t = document.getElementById('terminal-agenti');
    t.textContent = `▶ Zaganjam ${agent}...\n`;
    const r = await ajax({ akcija: 'zazeni_agenta', agent });
    t.textContent += r.uspeh ? (r.izpis || '(brez izpisa)') + '\n✅ Končano.' : '❌ ' + r.napaka;
    t.scrollTop = t.scrollHeight;
    naloziStatistike();
}

async function zazeniCikel() {
    const t = document.getElementById('terminal-pregled');
    t.textContent = '▶▶ Zaganjam cel cikel...\n';
    const r = await ajax({ akcija: 'zazeni_cikel' });
    if (r.uspeh) {
        for (const [a, izpis] of Object.entries(r.rezultati)) {
            t.textContent += `\n── ${a.toUpperCase()} ──\n${izpis || '(brez izpisa)'}`;
        }
        t.textContent += '\n\n✅ Cikel zaključen.';
    } else {
        t.textContent += '❌ ' + r.napaka;
    }
    t.scrollTop = t.scrollHeight;
    naloziStatistike();
}

// ── PREDLOGI ──────────────────────────────────────────────────
async function naloziPredloge() {
    const vsebina = document.getElementById('predlogi-vsebina');
    vsebina.innerHTML = '<div class="nalaganje"></div>';
    const r = await ajax({ akcija: 'seznam_predlogov' });

    if (!r.uspeh) { vsebina.innerHTML = '<p style="color:var(--rdeca)">Napaka: ' + r.napaka + '</p>'; return; }

    if (!r.predlogi.length) {
        vsebina.innerHTML = '<div class="kartica"><p style="color:var(--muted)">✅ Ni predlogov, ki čakajo na potrditev.</p></div>';
        return;
    }

    vsebina.innerHTML = r.predlogi.map(p => `
        <div class="predlog-kartica">
            <div class="predlog-glava">
                <span class="predlog-id">${p.id}</span>
                <span class="status-znacka status-${p.status ?? 'čaka'}">${p.status ?? 'čaka'}</span>
            </div>
            <div class="predlog-pot">📄 ${p.ciljna_pot}</div>
            <div class="predlog-opis">${p.opis || '(brez opisa)'}</div>
            <div style="font-size:12px;color:var(--muted);margin-top:4px">🤖 ${p.agent} · ${p.cas}</div>
            ${p.revizija ? `<div style="font-size:12px;margin-top:8px;padding:8px;background:var(--bg3);border-radius:6px">
                <strong>Revizor:</strong> V: ${p.revizija.ocene?.varnost ?? '?'}/10 · A: ${p.revizija.ocene?.arhitektura ?? '?'}/10
                ${p.revizija.problemi?.length ? '<br>⚠️ ' + p.revizija.problemi.join(', ') : ''}
            </div>` : ''}
            <div class="predlog-akcije">
                <button class="gumb gumb-zeleni" onclick="potrdi('${p.id}')">✅ Potrdi</button>
                <button class="gumb gumb-rdeci" onclick="zavrni('${p.id}')">❌ Zavrni</button>
            </div>
        </div>
    `).join('');
}

async function potrdi(id) {
    if (!confirm(`Potrdiš predlog ${id}? Datoteka bo zapisana.`)) return;
    const r = await ajax({ akcija: 'potrdi_predlog', id });
    alert(r.uspeh ? r.sporocilo : '❌ ' + r.napaka);
    naloziPredloge();
}

async function zavrni(id) {
    const razlog = prompt('Razlog zavrnitve:') ?? '';
    const r = await ajax({ akcija: 'zavrni_predlog', id, razlog });
    alert(r.uspeh ? r.sporocilo : '❌ ' + r.napaka);
    naloziPredloge();
}

// ── NOVA NALOGA ───────────────────────────────────────────────
async function dodajNalogo() {
    const agent  = document.getElementById('naloga-agent').value;
    const naslov = document.getElementById('naloga-naslov').value.trim();
    const opis   = document.getElementById('naloga-opis').value.trim();
    const msg    = document.getElementById('naloga-sporocilo');

    if (!naslov) { msg.innerHTML = '<div class="sporocilo sporocilo-napaka">Vnesi naslov naloge.</div>'; return; }

    const r = await ajax({ akcija: 'nova_naloga', agent, naslov, opis });
    msg.innerHTML = r.uspeh
        ? `<div class="sporocilo sporocilo-uspeh">✅ Naloga dodana: ${r.id}</div>`
        : `<div class="sporocilo sporocilo-napaka">❌ ${r.napaka}</div>`;

    if (r.uspeh) {
        document.getElementById('naloga-naslov').value = '';
        document.getElementById('naloga-opis').value = '';
    }
}

// ── POROČILA ──────────────────────────────────────────────────
async function naloziPorocila() {
    const vsebina = document.getElementById('porocila-vsebina');
    const r = await ajax({ akcija: 'preberi_porocila' });
    if (!r.uspeh) { vsebina.innerHTML = '<p style="color:var(--rdeca)">Napaka</p>'; return; }

    vsebina.innerHTML = r.porocila.length
        ? r.porocila.map(p => `
            <div class="porocilo-vrstica">
                <span class="porocilo-agent">🤖 ${p.agent}</span>
                <span class="porocilo-cas">${p.datum}</span>
                <span class="porocilo-stanje">${p.stanje ?? '?'}</span>
                <span style="color:var(--muted);font-size:12px">${JSON.stringify(p).length > 100 ? '...' : ''}</span>
            </div>`).join('')
        : '<p style="color:var(--muted)">Ni poročil.</p>';
}

// ── DNEVNIK ───────────────────────────────────────────────────
async function naloziDnevnik() {
    const r = await ajax({ akcija: 'preberi_dnevnik' });
    const el = document.getElementById('dnevnik-vsebina');
    el.textContent = r.uspeh ? (r.vsebina || '(prazen)') : '❌ ' + r.napaka;
    el.scrollTop = el.scrollHeight;
}

// ── STATISTIKE ────────────────────────────────────────────────
async function naloziStatistike() {
    const [rp, rr] = await Promise.all([
        ajax({ akcija: 'seznam_predlogov' }),
        ajax({ akcija: 'preberi_porocila' }),
    ]);
    document.getElementById('stat-predlogi').textContent =
        rp.uspeh ? rp.predlogi.filter(p => p.status === 'čaka' || !p.status).length : '?';
    document.getElementById('stat-porocila').textContent =
        rr.uspeh ? rr.porocila.filter(p => p.datum?.startsWith(new Date().toISOString().slice(0,10))).length : '?';
}

// ── ZAGON ─────────────────────────────────────────────────────
naloziStatistike();
</script>
<?php endif; ?>
</body>
</html>
