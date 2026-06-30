<?php require_once __DIR__ . "/modul_codexantiqua.php"; $mod = new ModulCodexAntiqua(); $v = $mod->pridobiVsebino(); ?>
<!DOCTYPE html><html lang="sl"><head><meta charset="UTF-8"><title>CodexAntiqua</title></head><body><h1>CodexAntiqua</h1><div><?php echo $v["vsebina"]; ?></div></body></html>
