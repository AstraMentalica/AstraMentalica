<?php
/**
 * ---------------------------------------------------------
 * POT: UPORABNIKI/prikaz/uporabnik/meditacije.php
 * v111 (27.5.2026 18:30)
 * ---------------------------------------------------------
 * OPIS: Meditacije – osebni dnevnik meditacij (PASSPORT)
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
$meditacije = $vsebina['meditacije'] ?? [];
$iskanje = $vsebina['iskanje'] ?? '';
$stran = $vsebina['stran'] ?? 1;
$naStran = $vsebina['na_stran'] ?? 10;

if (!$uporabnik) {
header('Location: ?svet=UPORABNIKI&pot=prijava');
exit;
}

// Paginacija
$skupaj = count($meditacije);
$skupajStrani = ceil($skupaj / $naStran);
$offset = ($stran - 1) * $naStran;
$prikazaneMeditacije = array_slice($meditacije, $offset, $naStran);

// Statistika
$skupajMinut = array_sum(array_column($meditacije, 'trajanje'));
$steviloMeditacij = count($meditacije);
$povprecnoTrajanje = $steviloMeditacij > 0 ? round($skupajMinut / $steviloMeditacij, 1) : 0;

// Grafični podatki (zadnjih 7 dni)
$zadnjih7Dni = [];
for ($i = 6; $i >= 0; $i--) {
$dan = date('Y-m-d', strtotime("-$i days"));
$zadnjih7Dni[$dan] = 0;
}
foreach ($meditacije as $meditacija) {
$dan = date('Y-m-d', $meditacija['cas'] ?? time());
if (isset($zadnjih7Dni[$dan])) {
    $zadnjih7Dni[$dan] += $meditacija['trajanje'] ?? 0;
}
}
?>

<div class="passport-stran">
<div class="passport-vsebina">
    <div class="passport-glava">
        <div class="passport-ikona">🧘</div>
        <h1 class="passport-naslov">Meditacije</h1>
        <p class="passport-podnaslov">Dnevnik meditacij – sledenje vaši praksi</p>
    </div>
    
    <div class="meditacije-statistika">
        <div class="statistika-kartica">
            <div class="statistika-stevilka"><?= $steviloMeditacij ?></div>
            <div class="statistika-oznaka">Skupaj meditacij</div>
        </div>
        <div class="statistika-kartica">
            <div class="statistika-stevilka"><?= $skupajMinut ?></div>
            <div class="statistika-oznaka">Skupaj minut</div>
        </div>
        <div class="statistika-kartica">
            <div class="statistika-stevilka"><?= $povprecnoTrajanje ?></div>
            <div class="statistika-oznaka">Povprečno trajanje (min)</div>
        </div>
    </div>
    
    <?php if (!empty($zadnjih7Dni)): ?>
    <div class="meditacije-graf">
        <h3>Zadnjih 7 dni</h3>
        <div class="graf-stolpci">
            <?php foreach ($zadnjih7Dni as $dan => $minute): 
                $visina = $minute > 0 ? min(100, ($minute / 60) * 100) : 0;
            ?>
                <div class="graf-stolpec">
                    <div class="stolpec" style="height: <?= $visina ?>px;"></div>
                    <div class="stolpec-oznaka"><?= date('d.m', strtotime($dan)) ?></div>
                    <div class="stolpec-vrednost"><?= $minute ?> min</div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
    
    <div class="passport-orodja">
        <button class="gumb gumb-primaren" id="novaMeditacija">+ Nova meditacija</button>
        <div class="passport-filtri">
            <input type="text" id="iskanje" placeholder="🔍 Išči po meditacijah..." class="iskanje-vnos" value="<?= htmlspecialchars($iskanje) ?>">
        </div>
    </div>
    
    <div class="passport-seznam" id="seznamMeditacij">
        <?php if (empty($prikazaneMeditacije)): ?>
            <div class="passport-prazno">
                <div class="prazno-ikona">🧘</div>
                <p>Še nimate zapisanih nobenih meditacij.</p>
                <p>Kliknite "Nova meditacija" za začetek.</p>
            </div>
        <?php else: ?>
            <?php foreach ($prikazaneMeditacije as $zapis): ?>
                <div class="passport-zapis meditacija-zapis" data-id="<?= $zapis['id'] ?? '' ?>">
                    <div class="zapis-datum"><?= date('d.m.Y H:i', $zapis['cas'] ?? time()) ?></div>
                    <div class="zapis-naslov"><?= htmlspecialchars($zapis['naslov'] ?? 'Meditacija') ?></div>
                    <div class="zapis-trajanje">⏱️ Trajanje: <?= $zapis['trajanje'] ?? 0 ?> minut</div>
                    <div class="zapis-vsebina"><?= nl2br(htmlspecialchars(mb_substr($zapis['vsebina'] ?? '', 0, 150))) ?></div>
                    <div class="zapis-obcutki">💭 <?= nl2br(htmlspecialchars(mb_substr($zapis['obcutki'] ?? '', 0, 100))) ?></div>
                    <div class="zapis-akcije">
                        <button class="gumb gumb-majhen urediMeditacijo" data-id="<?= $zapis['id'] ?? '' ?>">✏️ Uredi</button>
                        <button class="gumb gumb-majhen gumb-nevaren izbrisiMeditacijo" data-id="<?= $zapis['id'] ?? '' ?>">🗑️ Izbriši</button>
                    </div>
                </div>
            <?php endforeach; ?>
            
            <?php if ($skupajStrani > 1): ?>
                <div class="paginacija">
                    <?php for ($i = 1; $i <= $skupajStrani; $i++): ?>
                        <a href="?svet=UPORABNIKI&amp;pot=meditacije&amp;stran=<?= $i ?>" 
                           class="paginacija-gumb <?= $i == $stran ? 'aktivno' : '' ?>"><?= $i ?></a>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>
</div>

<!-- Modal za novo/uredi meditacijo -->
<div class="modal-ozadje" id="modalMeditacija" style="display: none;">
<div class="modal-vsebina" style="max-width: 600px;">
    <div class="modal-glava">
        <h3 class="modal-naslov" id="modalNaslov">Nova meditacija</h3>
        <button class="modal-zapri" data-modal="modalMeditacija">&times;</button>
    </div>
    <div class="modal-telo">
        <form id="obrazecMeditacija">
            <input type="hidden" id="meditacijaId" name="id" value="">
            <div class="obrazec-skupina">
                <label for="meditacijaNaslov" class="obrazec-oznaka">Naslov</label>
                <input type="text" id="meditacijaNaslov" name="naslov" class="obrazec-vnos" required>
            </div>
            <div class="obrazec-skupina">
                <label for="meditacijaTrajanje" class="obrazec-oznaka">Trajanje (minute)</label>
                <input type="number" id="meditacijaTrajanje" name="trajanje" class="obrazec-vnos" min="1" max="480" required>
            </div>
            <div class="obrazec-skupina">
                <label for="meditacijaVsebina" class="obrazec-oznaka">Opis / Izkušnja</label>
                <textarea id="meditacijaVsebina" name="vsebina" class="obrazec-vnos" rows="6" required placeholder="Kako je potekala meditacija?"></textarea>
            </div>
            <div class="obrazec-skupina">
                <label for="meditacijaObcutki" class="obrazec-oznaka">Občutki po meditaciji</label>
                <textarea id="meditacijaObcutki" name="obcutki" class="obrazec-vnos" rows="3" placeholder="Kako se počutite po meditaciji?"></textarea>
            </div>
            <div class="obrazec-skupina">
                <label for="meditacijaTehnika" class="obrazec-oznaka">Tehnika (opcijsko)</label>
                <input type="text" id="meditacijaTehnika" name="tehnika" class="obrazec-vnos" placeholder="Npr. čuječnost, dihanje, vodena...">
            </div>
        </form>
    </div>
    <div class="modal-noga">
        <button class="gumb gumb-sekundaren" data-modal="modalMeditacija">Prekliči</button>
        <button class="gumb gumb-primaren" id="shraniMeditacijo">Shrani</button>
    </div>
</div>
</div>

<script nonce="<?= $vsebina['csp_nonce'] ?? '' ?>">
const modalMeditacija = document.getElementById('modalMeditacija');
const modalNaslov = document.getElementById('modalNaslov');
const meditacijaId = document.getElementById('meditacijaId');
const meditacijaNaslov = document.getElementById('meditacijaNaslov');
const meditacijaTrajanje = document.getElementById('meditacijaTrajanje');
const meditacijaVsebina = document.getElementById('meditacijaVsebina');
const meditacijaObcutki = document.getElementById('meditacijaObcutki');
const meditacijaTehnika = document.getElementById('meditacijaTehnika');

function odpriModal(naslov, id = '', naslovMed = '', trajanje = '', vsebina = '', obcutki = '', tehnika = '') {
modalNaslov.textContent = naslov;
meditacijaId.value = id;
meditacijaNaslov.value = naslovMed;
meditacijaTrajanje.value = trajanje;
meditacijaVsebina.value = vsebina;
meditacijaObcutki.value = obcutki;
meditacijaTehnika.value = tehnika;
modalMeditacija.style.display = 'flex';
}

function zapriModal() {
modalMeditacija.style.display = 'none';
meditacijaId.value = '';
meditacijaNaslov.value = '';
meditacijaTrajanje.value = '';
meditacijaVsebina.value = '';
meditacijaObcutki.value = '';
meditacijaTehnika.value = '';
}

document.getElementById('novaMeditacija')?.addEventListener('click', () => {
odpriModal('Nova meditacija');
});

document.querySelectorAll('.urediMeditacijo').forEach(btn => {
btn.addEventListener('click', async () => {
    const id = btn.getAttribute('data-id');
    try {
        const odgovor = await fetch(`api.php?akcija=passport_meditacije_pridobi&id=${id}`);
        const podatki = await odgovor.json();
        if (podatki.status === 'uspeh') {
            odpriModal('Uredi meditacijo', id, podatki.vsebina.naslov, podatki.vsebina.trajanje, 
                       podatki.vsebina.vsebina, podatki.vsebina.obcutki || '', podatki.vsebina.tehnika || '');
        }
    } catch (e) {
        alert('Napaka pri nalaganju meditacije.');
    }
});
});

document.getElementById('shraniMeditacijo')?.addEventListener('click', async () => {
const id = meditacijaId.value;
const naslov = meditacijaNaslov.value;
const trajanje = meditacijaTrajanje.value;
const vsebina = meditacijaVsebina.value;
const obcutki = meditacijaObcutki.value;
const tehnika = meditacijaTehnika.value;

if (!naslov || !trajanje || !vsebina) {
    alert('Naslov, trajanje in opis so obvezni.');
    return;
}

const akcija = id ? 'passport_meditacije_posodobi' : 'passport_meditacije_dodaj';
const podatki = id ? { id, naslov, trajanje, vsebina, obcutki, tehnika } : { naslov, trajanje, vsebina, obcutki, tehnika };

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

document.querySelectorAll('.izbrisiMeditacijo').forEach(btn => {
btn.addEventListener('click', async () => {
    if (confirm('Ste prepričani, da želite izbrisati to meditacijo?')) {
        const id = btn.getAttribute('data-id');
        try {
            const odgovor = await fetch(`api.php?akcija=passport_meditacije_zbrisi&id=${id}`, { method: 'DELETE' });
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

document.querySelectorAll('.modal-zapri, [data-modal="modalMeditacija"]').forEach(el => {
el.addEventListener('click', zapriModal);
});

// Iskanje
let iskalniTimeout;
document.getElementById('iskanje')?.addEventListener('input', (e) => {
clearTimeout(iskalniTimeout);
iskalniTimeout = setTimeout(() => {
    const iskalniNiz = e.target.value;
    window.location.href = `?svet=UPORABNIKI&pot=meditacije&iskanje=${encodeURIComponent(iskalniNiz)}`;
}, 500);
});
</script>

<style>
@import url('dnevnik.css');

.meditacije-statistika {
display: grid;
grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
gap: 1rem;
margin-bottom: 2rem;
}

.meditacije-graf {
background: rgba(255, 255, 255, 0.03);
border-radius: 15px;
padding: 1.5rem;
margin-bottom: 2rem;
}

.meditacije-graf h3 {
color: #e8c84a;
margin-bottom: 1rem;
text-align: center;
}

.graf-stolpci {
display: flex;
justify-content: space-around;
align-items: flex-end;
gap: 0.5rem;
height: 150px;
}

.graf-stolpec {
flex: 1;
display: flex;
flex-direction: column;
align-items: center;
gap: 0.5rem;
}

.stolpec {
width: 100%;
max-width: 40px;
background: linear-gradient(180deg, #e8c84a, #a88a2a);
border-radius: 5px 5px 0 0;
transition: height 0.5s;
min-height: 2px;
}

.stolpec-oznaka {
font-size: 0.7rem;
color: #888;
}

.stolpec-vrednost {
font-size: 0.7rem;
color: #e8c84a;
}

.zapis-trajanje {
font-size: 0.85rem;
color: #e8c84a;
margin-bottom: 0.5rem;
}

.zapis-obcutki {
font-size: 0.85rem;
color: #aaa;
margin-top: 0.5rem;
padding-top: 0.5rem;
border-top: 1px dashed rgba(255, 255, 255, 0.1);
}

.meditacija-zapis {
transition: transform 0.3s;
}

.meditacija-zapis:hover {
transform: translateX(5px);
}
</style>