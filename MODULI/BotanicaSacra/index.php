<?php
require_once __DIR__ . '/modul_botanicasacra.php';
$mod = new ModulBotanicaSacra();
$v = $mod->pridobiVsebino();
?>
<!DOCTYPE html>
<html lang="sl">
<head><meta charset="UTF-8"><title>BotanicaSacra</title></head>
<body>
<h1>BotanicaSacra</h1>
<div><?php echo $v['vsebina']; ?></div>
</body>
</html>