<?php //GLOBALNO/gradniki/kartica.php
if (!defined('SISTEM_VARNOST')) die(); 
$naslov = $naslov ?? '';
$vsebina = $vsebina ?? '';
$slika = $slika ?? '';
$povezava = $povezava ?? '';
$tip = $tip ?? 'normal';
$class = 'kartica';
if ($tip !== 'normal') $class .= ' kartica-' . $tip;
?>
<div class="<?= $class ?>">
    <?php if ($slika): ?><div class="kartica-slika"><img src="<?= sanitiziraj($slika) ?>" alt=""></div><?php endif; ?>
    <div class="kartica-vsebina">
        <?php if ($naslov): ?>
            <h3 class="kartica-naslov"><?= $povezava ? '<a href="' . sanitiziraj($povezava) . '">' . sanitiziraj($naslov) . '</a>' : sanitiziraj($naslov) ?></h3>
        <?php endif; ?>
        <div class="kartica-besedilo"><?= $vsebina ?></div>
    </div>
</div>