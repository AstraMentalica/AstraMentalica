<?php
require_once __DIR__ . '/modul_aegypticaarcana.php';
$mod = new ModulAegypticaArcana();
$v = $mod->pridobiVsebino();
?>
<!DOCTYPE html>
<html lang="sl">
<head><meta charset="UTF-8"><title>AegypticaArcana</title></head>
<body>
<h1>AegypticaArcana</h1>
<div><?php echo $v['vsebina']; ?></div>
</body>
</html>