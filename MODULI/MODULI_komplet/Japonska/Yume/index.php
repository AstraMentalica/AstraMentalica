<?php require_once __DIR__ . "/modul_yume.php"; $mod = new ModulYume(); $v = $mod->pridobiVsebino(); ?>
<!DOCTYPE html><html lang="sl"><head><meta charset="UTF-8"><title>Yume</title></head><body><h1>Yume</h1><div><?php echo $v["vsebina"]; ?></div></body></html>
