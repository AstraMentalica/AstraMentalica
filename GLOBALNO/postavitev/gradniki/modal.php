<?php
/**
 * ---------------------------------------------------------
 * POT: GLOBALNO/postavitev/gradniki/modal.php
 * v111 (27.5.2026 15:00)
 * ---------------------------------------------------------
 * OPIS: Modalno okno element – pasivni PHP fragment
 * ---------------------------------------------------------
 *
 * ODVISNOSTI:
 * - nobenih
 *
 * UPORABA:
 * - GLOBALNO/render/strani/*.php
 *
 * PARAMETRI:
 * - $id (string) – enolični identifikator modala
 * - $naslov (string) – naslov modala
 * - $vsebina (string) – vsebina modala
 * - $gumbi (array) – seznam gumbov v nogi
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

// Parametri
$id = $id ?? 'modal_' . uniqid();
$naslov = $naslov ?? 'Okno';
$vsebina = $vsebina ?? '';
$gumbi = $gumbi ?? [];
$razred = $razred ?? '';
?>

<div class="modal-ozadje <?= htmlspecialchars($razred) ?>" id="<?= htmlspecialchars($id) ?>" style="display: none;">
    <div class="modal-vsebina">
        <div class="modal-glava">
            <h3 class="modal-naslov"><?= htmlspecialchars($naslov) ?></h3>
            <button class="modal-zapri" data-modal="<?= htmlspecialchars($id) ?>">&times;</button>
        </div>
        <div class="modal-telo">
            <?= $vsebina ?>
        </div>
        <?php if (!empty($gumbi)): ?>
        <div class="modal-noga">
            <?php foreach ($gumbi as $gumb): 
                $gumbVrsta = $gumb['vrsta'] ?? 'sekundaren';
                $gumbBesedilo = $gumb['besedilo'] ?? 'Gumb';
                $gumbAkcija = $gumb['akcija'] ?? '';
            ?>
                <button class="gumb gumb-<?= $gumbVrsta ?>" data-modal-akcija="<?= htmlspecialchars($gumbAkcija) ?>" data-modal="<?= htmlspecialchars($id) ?>">
                    <?= htmlspecialchars($gumbBesedilo) ?>
                </button>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<style>
.modal-ozadje {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.8);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1000;
}

.modal-vsebina {
    background: #1a1a2e;
    border-radius: 20px;
    max-width: 500px;
    width: 90%;
    max-height: 80vh;
    overflow: auto;
    animation: modal-odpri 0.3s ease;
}

@keyframes modal-odpri {
    from {
        opacity: 0;
        transform: scale(0.9);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

.modal-glava {
    padding: 1rem 1.5rem;
    border-bottom: 1px solid #2a2a4a;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-naslov {
    margin: 0;
    color: #e8c84a;
}

.modal-zapri {
    background: none;
    border: none;
    font-size: 1.5rem;
    cursor: pointer;
    color: #aaa;
    transition: color 0.3s;
}

.modal-zapri:hover {
    color: #fff;
}

.modal-telo {
    padding: 1.5rem;
    color: #d4c5a9;
}

.modal-noga {
    padding: 1rem 1.5rem;
    border-top: 1px solid #2a2a4a;
    display: flex;
    justify-content: flex-end;
    gap: 0.75rem;
}
</style>

<script>
(function() {
    // Odpri modal
    function odpriModal(id) {
        const modal = document.getElementById(id);
        if (modal) {
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }
    }
    
    // Zapri modal
    function zapriModal(id) {
        const modal = document.getElementById(id);
        if (modal) {
            modal.style.display = 'none';
            document.body.style.overflow = '';
        }
    }
    
    // Registriraj odpiranje preko gumbov z data-modal-odpri
    document.querySelectorAll('[data-modal-odpri]').forEach(gumb => {
        const modalId = gumb.getAttribute('data-modal-odpri');
        gumb.addEventListener('click', () => odpriModal(modalId));
    });
    
    // Zapiranje preko gumbov z data-modal-zapri ali .modal-zapri
    document.addEventListener('click', (e) => {
        // Zapri gumb
        if (e.target.classList.contains('modal-zapri') || e.target.getAttribute('data-modal-zapri')) {
            const modal = e.target.closest('.modal-ozadje');
            if (modal) zapriModal(modal.id);
        }
        
        // Klik izven modala
        if (e.target.classList.contains('modal-ozadje')) {
            zapriModal(e.target.id);
        }
        
        // Gumb z akcijo
        if (e.target.getAttribute('data-modal-akcija')) {
            const modalId = e.target.getAttribute('data-modal');
            const akcija = e.target.getAttribute('data-modal-akcija');
            if (akcija === 'zapri' && modalId) {
                zapriModal(modalId);
            }
        }
    });
})();
</script>