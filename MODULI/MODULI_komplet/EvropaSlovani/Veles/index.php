<?php require_once __DIR__ . "/modul_veles.php"; $mod = new ModulVeles(); $v = $mod->pridobiVsebino(); ?>
<!DOCTYPE html><html lang="sl"><head><meta charset="UTF-8"><title>Veles</title></head><body><h1>Veles</h1><div><?php echo $v["vsebina"]; ?></div></body></html>
