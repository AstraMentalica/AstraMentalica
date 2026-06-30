<?php require_once __DIR__ . "/modul_tarot.php"; $mod = new ModulTarot(); $v = $mod->pridobiVsebino(); ?>
<!DOCTYPE html><html lang="sl"><head><meta charset="UTF-8"><title>Tarot</title></head><body><h1>Tarot</h1><div><?php echo $v["vsebina"]; ?></div></body></html>
