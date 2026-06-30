<?php require_once __DIR__ . "/modul_cosmicascientia.php"; $mod = new ModulCosmicaScientia(); $v = $mod->pridobiVsebino(); ?>
<!DOCTYPE html><html lang="sl"><head><meta charset="UTF-8"><title>CosmicaScientia</title></head><body><h1>CosmicaScientia</h1><div><?php echo $v["vsebina"]; ?></div></body></html>
