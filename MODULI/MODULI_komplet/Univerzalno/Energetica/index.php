<?php require_once __DIR__ . "/modul_energetica.php"; $mod = new ModulEnergetica(); $v = $mod->pridobiVsebino(); ?>
<!DOCTYPE html><html lang="sl"><head><meta charset="UTF-8"><title>Energetica</title></head><body><h1>Energetica</h1><div><?php echo $v["vsebina"]; ?></div></body></html>
