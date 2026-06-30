<?php require_once __DIR__ . "/modul_labyrinthos.php"; $mod = new ModulLabyrinthos(); $v = $mod->pridobiVsebino(); ?>
<!DOCTYPE html><html lang="sl"><head><meta charset="UTF-8"><title>Labyrinthos</title></head><body><h1>Labyrinthos</h1><div><?php echo $v["vsebina"]; ?></div></body></html>
