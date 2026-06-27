<?php
/**
 * PATH: GLOBALNO/mail/welcome.php
 * NAMEN: MAIL PREDLOGA – DOBRODOŠLICA (nivo 0)
 * 
 * AKTUALNA PRAVILA:
 * - modularna/servisi.md (v2.0.0) – mail predloge
 * 
 * SPREMENLJIVKE:
 * - $ime (string) – ime uporabnika
 * - $email (string) – email uporabnika
 * - $povezava (string) – povezava za potrditev (opcijsko)
 * 
 * VERZIJA: 1.0.0
 * ZADNJA_SPREMEMBA: 2026-04-02
 */
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Dobrodošli v Astri</title>
    <style>
        body { font-family: sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #4a90e2; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; }
        .footer { background: #f5f5f5; padding: 10px; text-align: center; font-size: 12px; color: #666; }
        .gumb { display: inline-block; background: #4a90e2; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>Astra</h1>
    </div>
    <div class="content">
        <h2>Dobrodošli, <?= htmlspecialchars($ime) ?>!</h2>
        <p>Hvala, ker ste se registrirali v sistem Astra. Vaš račun je bil uspešno ustvarjen.</p>
        <p>Vaš email: <strong><?= htmlspecialchars($email) ?></strong></p>
        
        <?php if (!empty($povezava)): ?>
        <p>Za potrditev računa kliknite na spodnjo povezavo:</p>
        <p style="text-align: center;">
            <a href="<?= htmlspecialchars($povezava) ?>" class="gumb">Potrdi račun</a>
        </p>
        <?php endif; ?>
        
        <p>Zdaj se lahko prijavite in začnete raziskovati vsebine:</p>
        <p style="text-align: center;">
            <a href="<?= ROOT_URL ?>uporabnik/prijava" class="gumb">Prijava</a>
        </p>
    </div>
    <div class="footer">
        <p>Astra sistem &copy; <?= date('Y') ?></p>
        <p>To sporočilo je bilo generirano avtomatsko. Prosimo, ne odgovarjajte nanj.</p>
    </div>
</div>
</body>
</html>