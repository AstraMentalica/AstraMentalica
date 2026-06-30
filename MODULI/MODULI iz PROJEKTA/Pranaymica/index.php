<?php
require_once __DIR__ . '/modul_pranaymica.php';
$mod = new ModulPranaymica();
$v = $mod->pridobiVsebino();
?>
<!DOCTYPE html>
<html lang="sl">
<head><meta charset="UTF-8"><title>Pranaymica</title></head>
<body>
<h1>Pranaymica</h1>
<div><?php echo $v['vsebina']; ?></div>
</body>
</html>