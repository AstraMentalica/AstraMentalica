<?php require_once __DIR__ . "/modul_perun.php"; $mod = new ModulPerun(); $v = $mod->pridobiVsebino(); ?>
<!DOCTYPE html><html lang="sl"><head><meta charset="UTF-8"><title>Perun</title></head><body><h1>Perun</h1><div><?php echo $v["vsebina"]; ?></div></body></html>
