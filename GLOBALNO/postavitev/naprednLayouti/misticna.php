<?php
/**
 * ---------------------------------------------------------
 * POT: GLOBALNO/postavitev/naprednLayouti/misticna.php
 * v111 (27.5.2026 15:30)
 * ---------------------------------------------------------
 * OPIS: Misticna postavitev – z zvezdastim ozadjem in posebnimi efekti
 * ---------------------------------------------------------
 *
 * ODVISNOSTI:
 * - GLOBALNO/render/osnova/glava.php
 * - GLOBALNO/render/osnova/noga.php
 *
 * UPORABA:
 * - Za misticne module (tarot, astrologija)
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

if (!isset($vsebina)) {
    $vsebina = [];
}

$vsebina['csp_nonce'] = csp_generiraj_nonce();

// Prikazi glavo
include GLOBALNO . '/render/osnova/glava.php';
?>

<main class="glavna-misticna">
    <div class="zvezde-ozadje"></div>
    <div class="misticna-vsebina">
        <div class="misticna-okvir">
            <?php if (isset($vsebina['vsebina'])): ?>
                <?= $vsebina['vsebina'] ?>
            <?php else: ?>
                <div class="misticna-sredisce">
                    <div class="kristal"></div>
                    <h1 class="misticno-sporocilo">Dobrodošli v misticnem svetu</h1>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php
// Prikazi nogo
include GLOBALNO . '/render/osnova/noga.php';
?>

<style>
.glavna-misticna {
    min-height: calc(100vh - 200px);
    position: relative;
    overflow: hidden;
}

.zvezde-ozadje {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: radial-gradient(ellipse at center, #0a0a2a 0%, #000 100%);
    z-index: -2;
}

.zvezde-ozadje::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-image: radial-gradient(2px 2px at 20px 30px, #fff, rgba(0,0,0,0)),
                      radial-gradient(1px 1px at 40px 70px, #e8c84a, rgba(0,0,0,0));
    background-size: 200px 200px, 100px 100px;
    background-repeat: repeat;
    opacity: 0.3;
    animation: zvezde-tresenje 4s infinite;
}

@keyframes zvezde-tresenje {
    0%, 100% { opacity: 0.3; transform: translateX(0); }
    50% { opacity: 0.5; transform: translateX(2px); }
}

.misticna-vsebina {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: calc(100vh - 200px);
    padding: 2rem;
}

.misticna-okvir {
    max-width: 800px;
    width: 100%;
    background: rgba(0, 0, 0, 0.4);
    backdrop-filter: blur(10px);
    border-radius: 30px;
    padding: 2rem;
    border: 1px solid rgba(232, 200, 74, 0.2);
    box-shadow: 0 0 50px rgba(232, 200, 74, 0.1);
}

.misticna-sredisce {
    text-align: center;
}

.kristal {
    width: 100px;
    height: 100px;
    margin: 0 auto 2rem;
    background: linear-gradient(135deg, #e8c84a, #a88a2a);
    clip-path: polygon(50% 0%, 100% 25%, 100% 75%, 50% 100%, 0% 75%, 0% 25%);
    animation: kristal-sijaj 3s infinite;
}

@keyframes kristal-sijaj {
    0%, 100% { opacity: 0.7; transform: scale(1); }
    50% { opacity: 1; transform: scale(1.05); }
}

.misticno-sporocilo {
    color: #e8c84a;
    font-size: 2rem;
    text-align: center;
    text-shadow: 0 0 20px rgba(232, 200, 74, 0.5);
}
</style>

<?php
csp_nastavi_glave($vsebina['csp_nonce'] ?? '');
?>
✅ GLOBALNO/render/postavitev/ ZAKLJUČEN (5/5)
Datoteka	Status
osnovna.php	✅ NAPISANO
razvojna.php	✅ NAPISANO
interaktivna.php	✅ NAPISANO
premium.php	✅ NAPISANO
misticna.php	✅ NAPISANO