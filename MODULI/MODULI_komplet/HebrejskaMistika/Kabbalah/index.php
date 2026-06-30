<?php require_once __DIR__ . "/modul_kabbalah.php"; $mod = new ModulKabbalah(); $v = $mod->pridobiVsebino(); ?>
<!DOCTYPE html><html lang="sl"><head><meta charset="UTF-8"><title>Kabbalah</title></head><body><h1>Kabbalah</h1><div><?php echo $v["vsebina"]; ?></div></body></html>
