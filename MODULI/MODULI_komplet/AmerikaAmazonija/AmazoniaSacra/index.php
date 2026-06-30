<?php require_once __DIR__ . "/modul_amazoniasacra.php"; $mod = new ModulAmazoniaSacra(); $v = $mod->pridobiVsebino(); ?>
<!DOCTYPE html><html lang="sl"><head><meta charset="UTF-8"><title>AmazoniaSacra</title></head><body><h1>AmazoniaSacra</h1><div><?php echo $v["vsebina"]; ?></div></body></html>
