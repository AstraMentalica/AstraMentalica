<?php
require_once __DIR__ . '/modul_africanamystica.php';
$mod = new ModulAfricanaMystica();
$v = $mod->pridobiVsebino();
?>
<!DOCTYPE html>
<html lang="sl">
<head><meta charset="UTF-8"><title>AfricanaMystica</title></head>
<body>
<h1>AfricanaMystica</h1>
<div><?php echo $v['vsebina']; ?></div>
</body>
</html>
