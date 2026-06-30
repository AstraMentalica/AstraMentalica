<?php require_once __DIR__ . "/modul_meduneter.php"; $mod = new ModulMeduNeter(); $v = $mod->pridobiVsebino(); ?>
<!DOCTYPE html><html lang="sl"><head><meta charset="UTF-8"><title>MeduNeter</title></head><body><h1>MeduNeter</h1><div><?php echo $v["vsebina"]; ?></div></body></html>
