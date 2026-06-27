<?php
require_once __DIR__ . '/modul_qivitalis.php';
$mod = new ModulQiVitalis();
$v = $mod->pridobiVsebino();
?>
<!DOCTYPE html>
<html lang="sl">
<head><meta charset="UTF-8"><title>QiVitalis</title></head>
<body>
<h1>QiVitalis</h1>
<div><?php echo $v['vsebina']; ?></div>
</body>
</html>