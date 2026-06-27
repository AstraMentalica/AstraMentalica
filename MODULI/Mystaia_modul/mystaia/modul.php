<?php
/**
 * MODULI/premium/Mystaia/modul.php
 * Mystaia – Aromatična spletna trgovina
 *
 * Neodvisna od sistema:
 *  - Podatki: direktno v PODATKI/mystaia/ (JSON)
 *  - Prijava: bridge samo za preverjanje vloge (admin panel)
 *  - Košarica: $_SESSION
 *  - Naročilo: gost OK, admin: >= S1
 */

define('MYSTAIA_ACTIVE', true);

// ── POTI ────────────────────────────────────────────────────────────────────
if (!defined('PODATKI')) define('PODATKI', dirname(__DIR__, 3) . '/PODATKI');
if (!defined('KOREN_URL')) define('KOREN_URL', '');

$MYSTAIA_DIR = __DIR__;
$MYSTAIA_URL = KOREN_URL . '/MODULI/premium/Mystaia';

require_once $MYSTAIA_DIR . '/mystaia_podatki.php';
mystaia_init();

// ── SEJA ─────────────────────────────────────────────────────────────────────
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_name('ASTRA_SID');
    session_start();
}

// ── AJAX HANDLER ─────────────────────────────────────────────────────────────
if (isset($_GET['api'])) {
    require_once $MYSTAIA_DIR . '/mystaia_api.php';
    exit;
}

// ── PODATKI ZA PRIKAZ ────────────────────────────────────────────────────────
$vloga       = $_SESSION['vloga'] ?? 'gost';
$je_admin    = in_array($vloga, ['S1','S2','S3','S4','S5','admin']);
$nastavitve  = mystaia_nastavitve();
$kategorije  = my_beri(MY_KATEGORIJE);
$kosarica    = mystaia_kosarica_skupaj();
$ime_trgovine = $nastavitve['ime_trgovine'] ?? 'Mystaia';

// ── API URL ───────────────────────────────────────────────────────────────────
$api_url = KOREN_URL . '/?svet=MODULI&pot=premium/Mystaia&api=1';
?>
<!DOCTYPE html>
<html lang="sl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= htmlspecialchars($ime_trgovine) ?> – Aromatična svetišča</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;1,300;1,400&family=Jost:wght@300;400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= $MYSTAIA_URL ?>/mystaia.css">
</head>
<body>

<div class="my-layout">

  <!-- ══════════════════ SIDEBAR ══════════════════ -->
  <aside class="my-sidebar">
    <div class="my-logo">
      <span class="my-logo-ikona">✦</span>
      <div class="my-logo-ime"><?= htmlspecialchars($ime_trgovine) ?></div>
      <div class="my-logo-pod"><?= htmlspecialchars($nastavitve['podnaslov'] ?? '') ?></div>
    </div>

    <nav class="my-nav">
      <div class="my-nav-naslov">Navigacija</div>
      <button class="my-nav-gumb aktiven" onclick="pokaziStran('trgovina',this)">
        <span class="my-nav-ikona">🏪</span> Trgovina
      </button>
      <button class="my-nav-gumb" onclick="pokaziStran('kosarica',this)">
        <span class="my-nav-ikona">🛍</span> Košarica
        <span class="my-nav-stevilo" id="nav-kosarica-st"><?= $kosarica['stevilo'] ?: '' ?></span>
      </button>

      <?php if ($je_admin): ?>
      <div class="my-nav-locilo"></div>
      <div class="my-nav-naslov">Admin</div>
      <button class="my-nav-gumb" onclick="pokaziStran('admin-narocila',this)">
        <span class="my-nav-ikona">📋</span> Naročila
      </button>
      <button class="my-nav-gumb" onclick="pokaziStran('admin-artikli',this)">
        <span class="my-nav-ikona">📦</span> Artikli
      </button>
      <?php endif; ?>

      <div class="my-nav-locilo"></div>
      <div style="padding:0 16px;margin-top:auto">
        <div style="font-size:.7rem;color:var(--my-siva);opacity:.5;line-height:1.6">
          Prijavljeni:<br>
          <span style="color:var(--my-zlato)"><?= $je_admin ? htmlspecialchars($vloga) : 'gost' ?></span>
        </div>
      </div>
    </nav>
  </aside>

  <!-- ══════════════════ GLAVNA VSEBINA ══════════════════ -->
  <main class="my-vsebina">

    <!-- ── TRGOVINA ── -->
    <section class="my-stran aktivna" id="stran-trgovina">
      <div class="my-header">
        <h1>Aromatična svetišča</h1>
        <p>Ručno izbrani izdelki za dušo, telo in prostor</p>
      </div>

      <div class="my-filtri" id="filtri">
        <?php foreach ($kategorije as $kat): ?>
        <button class="my-filter<?= $kat['id']==='vse'?' aktiven':'' ?>"
                onclick="filtriraj('<?= $kat['id'] ?>',this)">
          <?= $kat['ikona'] ?> <?= htmlspecialchars($kat['ime']) ?>
        </button>
        <?php endforeach; ?>
      </div>

      <div class="my-mreza" id="mreza-artiklov">
        <?php
        $artikli = mystaia_artikli();
        foreach ($artikli as $a):
            $znacka = $a['znacke'][0] ?? '';
            $ni_zaloge = $a['zalogo'] <= 0;
        ?>
        <div class="my-kartica-artikel<?= $ni_zaloge?' my-zaloga-0':'' ?>"
             data-kategorija="<?= $a['kategorija'] ?>"
             onclick="pokaziArtikel('<?= $a['id'] ?>')">
          <div class="my-kartica-slika"><?= $a['ikona'] ?></div>
          <div class="my-kartica-body">
            <div class="my-kartica-kategorija"><?= htmlspecialchars($a['kategorija']) ?></div>
            <div class="my-kartica-ime"><?= htmlspecialchars($a['ime']) ?></div>
            <div class="my-kartica-opis"><?= htmlspecialchars($a['opis_kratek']) ?></div>
            <div class="my-kartica-footer">
              <div>
                <span class="my-cena">€<?= number_format($a['cena'],2,',','.') ?></span>
                <?php if ($znacka): ?>
                  <div class="my-znacka <?= $znacka ?>" style="margin-top:4px"><?= htmlspecialchars($znacka) ?></div>
                <?php endif; ?>
              </div>
              <?php if ($ni_zaloge): ?>
                <span class="my-zaloga-label">Razprodano</span>
              <?php else: ?>
                <button class="my-gumb-dodaj" onclick="event.stopPropagation();dodajVKosarico('<?= $a['id'] ?>',this)">
                  + Dodaj
                </button>
              <?php endif; ?>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </section>

    <!-- ── ARTIKEL DETAIL ── -->
    <section class="my-stran" id="stran-artikel">
      <button class="my-detail-nazaj" onclick="pokaziStran('trgovina',null)">
        ← Nazaj na trgovino
      </button>
      <div class="my-detail" id="detail-vsebina"></div>
    </section>

    <!-- ── KOŠARICA ── -->
    <section class="my-stran" id="stran-kosarica">
      <div class="my-header">
        <h1>Košarica</h1>
        <p>Vaši izbrani aromatični zakladi</p>
      </div>
      <div id="kosarica-vsebina"></div>
    </section>

    <!-- ── NAROČILO ── -->
    <section class="my-stran" id="stran-narocilo">
      <div class="my-header">
        <h1>Zaključi naročilo</h1>
        <p>Izpolni podatke za dostavo</p>
      </div>
      <div style="display:grid;grid-template-columns:1fr 380px;gap:40px;max-width:900px">
        <div>
          <div class="my-narocilo-naslov">Podatki za dostavo</div>
          <div class="my-form-grid">
            <div class="my-form-skupina">
              <label class="my-form-lbl">Ime in priimek *</label>
              <input class="my-form-vnos" id="n-ime" placeholder="Ana Novak" type="text">
            </div>
            <div class="my-form-skupina">
              <label class="my-form-lbl">E-pošta *</label>
              <input class="my-form-vnos" id="n-email" placeholder="ana@primer.si" type="email">
            </div>
          </div>
          <div class="my-form-grid" style="margin-top:14px">
            <div class="my-form-skupina">
              <label class="my-form-lbl">Telefonska številka</label>
              <input class="my-form-vnos" id="n-tel" placeholder="+386 41 123 456" type="tel">
            </div>
            <div class="my-form-skupina">
              <label class="my-form-lbl">Naslov dostave *</label>
              <input class="my-form-vnos" id="n-naslov" placeholder="Ulica 1, 1000 Ljubljana" type="text">
            </div>
          </div>
          <div class="my-form-locilo"></div>
          <div class="my-narocilo-naslov">Plačilo</div>
          <div class="my-form-skupina">
            <label class="my-form-lbl">Način plačila</label>
            <select class="my-form-select" id="n-placilo">
              <option value="po_povzetku">Po povzetku (plačilo ob prevzemu)</option>
              <option value="bančno_nakazilo">Bančno nakazilo</option>
              <option value="paypal">PayPal</option>
            </select>
          </div>
          <div class="my-form-skupina" style="margin-top:14px">
            <label class="my-form-lbl">Opomba k naročilu</label>
            <textarea class="my-form-textarea" id="n-opomba" placeholder="Posebne željre, navodila za dostavo..."></textarea>
          </div>
          <button class="my-gumb-narocilo" style="margin-top:24px" onclick="oddajNarocilo()">
            Potrdi naročilo ✦
          </button>
        </div>
        <div>
          <div class="my-narocilo-naslov">Povzetek naročila</div>
          <div id="narocilo-povzetek"></div>
        </div>
      </div>
    </section>

    <!-- ── POTRJENO ── -->
    <section class="my-stran" id="stran-potrjeno">
      <div class="my-potrjeno">
        <div class="my-potrjeno-ikona">✦</div>
        <h2>Naročilo sprejeto</h2>
        <div class="my-potrjeno-id" id="potrjeno-id">MY-XXXXXX-DATE</div>
        <p>Hvala za vaše zaupanje. Potrdilo bo poslano na vaš e-naslov. Naročilo bomo odposlali v 1–2 delovnih dneh.</p>
        <button class="my-gumb-narocilo" style="max-width:280px;margin-top:8px" onclick="pokaziStran('trgovina',null)">
          Nazaj v trgovino
        </button>
      </div>
    </section>

    <!-- ── ADMIN NAROČILA ── -->
    <?php if ($je_admin): ?>
    <section class="my-stran" id="stran-admin-narocila">
      <div class="my-header">
        <h1>Naročila</h1>
        <p>Pregled in upravljanje vseh naročil</p>
      </div>
      <div id="admin-narocila-vsebina">
        <?php
        $narocila = mystaia_narocila_vse();
        if (empty($narocila)): ?>
          <div class="my-prazno"><div class="my-prazno-ikona">📋</div><p>Še ni naročil.</p></div>
        <?php else: ?>
          <div style="overflow-x:auto">
          <table class="my-admin-tabela">
            <thead>
              <tr>
                <th>ID</th><th>Datum</th><th>Kupec</th><th>Skupaj</th><th>Status</th><th>Akcija</th>
              </tr>
            </thead>
            <tbody>
            <?php foreach ($narocila as $n): ?>
              <tr>
                <td style="font-family:monospace;font-size:.75rem;color:var(--my-zlato)"><?= htmlspecialchars($n['id']) ?></td>
                <td style="font-size:.78rem;color:var(--my-siva)"><?= htmlspecialchars(substr($n['cas'],0,16)) ?></td>
                <td>
                  <div style="font-size:.85rem"><?= htmlspecialchars($n['kupec']['ime']) ?></div>
                  <div style="font-size:.72rem;color:var(--my-siva)"><?= htmlspecialchars($n['kupec']['email']) ?></div>
                </td>
                <td style="font-family:'Cormorant Garamond',serif;font-size:1rem;color:var(--my-zlato)">
                  €<?= number_format($n['vsota'],2,',','.') ?>
                </td>
                <td><span class="my-status-badge status-<?= $n['status'] ?>"><?= htmlspecialchars($n['status']) ?></span></td>
                <td>
                  <select class="my-admin-select" onchange="spremiStatus('<?= $n['id'] ?>',this.value)" style="margin-right:4px">
                    <?php foreach (['novo','potrjeno','v_obdelavi','poslano','dostavljeno','stornirano'] as $st): ?>
                      <option value="<?= $st ?>"<?= $n['status']===$st?' selected':'' ?>><?= $st ?></option>
                    <?php endforeach; ?>
                  </select>
                </td>
              </tr>
            <?php endforeach; ?>
            </tbody>
          </table>
          </div>
        <?php endif; ?>
      </div>
    </section>

    <!-- ── ADMIN ARTIKLI ── -->
    <section class="my-stran" id="stran-admin-artikli">
      <div class="my-header" style="display:flex;justify-content:space-between;align-items:flex-start">
        <div>
          <h1>Artikli</h1>
          <p>Upravljanje kataloga izdelkov</p>
        </div>
        <button class="my-gumb-dodaj" onclick="odpriFormArtikel(null)" style="padding:10px 20px;font-size:.8rem">
          + Nov artikel
        </button>
      </div>
      <div style="overflow-x:auto">
      <table class="my-admin-tabela" id="admin-artikli-tabela">
        <thead>
          <tr><th>Artikel</th><th>Kategorija</th><th>Cena</th><th>Zaloga</th><th>Status</th><th>Akcija</th></tr>
        </thead>
        <tbody>
        <?php foreach (my_beri(MY_ARTIKLI) as $a): ?>
          <tr id="art-vrstica-<?= $a['id'] ?>">
            <td>
              <div style="display:flex;align-items:center;gap:10px">
                <span style="font-size:1.5rem"><?= $a['ikona'] ?></span>
                <div>
                  <div style="font-size:.85rem"><?= htmlspecialchars($a['ime']) ?></div>
                  <div style="font-size:.72rem;color:var(--my-siva)"><?= htmlspecialchars($a['teza']??'') ?></div>
                </div>
              </div>
            </td>
            <td style="font-size:.78rem;color:var(--my-siva)"><?= htmlspecialchars($a['kategorija']) ?></td>
            <td style="font-family:'Cormorant Garamond',serif;font-size:1rem;color:var(--my-zlato)">€<?= number_format($a['cena'],2,',','.') ?></td>
            <td style="font-size:.85rem"><?= (int)$a['zalogo'] ?></td>
            <td><span class="my-znacka <?= $a['aktivno']?'bestseller':'novo' ?>"><?= $a['aktivno']?'Aktivno':'Skrito' ?></span></td>
            <td>
              <button class="my-admin-akcija" onclick='odpriFormArtikel(<?= json_encode($a) ?>)'>Uredi</button>
              <button class="my-admin-akcija" onclick="briziArtikel('<?= $a['id'] ?>','<?= htmlspecialchars(addslashes($a['ime'])) ?>')" style="border-color:rgba(138,74,74,.3);color:var(--my-rdeca)">Briši</button>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
      </div>

      <!-- MODAL FORMA ARTIKEL -->
      <div id="modal-artikel" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.7);z-index:100;display:none;align-items:center;justify-content:center;padding:20px">
        <div style="background:var(--my-pov);border:1px solid var(--my-rob2);border-radius:var(--r);padding:32px;width:100%;max-width:560px;max-height:90vh;overflow-y:auto">
          <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px">
            <div class="my-narocilo-naslov" id="form-artikel-naslov" style="margin:0">Nov artikel</div>
            <button onclick="zapriModal()" style="background:none;border:none;color:var(--my-siva);font-size:1.2rem;cursor:pointer">✕</button>
          </div>
          <input type="hidden" id="fa-id">
          <div class="my-form-grid">
            <div class="my-form-skupina" style="grid-column:1/-1">
              <label class="my-form-lbl">Ime artikla *</label>
              <input class="my-form-vnos" id="fa-ime" type="text">
            </div>
            <div class="my-form-skupina">
              <label class="my-form-lbl">Cena (EUR) *</label>
              <input class="my-form-vnos" id="fa-cena" type="number" step="0.01" min="0">
            </div>
            <div class="my-form-skupina">
              <label class="my-form-lbl">Zaloga *</label>
              <input class="my-form-vnos" id="fa-zaloga" type="number" min="0">
            </div>
            <div class="my-form-skupina">
              <label class="my-form-lbl">Kategorija</label>
              <select class="my-form-select" id="fa-kategorija">
                <?php foreach ($kategorije as $k): if($k['id']==='vse') continue; ?>
                  <option value="<?= $k['id'] ?>"><?= htmlspecialchars($k['ime']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="my-form-skupina">
              <label class="my-form-lbl">Ikona (emoji)</label>
              <input class="my-form-vnos" id="fa-ikona" type="text" maxlength="4" placeholder="🌸">
            </div>
            <div class="my-form-skupina" style="grid-column:1/-1">
              <label class="my-form-lbl">Kratek opis</label>
              <input class="my-form-vnos" id="fa-opis-kratek" type="text">
            </div>
            <div class="my-form-skupina" style="grid-column:1/-1">
              <label class="my-form-lbl">Polni opis</label>
              <textarea class="my-form-textarea" id="fa-opis"></textarea>
            </div>
            <div class="my-form-skupina">
              <label class="my-form-lbl">Teža/Volumen</label>
              <input class="my-form-vnos" id="fa-teza" type="text" placeholder="50ml">
            </div>
            <div class="my-form-skupina" style="display:flex;align-items:center;gap:10px;padding-top:20px">
              <input type="checkbox" id="fa-aktivno" checked style="accent-color:var(--my-zlato);width:16px;height:16px">
              <label for="fa-aktivno" class="my-form-lbl" style="margin:0">Artikel aktiviran</label>
            </div>
          </div>
          <button class="my-gumb-narocilo" style="margin-top:24px" onclick="shraniArtikel()">
            Shrani artikel ✦
          </button>
        </div>
      </div>
    </section>
    <?php endif; ?>

  </main>
</div>

<!-- ══════════════════ JAVASCRIPT ══════════════════ -->
<script>
const API = '<?= $api_url ?>';
const JE_ADMIN = <?= $je_admin ? 'true' : 'false' ?>;
const DOSTAVA_CENA = <?= (float)($nastavitve['dostava_cena'] ?? 4.90) ?>;
const DOSTAVA_BREZPLACNA = <?= (float)($nastavitve['dostava_brezplacna_nad'] ?? 49.00) ?>;

const ARTIKLI_DATA = <?= json_encode(mystaia_artikli(), JSON_UNESCAPED_UNICODE) ?>;

// ── NAVIGACIJA ───────────────────────────────────────────────────────────────
let aktivnaStran = 'trgovina';
function pokaziStran(ime, gumb) {
    document.querySelectorAll('.my-stran').forEach(s => s.classList.remove('aktivna'));
    const stran = document.getElementById('stran-' + ime);
    if (stran) stran.classList.add('aktivna');
    if (gumb) {
        document.querySelectorAll('.my-nav-gumb').forEach(g => g.classList.remove('aktiven'));
        gumb.classList.add('aktiven');
    }
    aktivnaStran = ime;
    if (ime === 'kosarica' || ime === 'narocilo') osveziKosarico();
    if (ime === 'narocilo') osveziPovzetek();
}

// ── TOAST ────────────────────────────────────────────────────────────────────
function toast(sporocilo, tip = 'uspeh') {
    const el = document.createElement('div');
    el.className = 'my-toast ' + tip;
    el.textContent = sporocilo;
    document.body.appendChild(el);
    setTimeout(() => el.remove(), 3100);
}

// ── FILTRIRANJE ──────────────────────────────────────────────────────────────
function filtriraj(kat, gumb) {
    document.querySelectorAll('.my-filter').forEach(g => g.classList.remove('aktiven'));
    gumb.classList.add('aktiven');
    document.querySelectorAll('.my-kartica-artikel').forEach(k => {
        const ujema = kat === 'vse' || k.dataset.kategorija === kat;
        k.style.display = ujema ? 'flex' : 'none';
    });
}

// ── DODAJ V KOŠARICO ──────────────────────────────────────────────────────────
async function dodajVKosarico(id, gumb) {
    if (gumb) { gumb.disabled = true; gumb.textContent = '...'; }
    const rez = await apiKlic('dodaj', {id, kolicina:1});
    if (rez.ok) {
        posodobiKosaricaStevilo(rez.data.stevilo);
        toast('✦ Dodano v košarico');
        if (gumb) {
            gumb.classList.add('dodano');
            gumb.textContent = '✓ Dodano';
            setTimeout(() => {
                gumb.classList.remove('dodano');
                gumb.textContent = '+ Dodaj';
                gumb.disabled = false;
            }, 1600);
        }
    } else {
        toast(rez.napaka || 'Napaka', 'napaka');
        if (gumb) { gumb.disabled = false; gumb.textContent = '+ Dodaj'; }
    }
}

// ── ARTIKEL DETAIL ────────────────────────────────────────────────────────────
function pokaziArtikel(id) {
    const a = ARTIKLI_DATA.find(x => x.id === id);
    if (!a) return;
    document.getElementById('detail-vsebina').innerHTML = `
      <div class="my-detail-okvir">
        <div class="my-detail-slika">${a.ikona}</div>
        <div class="my-detail-info">
          <div class="my-detail-kategorija">${a.kategorija}</div>
          <div class="my-detail-ime">${a.ime}</div>
          <div class="my-detail-cena">€${a.cena.toFixed(2).replace('.',',')}</div>
          <div class="my-detail-opis">${a.opis}</div>
          <div class="my-detail-locilo"></div>
          <div class="my-detail-meta">
            Teža/volumen: <span>${a.teza || '/'}</span><br>
            Zaloga: <span>${a.zalogo > 0 ? a.zalogo + ' kos' : 'Razprodano'}</span>
          </div>
          <div class="my-detail-kolicina">
            <button class="my-kol-gumb" onclick="spremiKolicinoDetail(-1)">−</button>
            <span class="my-kol-stevilo" id="detail-kolicina">1</span>
            <button class="my-kol-gumb" onclick="spremiKolicinoDetail(1)">+</button>
          </div>
          ${a.zalogo > 0
            ? `<button class="my-detail-gumb" onclick="dodajDetailKosarico('${a.id}')">Dodaj v košarico</button>`
            : `<span style="color:var(--my-rdeca);font-size:.85rem">Razprodano</span>`}
          <div style="display:flex;gap:8px;flex-wrap:wrap;margin-top:4px">
            ${(a.znacke||[]).map(z=>`<span class="my-znacka ${z}">${z}</span>`).join('')}
          </div>
        </div>
      </div>`;
    document.getElementById('stran-artikel')._aktArtikel = a;
    pokaziStran('artikel', null);
}

function spremiKolicinoDetail(delta) {
    const el = document.getElementById('detail-kolicina');
    let v = parseInt(el.textContent) + delta;
    if (v < 1) v = 1;
    if (v > 99) v = 99;
    el.textContent = v;
}

async function dodajDetailKosarico(id) {
    const kolicina = parseInt(document.getElementById('detail-kolicina').textContent);
    const rez = await apiKlic('dodaj', {id, kolicina});
    if (rez.ok) { toast('✦ Dodano v košarico'); posodobiKosaricaStevilo(rez.data.stevilo); }
    else toast(rez.napaka || 'Napaka', 'napaka');
}

// ── KOŠARICA ─────────────────────────────────────────────────────────────────
async function osveziKosarico() {
    const rez = await apiKlic('kosarica', {});
    if (!rez.ok) return;
    const k = rez.data;
    posodobiKosaricaStevilo(k.stevilo);
    const el = document.getElementById('kosarica-vsebina');
    if (!k.stevilo) {
        el.innerHTML = `<div class="my-kosarica-prazna"><div class="my-kosarica-prazna-ikona">🛍</div><p>Vaša košarica je prazna.</p></div>`;
        return;
    }
    const dostava = k.vsota >= DOSTAVA_BREZPLACNA ? 0 : DOSTAVA_CENA;
    const skupaj  = k.vsota + dostava;
    el.innerHTML = `
      <div class="my-kosarica-seznam">
        ${Object.entries(k.postavke).map(([aid,vnos]) => `
          <div class="my-kosarica-vnos">
            <div class="my-kosarica-ikona">${vnos.artikel.ikona}</div>
            <div>
              <div class="my-kosarica-ime">${vnos.artikel.ime}</div>
              <div class="my-kosarica-pod">${vnos.artikel.teza || ''}</div>
            </div>
            <div class="my-kosarica-ctrl">
              <button class="my-kol-gumb" onclick="spremiKolicino('${aid}', -1)">−</button>
              <span style="min-width:24px;text-align:center;font-size:.9rem" id="kol-${aid}">${vnos.kolicina}</span>
              <button class="my-kol-gumb" onclick="spremiKolicino('${aid}', 1)">+</button>
              <span class="my-kosarica-cena">€${(vnos.artikel.cena * vnos.kolicina).toFixed(2).replace('.',',')}</span>
              <button class="my-kosarica-odstrani" onclick="odstraniIzKosarice('${aid}')">✕</button>
            </div>
          </div>`).join('')}
      </div>
      <div class="my-povzetek">
        <div class="my-povzetek-vrsta"><span>Vmesni seštevek</span><span>€${k.vsota.toFixed(2).replace('.',',')}</span></div>
        <div class="my-povzetek-vrsta">
          <span>Dostava</span>
          <span>${dostava === 0 ? '<span style="color:var(--my-zelena)">Brezplačna ✦</span>' : '€' + dostava.toFixed(2).replace('.',',')}</span>
        </div>
        <div class="my-povzetek-skupaj">
          <span class="my-povzetek-skupaj-lbl">Skupaj</span>
          <span class="my-povzetek-skupaj-cena">€${skupaj.toFixed(2).replace('.',',')}</span>
        </div>
        ${k.vsota < DOSTAVA_BREZPLACNA ? `<div style="font-size:.72rem;color:var(--my-siva);text-align:center">Do brezplačne dostave manjka €${(DOSTAVA_BREZPLACNA-k.vsota).toFixed(2).replace('.',',')}</div>` : ''}
        <button class="my-gumb-narocilo" onclick="pokaziStran('narocilo',null)">Zaključi naročilo →</button>
      </div>`;
}

async function spremiKolicino(id, delta) {
    const el = document.getElementById('kol-'+id);
    if (!el) return;
    const nova = parseInt(el.textContent) + delta;
    if (nova < 1) { odstraniIzKosarice(id); return; }
    el.textContent = nova;
    await apiKlic('kolicina', {id, kolicina:nova});
    await osveziKosarico();
}

async function odstraniIzKosarice(id) {
    await apiKlic('odstrani', {id});
    await osveziKosarico();
    toast('Artikel odstranjen');
}

// ── NAROČILO POVZETEK ─────────────────────────────────────────────────────────
async function osveziPovzetek() {
    const rez = await apiKlic('kosarica', {});
    if (!rez.ok) return;
    const k = rez.data;
    const dostava = k.vsota >= DOSTAVA_BREZPLACNA ? 0 : DOSTAVA_CENA;
    document.getElementById('narocilo-povzetek').innerHTML = `
      <div class="my-povzetek">
        ${Object.entries(k.postavke).map(([,v]) => `
          <div class="my-povzetek-vrsta">
            <span>${v.artikel.ikona} ${v.artikel.ime} ×${v.kolicina}</span>
            <span>€${(v.artikel.cena*v.kolicina).toFixed(2).replace('.',',')}</span>
          </div>`).join('')}
        <div class="my-form-locilo" style="margin:8px 0"></div>
        <div class="my-povzetek-vrsta"><span>Dostava</span><span>${dostava?'€'+dostava.toFixed(2).replace('.',','):'Brezplačna'}</span></div>
        <div class="my-povzetek-skupaj">
          <span class="my-povzetek-skupaj-lbl">Skupaj</span>
          <span class="my-povzetek-skupaj-cena">€${(k.vsota+dostava).toFixed(2).replace('.',',')}</span>
        </div>
      </div>`;
}

// ── ODDAJ NAROČILO ────────────────────────────────────────────────────────────
async function oddajNarocilo() {
    const podatki = {
        ime:     document.getElementById('n-ime').value.trim(),
        email:   document.getElementById('n-email').value.trim(),
        tel:     document.getElementById('n-tel').value.trim(),
        naslov:  document.getElementById('n-naslov').value.trim(),
        placilo: document.getElementById('n-placilo').value,
        opomba:  document.getElementById('n-opomba').value.trim(),
    };
    const gumb = event.currentTarget;
    gumb.disabled = true; gumb.textContent = 'Pošiljam...';
    const rez = await apiKlic('narocilo', podatki);
    gumb.disabled = false; gumb.textContent = 'Potrdi naročilo ✦';
    if (rez.ok) {
        document.getElementById('potrjeno-id').textContent = rez.data.id;
        pokaziStran('potrjeno', null);
        posodobiKosaricaStevilo(0);
    } else {
        toast(rez.napaka || 'Napaka pri naročilu', 'napaka');
    }
}

// ── ADMIN: STATUS NAROČILA ────────────────────────────────────────────────────
async function spremiStatus(id, status) {
    const rez = await apiKlic('admin_status', {id, status});
    if (rez.ok) toast('Status posodobljen: ' + status);
    else toast(rez.napaka, 'napaka');
}

// ── ADMIN: ARTIKEL FORMA ──────────────────────────────────────────────────────
function odpriFormArtikel(artikel) {
    const modal = document.getElementById('modal-artikel');
    if (!modal) return;
    modal.style.display = 'flex';
    if (artikel) {
        document.getElementById('form-artikel-naslov').textContent = 'Uredi artikel';
        document.getElementById('fa-id').value        = artikel.id;
        document.getElementById('fa-ime').value       = artikel.ime;
        document.getElementById('fa-cena').value      = artikel.cena;
        document.getElementById('fa-zaloga').value    = artikel.zalogo;
        document.getElementById('fa-kategorija').value= artikel.kategorija;
        document.getElementById('fa-ikona').value     = artikel.ikona;
        document.getElementById('fa-opis-kratek').value = artikel.opis_kratek || '';
        document.getElementById('fa-opis').value      = artikel.opis || '';
        document.getElementById('fa-teza').value      = artikel.teza || '';
        document.getElementById('fa-aktivno').checked = artikel.aktivno;
    } else {
        document.getElementById('form-artikel-naslov').textContent = 'Nov artikel';
        ['fa-id','fa-ime','fa-cena','fa-zaloga','fa-ikona','fa-opis-kratek','fa-opis','fa-teza'].forEach(id => {
            document.getElementById(id).value = '';
        });
        document.getElementById('fa-aktivno').checked = true;
        document.getElementById('fa-id').value = 'mys-' + Date.now();
    }
}

function zapriModal() {
    const modal = document.getElementById('modal-artikel');
    if (modal) modal.style.display = 'none';
}

async function shraniArtikel() {
    const artikel = {
        id:          document.getElementById('fa-id').value,
        ime:         document.getElementById('fa-ime').value.trim(),
        cena:        parseFloat(document.getElementById('fa-cena').value),
        zalogo:      parseInt(document.getElementById('fa-zaloga').value),
        kategorija:  document.getElementById('fa-kategorija').value,
        ikona:       document.getElementById('fa-ikona').value || '📦',
        opis_kratek: document.getElementById('fa-opis-kratek').value.trim(),
        opis:        document.getElementById('fa-opis').value.trim(),
        teza:        document.getElementById('fa-teza').value.trim(),
        aktivno:     document.getElementById('fa-aktivno').checked,
        znacke:      [],
    };
    if (!artikel.ime) { toast('Ime je obvezno', 'napaka'); return; }
    const rez = await apiKlic('admin_shrani_artikel', {artikel});
    if (rez.ok) { toast('Artikel shranjen ✦'); zapriModal(); setTimeout(()=>location.reload(),700); }
    else toast(rez.napaka, 'napaka');
}

async function briziArtikel(id, ime) {
    if (!confirm(`Izbriši artikel "${ime}"?`)) return;
    const rez = await apiKlic('admin_brisi_artikel', {id});
    if (rez.ok) {
        const vrstica = document.getElementById('art-vrstica-' + id);
        if (vrstica) vrstica.remove();
        toast('Artikel izbrisan');
    } else toast(rez.napaka, 'napaka');
}

// ── POMOČNIKI ─────────────────────────────────────────────────────────────────
function posodobiKosaricaStevilo(n) {
    const el = document.getElementById('nav-kosarica-st');
    if (el) el.textContent = n > 0 ? n : '';
}

async function apiKlic(akcija, podatki) {
    try {
        const r = await fetch(API, {
            method:'POST',
            headers:{'Content-Type':'application/json'},
            body: JSON.stringify({akcija, ...podatki})
        });
        return await r.json();
    } catch(e) {
        return {ok:false, napaka:'Napaka povezave: ' + e.message};
    }
}

// Modal zapri ob kliku zunaj
document.addEventListener('click', e => {
    const modal = document.getElementById('modal-artikel');
    if (modal && e.target === modal) zapriModal();
});
</script>

</body>
</html>
