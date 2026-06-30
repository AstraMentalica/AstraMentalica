<?php
/**
 * ASTRA/admin_portal.php
 * Admin portal — premium kode, magični portali, sistem
 * Usklajen z AstraMentalica arhitekturo
 */

require_once __DIR__ . '/../pot.php';
require_once SISTEM . '/sistem/zaganjalnik.php'; // naloži jedro + funkcije

if (session_status() === PHP_SESSION_NONE) { session_name('ASTRA_SID'); session_start(); }

// RBAC – samo S5 / admin
if (($_SESSION['vloga_int'] ?? 0) < 60) {
    header('Location: ' . KOREN_URL . '/?svet=UPORABNIKI&pot=prijava');
    exit;
}

// Poti (spremenljivke, ne konstante – varno za večkratni include)
$PORTALI_JSON = PODATKI_JSON . '/portali/portali.json';
$KODE_JSON    = PODATKI_JSON . '/portali/kode.json';
$PREMIUM_JSON = PODATKI_JSON . '/portali/premium_uporabniki.json';

$portali_mapa = PODATKI_JSON . '/portali';
if (!is_dir($portali_mapa)) mkdir($portali_mapa, 0755, true);

function adm_beri(string $pot): array {
    return file_exists($pot) ? (json_decode(file_get_contents($pot), true) ?? []) : [];
}
function adm_pisi(string $pot, array $data): void {
    file_put_contents($pot, json_encode($data, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE), LOCK_EX);
}
function adm_koda(): string {
    $z = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    return substr(str_shuffle($z),0,4).'-'.substr(str_shuffle($z),0,4).'-'.substr(str_shuffle($z),0,3);
}

$register = adm_beri(PODATKI_JSON . '/moduli_register.json');
$sporocilo = $napaka = '';

// 1. Generiraj kode
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['generiraj_kode'])) {
    $modul    = $_POST['koda_modul'] ?? '';
    $stevilo  = min((int)($_POST['koda_stevilo'] ?? 5), 100);
    $trajanje = (int)($_POST['koda_trajanje'] ?? 30);
    $kode = adm_beri($KODE_JSON);
    for ($i = 0; $i < $stevilo; $i++) {
        $kode[] = [
            'koda' => adm_koda(), 'modul' => $modul,
            'trajanje' => $trajanje, 'porabljena' => false,
            'porabljeno_ob' => null, 'porabljeno_id' => null,
            'ustvarjena' => date('Y-m-d H:i:s'),
        ];
    }
    adm_pisi($KODE_JSON, $kode);
    $sporocilo = "Ustvarjenih $stevilo kod za '".basename($modul)."'.";
}

// 2. Aktiviraj kodo
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aktiviraj_kodo'])) {
    $vnos    = strtoupper(trim($_POST['aktivacijska_koda'] ?? ''));
    $uid     = $_SESSION['uporabnik_id'] ?? 0;
    $kode    = adm_beri($KODE_JSON);
    $premium = adm_beri($PREMIUM_JSON);
    $najdena = false;
    foreach ($kode as &$k) {
        if ($k['koda'] === $vnos && !$k['porabljena']) {
            $k['porabljena'] = true;
            $k['porabljeno_ob'] = date('Y-m-d H:i:s');
            $k['porabljeno_id'] = $uid;
            $premium[] = [
                'uporabnik_id' => $uid, 'modul' => $k['modul'],
                'poteče' => date('Y-m-d H:i:s', strtotime("+{$k['trajanje']} days")),
                'aktivirano' => date('Y-m-d H:i:s'),
            ];
            $najdena = true;
            $sporocilo = "Koda aktivirana! Dostop do '".basename($k['modul'])."' velja {$k['trajanje']} dni.";
            break;
        }
    }
    if (!$najdena) $napaka = 'Neveljavna ali že porabljena koda.';
    adm_pisi($KODE_JSON, $kode);
    if ($najdena) adm_pisi($PREMIUM_JSON, $premium);
}

// 3. Ustvari portal
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ustvari_portal'])) {
    $portali = adm_beri($PORTALI_JSON);
    $portali[] = [
        'id'         => uniqid('portal_'),
        'ime'        => htmlspecialchars($_POST['portal_ime'] ?? ''),
        'ure'        => $_POST['trigger_ure'] ?? '',
        'dnevi'      => $_POST['trigger_dnevi'] ?? '',
        'polna_luna' => isset($_POST['trigger_luna']),
        'efekt'      => $_POST['portal_efekt'] ?? 'stars',
        'nagrada'    => htmlspecialchars($_POST['portal_nagrada'] ?? ''),
        'modul'      => $_POST['nagrada_modul'] ?? '',
        'aktiven'    => true,
        'ustvarjen'  => date('Y-m-d H:i:s'),
    ];
    adm_pisi($PORTALI_JSON, $portali);
    $sporocilo = "Portal '{$_POST['portal_ime']}' ustvarjen!";
}

// 4. Toggle portal
if (isset($_GET['toggle_portal'])) {
    $pid = $_GET['toggle_portal'];
    $portali = adm_beri($PORTALI_JSON);
    foreach ($portali as &$p) {
        if ($p['id'] === $pid) $p['aktiven'] = !$p['aktiven'];
    }
    adm_pisi($PORTALI_JSON, $portali);
    header('Location: ' . KOREN_URL . '/ASTRA/admin_portal.php?tab=portali'); exit;
}

// 5. Briši kodo
if (isset($_GET['brisi_kodo'])) {
    $kode = adm_beri($KODE_JSON);
    $kode = array_values(array_filter($kode, fn($k) => $k['koda'] !== $_GET['brisi_kodo']));
    adm_pisi($KODE_JSON, $kode);
    header('Location: ' . KOREN_URL . '/ASTRA/admin_portal.php?tab=kode'); exit;
}

$portali = adm_beri($PORTALI_JSON);
$kode    = array_slice(array_reverse(adm_beri($KODE_JSON)), 0, 30);
$premium = array_reverse(array_values(array_filter(
    adm_beri($PREMIUM_JSON), fn($p) => strtotime($p['poteče']) > time()
)));
$aktivni_tab = $_GET['tab'] ?? 'kode';
$GLOBALNO_URL_L = GLOBALNO_URL;
?>
<!DOCTYPE html>
<html lang="sl" data-tema="temna">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Admin Portal – AstraMentalica</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400&family=Jost:wght@300;400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= $GLOBALNO_URL_L ?>/slog/osnova.css">
<style>
.adm-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(440px,1fr));gap:20px;}
.adm-tabela{width:100%;border-collapse:collapse;font-size:.82rem;}
.adm-tabela th{font-size:.62rem;letter-spacing:1.5px;text-transform:uppercase;color:var(--siva);padding:8px 10px;text-align:left;border-bottom:1px solid var(--rob);}
.adm-tabela td{padding:10px;border-bottom:1px solid rgba(255,255,255,.04);vertical-align:middle;}
.adm-tabela tr:hover td{background:var(--rob);}
.koda-badge{font-family:'Courier New',monospace;font-size:.8rem;background:rgba(201,169,110,.1);color:var(--zlato);padding:3px 10px;border-radius:4px;letter-spacing:1px;}
.tab-row{display:flex;gap:8px;flex-wrap:wrap;margin-bottom:28px;}
.tab-gumb{padding:8px 20px;border-radius:30px;border:1px solid var(--rob);background:transparent;color:var(--siva);font-size:.78rem;cursor:pointer;transition:var(--prehod);font-family:'Jost',sans-serif;}
.tab-gumb:hover,.tab-gumb.aktiven{background:rgba(201,169,110,.12);border-color:rgba(201,169,110,.3);color:var(--zlato2);}
.tab-vsebina{display:none;animation:pojavi .3s ease;}
.tab-vsebina.aktivna{display:block;}
@keyframes pojavi{from{opacity:0;transform:translateY(8px)}to{opacity:1;transform:none}}
.status-ok{color:var(--zelena)}.status-ne{color:var(--rdeca)}
.efekt-badge{font-size:.7rem;padding:2px 8px;border-radius:4px;background:var(--oz3);border:1px solid var(--rob);}
</style>
</head>
<body>
<div class="am-ozadje"></div>
<div class="am-layout">
<?php
$nav = GLOBALNO . '/postavitev/nav.php';
if (file_exists($nav)) require_once $nav;
?>
<main class="am-main"><div class="am-vsebina">

<div class="am-header">
  <div class="am-header-meta">Admin · <?= htmlspecialchars($_SESSION['vloga']??'') ?></div>
  <h1>⚡ Admin Portal</h1>
  <p>Premium kode · Magični portali · Sistem</p>
</div>

<?php if ($sporocilo): ?>
<div class="am-obvestilo uspeh" style="margin-bottom:20px">✓ <?= htmlspecialchars($sporocilo) ?></div>
<?php endif; ?>
<?php if ($napaka): ?>
<div class="am-obvestilo napaka" style="margin-bottom:20px">✕ <?= htmlspecialchars($napaka) ?></div>
<?php endif; ?>

<div class="tab-row">
  <button class="tab-gumb <?= $aktivni_tab==='kode'?'aktiven':'' ?>"      onclick="tab('kode',this)">🎫 Kode</button>
  <button class="tab-gumb <?= $aktivni_tab==='portali'?'aktiven':'' ?>"   onclick="tab('portali',this)">🌀 Portali</button>
  <button class="tab-gumb <?= $aktivni_tab==='premium'?'aktiven':'' ?>"   onclick="tab('premium',this)">👑 Premium</button>
  <button class="tab-gumb <?= $aktivni_tab==='aktivacija'?'aktiven':'' ?>" onclick="tab('aktivacija',this)">🔑 Aktivacija</button>
  <button class="tab-gumb <?= $aktivni_tab==='sistem'?'aktiven':'' ?>"    onclick="tab('sistem',this)">📊 Sistem</button>
</div>

<!-- KODE -->
<div class="tab-vsebina <?= $aktivni_tab==='kode'?'aktivna':'' ?>" id="tab-kode">
<div class="adm-grid">
  <div class="am-kartica luxury">
    <h3 style="margin-bottom:20px;color:var(--zlato2)">🎫 Generiraj kode</h3>
    <form method="POST" class="am-form">
      <div class="am-form-skupina">
        <label class="am-form-lbl">Modul</label>
        <select name="koda_modul" class="am-form-select" required>
          <option value="">Izberi modul...</option>
          <?php foreach ($register as $rel => $info): ?>
          <option value="<?= htmlspecialchars($rel) ?>"><?= htmlspecialchars(basename($rel)) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
        <div class="am-form-skupina">
          <label class="am-form-lbl">Število kod</label>
          <input type="number" name="koda_stevilo" class="am-form-vnos" value="5" min="1" max="100">
        </div>
        <div class="am-form-skupina">
          <label class="am-form-lbl">Trajanje (dni)</label>
          <input type="number" name="koda_trajanje" class="am-form-vnos" value="30" min="1">
        </div>
      </div>
      <button type="submit" name="generiraj_kode" class="am-gumb">✨ Generiraj</button>
    </form>
  </div>
  <div class="am-kartica">
    <h3 style="margin-bottom:16px;color:var(--zlato2)">Zadnje kode</h3>
    <?php if (empty($kode)): ?>
    <p style="color:var(--siva);font-size:.85rem">Ni še nobene kode.</p>
    <?php else: ?>
    <div style="overflow-x:auto">
    <table class="adm-tabela">
      <thead><tr><th>Koda</th><th>Modul</th><th>Dni</th><th>Status</th><th></th></tr></thead>
      <tbody>
      <?php foreach ($kode as $k): ?>
      <tr>
        <td><span class="koda-badge"><?= htmlspecialchars($k['koda']) ?></span></td>
        <td style="font-size:.75rem;color:var(--siva)"><?= htmlspecialchars(basename($k['modul'])) ?></td>
        <td style="font-size:.78rem"><?= $k['trajanje'] ?></td>
        <td><?= $k['porabljena'] ? '<span class="status-ne">Porabljena</span>' : '<span class="status-ok">Aktivna</span>' ?></td>
        <td><?php if (!$k['porabljena']): ?>
          <a href="?brisi_kodo=<?= urlencode($k['koda']) ?>" onclick="return confirm('Briši?')"
             style="font-size:.7rem;color:var(--rdeca)">briši</a>
        <?php endif; ?></td>
      </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
    </div>
    <?php endif; ?>
  </div>
</div>
</div>

<!-- PORTALI -->
<div class="tab-vsebina <?= $aktivni_tab==='portali'?'aktivna':'' ?>" id="tab-portali">
<div class="adm-grid">
  <div class="am-kartica luxury">
    <h3 style="margin-bottom:20px;color:var(--zlato2)">🌀 Nov magični portal</h3>
    <form method="POST" class="am-form">
      <div class="am-form-skupina">
        <label class="am-form-lbl">Ime portala</label>
        <input type="text" name="portal_ime" class="am-form-vnos" placeholder="npr. Lunin portal" required>
      </div>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
        <div class="am-form-skupina">
          <label class="am-form-lbl">Ure (8,12,21)</label>
          <input type="text" name="trigger_ure" class="am-form-vnos" placeholder="8,12,21">
        </div>
        <div class="am-form-skupina">
          <label class="am-form-lbl">Dnevi (1,14,28)</label>
          <input type="text" name="trigger_dnevi" class="am-form-vnos" placeholder="1,14,28">
        </div>
      </div>
      <div class="am-form-skupina" style="display:flex;align-items:center;gap:10px">
        <input type="checkbox" name="trigger_luna" id="luna" style="accent-color:var(--zlato);width:16px;height:16px">
        <label for="luna" class="am-form-lbl" style="margin:0">Aktiviraj ob polni luni</label>
      </div>
      <div class="am-form-skupina">
        <label class="am-form-lbl">Efekt</label>
        <select name="portal_efekt" class="am-form-select">
          <option value="stars">✨ Zvezde</option>
          <option value="portal">🌀 Portal</option>
          <option value="mist">🌫️ Meglica</option>
          <option value="fire">🔥 Ogenj</option>
          <option value="water">💧 Voda</option>
          <option value="moon">🌙 Luna</option>
          <option value="sun">☀️ Sonce</option>
        </select>
      </div>
      <div class="am-form-skupina">
        <label class="am-form-lbl">Nagrada (besedilo)</label>
        <textarea name="portal_nagrada" class="am-form-textarea" rows="2" placeholder="Ko portal odpre..."></textarea>
      </div>
      <div class="am-form-skupina">
        <label class="am-form-lbl">Odpre modul (opcijsko)</label>
        <select name="nagrada_modul" class="am-form-select">
          <option value="">Brez</option>
          <?php foreach ($register as $rel => $info): ?>
          <option value="<?= htmlspecialchars($rel) ?>"><?= htmlspecialchars(basename($rel)) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <button type="submit" name="ustvari_portal" class="am-gumb">🌀 Ustvari</button>
    </form>
  </div>
  <div class="am-kartica">
    <h3 style="margin-bottom:16px;color:var(--zlato2)">Portali</h3>
    <?php if (empty($portali)): ?>
    <p style="color:var(--siva);font-size:.85rem">Ni še nobenega portala.</p>
    <?php else: ?>
    <table class="adm-tabela">
      <thead><tr><th>Ime</th><th>Ure</th><th>Efekt</th><th>Status</th><th></th></tr></thead>
      <tbody>
      <?php foreach ($portali as $p): ?>
      <tr>
        <td><?= htmlspecialchars($p['ime']) ?></td>
        <td style="font-size:.75rem;color:var(--siva)"><?= $p['ure'] ?: '—' ?></td>
        <td><span class="efekt-badge"><?= htmlspecialchars($p['efekt']) ?></span></td>
        <td><?= $p['aktiven'] ? '<span class="status-ok">Aktiven</span>' : '<span class="status-ne">Izklop</span>' ?></td>
        <td><a href="?toggle_portal=<?= $p['id'] ?>&tab=portali" style="font-size:.7rem;color:var(--zlato)">
          <?= $p['aktiven'] ? 'izklopi' : 'vklopi' ?></a></td>
      </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
    <?php endif; ?>
  </div>
</div>
</div>

<!-- PREMIUM -->
<div class="tab-vsebina <?= $aktivni_tab==='premium'?'aktivna':'' ?>" id="tab-premium">
<div class="am-kartica">
  <h3 style="margin-bottom:16px;color:var(--zlato2)">👑 Aktivni premium dostopi</h3>
  <?php if (empty($premium)): ?>
  <p style="color:var(--siva);font-size:.85rem">Ni aktivnih premium dostopov.</p>
  <?php else: ?>
  <div style="overflow-x:auto">
  <table class="adm-tabela">
    <thead><tr><th>ID</th><th>Modul</th><th>Aktivirano</th><th>Poteče</th><th>Preostalo</th></tr></thead>
    <tbody>
    <?php foreach ($premium as $pu):
      $dni = ceil((strtotime($pu['poteče']) - time()) / 86400); ?>
    <tr>
      <td style="font-family:monospace;font-size:.75rem"><?= $pu['uporabnik_id'] ?></td>
      <td><?= htmlspecialchars(basename($pu['modul'])) ?></td>
      <td style="font-size:.75rem;color:var(--siva)"><?= substr($pu['aktivirano'],0,16) ?></td>
      <td style="font-size:.75rem"><?= substr($pu['poteče'],0,16) ?></td>
      <td class="<?= $dni < 7 ? 'status-ne' : 'status-ok' ?>"><?= $dni ?> dni</td>
    </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
  </div>
  <?php endif; ?>
</div>
</div>

<!-- AKTIVACIJA -->
<div class="tab-vsebina <?= $aktivni_tab==='aktivacija'?'aktivna':'' ?>" id="tab-aktivacija">
<div class="am-kartica luxury" style="max-width:480px">
  <h3 style="margin-bottom:20px;color:var(--zlato2)">🔑 Aktiviraj kodo</h3>
  <form method="POST" class="am-form">
    <div class="am-form-skupina">
      <label class="am-form-lbl">Aktivacijska koda</label>
      <input type="text" name="aktivacijska_koda" class="am-form-vnos"
             placeholder="XXXX-XXXX-XXX" style="letter-spacing:2px;text-transform:uppercase"
             required autocomplete="off" id="koda-input">
    </div>
    <button type="submit" name="aktiviraj_kodo" class="am-gumb blok">✨ Aktiviraj</button>
  </form>
</div>
</div>

<!-- SISTEM -->
<div class="tab-vsebina <?= $aktivni_tab==='sistem'?'aktivna':'' ?>" id="tab-sistem">
<div class="adm-grid">
  <div class="am-kartica">
    <h3 style="margin-bottom:16px;color:var(--zlato2)">📊 Sistem</h3>
    <table class="adm-tabela">
      <tr><td style="color:var(--siva)">PHP</td><td><?= PHP_VERSION ?></td></tr>
      <tr><td style="color:var(--siva)">Verzija</td><td><?= defined('SISTEM_VERZIJA') ? SISTEM_VERZIJA : '3.3' ?></td></tr>
      <tr><td style="color:var(--siva)">Čas</td><td><?= date('Y-m-d H:i:s') ?></td></tr>
      <tr><td style="color:var(--siva)">Vloga</td><td><?= htmlspecialchars($_SESSION['vloga']??'') ?></td></tr>
      <tr><td style="color:var(--siva)">Moduli</td><td><?= count($register) ?> registriranih</td></tr>
      <tr><td style="color:var(--siva)">Kode skupaj</td><td><?= count(adm_beri($KODE_JSON)) ?></td></tr>
      <tr><td style="color:var(--siva)">Portali</td><td><?= count($portali) ?></td></tr>
    </table>
  </div>
  <div class="am-kartica">
    <h3 style="margin-bottom:16px;color:var(--zlato2)">🔧 Akcije</h3>
    <div style="display:flex;flex-direction:column;gap:10px">
      <button class="am-gumb sekundarni" onclick="sysKlic('ping')">📡 Ping</button>
      <button class="am-gumb sekundarni" onclick="sysKlic('cache_ocisti')">🗑️ Počisti cache</button>
      <button class="am-gumb sekundarni" onclick="sysKlic('sistem_info')">ℹ️ Info</button>
      <a href="/" class="am-gumb sekundarni" style="text-align:center">← Domov</a>
    </div>
    <pre id="sys-odziv" style="margin-top:16px;font-size:.72rem;color:var(--modra);background:var(--oz3);padding:10px;border-radius:8px;display:none;overflow-x:auto"></pre>
  </div>
</div>
</div>

</div></main>
</div>

<script src="<?= $GLOBALNO_URL_L ?>/skripte/magic_portali.js"></script>
<script>
(function(){
    const t=localStorage.getItem('am_tema')||'temna';
    document.documentElement.setAttribute('data-tema',t);
    document.querySelectorAll('.am-tema-preklop').forEach(p=>p.classList.toggle('svetla',t==='svetla'));
})();

function tab(ime,gumb){
    document.querySelectorAll('.tab-vsebina').forEach(t=>t.classList.remove('aktivna'));
    document.querySelectorAll('.tab-gumb').forEach(g=>g.classList.remove('aktiven'));
    document.getElementById('tab-'+ime).classList.add('aktivna');
    gumb.classList.add('aktiven');
}

async function sysKlic(akcija){
    const el=document.getElementById('sys-odziv');
    el.style.display='block'; el.textContent='...';
    const r=await fetch('/?svet=SISTEM',{
        method:'POST',headers:{'Content-Type':'application/json'},
        body:JSON.stringify({akcija,podatki:{}})
    });
    el.textContent=JSON.stringify(await r.json(),null,2);
}

document.getElementById('koda-input')?.addEventListener('input',function(){
    this.value=this.value.toUpperCase();
});

// Inicializiraj magične portale
document.addEventListener('DOMContentLoaded',()=>{
    const portali = <?= json_encode($portali, JSON_UNESCAPED_UNICODE) ?>;
    if (window.MagicPortali) window.MagicPortali.init(portali);
});
</script>
</body>
</html>
