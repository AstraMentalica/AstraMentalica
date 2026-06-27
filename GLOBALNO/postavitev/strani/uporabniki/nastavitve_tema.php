<?php
/**
 * ============================================================
 * POT: GLOBALNO/render/strani/nastavitve_tema.php
 * 📅 VERZIJA: v114 (9.6.2026 18:00)
 * ============================================================
 *
 * 🏛️ NIVO: GLOBALNO (render/strani)
 * 📰 NAMEN: Nastavitve – izbira teme in jezika.
 * ✅ DOVOLJENO: echo, HTML
 * 🚫 PREPOVEDI: Brez business logike, brez SQL
 * 📌 STATUS: Stabilno
 * 👤 AVTOR: AstraMentalica Mojster
 * 🌐 JEZIK: sl
 * 🏷️ OZNAKE: globalno, render, nastavitve, tema, jezik
 * ============================================================
 */

declare(strict_types=1);

defined('SISTEM_VARNOST') or die('Direkten dostop ni dovoljen.');

require_once defined('POT_GLOBALNO')
    ? POT_GLOBALNO . '/vmesnik/teme/upravljalec_tem.php'
    : __DIR__ . '/../../vmesnik/teme/upravljalec_tem.php';

$vseTeme   = tema_pridobi_vse();
$vsiJeziki = jezik_pridobi_vse();
$trenutnaTema   = tema_pridobi_trenutno();
$trenutniJezik  = jezik_pridobi_trenutnega();
$csrf = $vsebina['csrf'] ?? '';
?>

<div class="nast-ovoj">

    <!-- Tema -->
    <section class="kartica nast-sekcija">
        <h2 class="kartica-naslov">🎨 <?= t('nastavitve.tema') ?></h2>
        <p class="nast-opis"><?= t('nastavitve.opis_tema') ?></p>

        <div class="tema-mreza" id="temaMreza">
            <?php foreach ($vseTeme as $id => $tema): ?>
            <?php
                $jeAktivna = ($id === $trenutnaTema);
                $vlMinReq  = (int)($tema['vloga_min'] ?? 0);
                $jeZaklenjena = ($vlMinReq > (int)($_SESSION['uporabnik']['vloga'] ?? 0));
            ?>
            <button
                type="button"
                class="tema-kartica<?= $jeAktivna ? ' aktivna' : '' ?><?= $jeZaklenjena ? ' zaklenjena' : '' ?>"
                data-tema="<?= htmlspecialchars($id) ?>"
                <?= $jeZaklenjena ? 'disabled aria-disabled="true"' : '' ?>
                title="<?= htmlspecialchars($tema['naziv']) ?>"
                style="--tm-barva: <?= htmlspecialchars($tema['barva']) ?>; --tm-oz: <?= htmlspecialchars($tema['ozadje']) ?>;"
            >
                <div class="tema-k-ozadje">
                    <!-- Mini predogled -->
                    <div class="tema-k-nav"></div>
                    <div class="tema-k-vsebina">
                        <div class="tema-k-kartica"></div>
                        <div class="tema-k-kartica tema-k-kartica-2"></div>
                    </div>
                    <?php if ($jeZaklenjena): ?>
                        <div class="tema-zaklenjena-overlay">🔒</div>
                    <?php endif; ?>
                    <?php if ($jeAktivna): ?>
                        <div class="tema-aktivna-oznaka">✓</div>
                    <?php endif; ?>
                </div>
                <div class="tema-k-info">
                    <span class="tema-k-ikona"><?= $tema['ikona'] ?></span>
                    <div>
                        <div class="tema-k-naziv"><?= htmlspecialchars($tema['naziv']) ?></div>
                        <div class="tema-k-opis"><?= htmlspecialchars($tema['opis']) ?></div>
                    </div>
                </div>
            </button>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Jezik -->
    <section class="kartica nast-sekcija">
        <h2 class="kartica-naslov">🌐 <?= t('nastavitve.jezik') ?></h2>
        <p class="nast-opis"><?= t('nastavitve.opis_jezik') ?></p>

        <div class="jezik-mreza" id="jezikmreza">
            <?php foreach ($vsiJeziki as $id => $jezik):
                if (!($jezik['dostopen'] ?? false)) continue;
                $jeAktiven = ($id === $trenutniJezik);
            ?>
            <button
                type="button"
                class="jezik-gumb<?= $jeAktiven ? ' aktiven' : '' ?>"
                data-jezik="<?= htmlspecialchars($id) ?>"
            >
                <span class="jezik-ikona"><?= $jezik['ikona'] ?></span>
                <span class="jezik-naziv"><?= htmlspecialchars($jezik['naziv']) ?></span>
                <?php if ($jeAktiven): ?>
                    <span class="jezik-kljukica">✓</span>
                <?php endif; ?>
            </button>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Feedback sporocilo -->
    <div id="nastSporocilo" class="sporocilo sporocilo-uspeh skrij" role="status"></div>

</div>

<style>
.nast-ovoj  { display: flex; flex-direction: column; gap: var(--razmik-l); max-width: 900px; }
.nast-sekcija { }
.nast-opis  { color: var(--besedilo-d); font-size: var(--velikost-s); margin-bottom: var(--razmik-l); margin-top: calc(-1 * var(--razmik-s)); }

/* TEMA MREZA */
.tema-mreza {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: var(--razmik-m);
}

.tema-kartica {
    background: none;
    border: 2px solid var(--rob);
    border-radius: var(--rob-l);
    cursor: pointer;
    padding: 0;
    overflow: hidden;
    transition: all 0.2s;
    text-align: left;
}

.tema-kartica:hover:not(:disabled) {
    border-color: var(--tm-barva, var(--zl));
    transform: translateY(-3px);
    box-shadow: 0 8px 24px rgba(0,0,0,.3);
}

.tema-kartica.aktivna {
    border-color: var(--tm-barva, var(--zl));
    box-shadow: 0 0 0 1px var(--tm-barva, var(--zl)), 0 8px 24px rgba(0,0,0,.3);
}

.tema-kartica.zaklenjena {
    opacity: 0.5;
    cursor: not-allowed;
}

/* Mini predogled */
.tema-k-ozadje {
    background: var(--tm-oz, #07071a);
    height: 110px;
    position: relative;
    overflow: hidden;
    display: flex;
    gap: 6px;
    padding: 8px;
}

.tema-k-nav {
    width: 28px;
    background: rgba(255,255,255,.08);
    border-radius: 5px;
    flex-shrink: 0;
}

.tema-k-vsebina {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.tema-k-kartica {
    flex: 1;
    background: rgba(255,255,255,.06);
    border-radius: 4px;
    border: 1px solid rgba(255,255,255,.06);
}

.tema-k-kartica-2 { flex: 0.6; }

.tema-zaklenjena-overlay {
    position: absolute;
    inset: 0;
    background: rgba(0,0,0,.6);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}

.tema-aktivna-oznaka {
    position: absolute;
    top: 8px;
    right: 8px;
    width: 22px;
    height: 22px;
    background: var(--tm-barva, var(--zl));
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: .75rem;
    color: #000;
    font-weight: 700;
}

.tema-k-info {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 12px;
    background: var(--kar);
}

.tema-k-ikona  { font-size: 1.2rem; flex-shrink: 0; }
.tema-k-naziv  { font-size: .82rem; font-weight: 600; color: var(--be-s); }
.tema-k-opis   { font-size: .7rem;  color: var(--be-d); margin-top: 2px; }

/* JEZIK MREZA */
.jezik-mreza {
    display: flex;
    flex-wrap: wrap;
    gap: var(--razmik-s);
}

.jezik-gumb {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 18px;
    background: var(--kar);
    border: 1px solid var(--rob);
    border-radius: var(--rob-pill);
    color: var(--be-d);
    font-size: var(--velikost-s);
    cursor: pointer;
    transition: all 0.2s;
}

.jezik-gumb:hover {
    background: var(--kar-h);
    color: var(--be-s);
    border-color: rgba(255,255,255,.15);
}

.jezik-gumb.aktiven {
    background: var(--zl-d);
    color: var(--zl);
    border-color: var(--rob-a);
    font-weight: 600;
}

.jezik-ikona   { font-size: 1.2rem; }
.jezik-naziv   { font-weight: 500; }
.jezik-kljukica { color: var(--zl); font-size: .8rem; margin-left: 4px; }
</style>

<script>
(function() {
    var csrf = <?= json_encode($csrf) ?>;

    function posljиApiKlic(akcija, podatki, callback) {
        var telo = Object.assign({ csrf: csrf }, podatki);
        fetch('/api?akcija=' + akcija, {
            method:  'POST',
            headers: { 'Content-Type': 'application/json' },
            body:    JSON.stringify(telo)
        })
        .then(function(r) { return r.json(); })
        .then(callback)
        .catch(function() { prikaziSporocilo('Napaka pri shranjevanju.', 'napaka'); });
    }

    function prikaziSporocilo(besedilo, tip) {
        var el = document.getElementById('nastSporocilo');
        if (!el) return;
        el.textContent = besedilo;
        el.className = 'sporocilo sporocilo-' + (tip === 'napaka' ? 'napaka' : 'uspeh');
        el.classList.remove('skrij');
        setTimeout(function() { el.classList.add('skrij'); }, 3000);
    }

    // TEMA — klik
    document.querySelectorAll('.tema-kartica:not(:disabled)').forEach(function(k) {
        k.addEventListener('click', function() {
            var novaТema = k.getAttribute('data-tema');

            // Takojšnja vizualna sprememba
            document.documentElement.setAttribute('data-tema', novaТema);
            localStorage.setItem('tema', novaТema);

            // Posodobi aktivno kartico
            document.querySelectorAll('.tema-kartica').forEach(function(x) {
                x.classList.remove('aktivna');
                x.querySelector('.tema-aktivna-oznaka') && x.querySelector('.tema-aktivna-oznaka').remove();
            });
            k.classList.add('aktivna');
            var oznaka = document.createElement('div');
            oznaka.className = 'tema-aktivna-oznaka';
            oznaka.textContent = '✓';
            k.querySelector('.tema-k-ozadje').appendChild(oznaka);

            // Shrani na strežnik
            posljиApiKlic('nastavitve_tema', { tema: novaТema }, function(r) {
                if (r.status === 'success') {
                    prikaziSporocilo('Tema shranjena.', 'uspeh');
                } else {
                    prikaziSporocilo(r.sporocilo || 'Napaka.', 'napaka');
                }
            });
        });
    });

    // JEZIK — klik
    document.querySelectorAll('.jezik-gumb').forEach(function(g) {
        g.addEventListener('click', function() {
            var noviJezik = g.getAttribute('data-jezik');

            document.querySelectorAll('.jezik-gumb').forEach(function(x) {
                x.classList.remove('aktiven');
                var kl = x.querySelector('.jezik-kljukica');
                if (kl) kl.remove();
            });
            g.classList.add('aktiven');
            var kl = document.createElement('span');
            kl.className = 'jezik-kljukica';
            kl.textContent = '✓';
            g.appendChild(kl);

            localStorage.setItem('jezik', noviJezik);

            posljиApiKlic('nastavitve_jezik', { jezik: noviJezik }, function(r) {
                if (r.status === 'success') {
                    prikaziSporocilo('Jezik shranjen. Stran se bo osvežila...', 'uspeh');
                    setTimeout(function() { window.location.reload(); }, 1200);
                } else {
                    prikaziSporocilo(r.sporocilo || 'Napaka.', 'napaka');
                }
            });
        });
    });

    // Ob zagonu – povrni temo iz localStorage (instant, brez flash)
    (function() {
        var shTema = localStorage.getItem('tema');
        if (shTema) document.documentElement.setAttribute('data-tema', shTema);
    })();
})();
</script>
