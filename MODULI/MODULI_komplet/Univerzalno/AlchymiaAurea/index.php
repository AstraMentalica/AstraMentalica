<?php require_once __DIR__ . "/modul_alchymiaaurea.php"; $mod = new ModulAlchymiaAurea(); $v = $mod->pridobiVsebino(); ?>
<!DOCTYPE html><html lang="sl"><head><meta charset="UTF-8"><title>AlchymiaAurea</title></head><body><h1>AlchymiaAurea</h1><div><?php echo $v["vsebina"]; ?></div></body></html>
