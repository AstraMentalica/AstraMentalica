<?php require_once __DIR__ . "/modul_chakravidya.php"; $mod = new ModulChakraVidya(); $v = $mod->pridobiVsebino(); ?>
<!DOCTYPE html><html lang="sl"><head><meta charset="UTF-8"><title>ChakraVidya</title></head><body><h1>ChakraVidya</h1><div><?php echo $v["vsebina"]; ?></div></body></html>
