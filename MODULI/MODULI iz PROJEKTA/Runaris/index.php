<?php
require_once __DIR__ . '/modul_rune.php';
$mod = new ModulRune();
$v = $mod->pridobiVsebino();
?>
<!DOCTYPE html>
<html lang="sl">
<head><meta charset="UTF-8"><title>Rune</title></head>
<body>
<h1>Rune</h1>
<div><?php echo $v['vsebina']; ?></div>
</body>
</html>