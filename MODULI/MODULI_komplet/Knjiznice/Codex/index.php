<?php require_once __DIR__ . "/modul_codex.php"; $mod = new ModulCodex(); $v = $mod->pridobiVsebino(); ?>
<!DOCTYPE html><html lang="sl"><head><meta charset="UTF-8"><title>Codex</title></head><body><h1>Codex</h1><div><?php echo $v["vsebina"]; ?></div></body></html>
