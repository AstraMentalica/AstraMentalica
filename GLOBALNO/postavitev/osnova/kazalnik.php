<?php //GLOBALNO/gradniki/kazalnik.php
if (!defined('SISTEM_VARNOST')) die();
$koraki = $koraki ?? 1;
$trenutni = $trenutni ?? 1;
$opravljeni = $opravljeni ?? [];
$prikaziStevilke = $prikaziStevilke ?? true;
$trenutni = max(1, min($koraki, $trenutni));
?>
<div class="kazalnik">
    <?php for ($i = 1; $i <= $koraki; $i++): 
        $class = 'kazalnik-korak';
        if (in_array($i, $opravljeni)) $class .= ' opravljen';
        elseif ($i == $trenutni) $class .= ' trenutni';
    ?>
        <div class="<?= $class ?>">
            <?php if ($prikaziStevilke): ?><span class="stevilka"><?= $i ?></span><?php endif; ?>
            <?php if (in_array($i, $opravljeni)): ?><span class="kljukica">✓</span><?php endif; ?>
        </div>
    <?php endfor; ?>
</div>