<?php
/**
 * ---------------------------------------------------------
 * POT: GLOBALNO/postavitev/osnova/navigacija.php
 * v111 (27.5.2026 14:30)
 * ---------------------------------------------------------
 * OPIS: Navigacijski meni – glavna navigacija
 * ---------------------------------------------------------
 *
 * ODVISNOSTI:
 * - pot.php
 *
 * UPORABA:
 * - GLOBALNO/render/postavitev/*.php
 *
 * PREPOVEDI:
 * - Brez business logike
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

$aktivnaPot = $_GET['svet'] ?? 'GLOBALNO';
$uporabnik = $vsebina['uporabnik'] ?? null;
$jePrijavljen = $uporabnik !== null;
?>

<nav class="navigacija">
    <div class="navigacija-vsebina">
        <div class="nav-logotip">
            <a href="?svet=">
                <span class="nav-logotip-ikona">🌌</span>
                <span class="nav-logotip-besedilo"><?= IME_APLIKACIJE ?></span>
            </a>
        </div>
        
        <button class="nav-meni-gumb" id="navMeniGumb" aria-label="Meni">☰</button>
        
        <ul class="nav-seznam" id="navSeznam">
            <li class="nav-element <?= $aktivnaPot === 'GLOBALNO' ? 'aktivno' : '' ?>">
                <a href="?svet=GLOBALNO" class="nav-povezava">
                    <span class="nav-ikona">🏠</span>
                    <span class="nav-besedilo">Domov</span>
                </a>
            </li>
            
            <li class="nav-element <?= $aktivnaPot === 'VODA' ? 'aktivno' : '' ?>">
                <a href="?svet=VODA" class="nav-povezava">
                    <span class="nav-ikona">💧</span>
                    <span class="nav-besedilo">Voda</span>
                </a>
            </li>
            
            <li class="nav-element <?= $aktivnaPot === 'ZRAK' ? 'aktivno' : '' ?>">
                <a href="?svet=ZRAK" class="nav-povezava">
                    <span class="nav-ikona">🌬️</span>
                    <span class="nav-besedilo">Zrak</span>
                </a>
            </li>
            
            <li class="nav-element <?= $aktivnaPot === 'ETER' ? 'aktivno' : '' ?>">
                <a href="?svet=ETER" class="nav-povezava">
                    <span class="nav-ikona">✨</span>
                    <span class="nav-besedilo">Eter</span>
                </a>
            </li>
            
            <li class="nav-element <?= $aktivnaPot === 'ZEMLJA' ? 'aktivno' : '' ?>">
                <a href="?svet=ZEMLJA" class="nav-povezava">
                    <span class="nav-ikona">🌍</span>
                    <span class="nav-besedilo">Zemlja</span>
                </a>
            </li>
            
            <li class="nav-element <?= $aktivnaPot === 'OGENJ' ? 'aktivno' : '' ?>">
                <a href="?svet=OGENJ" class="nav-povezava">
                    <span class="nav-ikona">🔥</span>
                    <span class="nav-besedilo">Ogenj</span>
                </a>
            </li>
            
            <li class="nav-element <?= $aktivnaPot === 'MODULI' ? 'aktivno' : '' ?>">
                <a href="?svet=MODULI" class="nav-povezava">
                    <span class="nav-ikona">📦</span>
                    <span class="nav-besedilo">Moduli</span>
                </a>
            </li>
            
            <li class="nav-element <?= $aktivnaPot === 'ASTRA' ? 'aktivno' : '' ?>">
                <a href="?svet=ASTRA" class="nav-povezava">
                    <span class="nav-ikona">🧪</span>
                    <span class="nav-besedilo">Astra</span>
                </a>
            </li>
            
            <?php if ($jePrijavljen): ?>
                <li class="nav-element <?= $aktivnaPot === 'UPORABNIKI' ? 'aktivno' : '' ?>">
                    <a href="?svet=UPORABNIKI&pot=profil" class="nav-povezava">
                        <span class="nav-ikona">👤</span>
                        <span class="nav-besedilo"><?= htmlspecialchars($uporabnik['ime'] ?? 'Profil') ?></span>
                    </a>
                </li>
                <li class="nav-element <?= (isset($_GET['pot']) && $_GET['pot'] === 'nastavitve') ? 'aktivno' : '' ?>">
                    <a href="?svet=UPORABNIKI&pot=nastavitve" class="nav-povezava">
                        <span class="nav-ikona">⚙️</span>
                        <span class="nav-besedilo">Nastavitve</span>
                    </a>
                </li>
                <li class="nav-element">
                    <a href="?svet=UPORABNIKI&pot=odjava" class="nav-povezava nav-odjava">
                        <span class="nav-ikona">🚪</span>
                        <span class="nav-besedilo">Odjava</span>
                    </a>
                </li>
            <?php else: ?>
                <li class="nav-element">
                    <a href="?svet=UPORABNIKI&amp;pot=prijava" class="nav-povezava">
                        <span class="nav-ikona">🔑</span>
                        <span class="nav-besedilo">Prijava</span>
                    </a>
                </li>
                <li class="nav-element">
                    <a href="?svet=UPORABNIKI&amp;pot=registracija" class="nav-povezava">
                        <span class="nav-ikona">📝</span>
                        <span class="nav-besedilo">Registracija</span>
                    </a>
                </li>
            <?php endif; ?>
            
            <li class="nav-element nav-tema">
                <button class="nav-povezava tema-preklop" id="temaPreklop">
                    <span class="nav-ikona">🌓</span>
                    <span class="nav-besedilo">Tema</span>
                </button>
            </li>
        </ul>
    </div>
</nav>

<style>
.navigacija {
    background: rgba(10, 10, 26, 0.95);
    border-bottom: 1px solid #2a2a4a;
    position: sticky;
    top: 0;
    z-index: 100;
    backdrop-filter: blur(10px);
}

.navigacija-vsebina {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0.75rem 2rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.nav-logotip a {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 1.2rem;
    font-weight: bold;
    color: #e8c84a;
    text-decoration: none;
}

.nav-logotip-ikona {
    font-size: 1.5rem;
}

.nav-seznam {
    list-style: none;
    display: flex;
    gap: 0.5rem;
    margin: 0;
    padding: 0;
}

.nav-element {
    position: relative;
}

.nav-povezava {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    border-radius: 25px;
    color: #d4c5a9;
    text-decoration: none;
    transition: all 0.3s;
    background: none;
    border: none;
    cursor: pointer;
    font-size: 1rem;
}

.nav-povezava:hover {
    background: rgba(232, 200, 74, 0.1);
    color: #e8c84a;
    text-decoration: none;
}

.nav-element.aktivno .nav-povezava {
    background: rgba(232, 200, 74, 0.2);
    color: #e8c84a;
}

.nav-ikona {
    font-size: 1.1rem;
}

.nav-meni-gumb {
    display: none;
    background: none;
    border: none;
    color: #d4c5a9;
    font-size: 1.5rem;
    cursor: pointer;
}

@media (max-width: 768px) {
    .nav-meni-gumb {
        display: block;
    }
    
    .nav-seznam {
        display: none;
        flex-direction: column;
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: #0a0a1a;
        padding: 1rem;
        border-bottom: 1px solid #2a2a4a;
    }
    
    .nav-seznam.odprt {
        display: flex;
    }
    
    .nav-povezava {
        justify-content: center;
    }
}
</style>

<script nonce="<?= $cspNonce ?? '' ?>">
(function() {
    const meniGumb = document.getElementById('navMeniGumb');
    const navSeznam = document.getElementById('navSeznam');
    
    if (meniGumb && navSeznam) {
        meniGumb.addEventListener('click', function() {
            navSeznam.classList.toggle('odprt');
        });
    }
    
    // Aktivna navigacija – označi trenutno pot
    const trenutnaPot = window.location.search;
    document.querySelectorAll('.nav-povezava').forEach(povezava => {
        const href = povezava.getAttribute('href');
        if (href && trenutnaPot.includes(href.replace('?', ''))) {
            povezava.closest('.nav-element')?.classList.add('aktivno');
        }
    });
})();
</script>