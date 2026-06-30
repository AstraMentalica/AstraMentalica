<?php require_once __DIR__ . "/modul_celestara.php"; $mod = new ModulCelestara(); $v = $mod->pridobiVsebino(); ?>
<!DOCTYPE html><html lang="sl"><head><meta charset="UTF-8"><title>Celestara</title></head><body><h1>Celestara</h1><div><?php echo $v["vsebina"]; ?></div></body></html>
