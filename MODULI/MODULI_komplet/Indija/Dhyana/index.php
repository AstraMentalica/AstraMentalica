<?php require_once __DIR__ . "/modul_dhyana.php"; $mod = new ModulDhyana(); $v = $mod->pridobiVsebino(); ?>
<!DOCTYPE html><html lang="sl"><head><meta charset="UTF-8"><title>Dhyana</title></head><body><h1>Dhyana</h1><div><?php echo $v["vsebina"]; ?></div></body></html>
