<?php
/**
 * ---------------------------------------------------------
 * POT: GLOBALNO/postavitev/strani/uporabniki/domov.php
 * v111 (27.5.2026 07:45)
 * ---------------------------------------------------------
 * OPIS: Domača stran – prikaz vsebine
 * ---------------------------------------------------------
 *
 * ODVISNOSTI:
 * - pot.php (posredno)
 *
 * UPORABA:
 * - Ko je tip 'domov'
 *
 * PREPOVEDI:
 * - Brez business logike
 * - Brez direktnih poizvedb v bazo
 *
 * STATUS: Stabilno
 *
 * ZGODOVINA:
 * - v111: FAZA 9 – implementacija
 *
 * ---------------------------------------------------------
 * AVTOR: AstraMentalica Mojster
 * ---------------------------------------------------------
 */

declare(strict_types=1);

// Podatki prihajajo iz backend-a (SISTEM/storitve_svetov/globalno/)
// $vsebina je podana preko globalno_prikaz_strani()

if (!isset($vsebina)) {
    $vsebina = [];
}

$uporabnik = $vsebina['uporabnik'] ?? null;
$gradniki = $vsebina['gradniki'] ?? [];
?>

<div class="domov">
    <section class="hero">
        <h1 class="hero-naslov">Dobrodošli v <?= IME_APLIKACIJE ?></h1>
        <p class="hero-opis">Platforma za duhovni razvoj in raziskovanje</p>
        
        <?php if (!$uporabnik): ?>
            <div class="hero-gumbi">
                <a href="?svet=UPORABNIKI&amp;pot=prijava" class="gumb gumb-primaren">Prijava</a>
                <a href="?svet=UPORABNIKI&amp;pot=registracija" class="gumb gumb-sekundaren">Registracija</a>
            </div>
        <?php else: ?>
            <div class="hero-pozdrav">
                <p>Pozdravljen/a, <?= htmlspecialchars($uporabnik['ime'] ?? $uporabnik['email'] ?? 'Uporabnik') ?>!</p>
                <a href="?svet=UPORABNIKI&amp;pot=profil" class="gumb">Moj profil</a>
            </div>
        <?php endif; ?>
    </section>
    
    <section class="gradniki">
        <h2>Moduli</h2>
        <div class="gradniki-mreza">
            <?php foreach ($gradniki as $gradnik): ?>
                <div class="kartica gradnik-kartica">
                    <div class="kartica-ikona"><?= htmlspecialchars($gradnik['ikona'] ?? '📦') ?></div>
                    <h3 class="kartica-naslov"><?= htmlspecialchars($gradnik['ime'] ?? 'Modul') ?></h3>
                    <p class="kartica-opis"><?= htmlspecialchars($gradnik['opis'] ?? '') ?></p>
                    <a href="?svet=MODULI_CEL&amp;rel=<?= urlencode($gradnik['kategorija'] . '/' . $gradnik['ime']) ?>" class="gumb gumb-majhen">
                        Odpri
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
    
    <section class="o-sistemu">
        <h2>O sistemu</h2>
        <p><?= IME_APLIKACIJE ?> je večsvetovni runtime sistem, ki omogoča modularno razširjanje funkcionalnosti.</p>
        <p>Verzija: <?= SISTEM_VERZIJA ?></p>
    </section>
</div>

<style>
.domov {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem;
}

.hero {
    text-align: center;
    padding: 4rem 2rem;
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
    border-radius: 20px;
    margin-bottom: 3rem;
}

.hero-naslov {
    font-size: 3rem;
    color: #e8c84a;
    margin-bottom: 1rem;
}

.hero-opis {
    font-size: 1.2rem;
    color: #ccc;
    margin-bottom: 2rem;
}

.hero-gumbi {
    display: flex;
    gap: 1rem;
    justify-content: center;
}

.hero-pozdrav {
    background: rgba(232,200,74,0.1);
    padding: 1rem;
    border-radius: 10px;
}

.gradniki-mreza {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-top: 1.5rem;
}

.kartica {
    background: rgba(255,255,255,0.05);
    border-radius: 15px;
    padding: 1.5rem;
    transition: transform 0.3s, box-shadow 0.3s;
}

.kartica:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.3);
}

.kartica-ikona {
    font-size: 3rem;
    text-align: center;
    margin-bottom: 1rem;
}

.kartica-naslov {
    text-align: center;
    margin-bottom: 0.5rem;
}

.kartica-opis {
    color: #aaa;
    font-size: 0.9rem;
    text-align: center;
    margin-bottom: 1rem;
}

.o-sistemu {
    margin-top: 3rem;
    padding: 2rem;
    background: rgba(255,255,255,0.03);
    border-radius: 15px;
    text-align: center;
}

@media (max-width: 768px) {
    .hero-naslov { font-size: 2rem; }
    .gradniki-mreza { grid-template-columns: 1fr; }
}
</style>