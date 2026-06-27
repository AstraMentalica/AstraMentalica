<?php
require_once __DIR__ . '/modul_liberumbrae.php';
$mod = new ModulLiberUmbrae();
$v = $mod->pridobiVsebino();
?>
<!DOCTYPE html>
<html lang="sl">
<head><meta charset="UTF-8"><title>LiberUmbrae</title></head>
<body>
<h1>LiberUmbrae</h1>
<div><?php echo $v['vsebina']; ?></div>
</body>
</html>