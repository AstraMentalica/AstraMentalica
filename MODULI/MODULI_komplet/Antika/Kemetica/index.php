<?php require_once __DIR__ . "/modul_kemetica.php"; $mod = new ModulKemetica(); $v = $mod->pridobiVsebino(); ?>
<!DOCTYPE html><html lang="sl"><head><meta charset="UTF-8"><title>Kemetica</title></head><body><h1>Kemetica</h1><div><?php echo $v["vsebina"]; ?></div></body></html>
