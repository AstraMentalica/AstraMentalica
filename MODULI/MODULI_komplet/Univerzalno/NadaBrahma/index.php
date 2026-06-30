<?php require_once __DIR__ . "/modul_nadabrahma.php"; $mod = new ModulNadaBrahma(); $v = $mod->pridobiVsebino(); ?>
<!DOCTYPE html><html lang="sl"><head><meta charset="UTF-8"><title>NadaBrahma</title></head><body><h1>NadaBrahma</h1><div><?php echo $v["vsebina"]; ?></div></body></html>
