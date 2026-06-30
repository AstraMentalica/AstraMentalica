<?php require_once __DIR__ . "/modul_ogham.php"; $mod = new ModulOgham(); $v = $mod->pridobiVsebino(); ?>
<!DOCTYPE html><html lang="sl"><head><meta charset="UTF-8"><title>Ogham</title></head><body><h1>Ogham</h1><div><?php echo $v["vsebina"]; ?></div></body></html>
