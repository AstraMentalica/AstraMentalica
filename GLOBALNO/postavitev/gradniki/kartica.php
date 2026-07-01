<?php
/**
 * ---------------------------------------------------------
 * POT: GLOBALNO/postavitev/gradniki/kartica.php
 * v111 (27.5.2026 15:00)
 * ---------------------------------------------------------
 * OPIS: Kartica element – pasivni PHP fragment
 * ---------------------------------------------------------
 *
 * ODVISNOSTI:
 * - nobenih
 *
 * UPORABA:
 * - GLOBALNO/render/strani/*.php
 *
 * PARAMETRI:
 * - $naslov (string) – naslov kartice
 * - $vsebina (string|array) – vsebina kartice
 * - $ikona (string) – emoji ikona
 * - $barva (string) – robna barva (css barva)
 * - $noga (string) – vsebina noge
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
$naslov = $naslov ?? '';
$vsebina = $vsebina ?? '';
$ikona = $ikona ?? '';
$barva = $barva ?? '';
$noga = $noga ?? '';
$razred = $razred ?? '';

$slog = $barva ? 'border-top-color: ' . htmlspecialchars($barva) . ';' : '';
?>

<div class="kartica <?= htmlspecialchars($razred) ?>" style="<?= $slog ?>">
    <?php if ($ikona || $naslov): ?>
    <div class="kartica-glava">
        <?php if ($ikona): ?>
        <div class="kartica-ikona"><?= htmlspecialchars($ikona) ?></div>
        <?php endif; ?>
        <?php if ($naslov): ?>
        <h3 class="kartica-naslov"><?= htmlspecialchars($naslov) ?></h3>
        <?php endif; ?>
    </div>
    <?php endif; ?>
    
    <div class="kartica-telo">
        <?php if (is_array($vsebina)): ?>
            <?php foreach ($vsebina as $kljuc => $vrednost): ?>
                <div class="kartica-vrstica">
                    <span class="kartica-kljuc"><?= htmlspecialchars($kljuc) ?>:</span>
                    <span class="kartica-vrednost"><?= htmlspecialchars($vrednost) ?></span>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <?= $vsebina ?>
        <?php endif; ?>
    </div>
    
    <?php if ($noga): ?>
    <div class="kartica-noga">
        <?= $noga ?>
    </div>
    <?php endif; ?>
</div>

<style>
.kartica {
    background: rgba(255, 255, 255, 0.05);
    border-radius: 15px;
    border-top: 3px solid #e8c84a;
    overflow: hidden;
    transition: transform 0.3s, box-shadow 0.3s;
}

.kartica:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
}

.kartica-glava {
    padding: 1.25rem 1.5rem 0.5rem 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.kartica-ikona {
    font-size: 2rem;
}

.kartica-naslov {
    margin: 0;
    font-size: 1.25rem;
    color: #e8c84a;
}

.kartica-telo {
    padding: 0.5rem 1.5rem 1.25rem 1.5rem;
    color: #d4c5a9;
}

.kartica-vrstica {
    display: flex;
    justify-content: space-between;
    padding: 0.25rem 0;
    border-bottom: 1px solid rgba(255, 255, 255, 0.05);
}

.kartica-kljuc {
    font-weight: 500;
    color: #aaa;
}

.kartica-vrednost {
    color: #e8c84a;
}

.kartica-noga {
    padding: 0.75rem 1.5rem 1.25rem 1.5rem;
    border-top: 1px solid rgba(255, 255, 255, 0.05);
    background: rgba(0, 0, 0, 0.2);
}
</style>