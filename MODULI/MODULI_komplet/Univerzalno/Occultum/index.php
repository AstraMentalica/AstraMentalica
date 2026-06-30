<?php require_once __DIR__ . "/modul_occultum.php"; $mod = new ModulOccultum(); $v = $mod->pridobiVsebino(); ?>
<!DOCTYPE html><html lang="sl"><head><meta charset="UTF-8"><title>Occultum</title></head><body><h1>Occultum</h1><div><?php echo $v["vsebina"]; ?></div></body></html>
