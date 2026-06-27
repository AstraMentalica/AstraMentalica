<?php
/**
 * ---------------------------------------------------------
 * POT: GLOBALNO/render/postavitev/interaktivna.php
 * v111 (27.5.2026 15:30)
 * ---------------------------------------------------------
 * OPIS: Interaktivna postavitev – z iframe za module
 * ---------------------------------------------------------
 *
 * ODVISNOSTI:
 * - GLOBALNO/render/osnova/glava.php
 * - GLOBALNO/render/osnova/noga.php
 *
 * UPORABA:
 * - Ko modul zahteva iframe
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

// Prikazi glavo (z iframe prijaznimi nastavitvami)
include GLOBALNO . '/render/osnova/glava.php';
?>

<main class="glavna-interaktivna">
    <div class="interaktivna-vsebina">
        <?php if (isset($vsebina['iframe_url'])): ?>
            <iframe 
                src="<?= htmlspecialchars($vsebina['iframe_url']) ?>" 
                class="interaktivna-iframe"
                sandbox="allow-same-origin allow-scripts allow-popups allow-forms allow-modals"
                allow="fullscreen"
                loading="lazy">
                Vaš brskalnik ne podpira iframe elementov.
            </iframe>
        <?php elseif (isset($vsebina['vsebina'])): ?>
            <?= $vsebina['vsebina'] ?>
        <?php else: ?>
            <p>Interaktivna vsebina ni na voljo.</p>
        <?php endif; ?>
    </div>
</main>

<?php
// Prikazi nogo
include GLOBALNO . '/render/osnova/noga.php';
?>

<style>
.glavna-interaktivna {
    position: fixed;
    top: 70px;
    left: 0;
    right: 0;
    bottom: 0;
    padding: 0;
    margin: 0;
}

.interaktivna-vsebina {
    width: 100%;
    height: 100%;
}

.interaktivna-iframe {
    width: 100%;
    height: 100%;
    border: none;
    background: #0a0a1a;
}
</style>

<?php
csp_nastavi_glave($vsebina['csp_nonce'] ?? '');
?>