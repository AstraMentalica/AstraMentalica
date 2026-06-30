<?php require_once __DIR__ . "/modul_buddhica.php"; $mod = new ModulBuddhica(); $v = $mod->pridobiVsebino(); ?>
<!DOCTYPE html><html lang="sl"><head><meta charset="UTF-8"><title>Buddhica</title></head><body><h1>Buddhica</h1><div><?php echo $v["vsebina"]; ?></div></body></html>
