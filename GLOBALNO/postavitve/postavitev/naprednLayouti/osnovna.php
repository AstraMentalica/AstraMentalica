<?php
/**
 * ---------------------------------------------------------
 * POT: GLOBALNO/render/postavitev/osnovna.php
 * v111 (27.5.2026 15:30)
 * ---------------------------------------------------------
 * OPIS: Osnovna postavitev (layout) – glava, vsebina, noga
 * ---------------------------------------------------------
 *
 * ODVISNOSTI:
 * - GLOBALNO/render/osnova/glava.php
 * - GLOBALNO/render/osnova/noga.php
 * - GLOBALNO/render/osnova/navigacija.php
 *
 * UPORABA:
 * - GLOBALNO/render/strani/*.php (posredno)
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

<main class="glavna">
    <div class="glavna-vsebina">
        <?php if (isset($vsebina['vsebina'])): ?>
            <?= $vsebina['vsebina'] ?>
        <?php elseif (isset($telo)): ?>
            <?= $telo ?>
        <?php else: ?>
            <p>Vsebina ni na voljo.</p>
        <?php endif; ?>
    </div>
</main>

<?php
// Prikazi nogo
include GLOBALNO . '/render/osnova/noga.php';
?>

<style>
.glavna {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem;
    min-height: calc(100vh - 200px);
}

.glavna-vsebina {
    width: 100%;
}

@media (max-width: 768px) {
    .glavna {
        padding: 1rem;
    }
}
</style>

<?php
// CSP glave
csp_nastavi_glave($vsebina['csp_nonce'] ?? '');
?>