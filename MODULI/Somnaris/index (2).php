<?php
require_once __DIR__ . '/modul_somnaris.php';
$mod = new ModulSomnaris();
$v = $mod->pridobiVsebino();
?>
<!DOCTYPE html>
<html lang="sl">
<head><meta charset="UTF-8"><title>Somnaris</title></head>
<body>
<h1>Somnaris</h1>
<div><?php echo $v['vsebina']; ?></div>
</body>
</html>