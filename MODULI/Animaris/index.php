<?php
require_once __DIR__ . '/modul_animaris.php';
$mod = new ModulAnimaris();
$v = $mod->pridobiVsebino();
?>
<!DOCTYPE html>
<html lang="sl">
<head><meta charset="UTF-8"><title>Animaris</title></head>
<body>
<h1>Animaris</h1>
<div><?php echo $v['vsebina']; ?></div>
</body>
</html>