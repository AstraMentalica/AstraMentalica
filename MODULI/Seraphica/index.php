<?php
require_once __DIR__ . '/modul_seraphica.php';
$mod = new ModulSeraphica();
$v = $mod->pridobiVsebino();
?>
<!DOCTYPE html>
<html lang="sl">
<head><meta charset="UTF-8"><title>Seraphica</title></head>
<body>
<h1>Seraphica</h1>
<div><?php echo $v['vsebina']; ?></div>
</body>
</html>