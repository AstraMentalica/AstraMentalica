<?php require_once __DIR__ . "/modul_anahuac.php"; $mod = new ModulAnahuac(); $v = $mod->pridobiVsebino(); ?>
<!DOCTYPE html><html lang="sl"><head><meta charset="UTF-8"><title>Anahuac</title></head><body><h1>Anahuac</h1><div><?php echo $v["vsebina"]; ?></div></body></html>
