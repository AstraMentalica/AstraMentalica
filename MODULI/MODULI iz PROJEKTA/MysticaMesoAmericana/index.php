<?php
require_once __DIR__ . '/modul_mysticamesoamericana.php';
$mod = new ModulMysticaMesoamericana();
$v = $mod->pridobiVsebino();
?>
<!DOCTYPE html>
<html lang="sl">
<head><meta charset="UTF-8"><title>MysticaMesoamericana</title></head>
<body>
<h1>MysticaMesoamericana</h1>
<div><?php echo $v['vsebina']; ?></div>
</body>
</html>