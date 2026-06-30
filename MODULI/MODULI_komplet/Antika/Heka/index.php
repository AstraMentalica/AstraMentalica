<?php require_once __DIR__ . "/modul_heka.php"; $mod = new ModulHeka(); $v = $mod->pridobiVsebino(); ?>
<!DOCTYPE html><html lang="sl"><head><meta charset="UTF-8"><title>Heka</title></head><body><h1>Heka</h1><div><?php echo $v["vsebina"]; ?></div></body></html>
