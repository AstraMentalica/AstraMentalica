<?php
require_once __DIR__ . '/modul_druidica.php';
$mod = new ModulDruidica();
$v = $mod->pridobiVsebino();
?>
<!DOCTYPE html>
<html lang="sl">
<head><meta charset="UTF-8"><title>Druidica</title></head>
<body>
<h1>Druidica</h1>
<div><?php echo $v['vsebina']; ?></div>
</body>
</html>
