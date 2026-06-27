<?php //GLOBALNO/gradniki/slika.php
if (!defined('SISTEM_VARNOST')) die();
$src = $src ?? '';
$alt = $alt ?? '';
$napis = $napis ?? '';
$oblika = $oblika ?? 'zaobljena';
$velikost = $velikost ?? 'srednja';
?>
<div class="slika-gradnik slika-<?= $oblika ?> slika-<?= $velikost ?>">
    <img src="<?= sanitiziraj($src) ?>" alt="<?= sanitiziraj($alt) ?>">
    <?php if ($napis): ?><div class="slika-napis"><?= sanitiziraj($napis) ?></div><?php endif; ?>
</div>