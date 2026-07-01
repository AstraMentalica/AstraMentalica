<?php
/**
 * ---------------------------------------------------------
 * POT: GLOBALNO/postavitev/strani/sistem/napake/404.php
 * v111 (27.5.2026 07:45)
 * ---------------------------------------------------------
 * OPIS: Stran za napako 404 (ni najdeno)
 * ---------------------------------------------------------
 *
 * ODVISNOSTI:
 * - pot.php (posredno)
 *
 * UPORABA:
 * - Ko pot ni najdena
 *
 * PREPOVEDI:
 * - Brez business logike
 *
 * STATUS: Stabilno
 *
 * ZGODOVINA:
 * - v111: FAZA 9 – implementacija
 *
 * ---------------------------------------------------------
 * AVTOR: AstraMentalica Mojster
 * ---------------------------------------------------------
 */

declare(strict_types=1);

$sporocilo = $vsebina['sporocilo'] ?? 'Stran ne obstaja';
$pot = $vsebina['pot'] ?? '';
?>

<div class="napaka-stran">
    <div class="napaka-vsebina">
        <div class="napaka-stevilka">404</div>
        <h1 class="napaka-naslov">Stran ni najdena</h1>
        <p class="napaka-sporocilo"><?= htmlspecialchars($sporocilo) ?></p>
        <?php if ($pot): ?>
            <p class="napaka-pot">Pot: <?= htmlspecialchars($pot) ?></p>
        <?php endif; ?>
        <div class="napaka-gumbi">
            <a href="?svet=" class="gumb gumb-primaren">← Nazaj na domov</a>
        </div>
    </div>
</div>

<style>
.napaka-stran {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 60vh;
    text-align: center;
}

.napaka-stevilka {
    font-size: 8rem;
    font-weight: bold;
    color: #e8c84a;
    margin-bottom: 1rem;
}

.napaka-naslov {
    font-size: 2rem;
    margin-bottom: 1rem;
}

.napaka-sporocilo {
    color: #aaa;
    margin-bottom: 0.5rem;
}

.napaka-pot {
    font-family: monospace;
    color: #666;
    margin-bottom: 2rem;
}

.napaka-gumbi {
    margin-top: 2rem;
}
</style>