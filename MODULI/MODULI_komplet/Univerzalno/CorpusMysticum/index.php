<?php require_once __DIR__ . "/modul_corpusmysticum.php"; $mod = new ModulCorpusMysticum(); $v = $mod->pridobiVsebino(); ?>
<!DOCTYPE html><html lang="sl"><head><meta charset="UTF-8"><title>CorpusMysticum</title></head><body><h1>CorpusMysticum</h1><div><?php echo $v["vsebina"]; ?></div></body></html>
