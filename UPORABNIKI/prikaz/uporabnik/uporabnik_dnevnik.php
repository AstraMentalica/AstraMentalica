<?php
/**
 * ---------------------------------------------------------
 * POT: UPORABNIKI/prikaz/uporabnik/dnevnik.php
 * v111 (27.5.2026 18:30)
 * ---------------------------------------------------------
 * OPIS: Dnevnik – osebni dnevnik uporabnika (PASSPORT)
 * ---------------------------------------------------------
 *
 * ODVISNOSTI:
 * - pot.php
 *
 * UPORABA:
 * - PASSPORT modul
 *
 * PREPOVEDI:
 * - Brez business logike
 *
 * STATUS: Stabilno
 *
 * ZGODOVINA:
 * - v111: FAZA 25 – implementacija
 *
 * ---------------------------------------------------------
 * AVTOR: AstraMentalica Mojster
 * ---------------------------------------------------------
 */

declare(strict_types=1);

$uporabnik = $vsebina['uporabnik'] ?? null;
$zapiski = $vsebina['zapiski'] ?? [];
$iskanje = $vsebina['iskanje'] ?? '';
$filter = $vsebina['filter'] ?? 'vse';
$stran = $vsebina['stran'] ?? 1;
$naStran = $vsebina['na_stran'] ?? 10;

if (!$uporabnik) {
header('Location: ?svet=UPORABNIKI&pot=prijava');
exit;
}

// Paginacija
$skupaj = count($zapiski);
$skupajStrani = ceil($skupaj / $naStran);
$offset = ($stran - 1) * $naStran;
$prikazaniZapiski = array_slice($zapiski, $offset, $naStran);
?>

<div class="passport-stran">
<div class="passport-vsebina">
    <div class="passport-glava">
        <div class="passport-ikona">📔</div>
        <h1 class="passport-naslov">Dnevnik</h1>
        <p class="passport-podnaslov">Osebni dnevnik – shranjen samo za vas</p>
    </div>
    
    <div class="passport-orodja">
        <button class="gumb gumb-primaren" id="novZapis">+ Nov zapis</button>
        <div class="passport-filtri">
            <input type="text" id="iskanje" placeholder="🔍 Išči po dnevniku..." class="iskanje-vnos" value="<?= htmlspecialchars($iskanje) ?>">
            <select id="filterTip" class="filter-select">
                <option value="vse" <?= $filter === 'vse' ? 'selected' : '' ?>>Vsi zapisi</option>
                <option value="zadnjih_7" <?= $filter === 'zadnjih_7' ? 'selected' : '' ?>>Zadnjih 7 dni</option>
                <option value="zadnjih_30" <?= $filter === 'zadnjih_30' ? 'selected' : '' ?>>Zadnjih 30 dni</option>
                <option value="ta_mesec" <?= $filter === 'ta_mesec' ? 'selected' : '' ?>>Ta mesec</option>
            </select>
        </div>
    </div>
    
    <div class="passport-seznam" id="seznamZapisov">
        <?php if (empty($prikazaniZapiski)): ?>
            <div class="passport-prazno">
                <div class="prazno-ikona">📭</div>
                <p>Še nimate nobenega zapisa v dnevniku.</p>
                <p>Kliknite "Nov zapis" za začetek.</p>
            </div>
        <?php else: ?>
            <?php foreach ($prikazaniZapiski as $zapis): ?>
                <div class="passport-zapis" data-id="<?= $zapis['id'] ?? '' ?>">
                    <div class="zapis-datum"><?= date('d.m.Y H:i', $zapis['cas'] ?? time()) ?></div>
                    <div class="zapis-naslov"><?= htmlspecialchars($zapis['naslov'] ?? 'Brez naslova') ?></div>
                    <div class="zapis-vsebina"><?= nl2br(htmlspecialchars(mb_substr($zapis['vsebina'] ?? '', 0, 200))) ?></div>
                    <?php if ((strlen($zapis['vsebina'] ?? '') > 200)): ?>
                        <a href="#" class="zapis-preberi-vec" data-id="<?= $zapis['id'] ?? '' ?>">Preberi več →</a>
                    <?php endif; ?>
                    <div class="zapis-akcije">
                        <button class="gumb gumb-majhen urediZapis" data-id="<?= $zapis['id'] ?? '' ?>">✏️ Uredi</button>
                        <button class="gumb gumb-majhen gumb-nevaren izbrisiZapis" data-id="<?= $zapis['id'] ?? '' ?>">🗑️ Izbriši</button>
                    </div>
                </div>
            <?php endforeach; ?>
            
            <?php if ($skupajStrani > 1): ?>
                <div class="paginacija">
                    <?php for ($i = 1; $i <= $skupajStrani; $i++): ?>
                        <a href="?svet=UPORABNIKI&amp;pot=dnevnik&amp;stran=<?= $i ?>&amp;filter=<?= urlencode($filter) ?>" 
                           class="paginacija-gumb <?= $i == $stran ? 'aktivno' : '' ?>"><?= $i ?></a>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>
</div>

<!-- Modal za nov/uredi zapis -->
<div class="modal-ozadje" id="modalZapis" style="display: none;">
<div class="modal-vsebina" style="max-width: 600px;">
    <div class="modal-glava">
        <h3 class="modal-naslov" id="modalNaslov">Nov zapis</h3>
        <button class="modal-zapri" data-modal="modalZapis">&times;</button>
    </div>
    <div class="modal-telo">
        <form id="obrazecZapis">
            <input type="hidden" id="zapisId" name="id" value="">
            <div class="obrazec-skupina">
                <label for="zapisNaslov" class="obrazec-oznaka">Naslov</label>
                <input type="text" id="zapisNaslov" name="naslov" class="obrazec-vnos" required>
            </div>
            <div class="obrazec-skupina">
                <label for="zapisVsebina" class="obrazec-oznaka">Vsebina</label>
                <textarea id="zapisVsebina" name="vsebina" class="obrazec-vnos" rows="10" required></textarea>
            </div>
            <div class="obrazec-skupina">
                <label for="zapisCustva" class="obrazec-oznaka">Čustva (opcijsko)</label>
                <div class="custva-izbira">
                    <button type="button" class="custvo" data-custvo="😊">😊</button>
                    <button type="button" class="custvo" data-custvo="😢">😢</button>
                    <button type="button" class="custvo" data-custvo="😡">😡</button>
                    <button type="button" class="custvo" data-custvo="😍">😍</button>
                    <button type="button" class="custvo" data-custvo="😨">😨</button>
                    <button type="button" class="custvo" data-custvo="🤔">🤔</button>
                    <button type="button" class="custvo" data-custvo="😇">😇</button>
                </div>
                <input type="hidden" id="zapisCustvaInput" name="custva">
            </div>
        </form>
    </div>
    <div class="modal-noga">
        <button class="gumb gumb-sekundaren" data-modal="modalZapis">Prekliči</button>
        <button class="gumb gumb-primaren" id="shraniZapis">Shrani</button>
    </div>
</div>
</div>

<script nonce="<?= $vsebina['csp_nonce'] ?? '' ?>">
const modalZapis = document.getElementById('modalZapis');
const modalNaslov = document.getElementById('modalNaslov');
const zapisId = document.getElementById('zapisId');
const zapisNaslov = document.getElementById('zapisNaslov');
const zapisVsebina = document.getElementById('zapisVsebina');
const zapisCustvaInput = document.getElementById('zapisCustvaInput');
let trenutnaCustva = '';

function odpriModal(naslov, id = '', naslovZapisa = '', vsebina = '', custva = '') {
modalNaslov.textContent = naslov;
zapisId.value = id;
zapisNaslov.value = naslovZapisa;
zapisVsebina.value = vsebina;
trenutnaCustva = custva;
zapisCustvaInput.value = custva;

// Označi izbrana čustva
document.querySelectorAll('.custvo').forEach(btn => {
    if (custva.includes(btn.getAttribute('data-custvo'))) {
        btn.classList.add('aktivno');
    } else {
        btn.classList.remove('aktivno');
    }
});

modalZapis.style.display = 'flex';
}

function zapriModal() {
modalZapis.style.display = 'none';
zapisId.value = '';
zapisNaslov.value = '';
zapisVsebina.value = '';
zapisCustvaInput.value = '';
trenutnaCustva = '';
document.querySelectorAll('.custvo').forEach(btn => btn.classList.remove('aktivno'));
}

document.getElementById('novZapis')?.addEventListener('click', () => {
odpriModal('Nov zapis');
});

document.querySelectorAll('.urediZapis').forEach(btn => {
btn.addEventListener('click', async () => {
    const id = btn.getAttribute('data-id');
    try {
        const odgovor = await fetch(`api.php?akcija=passport_dnevnik_pridobi&id=${id}`);
        const podatki = await odgovor.json();
        if (podatki.status === 'uspeh') {
            odpriModal('Uredi zapis', id, podatki.vsebina.naslov, podatki.vsebina.vsebina, podatki.vsebina.custva || '');
        }
    } catch (e) {
        alert('Napaka pri nalaganju zapisa.');
    }
});
});

document.querySelectorAll('.zapis-preberi-vec').forEach(link => {
link.addEventListener('click', async (e) => {
    e.preventDefault();
    const id = link.getAttribute('data-id');
    try {
        const odgovor = await fetch(`api.php?akcija=passport_dnevnik_pridobi&id=${id}`);
        const podatki = await odgovor.json();
        if (podatki.status === 'uspeh') {
            alert(podatki.vsebina.vsebina);
        }
    } catch (e) {
        alert('Napaka pri nalaganju zapisa.');
    }
});
});

document.getElementById('shraniZapis')?.addEventListener('click', async () => {
const id = zapisId.value;
const naslov = zapisNaslov.value;
const vsebina = zapisVsebina.value;
const custva = zapisCustvaInput.value;

if (!naslov || !vsebina) {
    alert('Naslov in vsebina sta obvezna.');
    return;
}

const akcija = id ? 'passport_dnevnik_posodobi' : 'passport_dnevnik_dodaj';
const podatki = id ? { id, naslov, vsebina, custva } : { naslov, vsebina, custva };

try {
    const odgovor = await fetch(`api.php?akcija=${akcija}`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(podatki)
    });
    const rezultat = await odgovor.json();
    if (rezultat.status === 'uspeh') {
        location.reload();
    } else {
        alert(rezultat.sporocilo || 'Napaka pri shranjevanju');
    }
} catch (e) {
    alert('Napaka pri povezavi.');
}
});

document.querySelectorAll('.izbrisiZapis').forEach(btn => {
btn.addEventListener('click', async () => {
    if (confirm('Ste prepričani, da želite izbrisati ta zapis?')) {
        const id = btn.getAttribute('data-id');
        try {
            const odgovor = await fetch(`api.php?akcija=passport_dnevnik_zbrisi&id=${id}`, { method: 'DELETE' });
            const rezultat = await odgovor.json();
            if (rezultat.status === 'uspeh') {
                location.reload();
            } else {
                alert(rezultat.sporocilo || 'Napaka pri brisanju');
            }
        } catch (e) {
            alert('Napaka pri povezavi.');
        }
    }
});
});

document.querySelectorAll('.modal-zapri, [data-modal="modalZapis"]').forEach(el => {
el.addEventListener('click', zapriModal);
});

// Čustva izbira
document.querySelectorAll('.custvo').forEach(btn => {
btn.addEventListener('click', () => {
    const custvo = btn.getAttribute('data-custvo');
    if (btn.classList.contains('aktivno')) {
        btn.classList.remove('aktivno');
        trenutnaCustva = trenutnaCustva.replace(custvo, '').trim();
    } else {
        btn.classList.add('aktivno');
        trenutnaCustva += custvo;
    }
    zapisCustvaInput.value = trenutnaCustva;
});
});

// Iskanje
let iskalniTimeout;
document.getElementById('iskanje')?.addEventListener('input', (e) => {
clearTimeout(iskalniTimeout);
iskalniTimeout = setTimeout(() => {
    const iskalniNiz = e.target.value;
    window.location.href = `?svet=UPORABNIKI&pot=dnevnik&iskanje=${encodeURIComponent(iskalniNiz)}&filter=${document.getElementById('filterTip').value}`;
}, 500);
});

document.getElementById('filterTip')?.addEventListener('change', (e) => {
window.location.href = `?svet=UPORABNIKI&pot=dnevnik&filter=${e.target.value}&iskanje=${encodeURIComponent(document.getElementById('iskanje').value)}`;
});
</script>

<style>
.passport-stran {
max-width: 800px;
margin: 0 auto;
padding: 2rem;
}

.passport-glava {
text-align: center;
margin-bottom: 2rem;
}

.passport-ikona {
font-size: 3rem;
margin-bottom: 0.5rem;
}

.passport-naslov {
color: #e8c84a;
font-size: 1.8rem;
}

.passport-podnaslov {
color: #aaa;
}

.passport-orodja {
display: flex;
justify-content: space-between;
align-items: center;
margin-bottom: 1.5rem;
flex-wrap: wrap;
gap: 1rem;
}

.passport-filtri {
display: flex;
gap: 0.5rem;
}

.iskanje-vnos {
padding: 0.5rem 1rem;
background: rgba(255, 255, 255, 0.1);
border: 1px solid #2a2a4a;
border-radius: 25px;
color: #d4c5a9;
width: 200px;
}

.filter-select {
padding: 0.5rem 1rem;
background: rgba(255, 255, 255, 0.1);
border: 1px solid #2a2a4a;
border-radius: 8px;
color: #d4c5a9;
}

.passport-zapis {
background: rgba(255, 255, 255, 0.05);
border-radius: 15px;
padding: 1.25rem;
margin-bottom: 1rem;
transition: transform 0.3s;
}

.passport-zapis:hover {
transform: translateX(5px);
}

.zapis-datum {
font-size: 0.75rem;
color: #aaa;
margin-bottom: 0.25rem;
}

.zapis-naslov {
font-size: 1.2rem;
font-weight: bold;
color: #e8c84a;
margin-bottom: 0.5rem;
}

.zapis-vsebina {
color: #d4c5a9;
margin-bottom: 0.5rem;
line-height: 1.5;
}

.zapis-preberi-vec {
color: #e8c84a;
font-size: 0.85rem;
text-decoration: none;
margin-bottom: 0.5rem;
display: inline-block;
}

.zapis-akcije {
display: flex;
gap: 0.5rem;
margin-top: 0.5rem;
}

.passport-prazno {
text-align: center;
padding: 3rem;
color: #888;
}

.prazno-ikona {
font-size: 4rem;
margin-bottom: 1rem;
opacity: 0.5;
}

.paginacija {
display: flex;
justify-content: center;
gap: 0.5rem;
margin-top: 2rem;
}

.paginacija-gumb {
display: inline-block;
padding: 0.3rem 0.8rem;
background: rgba(255, 255, 255, 0.05);
border-radius: 5px;
color: #d4c5a9;
text-decoration: none;
}

.paginacija-gumb.aktivno {
background: #e8c84a;
color: #0a0a1a;
}

.custva-izbira {
display: flex;
gap: 0.5rem;
flex-wrap: wrap;
margin-top: 0.5rem;
}

.custvo {
background: rgba(255, 255, 255, 0.1);
border: none;
border-radius: 50%;
width: 40px;
height: 40px;
cursor: pointer;
font-size: 1.2rem;
transition: all 0.3s;
}

.custvo:hover {
background: rgba(232, 200, 74, 0.3);
transform: scale(1.1);
}

.custvo.aktivno {
background: #e8c84a;
border: 2px solid #e8c84a;
}

@media (max-width: 768px) {
.passport-orodja {
    flex-direction: column;
}

.passport-filtri {
    width: 100%;
}

.iskanje-vnos {
    width: 100%;
}
}
</style>