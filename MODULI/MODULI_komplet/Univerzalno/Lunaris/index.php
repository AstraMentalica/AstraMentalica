<?php require_once __DIR__ . "/modul_lunaris.php"; $mod = new ModulLunaris(); $v = $mod->pridobiVsebino(); ?>
<!DOCTYPE html><html lang="sl"><head><meta charset="UTF-8"><title>Lunaris</title></head><body><h1>Lunaris</h1><div><?php echo $v["vsebina"]; ?></div></body></html>
