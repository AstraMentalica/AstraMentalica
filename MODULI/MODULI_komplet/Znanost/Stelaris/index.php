<?php require_once __DIR__ . "/modul_stelaris.php"; $mod = new ModulStelaris(); $v = $mod->pridobiVsebino(); ?>
<!DOCTYPE html><html lang="sl"><head><meta charset="UTF-8"><title>Stelaris</title></head><body><h1>Stelaris</h1><div><?php echo $v["vsebina"]; ?></div></body></html>
