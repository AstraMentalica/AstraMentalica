<?php require_once __DIR__ . "/modul_duat.php"; $mod = new ModulDuat(); $v = $mod->pridobiVsebino(); ?>
<!DOCTYPE html><html lang="sl"><head><meta charset="UTF-8"><title>Duat</title></head><body><h1>Duat</h1><div><?php echo $v["vsebina"]; ?></div></body></html>
