<?php require_once __DIR__ . "/modul_hanbang.php"; $mod = new ModulHanbang(); $v = $mod->pridobiVsebino(); ?>
<!DOCTYPE html><html lang="sl"><head><meta charset="UTF-8"><title>Hanbang</title></head><body><h1>Hanbang</h1><div><?php echo $v["vsebina"]; ?></div></body></html>
