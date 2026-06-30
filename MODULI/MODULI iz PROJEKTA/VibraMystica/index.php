<?php
require_once __DIR__ . '/modul_vibramystica.php';
$mod = new ModulVibraMystica();
$v = $mod->pridobiVsebino();
?>
<!DOCTYPE html>
<html lang="sl">
<head><meta charset="UTF-8"><title>VibraMystica</title></head>
<body>
<h1>VibraMystica</h1>
<div><?php echo $v['vsebina']; ?></div>
</body>
</html>