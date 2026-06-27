<?php
/**
 * ---------------------------------------------------------
 * POT: GLOBALNO/render/osnova/noga.php
 * v111 (27.5.2026 14:30)
 * ---------------------------------------------------------
 * OPIS: HTML noga – skupni footer za vse strani
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
?>

<footer class="noga">
    <div class="noga-vsebina">
        <div class="noga-levo">
            <p>&copy; <?= date('Y') ?> <?= IME_APLIKACIJE ?> – vse pravice pridržane</p>
            <p class="noga-verzija">Verzija: <?= SISTEM_VERZIJA ?></p>
        </div>
        <div class="noga-sredina">
            <nav class="noga-navigacija">
                <a href="?svet=GLOBALNO&amp;pot=domov">Domov</a>
                <a href="?svet=GLOBALNO&amp;pot=o_nas">O nas</a>
                <a href="?svet=GLOBALNO&amp;pot=pogoji">Pogoji uporabe</a>
                <a href="?svet=GLOBALNO&amp;pot=zasebnost">Zasebnost</a>
                <a href="?svet=GLOBALNO&amp;pot=kontakt">Kontakt</a>
            </nav>
        </div>
        <div class="noga-desno">
            <div class="noga-social">
                <a href="https://facebook.com/astramentalica" target="_blank" rel="noopener">📘</a>
                <a href="https://instagram.com/astramentalica" target="_blank" rel="noopener">📷</a>
                <a href="https://twitter.com/astramentalica" target="_blank" rel="noopener">🐦</a>
            </div>
        </div>
    </div>
</footer>

<style>
.noga {
    background: linear-gradient(135deg, #0a0a1a 0%, #1a1a2e 100%);
    border-top: 1px solid #2a2a4a;
    padding: 2rem;
    margin-top: 3rem;
}

.noga-vsebina {
    max-width: 1200px;
    margin: 0 auto;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
}

.noga p {
    margin: 0;
    color: #888;
    font-size: 0.85rem;
}

.noga-verzija {
    font-size: 0.75rem;
    color: #555;
    margin-top: 0.25rem;
}

.noga-navigacija {
    display: flex;
    gap: 1.5rem;
    flex-wrap: wrap;
}

.noga-navigacija a {
    color: #aaa;
    text-decoration: none;
    font-size: 0.85rem;
    transition: color 0.3s;
}

.noga-navigacija a:hover {
    color: #e8c84a;
    text-decoration: none;
}

.noga-social {
    display: flex;
    gap: 1rem;
}

.noga-social a {
    color: #aaa;
    text-decoration: none;
    font-size: 1.2rem;
    transition: color 0.3s;
}

.noga-social a:hover {
    color: #e8c84a;
}

@media (max-width: 768px) {
    .noga-vsebina {
        flex-direction: column;
        text-align: center;
    }
}
</style>

<script src="<?= GLOBALNO ?>/frontend/runtime/jedro/sistem.js" defer></script>
<script src="<?= GLOBALNO ?>/frontend/runtime/stanje/tema.js" defer></script>
</body>
</html>