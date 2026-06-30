<?php require_once __DIR__ . "/modul_solarnipojavi.php"; $mod = new ModulSolarniPojavi(); $v = $mod->pridobiVsebino(); ?>
<!DOCTYPE html><html lang="sl"><head><meta charset="UTF-8"><title>SolarniPojavi</title></head><body><h1>SolarniPojavi</h1><div><?php echo $v["vsebina"]; ?></div></body></html>
