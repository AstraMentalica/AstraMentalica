<?php
/**
 * ============================================================
 * POT: GLOBALNO/render/strani/domov.php
 * 📅 VERZIJA: v116 (27.6.2026)
 * ============================================================
 *
 * 🏛️ NIVO: GLOBALNO (render)
 *
 * 📰 NAMEN:
 *     Landing page – prvi vtis za obiskovalce.
 *     Čaroben, kozmičen design z modulskim prikazom.
 *
 * ✅ DOVOLJENO:
 *     - echo, HTML, CSS
 *
 * 🚫 PREPOVEDI:
 *     - Brez business logike
 *     - Brez direktnih poizvedb v bazo
 *     - Samo pasiven prikaz podatkov iz $vsebina
 *
 * 📡 PRIČAKUJE IZ $vsebina:
 *     - $vsebina['uporabnik']     = ['ime', 'email', 'avatar', 'vloga']
 *     - $vsebina['moduli']        = [[ime, ikona, opis, kategorija], ...]
 *     - $vsebina['statistika']    = ['uporabniki', 'moduli', 'branij']
 *
 * 📌 STATUS:
 *     Novo
 *
 * 👤 AVTOR:
 *     Mavis / AstraMentalica
 *
 * 🌐 JEZIK:
 *     sl
 * ============================================================
 */

declare(strict_types=1);

// Podatki prihajajo iz backend-a
if (!isset($vsebina)) {
    $vsebina = [];
}

$uporabnik  = $vsebina['uporabnik']  ?? null;
$moduli     = $vsebina['moduli']     ?? _domov_privzeti_moduli();
$statistika = $vsebina['statistika'] ?? ['uporabniki' => 0, 'moduli' => 60, 'branij' => 0];
$jePrijavljen = !empty($uporabnik);

// Glavni moduli za prikaz na landing
$glavniModuli = array_slice($moduli, 0, 8);
?>

<div class="landing">

    <!-- ========================================================
         HERO SEKCIJA
         ======================================================== -->
    <section class="hero">
        <div class="hero-ozadje">
            <div class="zvezde"></div>
            <div class="svetlobni-odsev"></div>
        </div>

        <div class="hero-vsebina">
            <div class="hero-emblem">✦</div>
            <h1 class="hero-naslov">
                <span class="hero-naslov-vrstila">Astra</span>
                <span class="hero-naslov-podnaslov">Mentalica</span>
            </h1>
            <p class="hero-tagline">Kozmična šola zavesti</p>
            <p class="hero-opis">
                Raziskujte skrite dimenzije sebe skozi tarot, numerologijo,
                astrološke sisteme in drevn modrosti sveta.
            </p>

            <div class="hero-dejanja">
                <?php if ($jePrijavljen): ?>
                    <a href="?svet=passport" class="gumb gumb-primarni gumb-l">
                        🌟 Moj Passport
                    </a>
                    <a href="?svet=moduli" class="gumb gumb-sekundarni gumb-l">
                        ✦ Moduli
                    </a>
                <?php else: ?>
                    <a href="?svet=registracija" class="gumb gumb-primarni gumb-l">
                        Začni pot
                    </a>
                    <a href="?svet=prijava" class="gumb gumb-sekundarni gumb-l">
                        Prijava
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- ========================================================
         STATISTIKA
         ======================================================== -->
    <section class="statistika">
        <div class="statistika-postavitev">
            <div class="statistika-element">
                <span class="statistika-stevilo"><?= number_format($statistika['uporabniki']) ?></span>
                <span class="statistika-napis">Uporabnikov</span>
            </div>
            <div class="statistika-lociilo">✦</div>
            <div class="statistika-element">
                <span class="statistika-stevilo"><?= $statistika['moduli'] ?></span>
                <span class="statistika-napis">Modulov</span>
            </div>
            <div class="statistika-lociilo">✦</div>
            <div class="statistika-element">
                <span class="statistika-stevilo"><?= number_format($statistika['branij']) ?></span>
                <span class="statistika-napis">Branij</span>
            </div>
        </div>
    </section>

    <!-- ========================================================
         MODULI PREGLED
         ======================================================== -->
    <section class="moduli-sekcija">
        <div class="sekcija-glava">
            <h2 class="sekcija-naslov">Svetovi znanja</h2>
            <p class="sekcija-podnaslov">
                Izberite svojo pot med različnimi duhovnimi tradicijami
            </p>
        </div>

        <div class="moduli-mreza">
            <?php foreach ($glavniModuli as $modul): ?>
                <a href="?svet=<?= urlencode($modul['oznaka'] ?? $modul['ime']) ?>"
                   class="modul-kartica">
                    <div class="modul-ikona"><?= htmlspecialchars($modul['ikona'] ?? '◈') ?></div>
                    <h3 class="modul-ime"><?= htmlspecialchars($modul['ime']) ?></h3>
                    <p class="modul-opis"><?= htmlspecialchars($modul['opis'] ?? '') ?></p>
                    <span class="modul-kategorija"><?= htmlspecialchars($modul['kategorija'] ?? '') ?></span>
                </a>
            <?php endforeach; ?>
        </div>

        <div class="sekcija-noga">
            <a href="?svet=moduli" class="gumb gumb-sekundarni">
                Vsi moduli ✦
            </a>
        </div>
    </section>

    <!-- ========================================================
         VREDNOTE
         ======================================================== -->
    <section class="vrednote-sekcija">
        <div class="sekcija-glava">
            <h2 class="sekcija-naslov"> zakaj AstraMentalica?</h2>
        </div>

        <div class="vrednote-mreza">
            <div class="vrednota">
                <div class="vrednota-ikona">🌙</div>
                <h3 class="vrednota-naslov">Popolna izolacija</h3>
                <p class="vrednota-opis">
                    Vsak modul deluje kot samostojen svet, ločen od drugih.
                    Vaši podatki ostanejo tam, kjer jih želite.
                </p>
            </div>

            <div class="vrednota">
                <div class="vrednota-ikona">🔮</div>
                <h3 class="vrednota-naslov">Drevna modrost</h3>
                <p class="vrednota-opis">
                    Tarot, numerologija, astrologija, kabala in več kot
                    60 modulov iz različnih duhovnih tradicij.
                </p>
            </div>

            <div class="vrednota">
                <div class="vrednota-ikona">✨</div>
                <h3 class="vrednota-naslov">Osebni peskovnik</h3>
                <p class="vrednota-opis">
                    Ustvarite svoj čarobni prostor za eksperimente,
                    dnevnike in osebni razvoj.
                </p>
            </div>
        </div>
    </section>

    <!-- ========================================================
         CTA SEKCIJA (za neprijavljene)
         ======================================================== -->
    <?php if (!$jePrijavljen): ?>
    <section class="cta-sekcija">
        <div class="cta-vsebina">
            <h2 class="cta-naslov">Pripravljeni na pot?</h2>
            <p class="cta-opis">
                Pridružite se skupnosti raziskovalcev zavesti.
                Brezplačna registracija, takojšen dostop.
            </p>
            <a href="?svet=registracija" class="gumb gumb-primarni gumb-l">
                Ustvari račun
            </a>
        </div>
    </section>
    <?php endif; ?>

</div>

<style>
/* ============================================================
 * LANDING PAGE
 * ============================================================ */
.landing {
    position: relative;
    z-index: 1;
}

/* --------------------------------------------------------
 * HERO
 * -------------------------------------------------------- */
.hero {
    position: relative;
    min-height: 85vh;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    padding: var(--razmik-3xl) var(--razmik-xl);
    overflow: hidden;
}

.hero-ozadje {
    position: absolute;
    inset: 0;
    pointer-events: none;
}

.zvezde {
    position: absolute;
    inset: 0;
    background-image:
        radial-gradient(2px 2px at 20px 30px, var(--zlata), transparent),
        radial-gradient(2px 2px at 40px 70px, rgba(255,255,255,0.8), transparent),
        radial-gradient(1px 1px at 90px 40px, var(--besedilo), transparent),
        radial-gradient(2px 2px at 130px 80px, var(--modra), transparent),
        radial-gradient(1px 1px at 160px 120px, var(--besedilo), transparent);
    background-size: 200px 200px;
    animation: zvezde-padanje 100s linear infinite;
    opacity: 0.6;
}

@keyframes zvezde-padanje {
    from { transform: translateY(0); }
    to   { transform: translateY(-200px); }
}

.svetlobni-odsev {
    position: absolute;
    top: -50%;
    left: 50%;
    transform: translateX(-50%);
    width: 150%;
    height: 100%;
    background: radial-gradient(ellipse at center top,
        rgba(232, 200, 74, 0.08) 0%,
        rgba(156, 111, 228, 0.04) 30%,
        transparent 60%);
    pointer-events: none;
}

.hero-vsebina {
    position: relative;
    max-width: 800px;
}

.hero-emblem {
    font-size: 4rem;
    color: var(--zlata);
    text-shadow: 0 0 40px rgba(232, 200, 74, 0.5);
    margin-bottom: var(--razmik-m);
    animation: emble-tok 4s ease-in-out infinite;
}

@keyframes emble-tok {
    0%, 100% { transform: scale(1); opacity: 1; }
    50% { transform: scale(1.1); opacity: 0.8; }
}

.hero-naslov {
    font-size: clamp(3rem, 10vw, 5rem);
    line-height: 1;
    margin-bottom: var(--razmik-s);
    display: flex;
    flex-direction: column;
    gap: 0;
}

.hero-naslov-vrstila {
    color: var(--besedilo-s);
}

.hero-naslov-podnaslov {
    color: var(--zlata);
    text-shadow: 0 0 30px rgba(232, 200, 74, 0.3);
}

.hero-tagline {
    font-family: var(--pisava-naslov);
    font-size: var(--velikost-xl);
    color: var(--besedilo-d);
    font-style: italic;
    margin-bottom: var(--razmik-l);
}

.hero-opis {
    font-size: var(--velikost-l);
    color: var(--besedilo);
    max-width: 600px;
    margin: 0 auto var(--razmik-xl);
    line-height: var(--vrsticna);
}

.hero-dejanja {
    display: flex;
    gap: var(--razmik-m);
    justify-content: center;
    flex-wrap: wrap;
}

/* --------------------------------------------------------
 * STATISTIKA
 * -------------------------------------------------------- */
.statistika {
    padding: var(--razmik-xl) var(--razmik-xl);
    background: var(--kartica);
    border-top: 1px solid var(--rob);
    border-bottom: 1px solid var(--rob);
}

.statistika-postavitev {
    max-width: 700px;
    margin: 0 auto;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: var(--razmik-xl);
}

.statistika-element {
    text-align: center;
}

.statistika-stevilo {
    display: block;
    font-size: var(--velikost-3xl);
    font-weight: var(--teza-krepka);
    color: var(--zlata);
    line-height: 1;
}

.statistika-napis {
    font-size: var(--velikost-s);
    color: var(--besedilo-d);
    margin-top: var(--razmik-xs);
}

.statistika-lociilo {
    color: var(--rob);
    font-size: var(--velikost-xl);
}

/* --------------------------------------------------------
 * SEKCIJE SKUPNO
 * -------------------------------------------------------- */
.moduli-sekcija,
.vrednote-sekcija {
    padding: var(--razmik-3xl) var(--razmik-xl);
}

.sekcija-glava {
    text-align: center;
    margin-bottom: var(--razmik-2xl);
}

.sekcija-naslov {
    font-size: var(--velikost-2xl);
    margin-bottom: var(--razmik-s);
}

.sekcija-podnaslov {
    color: var(--besedilo-d);
    font-size: var(--velikost-l);
    max-width: 500px;
    margin: 0 auto;
}

.sekcija-noga {
    text-align: center;
    margin-top: var(--razmik-2xl);
}

/* --------------------------------------------------------
 * MODULI MREŽA
 * -------------------------------------------------------- */
.moduli-mreza {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
    gap: var(--razmik-l);
    max-width: var(--vsebina-max);
    margin: 0 auto;
}

.modul-kartica {
    background: var(--kartica);
    border: 1px solid var(--rob);
    border-radius: var(--rob-l);
    padding: var(--razmik-l);
    text-decoration: none;
    transition: all var(--prehod);
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
}

.modul-kartica:hover {
    background: var(--kartica-hover);
    border-color: var(--rob-aktiven);
    transform: translateY(-4px);
    box-shadow: var(--senca-zlata);
}

.modul-ikona {
    font-size: 2.5rem;
    margin-bottom: var(--razmik-m);
}

.modul-ime {
    font-size: var(--velikost-l);
    color: var(--besedilo-s);
    margin-bottom: var(--razmik-s);
}

.modul-opis {
    font-size: var(--velikost-s);
    color: var(--besedilo-d);
    flex: 1;
    margin-bottom: var(--razmik-m);
}

.modul-kategorija {
    font-size: var(--velikost-xs);
    color: var(--besedilo-m);
    background: var(--kartica);
    padding: 0.2rem 0.6rem;
    border-radius: var(--rob-pill);
}

/* --------------------------------------------------------
 * VREDNOTE
 * -------------------------------------------------------- */
.vrednote-mreza {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: var(--razmik-xl);
    max-width: 1000px;
    margin: 0 auto;
}

.vrednota {
    text-align: center;
    padding: var(--razmik-l);
}

.vrednota-ikona {
    font-size: 3rem;
    margin-bottom: var(--razmik-m);
}

.vrednota-naslov {
    font-size: var(--velikost-l);
    color: var(--besedilo-s);
    margin-bottom: var(--razmik-s);
}

.vrednota-opis {
    font-size: var(--velikost-m);
    color: var(--besedilo-d);
    max-width: 300px;
    margin: 0 auto;
}

/* --------------------------------------------------------
 * CTA
 * -------------------------------------------------------- */
.cta-sekcija {
    padding: var(--razmik-3xl) var(--razmik-xl);
    background: linear-gradient(to bottom, transparent, rgba(232, 200, 74, 0.03));
    text-align: center;
}

.cta-naslov {
    font-size: var(--velikost-2xl);
    margin-bottom: var(--razmik-m);
}

.cta-opis {
    font-size: var(--velikost-l);
    color: var(--besedilo-d);
    max-width: 500px;
    margin: 0 auto var(--razmik-xl);
}

/* --------------------------------------------------------
 * RESPONSIVE
 * -------------------------------------------------------- */
@media (max-width: 768px) {
    .hero { min-height: 70vh; padding: var(--razmik-2xl) var(--razmik-m); }
    .statistika-postavitev { flex-direction: column; gap: var(--razmik-l); }
    .statistika-lociilo { display: none; }
    .moduli-mreza { grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); }
}
</style>

<?php
/**
 * Privzeti moduli za prikaz (če backend ne posreduje)
 */
function _domov_privzeti_moduli(): array
{
    return [
        ['ime' => 'Tarot', 'oznaka' => 'tarot', 'ikona' => '🂡', 'opis' => 'Klasični in Crowley tarot branje', 'kategorija' => 'Orakleum'],
        ['ime' => 'Numerologija', 'oznaka' => 'numyra', 'ikona' => '🔢', 'opis' => 'Številke kot ključ k usodi', 'kategorija' => 'Simboli'],
        ['ime' => 'Lunaris', 'oznaka' => 'lunaris', 'ikona' => '🌙', 'opis' => 'Lunina pot in rituali', 'kategorija' => 'Nebo'],
        ['ime' => 'Energetica', 'oznaka' => 'energetica', 'ikona' => '⚡', 'opis' => 'Čakre, aura in energetska čiščenja', 'kategorija' => 'Zemlja'],
        ['ime' => 'Codex', 'oznaka' => 'codex', 'ikona' => '📖', 'opis' => 'Knjižnica drevne modrosti', 'kategorija' => 'Svet'],
        ['ime' => 'Aeternum', 'oznaka' => 'aeternum', 'ikona' => '⏳', 'opis' => 'Časovna spirala in cikli', 'kategorija' => 'Svet'],
        ['ime' => 'Kabbaloria', 'oznaka' => 'kabbaloria', 'ikona' => '🌳', 'opis' => 'Kabala in drevo življenja', 'kategorija' => 'Simboli'],
        ['ime' => 'BotanicaSacra', 'oznaka' => 'botanicasacra', 'ikona' => '🌿', 'opis' => 'Zdravilna moč rastlin', 'kategorija' => 'Zemlja'],
    ];
}
?>