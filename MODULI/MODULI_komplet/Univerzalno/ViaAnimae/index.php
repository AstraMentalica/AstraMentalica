<?php require_once __DIR__ . "/modul_viaanimae.php"; $mod = new ModulViaAnimae(); $v = $mod->pridobiVsebino(); ?>
<!DOCTYPE html><html lang="sl"><head><meta charset="UTF-8"><title>ViaAnimae</title></head><body><h1>ViaAnimae</h1><div><?php echo $v["vsebina"]; ?></div></body></html>
