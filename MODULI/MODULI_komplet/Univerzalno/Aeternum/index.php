<?php require_once __DIR__ . "/modul_aeternum.php"; $mod = new ModulAeternum(); $v = $mod->pridobiVsebino(); ?>
<!DOCTYPE html><html lang="sl"><head><meta charset="UTF-8"><title>Aeternum</title></head><body><h1>Aeternum</h1><div><?php echo $v["vsebina"]; ?></div></body></html>
