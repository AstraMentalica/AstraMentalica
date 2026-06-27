<?php //GLOBALNO/gradniki/napredek.php
if (!defined('SISTEM_VARNOST')) die();
$trenutni = $trenutni ?? 0;
$skupaj = $skupaj ?? 100;
$prikaz = $prikaz ?? 'odstotek';
$barva = $barva ?? 'primarna';
$visina = $visina ?? 'srednji';
$odstotek = $skupaj > 0 ? round(($trenutni / $skupaj) * 100) : 0;
$odstotek = min(100, max(0, $odstotek));
?>
<div class="napredek napredek-<?= $visina ?> napredek-<?= $barva ?>">
    <?php if ($prikaz !== 'skrito'): ?>
        <div class="napredek-besedilo">
            <?php if ($prikaz === 'odstotek'): ?><span><?= $odstotek ?>%</span>
            <?php elseif ($prikaz === 'vrednost'): ?><span><?= $trenutni ?> / <?= $skupaj ?></span><?php endif; ?>
        </div>
    <?php endif; ?>
    <div class="napredek-vrstica"><div class="napredek-napolnjen" style="width: <?= $odstotek ?>%"></div></div>
</div>