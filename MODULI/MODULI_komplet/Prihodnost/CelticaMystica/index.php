<?php require_once __DIR__ . "/modul_celticamystica.php"; $mod = new ModulCelticaMystica(); $v = $mod->pridobiVsebino(); ?>
<!DOCTYPE html><html lang="sl"><head><meta charset="UTF-8"><title>CelticaMystica</title></head><body><h1>CelticaMystica</h1><div><?php echo $v["vsebina"]; ?></div></body></html>
