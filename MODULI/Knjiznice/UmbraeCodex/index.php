<?php
require_once __DIR__ . '/modul_umbraecodex.php';
$mod = new ModulUmbraeCodex();
$v = $mod->pridobiVsebino();
?>
<!DOCTYPE html>
<html lang="sl">
<head><meta charset="UTF-8"><title>UmbraeCodex</title></head>
<body>
<h1>UmbraeCodex</h1>
<div><?php echo $v['vsebina']; ?></div>
</body>
</html>
