<?php require_once __DIR__ . "/modul_azteca.php"; $mod = new ModulAzteca(); $v = $mod->pridobiVsebino(); ?>
<!DOCTYPE html><html lang="sl"><head><meta charset="UTF-8"><title>Azteca</title></head><body><h1>Azteca</h1><div><?php echo $v["vsebina"]; ?></div></body></html>
