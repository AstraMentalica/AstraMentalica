<?php
require_once __DIR__ . '/modul_lapidaria.php';
$mod = new ModulLapidaria();
$v = $mod->pridobiVsebino();
?>
<!DOCTYPE html>
<html lang="sl">
<head><meta charset="UTF-8"><title>Lapidaria</title></head>
<body>
<h1>Lapidaria</h1>
<div><?php echo $v['vsebina']; ?></div>
</body>
</html>