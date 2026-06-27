<?php
/**
 * ---------------------------------------------------------
 * POT: UPORABNIKI/prikaz/skrbnik/uporabniki.php
 * v111 (27.5.2026 22:00)
 * ---------------------------------------------------------
 * OPIS: Seznam uporabnikov – admin pregled in upravljanje
 * ---------------------------------------------------------
 *
 * ODVISNOSTI:
 * - pot.php
 * - SISTEM/kernel/baze/upravljalec_baz.php
 *
 * UPORABA:
 * - Admin dostop za upravljanje uporabnikov
 *
 * PREPOVEDI:
 * - Brez business logike (samo prikaz)
 *
 * STATUS: Stabilno
 *
 * ZGODOVINA:
 * - v111: FAZA 55 – implementacija
 *
 * ---------------------------------------------------------
 * AVTOR: AstraMentalica Mojster
 * ---------------------------------------------------------
 */

declare(strict_types=1);

if (!isset($vsebina)) {
$vsebina = [];
}

$uporabniki = $vsebina['uporabniki'] ?? [];
$statistika = $vsebina['statistika'] ?? [];
$iskanje = $vsebina['iskanje'] ?? '';
$filterVloga = $vsebina['filter_vloga'] ?? 'vse';
$stran = $vsebina['stran'] ?? 1;
$naStran = $vsebina['na_stran'] ?? 20;

$uporabnik = $vsebina['uporabnik'] ?? null;
$jeAdmin = $uporabnik && ($uporabnik['vloga'] ?? 0) >= VLOGA_ADMIN;

if (!$jeAdmin) {
header('Location: ?svet=GLOBALNO');
exit;
}

// Paginacija
$skupaj = count($uporabniki);
$skupajStrani = ceil($skupaj / $naStran);
$offset = ($stran - 1) * $naStran;
$prikazaniUporabniki = array_slice($uporabniki, $offset, $naStran);

$vloge = [
VLOGA_GOST => 'Gost',
VLOGA_S0 => 'S0 – Začetnik',
VLOGA_S1 => 'S1 – Učenec',
VLOGA_S2 => 'S2 – Raziskovalec',
VLOGA_S3 => 'S3 – Mojster',
VLOGA_S4 => 'S4 – Veliki Mojster',
VLOGA_S5 => 'S5 – Arhitekt',
VLOGA_ADMIN => 'Administrator'
];
?>

<div class="uporabniki-stran">
<div class="uporabniki-glava">
    <h1 class="uporabniki-naslov">👥 Uporabniki</h1>
    <p class="uporabniki-podnaslov">Pregled in upravljanje uporabniških računov</p>
</div>

<div class="uporabniki-statistika">
    <div class="stat-kartica">
        <div class="stat-stevilka"><?= $statistika['skupaj'] ?? 0 ?></div>
        <div class="stat-oznaka">Skupaj uporabnikov</div>
    </div>
    <div class="stat-kartica">
        <div class="stat-stevilka"><?= $statistika['aktivni'] ?? 0 ?></div>
        <div class="stat-oznaka">Aktivni (zadnjih 30 dni)</div>
    </div>
    <div class="stat-kartica">
        <div class="stat-stevilka"><?= $statistika['novi_mesec'] ?? 0 ?></div>
        <div class="stat-oznaka">Novi v tem mesecu</div>
    </div>
    <div class="stat-kartica">
        <div class="stat-stevilka"><?= $statistika['premium'] ?? 0 ?></div>
        <div class="stat-oznaka">Premium uporabnikov</div>
    </div>
</div>

<div class="uporabniki-filtri">
    <div class="iskanje">
        <input type="text" id="iskanjeUporabnikov" placeholder="🔍 Išči po imenu ali emailu..." 
               class="iskanje-vnos" value="<?= htmlspecialchars($iskanje) ?>">
    </div>
    <div class="filter">
        <select id="filterVloga" class="filter-select">
            <option value="vse" <?= $filterVloga === 'vse' ? 'selected' : '' ?>>Vse vloge</option>
            <?php foreach ($vloge as $vrednost => $ime): ?>
                <option value="<?= $vrednost ?>" <?= $filterVloga == $vrednost ? 'selected' : '' ?>>
                    <?= htmlspecialchars($ime) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div>
        <button class="gumb gumb-primaren" id="dodajUporabnika">+ Dodaj uporabnika</button>
    </div>
</div>

<div class="uporabniki-tabela">
    <table class="tabela">
        <thead>
            <tr><th>ID</th><th>Ime</th><th>Elektronski naslov</th><th>Vloga</th><th>Registriran</th><th>Zadnja prijava</th><th>Status</th><th>Akcije</th></tr>
        </thead>
        <tbody>
            <?php if (empty($prikazaniUporabniki)): ?>
                <tr><td colspan="8" class="tabela-prazno">Ni uporabnikov za prikaz.</td></tr>
            <?php else: ?>
                <?php foreach ($prikazaniUporabniki as $up): 
                    $jeAktiven = ($up['nazadnje_prijavljen'] ?? 0) > time() - 86400 * 30;
                ?>
                    <tr data-id="<?= $up['id'] ?>">
                        <td><?= htmlspecialchars($up['id']) ?></td>
                        <td><?= htmlspecialchars($up['ime'] ?? '') ?></td>
                        <td><?= htmlspecialchars($up['elektronski_naslov'] ?? '') ?></td>
                        <td><?= $vloge[$up['vloga']] ?? 'Neznana' ?></td>
                        <td><?= date('d.m.Y', $up['ustvarjeno'] ?? time()) ?></td>
                        <td><?= $up['nazadnje_prijavljen'] ? date('d.m.Y H:i', $up['nazadnje_prijavljen']) : 'Nikoli' ?></td>
                        <td class="<?= $jeAktiven ? 'status-aktiven' : 'status-neaktiven' ?>">
                            <?= $jeAktiven ? '✅ Aktiven' : '⭕ Neaktiven' ?>
                        </td>
                        <td class="akcije">
                            <button class="gumb gumb-majhen urediUporabnika" data-id="<?= $up['id'] ?>">✏️ Uredi</button>
                            <button class="gumb gumb-majhen gumb-nevaren izbrisiUporabnika" data-id="<?= $up['id'] ?>">🗑️ Izbriši</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php if ($skupajStrani > 1): ?>
    <div class="paginacija">
        <?php for ($i = 1; $i <= $skupajStrani; $i++): ?>
            <a href="?svet=UPORABNIKI&amp;pot=skrbnik/uporabniki&amp;stran=<?= $i ?>" 
               class="paginacija-gumb <?= $i == $stran ? 'aktivno' : '' ?>"><?= $i ?></a>
        <?php endfor; ?>
    </div>
<?php endif; ?>
</div>

<!-- Modal za dodajanje/urejanje uporabnika -->
<div class="modal-ozadje" id="modalUporabnik" style="display: none;">
<div class="modal-vsebina" style="max-width: 500px;">
    <div class="modal-glava">
        <h3 class="modal-naslov" id="modalNaslov">Dodaj uporabnika</h3>
        <button class="modal-zapri" data-modal="modalUporabnik">&times;</button>
    </div>
    <div class="modal-telo">
        <form id="obrazecUporabnik">
            <input type="hidden" id="uporabnikId" name="id" value="">
            <div class="obrazec-skupina">
                <label for="uporabnikIme" class="obrazec-oznaka">Ime</label>
                <input type="text" id="uporabnikIme" name="ime" class="obrazec-vnos" required>
            </div>
            <div class="obrazec-skupina">
                <label for="uporabnikEmail" class="obrazec-oznaka">Elektronski naslov</label>
                <input type="email" id="uporabnikEmail" name="email" class="obrazec-vnos" required>
            </div>
            <div class="obrazec-skupina" id="gesloSkupina">
                <label for="uporabnikGeslo" class="obrazec-oznaka">Geslo</label>
                <input type="password" id="uporabnikGeslo" name="geslo" class="obrazec-vnos">
                <small class="obrazec-namig">Pustite prazno za ohranitev obstoječega gesla</small>
            </div>
            <div class="obrazec-skupina">
                <label for="uporabnikVloga" class="obrazec-oznaka">Vloga</label>
                <select id="uporabnikVloga" name="vloga" class="obrazec-vnos">
                    <?php foreach ($vloge as $vrednost => $imeVloge): ?>
                        <option value="<?= $vrednost ?>"><?= htmlspecialchars($imeVloge) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="obrazec-skupina obrazec-potrdilo">
                <label class="obrazec-potrdilo-oznaka">
                    <input type="checkbox" name="aktiven" value="1" checked>
                    <span>Aktiven račun</span>
                </label>
            </div>
        </form>
    </div>
    <div class="modal-noga">
        <button class="gumb gumb-sekundaren" data-modal="modalUporabnik">Prekliči</button>
        <button class="gumb gumb-primaren" id="shraniUporabnika">Shrani</button>
    </div>
</div>
</div>

<script nonce="<?= $vsebina['csp_nonce'] ?? '' ?>">
const modal = document.getElementById('modalUporabnik');
const modalNaslov = document.getElementById('modalNaslov');
const uporabnikId = document.getElementById('uporabnikId');
const uporabnikIme = document.getElementById('uporabnikIme');
const uporabnikEmail = document.getElementById('uporabnikEmail');
const uporabnikGeslo = document.getElementById('uporabnikGeslo');
const uporabnikVloga = document.getElementById('uporabnikVloga');
const gesloSkupina = document.getElementById('gesloSkupina');

function odpriModal(naslov, id = '', ime = '', email = '', vloga = '10') {
modalNaslov.textContent = naslov;
uporabnikId.value = id;
uporabnikIme.value = ime;
uporabnikEmail.value = email;
uporabnikVloga.value = vloga;
uporabnikGeslo.value = '';

if (id) {
    gesloSkupina.style.opacity = '0.5';
    gesloSkupina.style.pointerEvents = 'none';
} else {
    gesloSkupina.style.opacity = '1';
    gesloSkupina.style.pointerEvents = 'auto';
    uporabnikGeslo.required = true;
}

modal.style.display = 'flex';
}

function zapriModal() {
modal.style.display = 'none';
uporabnikId.value = '';
uporabnikIme.value = '';
uporabnikEmail.value = '';
uporabnikGeslo.value = '';
uporabnikVloga.value = '10';
}

document.getElementById('dodajUporabnika')?.addEventListener('click', () => {
odpriModal('Dodaj uporabnika');
});

document.querySelectorAll('.urediUporabnika').forEach(btn => {
btn.addEventListener('click', async () => {
    const id = btn.getAttribute('data-id');
    try {
        const odgovor = await fetch(`api.php?akcija=uporabnik_pridobi&id=${id}`);
        const podatki = await odgovor.json();
        if (podatki.status === 'uspeh') {
            odpriModal('Uredi uporabnika', id, podatki.vsebina.ime, podatki.vsebina.email, podatki.vsebina.vloga);
        }
    } catch (e) {
        alert('Napaka pri nalaganju uporabnika.');
    }
});
});

document.getElementById('shraniUporabnika')?.addEventListener('click', async () => {
const id = uporabnikId.value;
const ime = uporabnikIme.value;
const email = uporabnikEmail.value;
const geslo = uporabnikGeslo.value;
const vloga = uporabnikVloga.value;

if (!ime || !email) {
    alert('Ime in elektronski naslov sta obvezna.');
    return;
}

if (!id && !geslo) {
    alert('Geslo je obvezno za novega uporabnika.');
    return;
}

const akcija = id ? 'uporabnik_posodobi' : 'uporabnik_dodaj';
const podatki = id ? { id, ime, email, vloga } : { ime, email, geslo, vloga };

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

document.querySelectorAll('.izbrisiUporabnika').forEach(btn => {
btn.addEventListener('click', async () => {
    if (confirm('Ste prepričani, da želite izbrisati tega uporabnika? Izbris je TRAJEN!')) {
        const id = btn.getAttribute('data-id');
        try {
            const odgovor = await fetch(`api.php?akcija=uporabnik_zbrisi&id=${id}`, { method: 'DELETE' });
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

document.querySelectorAll('.modal-zapri, [data-modal="modalUporabnik"]').forEach(el => {
el.addEventListener('click', zapriModal);
});

// Iskanje in filter
let iskalniTimeout;
document.getElementById('iskanjeUporabnikov')?.addEventListener('input', (e) => {
clearTimeout(iskalniTimeout);
iskalniTimeout = setTimeout(() => {
    window.location.href = `?svet=UPORABNIKI&pot=skrbnik/uporabniki&iskanje=${encodeURIComponent(e.target.value)}&filter_vloga=${document.getElementById('filterVloga').value}`;
}, 500);
});

document.getElementById('filterVloga')?.addEventListener('change', (e) => {
window.location.href = `?svet=UPORABNIKI&pot=skrbnik/uporabniki&filter_vloga=${e.target.value}&iskanje=${encodeURIComponent(document.getElementById('iskanjeUporabnikov').value)}`;
});
</script>

<style>
.uporabniki-stran {
max-width: 1200px;
margin: 0 auto;
padding: 2rem;
}

.uporabniki-glava {
text-align: center;
margin-bottom: 2rem;
}

.uporabniki-naslov {
font-size: 2rem;
color: #e8c84a;
}

.uporabniki-podnaslov {
color: #aaa;
}

.uporabniki-statistika {
display: grid;
grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
gap: 1rem;
margin-bottom: 2rem;
}

.stat-kartica {
background: rgba(255, 255, 255, 0.03);
border-radius: 15px;
padding: 1rem;
text-align: center;
}

.stat-stevilka {
font-size: 1.8rem;
font-weight: bold;
color: #e8c84a;
}

.stat-oznaka {
font-size: 0.8rem;
color: #888;
}

.uporabniki-filtri {
display: flex;
justify-content: space-between;
align-items: center;
margin-bottom: 1.5rem;
flex-wrap: wrap;
gap: 1rem;
}

.iskanje-vnos {
padding: 0.5rem 1rem;
background: rgba(255, 255, 255, 0.1);
border: 1px solid #2a2a4a;
border-radius: 25px;
color: #d4c5a9;
width: 250px;
}

.filter-select {
padding: 0.5rem 1rem;
background: rgba(255, 255, 255, 0.1);
border: 1px solid #2a2a4a;
border-radius: 8px;
color: #d4c5a9;
}

.uporabniki-tabela {
overflow-x: auto;
}

.akcije {
display: flex;
gap: 0.25rem;
flex-wrap: wrap;
}

.status-aktiven {
color: #4caf50;
}

.status-neaktiven {
color: #f44336;
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

@media (max-width: 768px) {
.uporabniki-filtri {
    flex-direction: column;
    align-items: stretch;
}

.iskanje-vnos, .filter-select {
    width: 100%;
}

.akcije {
    flex-direction: column;
}
}
</style>