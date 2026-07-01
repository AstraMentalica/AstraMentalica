<?php
/**
 * ---------------------------------------------------------
 * POT: GLOBALNO/postavitev/strani/admin/admin.php
 * v111 (27.5.2026 16:00)
 * ---------------------------------------------------------
 * OPIS: Admin stran – nadzorna plosca za administratorje
 * ---------------------------------------------------------
 *
 * ODVISNOSTI:
 * - pot.php
 *
 * UPORABA:
 * - Ko je tip 'admin'
 *
 * PREPOVEDI:
 * - Brez business logike (samo prikaz)
 *
 * STATUS: Stabilno
 *
 * ZGODOVINA:
 * - v111: FAZA 20+ – implementacija
 *
 * ---------------------------------------------------------
 * AVTOR: AstraMentalica Mojster
 * ---------------------------------------------------------
 */

declare(strict_types=1);

if (!isset($vsebina)) {
    $vsebina = [];
}

$statistika = $vsebina['statistika'] ?? [];
$sistem = $vsebina['sistem'] ?? [];
$uporabnik = $vsebina['uporabnik'] ?? null;

// Preveri ali je uporabnik admin
$jeAdmin = $uporabnik && ($uporabnik['vloga'] ?? 0) >= VLOGA_ADMIN;

if (!$jeAdmin):
?>
<div class="admin-ni-dostopa">
    <div class="admin-ni-dostopa-vsebina">
        <div class="admin-ni-dostopa-ikona">🔒</div>
        <h1>Dostop zavrnjen</h1>
        <p>Za dostop do admin strani potrebujete administratorske pravice.</p>
        <a href="?svet=GLOBALNO" class="gumb gumb-primaren">← Nazaj na domov</a>
    </div>
</div>
<?php else: ?>
<div class="admin-stran">
    <div class="admin-glava">
        <h1 class="admin-naslov">🛠️ Nadzorna plosca</h1>
        <p class="admin-podnaslov">Upravljanje sistema in modulov</p>
    </div>
    
    <div class="admin-statistika">
        <div class="statistika-kartica">
            <div class="statistika-ikona">📊</div>
            <div class="statistika-podatki">
                <div class="statistika-stevilka"><?= $statistika['stevilo_modulov'] ?? 0 ?></div>
                <div class="statistika-oznaka">Aktivnih modulov</div>
            </div>
        </div>
        <div class="statistika-kartica">
            <div class="statistika-ikona">👥</div>
            <div class="statistika-podatki">
                <div class="statistika-stevilka"><?= $statistika['stevilo_uporabnikov'] ?? 0 ?></div>
                <div class="statistika-oznaka">Uporabnikov</div>
            </div>
        </div>
        <div class="statistika-kartica">
            <div class="statistika-ikona">⚙️</div>
            <div class="statistika-podatki">
                <div class="statistika-stevilka"><?= $sistem['verzija'] ?? SISTEM_VERZIJA ?></div>
                <div class="statistika-oznaka">Verzija sistema</div>
            </div>
        </div>
        <div class="statistika-kartica">
            <div class="statistika-ikona">💾</div>
            <div class="statistika-podatki">
                <div class="statistika-stevilka"><?= $statistika['poraba_pomnilnika'] ?? '0' ?> MB</div>
                <div class="statistika-oznaka">Poraba pomnilnika</div>
            </div>
        </div>
    </div>
    
    <div class="admin-mreza">
        <div class="admin-kartica">
            <h2>📦 Moduli</h2>
            <p>Upravljanje nameščenih modulov.</p>
            <a href="?svet=ASTRA&amp;pot=moduli" class="gumb gumb-sekundaren">Upravljaj module</a>
        </div>
        <div class="admin-kartica">
            <h2>👤 Uporabniki</h2>
            <p>Upravljanje uporabniških računov.</p>
            <a href="?svet=ASTRA&amp;pot=uporabniki" class="gumb gumb-sekundaren">Upravljaj uporabnike</a>
        </div>
        <div class="admin-kartica">
            <h2>⚙️ Nastavitve</h2>
            <p>Sistemske nastavitve in konfiguracija.</p>
            <a href="?svet=ASTRA&amp;pot=nastavitve" class="gumb gumb-sekundaren">Sistemske nastavitve</a>
        </div>
        <div class="admin-kartica">
            <h2>📊 Dnevniki</h2>
            <p>Pregled sistemskih dnevnikov.</p>
            <a href="?svet=ASTRA&amp;pot=dnevniki" class="gumb gumb-sekundaren">Preglej dnevnike</a>
        </div>
        <div class="admin-kartica">
            <h2>🧹 Cache</h2>
            <p>Počisti predpomnilnik sistema.</p>
            <button class="gumb gumb-sekundaren" id="pocistiCache">Počisti cache</button>
        </div>
        <div class="admin-kartica">
            <h2>🔄 Reload</h2>
            <p>Ponovno naloži sistem.</p>
            <button class="gumb gumb-sekundaren" id="reloadSistem">Ponovno naloži</button>
        </div>
    </div>
    
    <div class="admin-sistem-info">
        <h2>ℹ️ Informacije o sistemu</h2>
        <table class="tabela">
            <tr><th>Lastnost</th><th>Vrednost</th></tr>
            <tr><td>Ime sistema</td><td><?= IME_APLIKACIJE ?></td></tr>
            <tr><td>Verzija</td><td><?= SISTEM_VERZIJA ?></td></tr>
            <tr><td>Okolje</td><td><?= RAZVOJNI_NACIN ? 'Razvoj' : 'Produkcija' ?></td></tr>
            <tr><td>PHP verzija</td><td><?= PHP_VERSION ?></td></tr>
            <tr><td>Časovna cona</td><td><?= CASOVNA_CONA ?></td></tr>
            <tr><td>Zadnji zagon</td><td><?= date('Y-m-d H:i:s', $sistem['zadnji_zagon'] ?? time()) ?></td></tr>
        </table>
    </div>
</div>

<script nonce="<?= $vsebina['csp_nonce'] ?? '' ?>">
document.getElementById('pocistiCache')?.addEventListener('click', async () => {
    const odgovor = await fetch('api.php?akcija=admin_cache_pocisti', { method: 'POST' });
    const rezultat = await odgovor.json();
    alert(rezultat.sporocilo || 'Cache pociscen');
    location.reload();
});

document.getElementById('reloadSistem')?.addEventListener('click', async () => {
    if (confirm('Ponovno nalaganje sistema lahko povzroci kratkotrajno nedosegljivost. Nadaljujem?')) {
        const odgovor = await fetch('api.php?akcija=admin_reload', { method: 'POST' });
        const rezultat = await odgovor.json();
        alert(rezultat.sporocilo || 'Sistem ponovno nalozen');
        location.reload();
    }
});
</script>
<?php endif; ?>

<style>
.admin-stran {
    max-width: 1200px;
    margin: 0 auto;
}

.admin-glava {
    text-align: center;
    margin-bottom: 2rem;
}

.admin-naslov {
    font-size: 2rem;
    color: #e8c84a;
    margin-bottom: 0.5rem;
}

.admin-podnaslov {
    color: #aaa;
}

.admin-statistika {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-bottom: 2rem;
}

.statistika-kartica {
    background: rgba(255, 255, 255, 0.05);
    border-radius: 15px;
    padding: 1.25rem;
    display: flex;
    align-items: center;
    gap: 1rem;
}

.statistika-ikona {
    font-size: 2rem;
}

.statistika-podatki {
    flex: 1;
}

.statistika-stevilka {
    font-size: 1.8rem;
    font-weight: bold;
    color: #e8c84a;
}

.statistika-oznaka {
    font-size: 0.8rem;
    color: #888;
}

.admin-mreza {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.admin-kartica {
    background: rgba(255, 255, 255, 0.03);
    border-radius: 15px;
    padding: 1.5rem;
}

.admin-kartica h2 {
    color: #e8c84a;
    margin-bottom: 0.5rem;
    font-size: 1.2rem;
}

.admin-kartica p {
    color: #aaa;
    margin-bottom: 1rem;
    font-size: 0.9rem;
}

.admin-sistem-info {
    background: rgba(255, 255, 255, 0.03);
    border-radius: 15px;
    padding: 1.5rem;
}

.admin-sistem-info h2 {
    color: #e8c84a;
    margin-bottom: 1rem;
}

.admin-ni-dostopa {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 60vh;
    text-align: center;
}

.admin-ni-dostopa-vsebina {
    background: rgba(255, 255, 255, 0.05);
    border-radius: 20px;
    padding: 3rem;
}

.admin-ni-dostopa-ikona {
    font-size: 4rem;
    margin-bottom: 1rem;
}

.admin-ni-dostopa h1 {
    color: #e8c84a;
    margin-bottom: 0.5rem;
}

.admin-ni-dostopa p {
    color: #aaa;
    margin-bottom: 1.5rem;
}
</style>