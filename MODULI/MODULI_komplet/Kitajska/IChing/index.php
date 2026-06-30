<?php require_once __DIR__ . "/modul_iching.php"; $mod = new ModulIChing(); $v = $mod->pridobiVsebino(); ?>
<!DOCTYPE html><html lang="sl"><head><meta charset="UTF-8"><title>IChing</title></head><body><h1>IChing</h1><div><?php echo $v["vsebina"]; ?></div></body></html>
