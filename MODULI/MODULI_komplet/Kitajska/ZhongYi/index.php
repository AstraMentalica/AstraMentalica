<?php require_once __DIR__ . "/modul_zhongyi.php"; $mod = new ModulZhongYi(); $v = $mod->pridobiVsebino(); ?>
<!DOCTYPE html><html lang="sl"><head><meta charset="UTF-8"><title>ZhongYi</title></head><body><h1>ZhongYi</h1><div><?php echo $v["vsebina"]; ?></div></body></html>
