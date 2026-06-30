<?php require_once __DIR__ . "/modul_kotodama.php"; $mod = new ModulKotodama(); $v = $mod->pridobiVsebino(); ?>
<!DOCTYPE html><html lang="sl"><head><meta charset="UTF-8"><title>Kotodama</title></head><body><h1>Kotodama</h1><div><?php echo $v["vsebina"]; ?></div></body></html>
