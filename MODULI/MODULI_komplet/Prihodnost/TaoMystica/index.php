<?php require_once __DIR__ . "/modul_taomystica.php"; $mod = new ModulTaoMystica(); $v = $mod->pridobiVsebino(); ?>
<!DOCTYPE html><html lang="sl"><head><meta charset="UTF-8"><title>TaoMystica</title></head><body><h1>TaoMystica</h1><div><?php echo $v["vsebina"]; ?></div></body></html>
