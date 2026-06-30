<?php require_once __DIR__ . "/modul_neteru.php"; $mod = new ModulNeteru(); $v = $mod->pridobiVsebino(); ?>
<!DOCTYPE html><html lang="sl"><head><meta charset="UTF-8"><title>Neteru</title></head><body><h1>Neteru</h1><div><?php echo $v["vsebina"]; ?></div></body></html>
