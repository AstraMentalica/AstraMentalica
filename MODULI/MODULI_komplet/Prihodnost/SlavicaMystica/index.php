<?php require_once __DIR__ . "/modul_slavicamystica.php"; $mod = new ModulSlavicaMystica(); $v = $mod->pridobiVsebino(); ?>
<!DOCTYPE html><html lang="sl"><head><meta charset="UTF-8"><title>SlavicaMystica</title></head><body><h1>SlavicaMystica</h1><div><?php echo $v["vsebina"]; ?></div></body></html>
