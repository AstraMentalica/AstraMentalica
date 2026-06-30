<?php require_once __DIR__ . "/modul_shinto.php"; $mod = new ModulShinto(); $v = $mod->pridobiVsebino(); ?>
<!DOCTYPE html><html lang="sl"><head><meta charset="UTF-8"><title>Shinto</title></head><body><h1>Shinto</h1><div><?php echo $v["vsebina"]; ?></div></body></html>
