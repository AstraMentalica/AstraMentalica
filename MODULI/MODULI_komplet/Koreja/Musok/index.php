<?php require_once __DIR__ . "/modul_musok.php"; $mod = new ModulMusok(); $v = $mod->pridobiVsebino(); ?>
<!DOCTYPE html><html lang="sl"><head><meta charset="UTF-8"><title>Musok</title></head><body><h1>Musok</h1><div><?php echo $v["vsebina"]; ?></div></body></html>
