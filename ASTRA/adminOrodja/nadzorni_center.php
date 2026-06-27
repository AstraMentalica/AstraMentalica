<?php
/**
 * DATOTEKA: nadzorni_center.php
 * POT:      ASTRA/nadzor/nadzorni_center.php
 * NAMEN:    Glavni nadzorni center projekta — brskalnik, urejevalnik, pravila, diagnostika
 * NIVO:     Admin (brez varnosti v razvojnem načinu)
 * ODVISNO:  pot.php (v115 – že naložen preko zaganjalnika)
 * VERZIJA:  1.0 (prilagojeno za v115 – kompatibilnostne konstante spodaj)
 * DATUM:    2026-04-26
 */

declare(strict_types=1);
defined('SISTEM_VARNOST') or die('Direkten dostop ni dovoljen.');

// ============================================================
// KOMPATIBILNOSTNE KONSTANTE (stari poimenovalni standard -> v115 POT_*)
// ============================================================
if (!defined('KOREN'))         define('KOREN', ROOT);
if (!defined('KOREN_URL'))     define('KOREN_URL', '');
if (!defined('ASTRA_POT'))     define('ASTRA_POT', POT_ASTRA . '/');
if (!defined('ASTRA_URL'))     define('ASTRA_URL', KOREN_URL . '/ASTRA/');
if (!defined('MODULI_POT'))    define('MODULI_POT', POT_MODULI . '/');
if (!defined('MODULI_URL'))    define('MODULI_URL', KOREN_URL . '/MODULI/');
if (!defined('UPORABNIKI_POT'))define('UPORABNIKI_POT', POT_UPORABNIKI . '/');
if (!defined('VSEBINA_POT'))   define('VSEBINA_POT', POT_VSEBINA . '/');
if (!defined('GLOBALNO_URL'))  define('GLOBALNO_URL', KOREN_URL . '/GLOBALNO');
if (!defined('SISTEM_URL'))    define('SISTEM_URL', KOREN_URL . '/SISTEM');
if (!defined('PODATKI_URL'))   define('PODATKI_URL', KOREN_URL . '/PODATKI');
if (!defined('PODATKI_POT'))   define('PODATKI_POT', POT_PODATKI . '/');
if (!defined('NC_URL'))        define('NC_URL', KOREN_URL . '/ASTRA/nadzor');

// ============================================================
// VARNOST — RBAC (v115: VLOGA_ADMIN = 100, VLOGA_S5 = 60)
// V razvojnem načinu (RAZVOJNI_NACIN) ostane odprto, sicer zahteva admin/S5.
// ============================================================
if (session_status() !== PHP_SESSION_ACTIVE) { session_name('ASTRA_SID'); session_start(); }
if (!RAZVOJNI_NACIN) {
    $vloga_int = $_SESSION['vloga_int'] ?? 0;
    if ($vloga_int < VLOGA_S5) {
        header('Location: /?svet=UPORABNIKI&pot=prijava');
        exit;
    }
}

// ============================================================
// AVTOMATSKI NALAGALNIK NADZORNIH MODULOV
// ============================================================
function naložiNadzorneModule(): array {
    $moduli = [];
    $mape = [
        'nadzor'     => ASTRA_POT . 'nadzor/',
        'admin'      => ASTRA_POT . 'admin/',
        'ai'         => ASTRA_POT . 'ai/',
        'diagnostika'=> ASTRA_POT . 'diagnostika/',
    ];

    foreach ($mape as $skupina => $pot) {
        if (!is_dir($pot)) continue;
        $datoteke = glob($pot . '*.php') ?: [];
        foreach ($datoteke as $dat) {
            $ime = basename($dat, '.php');
            $moduli[$skupina][] = [
                'ime'  => $ime,
                'pot'  => $dat,
                'url'  => ASTRA_URL . $skupina . '/' . basename($dat),
                'naziv'=> ucfirst(str_replace('_', ' ', $ime)),
            ];
        }
    }
    return $moduli;
}

// ============================================================
// NALAGALNIK SVETOV (svet_*.php)
// ============================================================
function naložiSvetove(): array {
    $svetovi = [
        'moduli'      => MODULI_POT . 'svet_modulov.php',
        'uporabniki'  => UPORABNIKI_POT . 'svet_uporabnikov.php',
        'vsebina'     => VSEBINA_POT . 'svet_vsebine.php',
    ];
    $nalozeni = [];
    foreach ($svetovi as $ime => $pot) {
        if (file_exists($pot)) {
            $nalozeni[$ime] = $pot;
        }
    }
    return $nalozeni;
}

$nadzorniModuli = naložiNadzorneModule();
$svetovi = naložiSvetove();

// ============================================================
// STATISTIKE ZA TOPBAR
// ============================================================
function stejiDatoteke(string $pot): int {
    if (!is_dir($pot)) return 0;
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($pot, FilesystemIterator::SKIP_DOTS));
    return iterator_count($iterator);
}

$stDatotek = stejiDatoteke(KOREN);
$stModulov = count(glob(MODULI_POT . 'osnovni/*/') ?: []) + count(glob(MODULI_POT . 'premium/*/') ?: []);
?>
<!DOCTYPE html>
<html lang="sl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>ASTRA — Nadzorni center</title>
<link rel="stylesheet" href="<?= ASTRA_URL ?>slog/style.css">
<script>
  // Konstante za JS
  window.NC_API    = '<?= KOREN_URL ?>/api_zate.php';
  window.NC_KOREN  = '<?= KOREN ?>';
  window.NC_URL    = '<?= KOREN_URL ?>';
  window.ASTRA_URL = '<?= ASTRA_URL ?>';
</script>
</head>
<body>

<!-- ══════════════════════════════════════════════════════════
     TOPBAR
     ══════════════════════════════════════════════════════════ -->
<div id="topbar">
  <span class="top-logo">✦ ASTRA</span>
  <div class="top-sep"></div>

  <button class="top-btn" onclick="toggleSidebar()" title="Sidebar">☰</button>
  <button class="top-btn" onclick="novaDataoteka()">✦ Nova dat.</button>
  <button class="top-btn" onclick="novaMapa()">📁 Nova mapa</button>

  <div class="top-sep"></div>

  <input type="text" id="isci-input"
    placeholder="Iskanje v projektu..."
    style="height:26px;padding:0 10px;background:var(--cosmos);border:1px solid var(--border);border-radius:3px;color:var(--text);font-family:'JetBrains Mono',monospace;font-size:11px;width:200px;outline:none"
    title="Enter za iskanje">

  <div class="top-sep"></div>

  <button class="top-btn zeleno" onclick="odpriUrejevalnik('<?= ASTRA_URL ?>nadzorni_center.php')" title="Uredi NC">✏ NC</button>
  <button class="top-btn" onclick="preverStrukturoProjekta()" title="Preveri strukturo">⚡ Struktura</button>

  <div class="top-spacer"></div>

  <!-- Statistike -->
  <span style="font-size:10px;color:var(--text3)">
    📄 <?= number_format($stDatotek) ?> dat ·
    🧩 <?= $stModulov ?> modulov
  </span>

  <div class="top-sep"></div>

  <a class="top-btn" href="<?= KOREN_URL ?>/" target="_blank">🌐 Stran</a>
  <a class="top-btn" href="<?= KOREN_URL ?>/api_zate.php?akcija=seznam&tip=vse" target="_blank">🔌 API</a>
</div>

<!-- ══════════════════════════════════════════════════════════
     LAYOUT
     ══════════════════════════════════════════════════════════ -->
<div id="layout">

  <!-- SIDEBAR -->
  <div id="sidebar">

    <!-- BRSKALNIK -->
    <div class="sidebar-sekcija">
      <div class="sidebar-glava" onclick="toggleSekcijaSidebar(this)">
        <span>📁 BRSKALNIK</span>
        <span class="pusc">▼</span>
      </div>
      <div class="sidebar-vsebina">
        <div id="drevo-vsebina"></div>
      </div>
    </div>

    <!-- NADZOR -->
    <?php foreach ($nadzorniModuli as $skupina => $moduli): ?>
    <div class="sidebar-sekcija">
      <div class="sidebar-glava" onclick="toggleSekcijaSidebar(this)">
        <span><?= mb_strtoupper($skupina) ?></span>
        <span class="pusc">▼</span>
      </div>
      <div class="sidebar-vsebina">
        <?php foreach ($moduli as $modul): ?>
        <div class="nav-item" onclick="odpriNadzorniModul('<?= $modul['url'] ?>', '<?= $modul['naziv'] ?>')">
          <span class="ikona">⚙</span>
          <span><?= htmlspecialchars($modul['naziv']) ?></span>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endforeach; ?>

    <!-- PRAVILA -->
    <div class="sidebar-sekcija">
      <div class="sidebar-glava" onclick="toggleSekcijaSidebar(this)">
        <span>📜 PRAVILA</span>
        <span class="pusc">▼</span>
      </div>
      <div class="sidebar-vsebina">
        <div class="nav-item" onclick="odpriPravila()">
          <span class="ikona">📖</span>
          <span>Preglej pravila</span>
        </div>
        <div class="nav-item" onclick="novoPravilo()">
          <span class="ikona">✦</span>
          <span>Novo pravilo</span>
        </div>
      </div>
    </div>

    <!-- SVETOVI -->
    <?php if (!empty($svetovi)): ?>
    <div class="sidebar-sekcija">
      <div class="sidebar-glava" onclick="toggleSekcijaSidebar(this)">
        <span>🌍 SVETOVI</span>
        <span class="pusc">▼</span>
      </div>
      <div class="sidebar-vsebina">
        <?php foreach ($svetovi as $ime => $pot): ?>
        <div class="nav-item" onclick="odpriSvet('<?= $ime ?>')">
          <span class="ikona">🔮</span>
          <span><?= ucfirst($ime) ?></span>
          <span class="znacka">✓</span>
        </div>
        <?php endforeach; ?>
        <?php
        $manjkajoci = ['moduli' => 'MODULI/', 'uporabniki' => 'UPORABNIKI/', 'vsebina' => 'VSEBINA/'];
        foreach ($manjkajoci as $ime => $mapa):
          if (!isset($svetovi[$ime])):
        ?>
        <div class="nav-item" style="opacity:0.4" title="<?= $mapa ?>svet_<?= $ime ?>.php ne obstaja">
          <span class="ikona">○</span>
          <span><?= ucfirst($ime) ?></span>
          <span class="znacka" style="color:var(--mist)">—</span>
        </div>
        <?php endif; endforeach; ?>
      </div>
    </div>
    <?php endif; ?>

    <!-- DOKUMENTACIJA -->
    <div class="sidebar-sekcija">
      <div class="sidebar-glava" onclick="toggleSekcijaSidebar(this)">
        <span>📚 DOKUMENTACIJA</span>
        <span class="pusc">▼</span>
      </div>
      <div class="sidebar-vsebina">
        <?php
        $docs = ['DOKUMENTACIJA/USTAVA.md','DOKUMENTACIJA/ARHITEKTURA.md','DOKUMENTACIJA/STANDARDI.md','DOKUMENTACIJA/MODULI.md','DOKUMENTACIJA/VIZIJA.md','DOKUMENTACIJA/FAZE.md','DOKUMENTACIJA/REGISTER.md'];
        foreach ($docs as $d):
          $exists = file_exists(KOREN . '/' . $d);
        ?>
        <div class="nav-item <?= $exists ? '' : 'style="opacity:0.4"' ?>" onclick="odpriDatoteko('<?= $d ?>')">
          <span class="ikona"><?= $exists ? '📄' : '○' ?></span>
          <span><?= basename($d) ?></span>
        </div>
        <?php endforeach; ?>
      </div>
    </div>

  </div><!-- /sidebar -->

  <!-- VSEBINA -->
  <div id="vsebina">

    <!-- ZAVIHKI -->
    <div id="zavihki"></div>

    <!-- POGLED -->
    <div id="pogled">

      <!-- BRSKALNIK POGLED -->
      <div class="pogled-panel" id="pogled-brskalnik">
        <div class="pot-navigacija" id="pot-nav">
          <span class="pot-del zadnji">⌂ root</span>
        </div>
        <div id="brskalnik-vsebina" style="flex:1;overflow-y:auto"></div>
      </div>

      <!-- PRAVILA POGLED -->
      <div class="pogled-panel" id="pogled-pravila">
        <div class="urejevalnik-glava">
          <span class="pot">📜 Pravila projekta — ASTRA/razvoj/pravila/</span>
          <button class="top-btn zeleno" onclick="novoPravilo()">+ Novo pravilo</button>
        </div>
        <div class="pravila-lista" id="pravila-lista">
          <div style="color:var(--text3);font-size:11px;padding:16px">Klikni "Preglej pravila" v sidebaru za nalaganje.</div>
        </div>
      </div>

      <!-- STATISTIKE POGLED -->
      <div class="pogled-panel" id="pogled-statistike">
        <div class="urejevalnik-glava">
          <span class="pot">📊 Statistike projekta</span>
          <button class="top-btn" onclick="osveziStatistike()">↻ Osveži</button>
        </div>
        <div class="stat-grid" id="stat-grid">
          <div class="stat-blok">
            <div class="stat-st"><?= number_format($stDatotek) ?></div>
            <div class="stat-naziv">Skupaj datotek</div>
          </div>
          <div class="stat-blok">
            <div class="stat-st"><?= $stModulov ?></div>
            <div class="stat-naziv">Modulov</div>
          </div>
          <div class="stat-blok">
            <div class="stat-st"><?= count($nadzorniModuli) ?></div>
            <div class="stat-naziv">Nadzornih skupin</div>
          </div>
          <div class="stat-blok">
            <div class="stat-st"><?= count($svetovi) ?></div>
            <div class="stat-naziv">Aktivnih svetov</div>
          </div>
          <div class="stat-blok">
            <div class="stat-st"><?= count($docs) ?></div>
            <div class="stat-naziv">Dokumentov</div>
          </div>
          <div class="stat-blok">
            <div class="stat-st">v1.0</div>
            <div class="stat-naziv">Verzija NC</div>
          </div>
        </div>
        <div style="padding:0 16px;font-family:'Cinzel',serif;font-size:9px;color:var(--gold);letter-spacing:2px;margin-bottom:8px">POTI</div>
        <div style="padding:0 16px;font-size:11px;color:var(--text2);display:flex;flex-direction:column;gap:4px">
          <?php
          $konstante = ['KOREN_URL','SISTEM_URL','GLOBALNO_URL','MODULI_URL','ASTRA_URL','PODATKI_URL'];
          foreach ($konstante as $k):
          ?>
          <div style="display:flex;gap:12px">
            <span style="color:var(--gold);min-width:140px"><?= $k ?></span>
            <span><?= htmlspecialchars(constant($k)) ?></span>
          </div>
          <?php endforeach; ?>
        </div>
      </div>

    </div><!-- /pogled -->

  </div><!-- /vsebina -->

  <!-- DESNI PANEL (predogled / info) -->
  <div id="desni-panel">
    <div class="desni-glava">
      <span id="desni-glava-naziv">PREDOGLED</span>
      <button class="top-btn" onclick="zapriDesniPanel()" style="font-size:12px;border:none;color:var(--mist)">×</button>
    </div>
    <div class="desni-vsebina" id="desni-vsebina">
      <div style="color:var(--text3)">Klikni datoteko za predogled.</div>
    </div>
  </div>

</div><!-- /layout -->

<!-- STATUS -->
<div id="status">
  <span id="status-msg" class="status-item">Inicializacija...</span>
  <span class="top-sep" style="margin:0"></span>
  <span class="status-item"><?= date('Y-m-d H:i') ?></span>
  <span class="status-item"><?= htmlspecialchars(KOREN_URL) ?></span>
</div>

<!-- MODAL -->
<div id="modal-overlay">
  <div id="modal">
    <h3 id="modal-title"></h3>
    <p id="modal-desc"></p>
    <div id="modal-body"></div>
    <div class="modal-gumbi" id="modal-gumbi"></div>
  </div>
</div>

<!-- TOAST -->
<div id="toast"></div>

<script src="<?= ASTRA_URL ?>slog/skripte.js"></script>
<script>
// ══════════════════════════════════════════════════════
// DODATNE FUNCKIJE SPECIFIČNE ZA NC
// ══════════════════════════════════════════════════════

function odpriNadzorniModul(url, naziv) {
  const id = 'mod-' + (++NC.stevec);
  NC.zavihki.push({ id, ime: naziv, ikona: '⚙', pogled: 'iframe-' + id });

  const pogled = document.getElementById('pogled');
  if (pogled) {
    const div = document.createElement('div');
    div.className = 'pogled-panel';
    div.id = 'pogled-iframe-' + id;
    div.innerHTML = `<iframe src="${url}" style="flex:1;border:none;width:100%;height:100%" frameborder="0"></iframe>`;
    div.style.display = 'flex';
    div.style.flexDirection = 'column';
    pogled.appendChild(div);
  }
  renderZavihki();
  aktivirajZavihek(id);
}

async function odpriPravila() {
  ustvariZavihek('pravila', '📜 Pravila', '📜', 'pravila');

  const data = await apiKlic('seznam', { tip: 'md' });
  if (!data.uspeh) return;

  const pravila = (data.datoteke || []).filter(d =>
    d.pot.includes('ASTRA/razvoj/pravila') ||
    d.pot.includes('DOKUMENTACIJA') ||
    d.pot.includes('pravila.md')
  );

  const lista = document.getElementById('pravila-lista');
  if (!lista) return;

  if (!pravila.length) {
    lista.innerHTML = '<div style="color:var(--text3);padding:16px;font-size:11px">Ni najdenih pravil.<br>Ustvari: ASTRA/razvoj/pravila/pravila.md</div>';
    return;
  }

  lista.innerHTML = pravila.map(p => `
    <div class="pravilo-kartica" onclick="odpriUrejevalnik('${p.pot}')">
      <div class="naziv">📄 ${p.pot.split('/').pop()}</div>
      <div class="opis" style="color:var(--text3)">${p.pot}</div>
    </div>
  `).join('');
}

function novoPravilo() {
  odpriModal(
    'NOVO PRAVILO',
    'Ustvari novo pravilo v ASTRA/razvoj/pravila/',
    '<input type="text" id="pravilo-ime" placeholder="ime_pravila.md">',
    '<button class="modal-btn" onclick="zapriModal()">Prekliči</button>' +
    '<button class="modal-btn primary" onclick="ustvariPravilo()">Ustvari</button>'
  );
}

async function ustvariPravilo() {
  const ime = document.getElementById('pravilo-ime')?.value?.trim();
  if (!ime) return;
  const pot = 'ASTRA/razvoj/pravila/' + (ime.endsWith('.md') ? ime : ime + '.md');
  const danes = new Date().toISOString().substring(0, 10);
  const vsebina = `# ${ime.replace('.md','')}\n\n> Datum: ${danes}\n\n## Namen\n\n*Opis pravila...*\n\n## Pravila\n\n1. \n2. \n\n---\n*verzija 1.0*\n`;

  await apiShrani(pot, vsebina);
  zapriModal();
  toast('Pravilo ustvarjeno: ' + ime, 'ok');
  odpriUrejevalnik(pot);
}

function odpriSvet(ime) {
  const url = window.NC_URL + '/' + {
    moduli: 'MODULI/svet_modulov.php',
    uporabniki: 'UPORABNIKI/svet_uporabnikov.php',
    vsebina: 'VSEBINA/svet_vsebine.php'
  }[ime];
  odpriNadzorniModul(url, '🌍 ' + ime.charAt(0).toUpperCase() + ime.slice(1));
}

async function preverStrukturoProjekta() {
  ustvariZavihek('struktura', '⚡ Struktura', '⚡', 'statistike');

  const pricakovane = [
    'pot.php', 'index.php',
    'SISTEM/api.php', 'SISTEM/sistem/zaganjalnik.php',
    'SISTEM/sistem/jedro/01_napake.php', 'SISTEM/sistem/jedro/13_zagon.php',
    'SISTEM/sistem/baze/shramba.php',
    'GLOBALNO/postavitev/antiquus_layout.php',
    'MODULI/osnovni/Aeternum/modul.php',
    'ASTRA/nadzorni_center.php',
  ];

  const data = await apiKlic('seznam', { tip: 'php' });
  const vse = new Set((data.datoteke || []).map(d => d.pot.replace(/\\/g,'/')));

  const rezultati = pricakovane.map(p => ({
    pot: p,
    obstaja: vse.has(p)
  }));

  const id = 'str-' + (++NC.stevec);
  NC.zavihki.push({ id, ime: '⚡ Struktura', ikona: '⚡', pogled: 'str-' + id });

  const pogled = document.getElementById('pogled');
  if (pogled) {
    const div = document.createElement('div');
    div.className = 'pogled-panel';
    div.id = 'pogled-str-' + id;
    div.innerHTML = `
      <div class="urejevalnik-glava">
        <span class="pot">⚡ Preverjanje strukture projekta</span>
      </div>
      <div style="padding:16px;overflow-y:auto;flex:1;display:flex;flex-direction:column;gap:4px">
        ${rezultati.map(r => `
          <div class="val-item">
            <span class="val-tip ${r.obstaja ? 'ok' : 'napaka'}">${r.obstaja ? '✓ OK' : '✗ MANJKA'}</span>
            <span class="val-sporocilo">${r.pot}</span>
          </div>
        `).join('')}
        <div style="margin-top:16px;color:var(--text3);font-size:10px">
          ${rezultati.filter(r=>r.obstaja).length} / ${rezultati.length} datotek najdenih
        </div>
      </div>
    `;
    pogled.appendChild(div);
  }

  renderZavihki();
  aktivirajZavihek(id);
}

// Statistike
function osveziStatistike() {
  location.reload();
}

// Keyboard shortcuts
document.addEventListener('keydown', e => {
  if (e.ctrlKey && e.key === 'n') { e.preventDefault(); novaDataoteka(); }
  if (e.ctrlKey && e.key === 'f') { e.preventDefault(); document.getElementById('isci-input')?.focus(); }
});
</script>

</body>
</html>
