
<?php
$data = file_get_contents('../modules/data/aetheris.json');
$frequencies = json_decode($data, true);
?>

<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <title>Aetheris - Frekvence</title>
    <link rel="stylesheet" href="../modules/css/aetheris.css">
</head>
<body>
    <h1>Aetheris - Zvočne Frekvence</h1>
    <div class="frequency-gallery">
        <?php foreach ($frequencies as $freq): ?>
            <div class="frequency-card">
                <h2><?= htmlspecialchars($freq['name']) ?></h2>
                <p><?= htmlspecialchars($freq['description']) ?></p>
                <?php if (!empty($freq['file'])): ?>
                    <audio controls>
                        <source src="../assets/audio/<?= htmlspecialchars($freq['file']) ?>" type="audio/mpeg">
                        Vaš brskalnik ne podpira zvoka.
                    </audio>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
    <script src="../modules/js/aetheris.js"></script>
</body>
</html>
