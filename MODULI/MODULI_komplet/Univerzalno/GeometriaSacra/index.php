<?php require_once __DIR__ . "/modul_geometriasacra.php"; $mod = new ModulGeometriaSacra(); $v = $mod->pridobiVsebino(); ?>
<!DOCTYPE html><html lang="sl"><head><meta charset="UTF-8"><title>GeometriaSacra</title></head><body><h1>GeometriaSacra</h1><div><?php echo $v["vsebina"]; ?></div></body></html>
