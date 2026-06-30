<?php require_once __DIR__ . "/modul_nahuatl.php"; $mod = new ModulNahuatl(); $v = $mod->pridobiVsebino(); ?>
<!DOCTYPE html><html lang="sl"><head><meta charset="UTF-8"><title>Nahuatl</title></head><body><h1>Nahuatl</h1><div><?php echo $v["vsebina"]; ?></div></body></html>
