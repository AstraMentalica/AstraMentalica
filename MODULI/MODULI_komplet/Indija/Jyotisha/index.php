<?php require_once __DIR__ . "/modul_jyotisha.php"; $mod = new ModulJyotisha(); $v = $mod->pridobiVsebino(); ?>
<!DOCTYPE html><html lang="sl"><head><meta charset="UTF-8"><title>Jyotisha</title></head><body><h1>Jyotisha</h1><div><?php echo $v["vsebina"]; ?></div></body></html>
