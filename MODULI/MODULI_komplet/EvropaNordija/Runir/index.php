<?php require_once __DIR__ . "/modul_runir.php"; $mod = new ModulRunir(); $v = $mod->pridobiVsebino(); ?>
<!DOCTYPE html><html lang="sl"><head><meta charset="UTF-8"><title>Runir</title></head><body><h1>Runir</h1><div><?php echo $v["vsebina"]; ?></div></body></html>
