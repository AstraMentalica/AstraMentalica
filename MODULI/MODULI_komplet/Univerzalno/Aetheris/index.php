<?php require_once __DIR__ . "/modul_aetheris.php"; $mod = new ModulAetheris(); $v = $mod->pridobiVsebino(); ?>
<!DOCTYPE html><html lang="sl"><head><meta charset="UTF-8"><title>Aetheris</title></head><body><h1>Aetheris</h1><div><?php echo $v["vsebina"]; ?></div></body></html>
