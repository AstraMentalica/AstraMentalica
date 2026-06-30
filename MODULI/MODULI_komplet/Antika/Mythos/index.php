<?php require_once __DIR__ . "/modul_mythos.php"; $mod = new ModulMythos(); $v = $mod->pridobiVsebino(); ?>
<!DOCTYPE html><html lang="sl"><head><meta charset="UTF-8"><title>Mythos</title></head><body><h1>Mythos</h1><div><?php echo $v["vsebina"]; ?></div></body></html>
