<?php require_once __DIR__ . "/modul_daimon.php"; $mod = new ModulDaimon(); $v = $mod->pridobiVsebino(); ?>
<!DOCTYPE html><html lang="sl"><head><meta charset="UTF-8"><title>Daimon</title></head><body><h1>Daimon</h1><div><?php echo $v["vsebina"]; ?></div></body></html>
