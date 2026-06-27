<?php
/**
 * ---------------------------------------------------------
 * POT: GLOBALNO/render/postavitev/razvojna.php
 * v111 (27.5.2026 15:30)
 * ---------------------------------------------------------
 * OPIS: Razvojna postavitev – s prikazom debug podatkov
 * ---------------------------------------------------------
 *
 * ODVISNOSTI:
 * - GLOBALNO/render/osnova/glava.php
 * - GLOBALNO/render/osnova/noga.php
 *
 * UPORABA:
 * - Samo v razvojnem nacinu
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
        <?php else: ?>
            <p>Vsebina ni na voljo.</p>
        <?php endif; ?>
    </div>
    
    <?php if (RAZVOJNI_NACIN): ?>
    <div class="razvojna-debug">
        <h3>Debug informacije</h3>
        <details>
            <summary>Vsebina</summary>
            <pre><?php print_r($vsebina); ?></pre>
        </details>
        <details>
            <summary>Seja</summary>
            <pre><?php print_r($_SESSION ?? []); ?></pre>
        </details>
        <details>
            <summary>Zahteva</summary>
            <pre><?php print_r($_SERVER); ?></pre>
        </details>
    </div>
    <?php endif; ?>
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

.razvojna-debug {
    margin-top: 3rem;
    padding: 1rem;
    background: rgba(0, 0, 0, 0.5);
    border-radius: 10px;
    font-family: monospace;
    font-size: 0.8rem;
}

.razvojna-debug h3 {
    color: #e8c84a;
    margin-bottom: 1rem;
}

.razvojna-debug details {
    margin-bottom: 0.5rem;
}

.razvojna-debug summary {
    cursor: pointer;
    color: #aaa;
}

.razvojna-debug pre {
    background: #0a0a1a;
    padding: 0.5rem;
    border-radius: 5px;
    overflow-x: auto;
}
</style>

<?php
csp_nastavi_glave($vsebina['csp_nonce'] ?? '');
?>