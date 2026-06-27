<?php
require_once __DIR__ . '/modul_mystaia.php';
$mod = new ModulMystaia();
$v = $mod->pridobiVsebino();
?>
<!DOCTYPE html>
<html lang="sl">
<head><meta charset="UTF-8"><title>Mystaia</title></head>
<body>
<h1>Mystaia</h1>
<div><?php echo $v['vsebina']; ?></div>
</body>
</html>