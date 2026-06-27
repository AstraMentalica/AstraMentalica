<?php
/**
 * PATH: GLOBALNO/mail/default.php
 * NAMEN: MAIL PREDLOGA – PRIVZETA (nivo 0)
 * 
 * AKTUALNA PRAVILA:
 * - modularna/servisi.md (v2.0.0) – mail predloge
 * 
 * SPREMENLJIVKE:
 * - $naslov (string) – naslov sporočila
 * - $vsebina (string) – vsebina sporočila
 * - $podpis (string) – podpis (opcijsko)
 * 
 * VERZIJA: 1.0.0
 * ZADNJA_SPREMEMBA: 2026-04-02
 */
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($naslov ?? 'Sporočilo iz Astr') ?></title>
    <style>
        body { font-family: sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #4a90e2; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; }
        .footer { background: #f5f5f5; padding: 10px; text-align: center; font-size: 12px; color: #666; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>Astra</h1>
    </div>
    <div class="content">
        <?= $vsebina ?? '<p>Sporočilo nima vsebine.</p>' ?>
        
        <?php if (!empty($podpis)): ?>
        <p><?= htmlspecialchars($podpis) ?></p>
        <?php endif; ?>
    </div>
    <div class="footer">
        <p>Astra sistem &copy; <?= date('Y') ?></p>
        <p>To sporočilo je bilo generirano avtomatsko. Prosimo, ne odgovarjajte nanj.</p>
    </div>
</div>
</body>
</html>