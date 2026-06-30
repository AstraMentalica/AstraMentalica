<?php require_once __DIR__ . "/modul_hermetica.php"; $mod = new ModulHermetica(); $v = $mod->pridobiVsebino(); ?>
<!DOCTYPE html><html lang="sl"><head><meta charset="UTF-8"><title>Hermetica</title></head><body><h1>Hermetica</h1><div><?php echo $v["vsebina"]; ?></div></body></html>
