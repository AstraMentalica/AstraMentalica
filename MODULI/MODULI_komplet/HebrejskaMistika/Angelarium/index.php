<?php require_once __DIR__ . "/modul_angelarium.php"; $mod = new ModulAngelarium(); $v = $mod->pridobiVsebino(); ?>
<!DOCTYPE html><html lang="sl"><head><meta charset="UTF-8"><title>Angelarium</title></head><body><h1>Angelarium</h1><div><?php echo $v["vsebina"]; ?></div></body></html>
