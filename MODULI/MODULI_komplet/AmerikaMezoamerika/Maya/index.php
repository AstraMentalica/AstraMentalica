<?php require_once __DIR__ . "/modul_maya.php"; $mod = new ModulMaya(); $v = $mod->pridobiVsebino(); ?>
<!DOCTYPE html><html lang="sl"><head><meta charset="UTF-8"><title>Maya</title></head><body><h1>Maya</h1><div><?php echo $v["vsebina"]; ?></div></body></html>
