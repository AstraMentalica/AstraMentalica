<?php require_once __DIR__ . "/modul_reiki.php"; $mod = new ModulReiki(); $v = $mod->pridobiVsebino(); ?>
<!DOCTYPE html><html lang="sl"><head><meta charset="UTF-8"><title>Reiki</title></head><body><h1>Reiki</h1><div><?php echo $v["vsebina"]; ?></div></body></html>
