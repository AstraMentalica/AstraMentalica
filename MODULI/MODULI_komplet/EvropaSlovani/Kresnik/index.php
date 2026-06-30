<?php require_once __DIR__ . "/modul_kresnik.php"; $mod = new ModulKresnik(); $v = $mod->pridobiVsebino(); ?>
<!DOCTYPE html><html lang="sl"><head><meta charset="UTF-8"><title>Kresnik</title></head><body><h1>Kresnik</h1><div><?php echo $v["vsebina"]; ?></div></body></html>
