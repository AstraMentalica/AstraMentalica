/* ============================================================================
   DATOTEKA: skripte.js
   NAMEN:    JavaScript logika nadzornega centra ASTRA
   VERZIJA:  1.0
   DATUM:    2026-04-26
   ============================================================================ */

// ══════════════════════════════════════════════════════
// GLOBALNO STANJE
// ══════════════════════════════════════════════════════
const NC = {
  zavihki: [],
  aktivniZavihek: null,
  aktivniPogled: 'brskalnik',
  trenutnaPot: '',
  oznacene: new Set(),
  stevec: 0,
  api: window.NC_API || '/api_zate.php',
  koren: window.NC_KOREN || '',
};

// ══════════════════════════════════════════════════════
// API KLIC
// ══════════════════════════════════════════════════════
async function apiKlic(akcija, params = {}) {
  try {
    const url = new URL(NC.api, window.location.origin);
    url.searchParams.set('akcija', akcija);
    for (const [k, v] of Object.entries(params)) {
      url.searchParams.set(k, v);
    }
    const resp = await fetch(url.toString());
    return await resp.json();
  } catch (e) {
    console.error('API napaka:', e);
    return { uspeh: false, napaka: e.message };
  }
}

async function apiShrani(pot, vsebina) {
  try {
    const resp = await fetch('/ASTRA/nadzor/shrani.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ pot, vsebina })
    });
    return await resp.json();
  } catch (e) {
    return { uspeh: false, napaka: e.message };
  }
}

// ══════════════════════════════════════════════════════
// TOAST
// ══════════════════════════════════════════════════════
function toast(msg, tip = 'info') {
  const el = document.getElementById('toast');
  if (!el) return;
  el.textContent = msg;
  el.className = 'show ' + tip;
  clearTimeout(el._t);
  el._t = setTimeout(() => el.classList.remove('show'), 3000);
}

// ══════════════════════════════════════════════════════
// STATUS
// ══════════════════════════════════════════════════════
function setStatus(msg, tip = '') {
  const el = document.getElementById('status-msg');
  if (el) { el.textContent = msg; el.className = 'status-item ' + tip; }
}

// ══════════════════════════════════════════════════════
// MODAL
// ══════════════════════════════════════════════════════
function odpriModal(naslov, opis, body, gumbi) {
  document.getElementById('modal-title').textContent = naslov;
  document.getElementById('modal-desc').textContent = opis;
  document.getElementById('modal-body').innerHTML = body;
  document.getElementById('modal-gumbi').innerHTML = gumbi;
  document.getElementById('modal-overlay').classList.add('show');
  setTimeout(() => document.querySelector('#modal input, #modal textarea, #modal select')?.focus(), 50);
}
function zapriModal() {
  document.getElementById('modal-overlay').classList.remove('show');
}
document.addEventListener('keydown', e => { if (e.key === 'Escape') zapriModal(); });

// ══════════════════════════════════════════════════════
// ZAVIHKI
// ══════════════════════════════════════════════════════
function ustvariZavihek(id, ime, ikona, pogled) {
  // Preveri če že obstaja
  const obs = NC.zavihki.find(z => z.id === id);
  if (obs) { aktivirajZavihek(obs.id); return; }

  NC.zavihki.push({ id, ime, ikona, pogled });
  renderZavihki();
  aktivirajZavihek(id);
}

function aktivirajZavihek(id) {
  NC.aktivniZavihek = id;
  const z = NC.zavihki.find(t => t.id === id);
  if (!z) return;

  // Skrij vse poglede
  document.querySelectorAll('.pogled-panel').forEach(p => p.classList.remove('aktiven'));

  // Pokaži pravi
  const panel = document.getElementById('pogled-' + z.pogled);
  if (panel) panel.classList.add('aktiven');
  NC.aktivniPogled = z.pogled;

  renderZavihki();
}

function zapriZavihek(id, e) {
  e?.stopPropagation();
  const z = NC.zavihki.find(t => t.id === id);
  if (!z) return;
  if (z.spremenjen && !confirm('Neshranjene spremembe. Zapri vseeno?')) return;
  NC.zavihki = NC.zavihki.filter(t => t.id !== id);
  if (NC.aktivniZavihek === id) {
    const zadnji = NC.zavihki.slice(-1)[0];
    if (zadnji) aktivirajZavihek(zadnji.id);
    else {
      NC.aktivniZavihek = null;
      document.querySelectorAll('.pogled-panel').forEach(p => p.classList.remove('aktiven'));
    }
  }
  renderZavihki();
}

function renderZavihki() {
  const cont = document.getElementById('zavihki');
  if (!cont) return;
  if (!NC.zavihki.length) {
    cont.innerHTML = '<div style="padding:0 8px;display:flex;align-items:center;color:var(--text3);font-size:10px">← Odpri datoteko ali pogled</div>';
    return;
  }
  cont.innerHTML = NC.zavihki.map(z => `
    <div class="zavihek ${z.id === NC.aktivniZavihek ? 'aktiven' : ''} ${z.spremenjen ? 'spremenjen' : ''}"
         onclick="aktivirajZavihek('${z.id}')">
      <span style="font-size:10px">${z.ikona || '📄'}</span>
      <span>${z.ime}</span>
      <span class="zavihek-zapri" onclick="zapriZavihek('${z.id}',event)">×</span>
    </div>
  `).join('');
}

// ══════════════════════════════════════════════════════
// DATOTEČNI BRSKALNIK
// ══════════════════════════════════════════════════════
async function odpriMapo(pot) {
  NC.trenutnaPot = pot;
  setStatus('Nalagam: ' + pot);

  const data = await apiKlic('seznam', { tip: 'vse' });
  if (!data.uspeh) { toast('Napaka pri branju: ' + (data.napaka || '?'), 'err'); return; }

  // Filtriraj na trenutno pot
  const vse = data.datoteke || [];
  const vsebina = filtrirájMapo(vse, pot);

  renderBrskalnik(pot, vsebina);
  posodobiPotNavigacijo(pot);
  setStatus(vsebina.mape.length + ' map, ' + vsebina.datoteke.length + ' datotek', 'ok');
}

function filtrirájMapo(vse, pot) {
  const normPot = pot ? pot.replace(/\/$/, '') : '';
  const mape = new Set();
  const datoteke = [];

  vse.forEach(d => {
    const relPot = d.pot.replace(/\\/g, '/');
    const brezKorena = normPot ? relPot.replace(normPot + '/', '') : relPot;

    if (!brezKorena.startsWith(normPot === '' ? '' : '')) {
      if (normPot && !relPot.startsWith(normPot + '/')) return;
    }

    const ostanek = normPot ? relPot.slice(normPot.length + 1) : relPot;
    if (!ostanek) return;

    const deli = ostanek.split('/');
    if (deli.length === 1) {
      datoteke.push(d);
    } else {
      mape.add((normPot ? normPot + '/' : '') + deli[0]);
    }
  });

  return { mape: [...mape], datoteke };
}

function renderBrskalnik(pot, vsebina) {
  const cont = document.getElementById('brskalnik-vsebina');
  if (!cont) return;

  // Orodna vrstica označevanja
  const oznacenoHtml = `
    <div class="oznaci-orodna" id="oznaci-orodna">
      <button class="top-btn" onclick="oznáciVse()">☑ Vse</button>
      <button class="top-btn" onclick="odznaci()">☐ Nič</button>
      <button class="top-btn zeleno" onclick="izvozOznacene('txt')">↓ TXT</button>
      <button class="top-btn" onclick="izvozOznacene('zip')">↓ ZIP</button>
      <button class="top-btn rdece" onclick="brisiOznacene()">🗑 Briši</button>
      <span style="margin-left:auto;color:var(--text3);font-size:10px" id="st-oznacenih">0 označenih</span>
    </div>
  `;

  let html = oznacenoHtml + '<div class="datoteke-grid">';

  // Mape
  vsebina.mape.forEach(m => {
    const ime = m.split('/').pop();
    html += `
      <div class="dat-kartica dat-mapa" onclick="odpriMapo('${m}')">
        <input type="checkbox" onclick="event.stopPropagation();toggleOznaceno('${m}')">
        <div class="dat-ikona">📁</div>
        <div class="dat-ime">${ime}/</div>
        <div class="dat-meta dat-mapa">mapa</div>
      </div>
    `;
  });

  // Datoteke
  vsebina.datoteke.forEach(d => {
    const ime = d.pot.split('/').pop();
    const ext = d.ext || '?';
    const ikona = tipIkona(ext);
    const vel = formatVelikost(d.velikost || 0);
    html += `
      <div class="dat-kartica" id="dat-${btoa(d.pot).replace(/=/g,'')}" 
           onclick="odpriDatoteko('${d.pot}')"
           ondblclick="odpriUrejevalnik('${d.pot}')">
        <input type="checkbox" onclick="event.stopPropagation();toggleOznaceno('${d.pot}')">
        <div class="dat-ikona">${ikona}</div>
        <div class="dat-ime">${ime}</div>
        <div class="dat-meta">${ext} · ${vel}</div>
      </div>
    `;
  });

  html += '</div>';
  cont.innerHTML = html;
}

function posodobiPotNavigacijo(pot) {
  const el = document.getElementById('pot-nav');
  if (!el) return;

  const deli = pot ? pot.split('/').filter(Boolean) : [];
  let html = `<span class="pot-del ${!deli.length ? 'zadnji' : ''}" onclick="odpriMapo('')">⌂ root</span>`;

  deli.forEach((del, i) => {
    const celaPot = deli.slice(0, i + 1).join('/');
    html += `<span class="pot-locilo">/</span>`;
    html += `<span class="pot-del ${i === deli.length - 1 ? 'zadnji' : ''}" onclick="odpriMapo('${celaPot}')">${del}</span>`;
  });

  el.innerHTML = html;
}

// ══════════════════════════════════════════════════════
// ODPIRANJE IN UREJANJE DATOTEK
// ══════════════════════════════════════════════════════
async function odpriDatoteko(pot) {
  setStatus('Nalagam: ' + pot);
  const data = await apiKlic('preberi', { pot });

  if (!data.uspeh) { toast('Napaka: ' + (data.napaka || '?'), 'err'); return; }

  // Pokaži v desnem panelu
  const panel = document.getElementById('desni-panel');
  const vsebina = document.getElementById('desni-vsebina');
  const glava = document.getElementById('desni-glava-naziv');

  if (panel && vsebina) {
    panel.classList.add('odprt');
    if (glava) glava.textContent = pot.split('/').pop();
    vsebina.innerHTML = `
      <div style="font-size:10px;color:var(--mist);margin-bottom:8px">${pot}</div>
      <div style="font-size:10px;color:var(--text3);margin-bottom:12px">${formatVelikost(data.velikost || 0)} · ${data.ext}</div>
      <pre style="white-space:pre-wrap;word-break:break-word;color:var(--text2);font-size:10px;line-height:1.6">${escHtml(trunciraj(data.vsebina || '', 2000))}</pre>
      <div style="display:flex;gap:6px;margin-top:12px;flex-wrap:wrap">
        <button class="top-btn" onclick="odpriUrejevalnik('${pot}')">✏ Uredi</button>
        <button class="top-btn zeleno" onclick="preverStandard('${pot}')">✓ Standard</button>
        <button class="top-btn" onclick="narediBackup('${pot}')">🛡 Backup</button>
        <button class="top-btn rdece" onclick="brisiDatoteko('${pot}')">🗑 Briši</button>
      </div>
    `;
  }
  setStatus('Odprto: ' + pot, 'ok');
}

async function odpriUrejevalnik(pot) {
  setStatus('Nalagam v urejevalnik: ' + pot);
  const data = await apiKlic('preberi', { pot });
  if (!data.uspeh) { toast('Napaka: ' + (data.napaka || '?'), 'err'); return; }

  const id = 'ur-' + (++NC.stevec);
  const ime = pot.split('/').pop();

  NC.zavihki.push({ id, ime, ikona: tipIkona(data.ext), pogled: 'urejevalnik-' + id, pot, vsebina: data.vsebina, spremenjen: false });

  // Ustvari panel
  const pogled = document.getElementById('pogled');
  if (pogled) {
    const div = document.createElement('div');
    div.className = 'pogled-panel';
    div.id = 'pogled-urejevalnik-' + id;
    div.innerHTML = `
      <div class="urejevalnik-glava">
        <span class="pot">${pot}</span>
        <button class="top-btn zeleno" onclick="shraniDatoteko('${id}','${pot}')">💾 Shrani</button>
        <button class="top-btn" onclick="preverStandard('${pot}')">✓ Standard</button>
        <button class="top-btn" onclick="narediBackup('${pot}')">🛡 Backup</button>
      </div>
      <textarea class="koda" id="koda-${id}" 
        onkeydown="handleTab(event,'${id}')"
        oninput="onKodaInput('${id}')"
        spellcheck="false">${escHtml(data.vsebina || '')}</textarea>
    `;
    pogled.appendChild(div);
  }

  renderZavihki();
  aktivirajZavihek(id);
  setStatus('Urejevalnik: ' + pot, 'ok');
}

async function shraniDatoteko(zavihekId, pot) {
  const ta = document.getElementById('koda-' + zavihekId);
  if (!ta) { toast('Urejevalnik ni najden', 'err'); return; }

  const vsebina = ta.value;
  const data = await apiShrani(pot, vsebina);

  if (data.uspeh) {
    const z = NC.zavihki.find(t => t.id === zavihekId);
    if (z) { z.spremenjen = false; z.vsebina = vsebina; }
    renderZavihki();
    toast('Shranjeno: ' + pot.split('/').pop(), 'ok');
  } else {
    toast('Napaka: ' + (data.napaka || '?'), 'err');
  }
}

function onKodaInput(id) {
  const z = NC.zavihki.find(t => t.id === id);
  if (z) { z.spremenjen = true; renderZavihki(); }
}

function handleTab(e, id) {
  if (e.key === 'Tab') {
    e.preventDefault();
    const el = document.getElementById('koda-' + id);
    const s = el.selectionStart;
    el.value = el.value.substring(0, s) + '    ' + el.value.substring(el.selectionEnd);
    el.selectionStart = el.selectionEnd = s + 4;
    onKodaInput(id);
  }
  if (e.ctrlKey && e.key === 's') { e.preventDefault(); shraniDatoteko(id, NC.zavihki.find(t=>t.id===id)?.pot); }
}

// ══════════════════════════════════════════════════════
// PREVERJANJE STANDARDOV
// ══════════════════════════════════════════════════════
async function preverStandard(pot) {
  const data = await apiKlic('preberi', { pot });
  if (!data.uspeh) return;

  const txt = data.vsebina || '';
  const napake = [];

  if (pot.endsWith('.php')) {
    if (!txt.includes('* DATOTEKA:')) napake.push({ tip: 'opozorilo', msg: 'Manjka glava (DATOTEKA/NAMEN/NIVO...)' });
    if (!txt.includes('pot.php')) napake.push({ tip: 'opozorilo', msg: 'Ni vključena pot.php' });
    if (txt.includes('die(') || txt.includes('exit(')) napake.push({ tip: 'napaka', msg: 'Prepovedano: die() ali exit()' });
    if (txt.match(/\$_GET\[|\$_POST\[/)) napake.push({ tip: 'napaka', msg: 'Direktna raba $_GET/$_POST' });
    if (txt.match(/\/var\/www|C:\\/)) napake.push({ tip: 'napaka', msg: 'Hardcoded absolutna pot' });
    if (txt.split('\n').length > 300) napake.push({ tip: 'opozorilo', msg: 'Datoteka > 300 vrstic' });
  }

  const vsebina = napake.length
    ? napake.map(n => `<div class="val-item"><span class="val-tip ${n.tip}">${n.tip.toUpperCase()}</span><span class="val-sporocilo">${n.msg}</span></div>`).join('')
    : '<div class="val-item"><span class="val-tip ok">✓ OK</span><span class="val-sporocilo">Vse je po standardu.</span></div>';

  odpriModal('STANDARD: ' + pot.split('/').pop(), '', vsebina,
    '<button class="modal-btn" onclick="zapriModal()">Zapri</button>' +
    '<button class="modal-btn primary" onclick="dodajGlavo(\'' + pot + '\');zapriModal()">Dodaj glavo</button>'
  );
}

async function dodajGlavo(pot) {
  const data = await apiKlic('preberi', { pot });
  if (!data.uspeh) return;

  const ime = pot.split('/').pop();
  const danes = new Date().toISOString().substring(0, 10);
  const glava = `<?php\n/**\n * DATOTEKA: ${ime}\n * POT:      ${pot}\n * NAMEN:    \n * NIVO:     \n * ODVISNO:  pot.php\n * VERZIJA:  1.0\n * DATUM:    ${danes}\n */\n\n`;

  const nova = data.vsebina.startsWith('<?php')
    ? data.vsebina.replace('<?php', glava.trimEnd())
    : glava + data.vsebina;

  await apiShrani(pot, nova);
  toast('Glava dodana: ' + ime, 'ok');
}

// ══════════════════════════════════════════════════════
// BACKUP
// ══════════════════════════════════════════════════════
async function narediBackup(pot) {
  odpriModal(
    'BACKUP: ' + pot.split('/').pop(),
    'Ustvari varnostno kopijo v ASTRA/razvoj/smeti/',
    '<input type="text" id="backup-opomba" placeholder="Opomba (npr. pred spremembo)">',
    '<button class="modal-btn" onclick="zapriModal()">Prekliči</button>' +
    '<button class="modal-btn primary" onclick="izvediBackup(\'' + pot + '\')">Ustvari backup</button>'
  );
}

async function izvediBackup(pot) {
  const opomba = document.getElementById('backup-opomba')?.value || 'backup';
  const ts = new Date().toISOString().replace(/[:.]/g, '-').substring(0, 19);
  const ime = pot.replace(/\//g, '_');
  const backupPot = 'ASTRA/razvoj/smeti/' + ime + '__' + ts + '__' + opomba;

  try {
    const data = await apiKlic('preberi', { pot });
    if (data.uspeh) {
      await apiShrani(backupPot, data.vsebina);
      zapriModal();
      toast('Backup: ' + backupPot.split('/').pop(), 'ok');
    }
  } catch (e) {
    toast('Napaka backupa', 'err');
  }
}

// ══════════════════════════════════════════════════════
// BRISANJE
// ══════════════════════════════════════════════════════
async function brisiDatoteko(pot) {
  if (!confirm('Datoteka bo premaknjena v ASTRA/razvoj/smeti/\nNadaljujem?')) return;
  await narediBackup(pot);
  toast('Datoteka premaknjena v smeti: ' + pot.split('/').pop(), 'info');
}

// ══════════════════════════════════════════════════════
// OZNAČEVANJE
// ══════════════════════════════════════════════════════
function toggleOznaceno(pot) {
  if (NC.oznacene.has(pot)) NC.oznacene.delete(pot);
  else NC.oznacene.add(pot);
  posodobiOznacene();
}

function oznáciVse() {
  document.querySelectorAll('.dat-kartica input[type=checkbox]').forEach(cb => {
    cb.checked = true;
    const pot = cb.closest('.dat-kartica')?.querySelector('.dat-ikona')?.nextSibling?.textContent?.trim();
  });
  toast('Vse označeno', 'info');
}

function odznaci() {
  NC.oznacene.clear();
  document.querySelectorAll('.dat-kartica input[type=checkbox]').forEach(cb => cb.checked = false);
  posodobiOznacene();
}

function posodobiOznacene() {
  const el = document.getElementById('st-oznacenih');
  if (el) el.textContent = NC.oznacene.size + ' označenih';
}

async function izvozOznacene(format) {
  if (!NC.oznacene.size) { toast('Nič ni označeno', 'info'); return; }
  if (format === 'txt') {
    let vsebina = '';
    for (const pot of NC.oznacene) {
      const data = await apiKlic('preberi', { pot });
      if (data.uspeh) {
        vsebina += `\n\n// ===== ${pot} =====\n\n${data.vsebina}`;
      }
    }
    prenesiTxt(vsebina, 'izvoz_' + new Date().toISOString().substring(0,10) + '.txt');
  }
  toast('Izvoženo ' + NC.oznacene.size + ' datotek', 'ok');
}

function prenesiTxt(vsebina, ime) {
  const a = document.createElement('a');
  a.href = URL.createObjectURL(new Blob([vsebina], { type: 'text/plain' }));
  a.download = ime;
  a.click();
}

// ══════════════════════════════════════════════════════
// NOVA DATOTEKA / MAPA
// ══════════════════════════════════════════════════════
function novaDataoteka() {
  odpriModal(
    'NOVA DATOTEKA',
    'Vnesi pot nove datoteke (relativno od root):',
    `<input type="text" id="nova-pot" placeholder="${NC.trenutnaPot ? NC.trenutnaPot + '/' : ''}nova.php">`,
    '<button class="modal-btn" onclick="zapriModal()">Prekliči</button>' +
    '<button class="modal-btn primary" onclick="ustvariNovo()">Ustvari</button>'
  );
}

async function ustvariNovo() {
  const pot = document.getElementById('nova-pot')?.value?.trim();
  if (!pot) return;
  const danes = new Date().toISOString().substring(0, 10);
  const ime = pot.split('/').pop();
  let template = '';

  if (pot.endsWith('.php')) {
    template = `<?php\n/**\n * DATOTEKA: ${ime}\n * POT:      ${pot}\n * NAMEN:    \n * NIVO:     \n * ODVISNO:  pot.php\n * VERZIJA:  1.0\n * DATUM:    ${danes}\n */\n\nrequire_once __DIR__ . '/pot.php';\n\n// TODO\n`;
  } else if (pot.endsWith('.md')) {
    template = `# ${ime.replace('.md', '')}\n\n## Opis\n\n*Vsebina...*\n\n---\n*verzija 1.0 — ${danes}*\n`;
  } else if (pot.endsWith('.json')) {
    template = `{\n    "ime": "",\n    "verzija": "1.0",\n    "datum": "${danes}"\n}\n`;
  }

  await apiShrani(pot, template);
  zapriModal();
  toast('Ustvarjeno: ' + ime, 'ok');
  odpriUrejevalnik(pot);
}

function novaMapa() {
  odpriModal(
    'NOVA MAPA',
    'Vnesi pot nove mape:',
    `<input type="text" id="nova-mapa-pot" placeholder="${NC.trenutnaPot ? NC.trenutnaPot + '/' : ''}nova_mapa">`,
    '<button class="modal-btn" onclick="zapriModal()">Prekliči</button>' +
    '<button class="modal-btn primary" onclick="ustvariMapo()">Ustvari</button>'
  );
}

async function ustvariMapo() {
  const pot = document.getElementById('nova-mapa-pot')?.value?.trim();
  if (!pot) return;
  // Ustvari .gitkeep da mapa obstaja
  await apiShrani(pot + '/.gitkeep', '');
  zapriModal();
  toast('Mapa ustvarjena: ' + pot, 'ok');
  odpriMapo(pot);
}

// ══════════════════════════════════════════════════════
// ISKANJE
// ══════════════════════════════════════════════════════
async function iskanje(pojem) {
  if (!pojem || pojem.length < 2) return;
  setStatus('Iščem: ' + pojem);

  const data = await apiKlic('isci', { pojem, tip: 'vse' });
  if (!data.uspeh) { toast('Napaka iskanja', 'err'); return; }

  const id = 'isci-' + (++NC.stevec);
  NC.zavihki.push({ id, ime: '🔍 ' + pojem, ikona: '🔍', pogled: 'iskanje-' + id });

  const pogled = document.getElementById('pogled');
  if (pogled) {
    const div = document.createElement('div');
    div.className = 'pogled-panel';
    div.id = 'pogled-iskanje-' + id;
    const zadetki = data.zadetki || [];
    div.innerHTML = `
      <div class="urejevalnik-glava">
        <span class="pot">Rezultati iskanja: "${pojem}" — ${zadetki.length} zadetkov</span>
      </div>
      <div style="overflow-y:auto;flex:1;padding:12px;display:flex;flex-direction:column;gap:4px">
        ${zadetki.map(z => `
          <div style="padding:8px;background:var(--panel);border:1px solid var(--border);cursor:pointer;display:flex;gap:8px;align-items:center;font-size:11px;transition:all 0.1s"
               onclick="odpriDatoteko('${z}')"
               ondblclick="odpriUrejevalnik('${z}')"
               onmouseover="this.style.borderColor='var(--border2)'"
               onmouseout="this.style.borderColor='var(--border)'">
            <span>${tipIkona(z.split('.').pop())}</span>
            <span style="color:var(--gold2)">${z.split('/').pop()}</span>
            <span style="color:var(--text3)">${z}</span>
          </div>
        `).join('')}
      </div>
    `;
    pogled.appendChild(div);
  }

  renderZavihki();
  aktivirajZavihek(id);
  setStatus(data.stevilo + ' zadetkov za: ' + pojem, 'ok');
}

// ══════════════════════════════════════════════════════
// DREVO V SIDEBARU
// ══════════════════════════════════════════════════════
async function napolniDrevo() {
  const el = document.getElementById('drevo-vsebina');
  if (!el) return;
  el.innerHTML = '<div style="padding:8px;color:var(--mist);font-size:10px">Nalagam...</div>';

  const data = await apiKlic('seznam', { tip: 'vse' });
  if (!data.uspeh) { el.innerHTML = '<div style="padding:8px;color:var(--red2)">Napaka</div>'; return; }

  // Zgradimo hierarhijo
  const hierarhija = {};
  (data.datoteke || []).forEach(d => {
    const deli = d.pot.replace(/\\/g, '/').split('/');
    let vozlisce = hierarhija;
    deli.forEach((del, i) => {
      if (i < deli.length - 1) {
        if (!vozlisce[del]) vozlisce[del] = { __mapa: true, __otroci: {} };
        vozlisce = vozlisce[del].__otroci;
      } else {
        vozlisce[del] = { __dat: true, __pot: d.pot, __ext: d.ext };
      }
    });
  });

  el.innerHTML = renderDrevo(hierarhija, 0, '');
}

function renderDrevo(vozlisce, nivo, predpona) {
  let html = '';
  const zamik = nivo * 12;

  Object.keys(vozlisce).sort((a, b) => {
    const aM = vozlisce[a].__mapa;
    const bM = vozlisce[b].__mapa;
    if (aM && !bM) return -1;
    if (!aM && bM) return 1;
    return a.localeCompare(b);
  }).forEach(kljuc => {
    const el = vozlisce[kljuc];
    const pot = predpona ? predpona + '/' + kljuc : kljuc;

    if (el.__mapa) {
      const id = 'drevo-' + btoa(pot).replace(/=/g, '');
      html += `
        <div class="drevo-mapa" style="padding-left:${8 + zamik}px" onclick="toggleDrevoMapa('${id}','${pot}')">
          <span class="drevo-ikona" id="ikona-${id}">📁</span>
          <span class="drevo-ime">${kljuc}/</span>
        </div>
        <div id="${id}" style="display:none">
          ${renderDrevo(el.__otroci, nivo + 1, pot)}
        </div>
      `;
    } else if (el.__dat) {
      html += `
        <div class="drevo-dat" style="padding-left:${8 + zamik}px"
             onclick="odpriDatoteko('${el.__pot}')"
             ondblclick="odpriUrejevalnik('${el.__pot}')">
          <span class="drevo-ikona">${tipIkona(el.__ext)}</span>
          <span class="drevo-ime">${kljuc}</span>
        </div>
      `;
    }
  });
  return html;
}

function toggleDrevoMapa(id, pot) {
  const el = document.getElementById(id);
  const ikona = document.getElementById('ikona-' + id);
  if (!el) return;
  if (el.style.display === 'none') {
    el.style.display = 'block';
    if (ikona) ikona.textContent = '📂';
  } else {
    el.style.display = 'none';
    if (ikona) ikona.textContent = '📁';
  }
}

// ══════════════════════════════════════════════════════
// SIDEBAR TOGGLE
// ══════════════════════════════════════════════════════
function toggleSidebar() {
  document.getElementById('sidebar')?.classList.toggle('skrit');
}

function toggleSekcijaSidebar(el) {
  el.classList.toggle('zaprt');
  const vsebina = el.nextElementSibling;
  if (vsebina) vsebina.classList.toggle('zaprt');
}

// ══════════════════════════════════════════════════════
// DESNI PANEL
// ══════════════════════════════════════════════════════
function zapriDesniPanel() {
  document.getElementById('desni-panel')?.classList.remove('odprt');
}

// ══════════════════════════════════════════════════════
// POMOŽNE
// ══════════════════════════════════════════════════════
function tipIkona(ext) {
  const m = { php:'🐘', js:'⚡', css:'🎨', md:'📄', json:'{}', sql:'🗄', html:'🌐', txt:'📝', log:'📋', sqlite:'💾', zip:'📦' };
  return m[ext] || '📝';
}

function formatVelikost(b) {
  if (b < 1024) return b + ' B';
  if (b < 1048576) return (b/1024).toFixed(1) + ' KB';
  return (b/1048576).toFixed(1) + ' MB';
}

function escHtml(txt) {
  return (txt || '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}

function trunciraj(txt, max) {
  if (!txt) return '';
  if (txt.length <= max) return txt;
  return txt.substring(0, max) + '\n\n... [prikazano prvih ' + max + ' znakov] ...';
}

// ══════════════════════════════════════════════════════
// INIT
// ══════════════════════════════════════════════════════
document.addEventListener('DOMContentLoaded', () => {
  // Odpri brskalnik ob zagonu
  ustvariZavihek('brskalnik', '📁 Brskalnik', '📁', 'brskalnik');
  odpriMapo('');
  napolniDrevo();

  // Iskanje
  document.getElementById('isci-input')?.addEventListener('keydown', e => {
    if (e.key === 'Enter') iskanje(e.target.value.trim());
  });

  // Modal overlay klik
  document.getElementById('modal-overlay')?.addEventListener('click', function(e) {
    if (e.target === this) zapriModal();
  });

  setStatus('ASTRA Nadzorni center aktiven', 'ok');
});
