<?php
require_once __DIR__ . '/modul_nordicamystica.php';
$mod = new ModulNordicaMystica();
$v = $mod->pridobiVsebino();
?>
<!DOCTYPE html>
<html lang="sl">
<head><meta charset="UTF-8"><title>NordicaMystica</title></head>
<body>
<h1>NordicaMystica</h1>
<div><?php echo $v['vsebina']; ?></div>
</body>
</html>