<?php require_once __DIR__ . "/modul_pranayama.php"; $mod = new ModulPranayama(); $v = $mod->pridobiVsebino(); ?>
<!DOCTYPE html><html lang="sl"><head><meta charset="UTF-8"><title>Pranayama</title></head><body><h1>Pranayama</h1><div><?php echo $v["vsebina"]; ?></div></body></html>
