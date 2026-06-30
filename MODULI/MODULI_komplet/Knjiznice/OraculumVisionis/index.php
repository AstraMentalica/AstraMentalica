<?php require_once __DIR__ . "/modul_oraculumvisionis.php"; $mod = new ModulOraculumVisionis(); $v = $mod->pridobiVsebino(); ?>
<!DOCTYPE html><html lang="sl"><head><meta charset="UTF-8"><title>OraculumVisionis</title></head><body><h1>OraculumVisionis</h1><div><?php echo $v["vsebina"]; ?></div></body></html>
