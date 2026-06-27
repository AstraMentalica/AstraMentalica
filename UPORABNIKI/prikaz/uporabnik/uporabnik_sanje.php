<?php
/**
 * ---------------------------------------------------------
 * POT: UPORABNIKI/prikaz/uporabnik/sanje.php
 * v111 (27.5.2026 18:30)
 * ---------------------------------------------------------
 * OPIS: Sanje – osebni dnevnik sanj (PASSPORT)
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
$sanje = $vsebina['sanje'] ?? [];
$iskanje = $vsebina['iskanje'] ?? '';
$filterTip = $vsebina['filter_tip'] ?? 'vse';
$stran = $vsebina['stran'] ?? 1;
$naStran = $vsebina['na_stran'] ?? 10;

if (!$uporabnik) {
header('Location: ?svet=UPORABNIKI&pot=prijava');
exit;
}

// Paginacija
$skupaj = count($sanje);
$skupajStrani = ceil($skupaj / $naStran);
$offset = ($stran - 1) * $naStran;
$prikazaneSanje = array_slice($sanje, $offset, $naStran);

// Tabela interpretacije sanj
$interpretacije = [
'lepe' => 'Lepe sanje pogosto nakazujejo pozitivne spremembe v življenju, notranji mir in harmonijo.',
'grozljive' => 'Grozljive sanje lahko odražajo notranje strahove in tesnobe. So priložnost za soočenje s tem, kar vas skrbi.',
'prerokbe' => 'Preroške sanje lahko vsebujejo namige o prihodnosti. Zapišite jih in opazujte, ali se uresničijo.',
'letenje' => 'Sanje o letenju simbolizirajo svobodo, sprostitev in željo po begu iz vsakdanjih obremenitev.',
'padec' => 'Sanje o padcu lahko nakazujejo občutek nemoči ali izgube nadzora nad življenjem.',
'voda' => 'Voda v sanjah predstavlja čustva. Mirna voda pomeni notranji mir, nemirna voda pa čustveno stisko.',
'ogenj' => 'Ogenj v sanjah simbolizira strast, energijo, lahko pa tudi jezo ali uničenje.',
'zivali' => 'Živali v sanjah predstavljajo vaše instinkte in notranje nagone.',
];

function interpretirajSanje($naslov, $vsebina, $tip) {
global $interpretacije;

// Vrni specifično interpretacijo glede na tip
if (isset($interpretacije[$tip])) {
    return $interpretacije[$tip];
}

// Poskusi uganiti iz vsebine
$vsebinaLower = strtolower($vsebina . ' ' . $naslov);
foreach ($interpretacije as $kljuc => $interpretacija) {
    if (strpos($vsebinaLower, $kljuc) !== false) {
        return $interpretacija;
    }
}

return 'Sanje so edinstvene in osebne. Razmislite o svojih občutkih v sanjah in kako se povezujejo z vašim trenutnim življenjem.';
}
?>

<div class="passport-stran">
<div class="passport-vsebina">
    <div class="passport-glava">
        <div class="passport-ikona">🌙</div>
        <h1 class="passport-naslov">Sanje</h1>
        <p class="passport-podnaslov">Dnevnik sanj – zapisovanje in analiza sanj</p>
    </div>
    
    <div class="passport-orodja">
        <button class="gumb gumb-primaren" id="noveSanje">+ Nove sanje</button>
        <div class="passport-filtri">
            <input type="text" id="iskanje" placeholder="🔍 Išči po sanjah..." class="iskanje-vnos" value="<?= htmlspecialchars($iskanje) ?>">
            <select id="filterTip" class="filter-select">
                <option value="vse" <?= $filterTip === 'vse' ? 'selected' : '' ?>>Vse sanje</option>
                <option value="lepe" <?= $filterTip === 'lepe' ? 'selected' : '' ?>>Lepe sanje</option>
                <option value="grozljive" <?= $filterTip === 'grozljive' ? 'selected' : '' ?>>Grozljive sanje</option>
                <option value="prerokbe" <?= $filterTip === 'prerokbe' ? 'selected' : '' ?>>Prerokbe</option>
                <option value="letenje" <?= $filterTip === 'letenje' ? 'selected' : '' ?>>Letenje</option>
                <option value="padec" <?= $filterTip === 'padec' ? 'selected' : '' ?>>Padec</option>
                <option value="voda" <?= $filterTip === 'voda' ? 'selected' : '' ?>>Voda</option>
                <option value="ogenj" <?= $filterTip === 'ogenj' ? 'selected' : '' ?>>Ogenj</option>
                <option value="zivali" <?= $filterTip === 'zivali' ? 'selected' : '' ?>>Živali</option>
            </select>
        </div>
    </div>
    
    <div class="passport-seznam" id="seznamSanj">
        <?php if (empty($prikazaneSanje)): ?>
            <div class="passport-prazno">
                <div class="prazno-ikona">🌙</div>
                <p>Še nimate zapisanih nobenih sanj.</p>
                <p>Kliknite "Nove sanje" za začetek.</p>
            </div>
        <?php else: ?>
            <?php foreach ($prikazaneSanje as $zapis): ?>
                <div class="passport-zapis" data-tip="<?= $zapis['tip'] ?? 'lepe' ?>" data-id="<?= $zapis['id'] ?? '' ?>">
                    <div class="zapis-datum"><?= date('d.m.Y H:i', $zapis['cas'] ?? time()) ?></div>
                    <div class="zapis-tip <?= $zapis['tip'] ?? 'lepe' ?>">
                        <?php
                            $tipIkon = [
                                'lepe' => '😊',
                                'grozljive' => '😨',
                                'prerokbe' => '🔮',
                                'letenje' => '🕊️',
                                'padec' => '⬇️',
                                'voda' => '💧',
                                'ogenj' => '🔥',
                                'zivali' => '🐾'
                            ];
                            $tipNaziv = [
                                'lepe' => 'Lepe sanje',
                                'grozljive' => 'Grozljive sanje',
                                'prerokbe' => 'Prerokbe',
                                'letenje' => 'Letenje',
                                'padec' => 'Padec',
                                'voda' => 'Voda',
                                'ogenj' => 'Ogenj',
                                'zivali' => 'Živali'
                            ];
                        ?>
                        <?= $tipIkon[$zapis['tip'] ?? 'lepe'] ?? '📖' ?> <?= $tipNaziv[$zapis['tip'] ?? 'lepe'] ?? 'Sanje' ?>
                    </div>
                    <div class="zapis-naslov"><?= htmlspecialchars($zapis['naslov'] ?? 'Brez naslova') ?></div>
                    <div class="zapis-vsebina"><?= nl2br(htmlspecialchars(mb_substr($zapis['vsebina'] ?? '', 0, 150))) ?></div>
                    <?php if ((strlen($zapis['vsebina'] ?? '') > 150)): ?>
                        <a href="#" class="zapis-preberi-vec" data-id="<?= $zapis['id'] ?? '' ?>">Preberi več →</a>
                    <?php endif; ?>
                    <details class="zapis-interpretacija">
                        <summary>🔮 Interpretacija</summary>
                        <p><?= htmlspecialchars(interpretirajSanje($zapis['naslov'] ?? '', $zapis['vsebina'] ?? '', $zapis['tip'] ?? 'lepe')) ?></p>
                    </details>
                    <div class="zapis-akcije">
                        <button class="gumb gumb-majhen urediSanje" data-id="<?= $zapis['id'] ?? '' ?>">✏️ Uredi</button>
                        <button class="gumb gumb-majhen gumb-nevaren izbrisiSanje" data-id="<?= $zapis['id'] ?? '' ?>">🗑️ Izbriši</button>
                    </div>
                </div>
            <?php endforeach; ?>
            
            <?php if ($skupajStrani > 1): ?>
                <div class="paginacija">
                    <?php for ($i = 1; $i <= $skupajStrani; $i++): ?>
                        <a href="?svet=UPORABNIKI&amp;pot=sanje&amp;stran=<?= $i ?>&amp;filter_tip=<?= urlencode($filterTip) ?>" 
                           class="paginacija-gumb <?= $i == $stran ? 'aktivno' : '' ?>"><?= $i ?></a>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>
</div>

<!-- Modal za nove/uredi sanje -->
<div class="modal-ozadje" id="modalSanje" style="display: none;">
<div class="modal-vsebina" style="max-width: 700px;">
    <div class="modal-glava">
        <h3 class="modal-naslov" id="modalNaslov">Nove sanje</h3>
        <button class="modal-zapri" data-modal="modalSanje">&times;</button>
    </div>
    <div class="modal-telo">
        <form id="obrazecSanje">
            <input type="hidden" id="sanjeId" name="id" value="">
            <div class="obrazec-skupina">
                <label for="sanjeNaslov" class="obrazec-oznaka">Naslov</label>
                <input type="text" id="sanjeNaslov" name="naslov" class="obrazec-vnos" required>
            </div>
            <div class="obrazec-skupina">
                <label for="sanjeTip" class="obrazec-oznaka">Tip sanj</label>
                <select id="sanjeTip" name="tip" class="obrazec-vnos">
                    <option value="lepe">😊 Lepe sanje</option>
                    <option value="grozljive">😨 Grozljive sanje</option>
                    <option value="prerokbe">🔮 Prerokbe</option>
                    <option value="letenje">🕊️ Letenje</option>
                    <option value="padec">⬇️ Padec</option>
                    <option value="voda">💧 Voda</option>
                    <option value="ogenj">🔥 Ogenj</option>
                    <option value="zivali">🐾 Živali</option>
                </select>
            </div>
            <div class="obrazec-skupina">
                <label for="sanjeVsebina" class="obrazec-oznaka">Opis sanj</label>
                <textarea id="sanjeVsebina" name="vsebina" class="obrazec-vnos" rows="8" required placeholder="Podrobno opišite svoje sanje..."></textarea>
            </div>
            <div class="obrazec-skupina">
                <label for="sanjeInterpretacija" class="obrazec-oznaka">Osebna interpretacija (opcijsko)</label>
                <textarea id="sanjeInterpretacija" name="interpretacija" class="obrazec-vnos" rows="4" placeholder="Kaj po vašem pomenijo te sanje?"></textarea>
            </div>
        </form>
    </div>
    <div class="modal-noga">
        <button class="gumb gumb-sekundaren" data-modal="modalSanje">Prekliči</button>
        <button class="gumb gumb-primaren" id="shraniSanje">Shrani</button>
    </div>
</div>
</div>

<script nonce="<?= $vsebina['csp_nonce'] ?? '' ?>">
const modalSanje = document.getElementById('modalSanje');
const modalNaslov = document.getElementById('modalNaslov');
const sanjeId = document.getElementById('sanjeId');
const sanjeNaslov = document.getElementById('sanjeNaslov');
const sanjeTip = document.getElementById('sanjeTip');
const sanjeVsebina = document.getElementById('sanjeVsebina');
const sanjeInterpretacija = document.getElementById('sanjeInterpretacija');

function odpriModal(naslov, id = '', naslovSanj = '', tip = 'lepe', vsebina = '', interpretacija = '') {
modalNaslov.textContent = naslov;
sanjeId.value = id;
sanjeNaslov.value = naslovSanj;
sanjeTip.value = tip;
sanjeVsebina.value = vsebina;
sanjeInterpretacija.value = interpretacija;
modalSanje.style.display = 'flex';
}

function zapriModal() {
modalSanje.style.display = 'none';
sanjeId.value = '';
sanjeNaslov.value = '';
sanjeTip.value = 'lepe';
sanjeVsebina.value = '';
sanjeInterpretacija.value = '';
}

document.getElementById('noveSanje')?.addEventListener('click', () => {
odpriModal('Nove sanje');
});

document.querySelectorAll('.urediSanje').forEach(btn => {
btn.addEventListener('click', async () => {
    const id = btn.getAttribute('data-id');
    try {
        const odgovor = await fetch(`api.php?akcija=passport_sanje_pridobi&id=${id}`);
        const podatki = await odgovor.json();
        if (podatki.status === 'uspeh') {
            odpriModal('Uredi sanje', id, podatki.vsebina.naslov, podatki.vsebina.tip, podatki.vsebina.vsebina, podatki.vsebina.interpretacija || '');
        }
    } catch (e) {
        alert('Napaka pri nalaganju sanj.');
    }
});
});

document.querySelectorAll('.zapis-preberi-vec').forEach(link => {
link.addEventListener('click', async (e) => {
    e.preventDefault();
    const id = link.getAttribute('data-id');
    try {
        const odgovor = await fetch(`api.php?akcija=passport_sanje_pridobi&id=${id}`);
        const podatki = await odgovor.json();
        if (podatki.status === 'uspeh') {
            alert(podatki.vsebina.vsebina);
        }
    } catch (e) {
        alert('Napaka pri nalaganju sanj.');
    }
});
});

document.getElementById('shraniSanje')?.addEventListener('click', async () => {
const id = sanjeId.value;
const naslov = sanjeNaslov.value;
const tip = sanjeTip.value;
const vsebina = sanjeVsebina.value;
const interpretacija = sanjeInterpretacija.value;

if (!naslov || !vsebina) {
    alert('Naslov in opis sanj sta obvezna.');
    return;
}

const akcija = id ? 'passport_sanje_posodobi' : 'passport_sanje_dodaj';
const podatki = id ? { id, naslov, tip, vsebina, interpretacija } : { naslov, tip, vsebina, interpretacija };

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

document.querySelectorAll('.izbrisiSanje').forEach(btn => {
btn.addEventListener('click', async () => {
    if (confirm('Ste prepričani, da želite izbrisati te sanje?')) {
        const id = btn.getAttribute('data-id');
        try {
            const odgovor = await fetch(`api.php?akcija=passport_sanje_zbrisi&id=${id}`, { method: 'DELETE' });
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

document.querySelectorAll('.modal-zapri, [data-modal="modalSanje"]').forEach(el => {
el.addEventListener('click', zapriModal);
});

// Iskanje in filter
let iskalniTimeout;
document.getElementById('iskanje')?.addEventListener('input', (e) => {
clearTimeout(iskalniTimeout);
iskalniTimeout = setTimeout(() => {
    const iskalniNiz = e.target.value;
    window.location.href = `?svet=UPORABNIKI&pot=sanje&iskanje=${encodeURIComponent(iskalniNiz)}&filter_tip=${document.getElementById('filterTip').value}`;
}, 500);
});

document.getElementById('filterTip')?.addEventListener('change', (e) => {
window.location.href = `?svet=UPORABNIKI&pot=sanje&filter_tip=${e.target.value}&iskanje=${encodeURIComponent(document.getElementById('iskanje').value)}`;
});
</script>

<style>
@import url('dnevnik.css');

.zapis-tip {
display: inline-block;
padding: 0.2rem 0.6rem;
border-radius: 20px;
font-size: 0.7rem;
margin-bottom: 0.5rem;
}

.zapis-tip.lepe {
background: rgba(76, 175, 80, 0.2);
color: #4caf50;
}

.zapis-tip.grozljive {
background: rgba(244, 67, 54, 0.2);
color: #f44336;
}

.zapis-tip.prerokbe {
background: rgba(156, 39, 176, 0.2);
color: #ce93d8;
}

.zapis-tip.letenje {
background: rgba(33, 150, 243, 0.2);
color: #64b5f6;
}

.zapis-tip.padec {
background: rgba(255, 152, 0, 0.2);
color: #ffb74d;
}

.zapis-tip.voda {
background: rgba(0, 188, 212, 0.2);
color: #4dd0e1;
}

.zapis-tip.ogenj {
background: rgba(255, 87, 34, 0.2);
color: #ff8a65;
}

.zapis-tip.zivali {
background: rgba(121, 85, 72, 0.2);
color: #a1887f;
}

.zapis-interpretacija {
margin: 0.5rem 0;
padding: 0.5rem;
background: rgba(0, 0, 0, 0.2);
border-radius: 8px;
}

.zapis-interpretacija summary {
cursor: pointer;
color: #e8c84a;
font-size: 0.85rem;
}

.zapis-interpretacija p {
margin-top: 0.5rem;
font-size: 0.85rem;
color: #aaa;
}
</style>