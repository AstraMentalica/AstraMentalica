<?php require_once __DIR__ . "/modul_orakleum.php"; $mod = new ModulOrakleum(); $v = $mod->pridobiVsebino(); ?>
<!DOCTYPE html><html lang="sl"><head><meta charset="UTF-8"><title>Orakleum</title></head><body><h1>Orakleum</h1><div><?php echo $v["vsebina"]; ?></div></body></html>
