<?php require_once __DIR__ . "/modul_nasa.php"; $mod = new ModulNasa(); $v = $mod->pridobiVsebino(); ?>
<!DOCTYPE html><html lang="sl"><head><meta charset="UTF-8"><title>Nasa</title></head><body><h1>Nasa</h1><div><?php echo $v["vsebina"]; ?></div></body></html>
