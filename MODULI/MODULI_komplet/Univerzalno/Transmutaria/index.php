<?php require_once __DIR__ . "/modul_transmutaria.php"; $mod = new ModulTransmutaria(); $v = $mod->pridobiVsebino(); ?>
<!DOCTYPE html><html lang="sl"><head><meta charset="UTF-8"><title>Transmutaria</title></head><body><h1>Transmutaria</h1><div><?php echo $v["vsebina"]; ?></div></body></html>
