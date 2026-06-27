<?php
/**
 * ============================================================
 * POT: GLOBALNO/render/strani/domov.php
 * 📅 VERZIJA: v114 (9.6.2026 18:00)
 * ============================================================
 *
 * 🏛️ NIVO: GLOBALNO (render/strani)
 * 📰 NAMEN: Domača stran – pozdrav, hitri dostop do modulov.
 * ✅ DOVOLJENO: echo, HTML
 * 🚫 PREPOVEDI: Brez business logike, brez SQL
 * 📌 STATUS: Stabilno
 * 👤 AVTOR: AstraMentalica Mojster
 * 🌐 JEZIK: sl
 * 🏷️ OZNAKE: globalno, render, strani, domov
 * ============================================================
 */

declare(strict_types=1);

defined('SISTEM_VARNOST') or die('Direkten dostop ni dovoljen.');

$moduli     = $vsebina['moduli'] ?? [];
$uporabnik  = $vsebina['uporabnik'] ?? ['ime' => 'Gost'];
$statistike = $vsebina['statistike'] ?? [];
$citati     = [
    '"Zvezde ne govorijo — osvetljujejo."',
    '"Vsaka planeta je ogledalo tvoje duše."',
    '"Pot se začne v tišini."',
    '"Zavedanje je prvi korak k svobodi."',
];
$citat = $citati[array_rand($citati)];
?>

<!-- POZDRAVNI BLOK -->
<div class="domov-pozdrav">
    <div class="domov-pozdrav-besedilo">
        <h1 class="domov-naslov">
            Dobrodošel<?= ((int)($uporabnik['spol'] ?? 0) === 2 ? 'a' : '') ?>,
            <span class="besedilo-zlato"><?= htmlspecialchars($uporabnik['ime'] ?? 'iskatelj') ?></span>
        </h1>
        <p class="domov-citat"><?= $citat ?></p>
    </div>
    <div class="domov-statistike">
        <?php if (!empty($statistike)): ?>
            <div class="stat-element">
                <span class="stat-stevilo"><?= (int)($statistike['moduli_obiskani'] ?? 0) ?></span>
                <span class="stat-napis">Modulov</span>
            </div>
            <div class="stat-element">
                <span class="stat-stevilo"><?= (int)($statistike['zapiski'] ?? 0) ?></span>
                <span class="stat-napis">Zapiskov</span>
            </div>
            <div class="stat-element">
                <span class="stat-stevilo"><?= (int)($statistike['dni_na_poti'] ?? 0) ?></span>
                <span class="stat-napis">Dni na poti</span>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- HITRI DOSTOP -->
<div class="domov-hitri">
    <a href="?svet=codex" class="hitri-kartica">
        <span class="hitri-ikona">📖</span>
        <span class="hitri-ime">Codex</span>
        <span class="hitri-opis">Knjiga znanja</span>
    </a>
    <a href="?svet=aeternum" class="hitri-kartica">
        <span class="hitri-ikona">📚</span>
        <span class="hitri-ime">Aeternum</span>
        <span class="hitri-opis">Knjižnica</span>
    </a>
    <a href="?svet=tarot" class="hitri-kartica">
        <span class="hitri-ikona">🃏</span>
        <span class="hitri-ime">Tarot</span>
        <span class="hitri-opis">Cikli in karte</span>
    </a>
    <a href="?svet=passport" class="hitri-kartica">
        <span class="hitri-ikona">🕊️</span>
        <span class="hitri-ime">Passport</span>
        <span class="hitri-opis">Moja pot</span>
    </a>
</div>

<!-- MODULI VESOLJE -->
<?php if (!empty($moduli)): ?>
<section class="domov-sekcija">
    <div class="sekcija-glava">
        <h2 class="sekcija-naslov">Razpoložljivi svetovi</h2>
        <a href="?svet=moduli" class="gumb gumb-sekundarni gumb-m">Vsi moduli →</a>
    </div>
    <div class="mreza-3">
        <?php foreach (array_slice($moduli, 0, 6) as $modul): ?>
        <a href="?svet=<?= urlencode($modul['oznaka'] ?? $modul['ime'] ?? '') ?>"
           class="modul-kartica">
            <div class="modul-kartica-ikona">
                <?= htmlspecialchars($modul['ikona'] ?? '◈') ?>
            </div>
            <div class="modul-kartica-vsebina">
                <h3 class="modul-kartica-ime"><?= htmlspecialchars($modul['ime'] ?? '') ?></h3>
                <p class="modul-kartica-opis"><?= htmlspecialchars($modul['opis'] ?? '') ?></p>
            </div>
            <?php if (!empty($modul['nova_vsebina'])): ?>
                <span class="znacka znacka-zlata">Novo</span>
            <?php endif; ?>
        </a>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<style>
/* ============================================================
 * DOMOV STRANI
 * ============================================================ */

/* Pozdravni blok */
.domov-pozdrav {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: var(--razmik-xl);
    padding: var(--razmik-xl) 0 var(--razmik-2xl);
    border-bottom: 1px solid var(--rob);
    margin-bottom: var(--razmik-xl);
}

.domov-naslov {
    font-size: var(--velikost-3xl);
    color: var(--besedilo-s);
    margin-bottom: var(--razmik-s);
    font-weight: var(--teza-mocna);
}

.domov-citat {
    color: var(--besedilo-d);
    font-style: italic;
    font-size: var(--velikost-l);
    margin: 0;
    max-width: 500px;
}

/* Statistike */
.domov-statistike {
    display: flex;
    gap: var(--razmik-l);
    flex-shrink: 0;
}

.stat-element {
    display: flex;
    flex-direction: column;
    align-items: center;
    background: var(--kartica);
    border: 1px solid var(--rob);
    border-radius: var(--rob-l);
    padding: var(--razmik-m) var(--razmik-l);
    min-width: 80px;
}

.stat-stevilo {
    font-size: var(--velikost-2xl);
    font-weight: var(--teza-mocna);
    color: var(--zlata);
    line-height: 1;
}

.stat-napis {
    font-size: var(--velikost-xs);
    color: var(--besedilo-d);
    margin-top: var(--razmik-xs);
}

/* Hitri dostop */
.domov-hitri {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: var(--razmik-m);
    margin-bottom: var(--razmik-2xl);
}

.hitri-kartica {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: var(--razmik-s);
    padding: var(--razmik-l) var(--razmik-m);
    background: var(--kartica);
    border: 1px solid var(--rob);
    border-radius: var(--rob-l);
    text-decoration: none;
    text-align: center;
    transition: all var(--prehod);
    cursor: pointer;
}

.hitri-kartica:hover {
    background: var(--zlata-dim);
    border-color: var(--rob-aktiven);
    transform: translateY(-3px);
    box-shadow: var(--senca-zlata);
}

.hitri-ikona  { font-size: 2rem; }
.hitri-ime    { font-weight: var(--teza-krepka); color: var(--besedilo-s); font-size: var(--velikost-m); }
.hitri-opis   { font-size: var(--velikost-xs); color: var(--besedilo-d); margin: 0; }

/* Sekcija */
.domov-sekcija { margin-bottom: var(--razmik-2xl); }

.sekcija-glava {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: var(--razmik-l);
}

.sekcija-naslov {
    font-size: var(--velikost-xl);
    color: var(--besedilo-s);
    font-family: var(--pisava-osnovna);
    font-weight: var(--teza-krepka);
}

/* Modul kartice */
.modul-kartica {
    display: flex;
    align-items: center;
    gap: var(--razmik-m);
    padding: var(--razmik-m) var(--razmik-l);
    background: var(--kartica);
    border: 1px solid var(--rob);
    border-radius: var(--rob-l);
    text-decoration: none;
    transition: all var(--prehod);
    position: relative;
    overflow: hidden;
}

.modul-kartica::before {
    content: '';
    position: absolute;
    left: 0; top: 0; bottom: 0;
    width: 3px;
    background: var(--zlata);
    opacity: 0;
    transition: opacity var(--prehod);
}

.modul-kartica:hover {
    background: var(--kartica-hover);
    border-color: rgba(255,255,255,0.12);
    transform: translateX(4px);
}

.modul-kartica:hover::before { opacity: 1; }

.modul-kartica-ikona {
    font-size: 1.8rem;
    flex-shrink: 0;
    width: 2.5rem;
    text-align: center;
}

.modul-kartica-vsebina { flex: 1; overflow: hidden; }

.modul-kartica-ime {
    font-size: var(--velikost-m);
    color: var(--besedilo-s);
    font-family: var(--pisava-osnovna);
    font-weight: var(--teza-srednja);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    margin-bottom: 2px;
}

.modul-kartica-opis {
    font-size: var(--velikost-xs);
    color: var(--besedilo-d);
    margin: 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

@media (max-width: 800px) {
    .domov-pozdrav  { flex-direction: column; }
    .domov-hitri    { grid-template-columns: repeat(2, 1fr); }
    .domov-statistike { flex-wrap: wrap; }
}
</style>
