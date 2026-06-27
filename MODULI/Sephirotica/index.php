<?php
require_once __DIR__ . '/modul_sephirotica.php';
$mod = new ModulSephirotica();
$v = $mod->pridobiVsebino();
?>
<!DOCTYPE html>
<html lang="sl">
<head><meta charset="UTF-8"><title>Sephirotica</title></head>
<body>
<h1>Sephirotica</h1>
<div><?php echo $v['vsebina']; ?></div>
</body>
</html>