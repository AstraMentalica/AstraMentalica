<?php
require_once __DIR__ . '/modul_numerariumcosmicum.php';
$mod = new ModulNumerariumCosmicum();
$v = $mod->pridobiVsebino();
?>
<!DOCTYPE html>
<html lang="sl">
<head><meta charset="UTF-8"><title>NumerariumCosmicum</title></head>
<body>
<h1>NumerariumCosmicum</h1>
<div><?php echo $v['vsebina']; ?></div>
</body>
</html>