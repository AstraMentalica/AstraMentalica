<?php
/**
 * ---------------------------------------------------------
 * POT: GLOBALNO/render/postavitev/premium.php
 * v111 (27.5.2026 15:30)
 * ---------------------------------------------------------
 * OPIS: Premium postavitev – za premium uporabnike
 * ---------------------------------------------------------
 *
 * ODVISNOSTI:
 * - GLOBALNO/render/osnova/glava.php
 * - GLOBALNO/render/osnova/noga.php
 * - GLOBALNO/render/osnova/navigacija.php
 *
 * UPORABA:
 * - Za uporabnike z vlogo S4+
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

// Prikazi navigacijo
include GLOBALNO . '/render/osnova/navigacija.php';
?>

<main class="glavna-premium">
    <div class="premium-ozadje"></div>
    <div class="premium-vsebina">
        <div class="premium-stranski-pas">
            <?php if (isset($vsebina['stranski_pas'])): ?>
                <?= $vsebina['stranski_pas'] ?>
            <?php else: ?>
                <div class="premium-widget">
                    <h3>Premium vsebina</h3>
                    <p>Dostop do ekskluzivnih vsebin.</p>
                </div>
                <div class="premium-widget">
                    <h3>Statistika</h3>
                    <p>Aktivnih modulov: <?= $vsebina['stevilo_modulov'] ?? '0' ?></p>
                </div>
            <?php endif; ?>
        </div>
        <div class="premium-glavni-del">
            <?php if (isset($vsebina['vsebina'])): ?>
                <?= $vsebina['vsebina'] ?>
            <?php else: ?>
                <div class="premium-kartica">
                    <h2>Premium obmocje</h2>
                    <p>Dobrodošli v premium delu sistema.</p>
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
.glavna-premium {
    max-width: 1400px;
    margin: 0 auto;
    padding: 2rem;
    position: relative;
}

.premium-ozadje {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: radial-gradient(ellipse at top, #1a1a3e, #0a0a1a);
    z-index: -1;
}

.premium-vsebina {
    display: flex;
    gap: 2rem;
}

.premium-stranski-pas {
    width: 280px;
    flex-shrink: 0;
}

.premium-glavni-del {
    flex: 1;
}

.premium-widget {
    background: rgba(255, 255, 255, 0.05);
    border-radius: 15px;
    padding: 1.25rem;
    margin-bottom: 1.5rem;
    border-left: 3px solid #e8c84a;
}

.premium-widget h3 {
    color: #e8c84a;
    margin-bottom: 0.75rem;
}

.premium-kartica {
    background: rgba(255, 255, 255, 0.03);
    border-radius: 20px;
    padding: 2rem;
}

@media (max-width: 768px) {
    .premium-vsebina {
        flex-direction: column;
    }
    
    .premium-stranski-pas {
        width: 100%;
    }
}
</style>

<?php
csp_nastavi_glave($vsebina['csp_nonce'] ?? '');
?>