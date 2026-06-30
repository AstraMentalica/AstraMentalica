<?php
require_once __DIR__ . '/modul_polynesiamystica.php';
$mod = new ModulPolynesiaMystica();
$v = $mod->pridobiVsebino();
?>
<!DOCTYPE html>
<html lang="sl">
<head><meta charset="UTF-8"><title>PolynesiaMystica</title></head>
<body>
<h1>PolynesiaMystica</h1>
<div><?php echo $v['vsebina']; ?></div>
</body>
</html>
