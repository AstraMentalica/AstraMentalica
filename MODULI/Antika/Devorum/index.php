<?php
require_once __DIR__ . '/modul_devorum.php';
$mod = new ModulDevorum();
$v = $mod->pridobiVsebino();
?>
<!DOCTYPE html>
<html lang="sl">
<head><meta charset="UTF-8"><title>Devorum</title></head>
<body>
<h1>Devorum</h1>
<div><?php echo $v['vsebina']; ?></div>
</body>
</html>
