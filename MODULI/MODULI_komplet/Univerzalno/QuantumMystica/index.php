<?php require_once __DIR__ . "/modul_quantummystica.php"; $mod = new ModulQuantumMystica(); $v = $mod->pridobiVsebino(); ?>
<!DOCTYPE html><html lang="sl"><head><meta charset="UTF-8"><title>QuantumMystica</title></head><body><h1>QuantumMystica</h1><div><?php echo $v["vsebina"]; ?></div></body></html>
