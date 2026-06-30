<?php require_once __DIR__ . "/modul_rodnoverje.php"; $mod = new ModulRodnoverje(); $v = $mod->pridobiVsebino(); ?>
<!DOCTYPE html><html lang="sl"><head><meta charset="UTF-8"><title>Rodnoverje</title></head><body><h1>Rodnoverje</h1><div><?php echo $v["vsebina"]; ?></div></body></html>
