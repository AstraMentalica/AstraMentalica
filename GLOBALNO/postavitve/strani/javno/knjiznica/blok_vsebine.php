<?php //GLOBALNO/gradniki/blok_vsebine.php
if (!defined('SISTEM_VARNOST')) die();
$naslov = $naslov ?? '';
$vsebina = $vsebina ?? '';
$tip = $tip ?? 'normal';
$ikona = $ikona ?? '';
$class = 'blok-vsebine';
if ($tip !== 'normal') $class .= ' blok-' . $tip;
?>
<div class="<?= $class ?>">
    <?php if ($naslov): ?>
        <div class="blok-naslov"><?= $ikona ? '<span class="ikona">' . sanitiziraj($ikona) . '</span>' : '' ?><h3><?= sanitiziraj($naslov) ?></h3></div>
    <?php endif; ?>
    <div class="blok-vsebina"><?= $vsebina ?></div>
</div>