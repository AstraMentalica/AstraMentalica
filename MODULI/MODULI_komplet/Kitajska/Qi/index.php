<?php require_once __DIR__ . "/modul_qi.php"; $mod = new ModulQi(); $v = $mod->pridobiVsebino(); ?>
<!DOCTYPE html><html lang="sl"><head><meta charset="UTF-8"><title>Qi</title></head><body><h1>Qi</h1><div><?php echo $v["vsebina"]; ?></div></body></html>
