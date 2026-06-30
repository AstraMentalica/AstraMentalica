<?php require_once __DIR__ . "/modul_synera.php"; $mod = new ModulSynera(); $v = $mod->pridobiVsebino(); ?>
<!DOCTYPE html><html lang="sl"><head><meta charset="UTF-8"><title>Synera</title></head><body><h1>Synera</h1><div><?php echo $v["vsebina"]; ?></div></body></html>
