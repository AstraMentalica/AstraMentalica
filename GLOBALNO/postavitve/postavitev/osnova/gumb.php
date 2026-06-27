<?php GLOBALNO/gradniki/gumb.php
if (!defined('SISTEM_VARNOST')) die();
$besedilo = $besedilo ?? 'Gumb';
$url = $url ?? '#';
$tip = $tip ?? 'primarni';
$velikost = $velikost ?? 'srednji';
$obrazec = $obrazec ?? false;
$onemogocen = $onemogocen ?? false;
$class = "gumb gumb-$tip gumb-$velikost";
if ($onemogocen) $class .= ' onemogocen';
?>
<?php if ($obrazec): ?>
    <button type="submit" class="<?= $class ?>" <?= $onemogocen ? 'disabled' : '' ?>><?= sanitiziraj($besedilo) ?></button>
<?php else: ?>
    <a href="<?= sanitiziraj($url) ?>" class="<?= $class ?>" <?= $onemogocen ? 'disabled' : '' ?>><?= sanitiziraj($besedilo) ?></a>
<?php endif; ?>