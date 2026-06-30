<?php require_once __DIR__ . "/modul_malakhim.php"; $mod = new ModulMalakhim(); $v = $mod->pridobiVsebino(); ?>
<!DOCTYPE html><html lang="sl"><head><meta charset="UTF-8"><title>Malakhim</title></head><body><h1>Malakhim</h1><div><?php echo $v["vsebina"]; ?></div></body></html>
