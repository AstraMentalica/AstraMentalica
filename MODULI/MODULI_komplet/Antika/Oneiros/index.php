<?php require_once __DIR__ . "/modul_oneiros.php"; $mod = new ModulOneiros(); $v = $mod->pridobiVsebino(); ?>
<!DOCTYPE html><html lang="sl"><head><meta charset="UTF-8"><title>Oneiros</title></head><body><h1>Oneiros</h1><div><?php echo $v["vsebina"]; ?></div></body></html>
