<?php require_once __DIR__ . "/modul_sufica.php"; $mod = new ModulSufica(); $v = $mod->pridobiVsebino(); ?>
<!DOCTYPE html><html lang="sl"><head><meta charset="UTF-8"><title>Sufica</title></head><body><h1>Sufica</h1><div><?php echo $v["vsebina"]; ?></div></body></html>
