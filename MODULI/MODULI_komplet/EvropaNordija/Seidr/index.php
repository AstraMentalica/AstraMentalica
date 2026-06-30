<?php require_once __DIR__ . "/modul_seidr.php"; $mod = new ModulSeidr(); $v = $mod->pridobiVsebino(); ?>
<!DOCTYPE html><html lang="sl"><head><meta charset="UTF-8"><title>Seidr</title></head><body><h1>Seidr</h1><div><?php echo $v["vsebina"]; ?></div></body></html>
