<?php require_once __DIR__ . "/modul_wuxing.php"; $mod = new ModulWuXing(); $v = $mod->pridobiVsebino(); ?>
<!DOCTYPE html><html lang="sl"><head><meta charset="UTF-8"><title>WuXing</title></head><body><h1>WuXing</h1><div><?php echo $v["vsebina"]; ?></div></body></html>
