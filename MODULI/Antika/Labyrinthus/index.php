<?php
require_once __DIR__ . '/modul_labyrinthus.php';
$mod = new ModulLabyrinthus();
$v = $mod->pridobiVsebino();
?>
<!DOCTYPE html>
<html lang="sl">
<head><meta charset="UTF-8"><title>Labyrinthus</title></head>
<body>
<h1>Labyrinthus</h1>
<div><?php echo $v['vsebina']; ?></div>
</body>
</html>
