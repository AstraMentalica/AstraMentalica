<?php
/**
 * PATH: GLOBALNO/mail/reset_password.php
 * NAMEN: MAIL PREDLOGA – PONASTAVITEV GESLA (nivo 0)
 * 
 * AKTUALNA PRAVILA:
 * - modularna/servisi.md (v2.0.0) – mail predloge
 * 
 * SPREMENLJIVKE:
 * - $ime (string) – ime uporabnika
 * - $email (string) – email uporabnika
 * - $povezava (string) – povezava za ponastavitev
 * - $token (string) – varnostni token
 * 
 * VERZIJA: 1.0.0
 * ZADNJA_SPREMEMBA: 2026-04-02
 */
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Ponastavitev gesla – Astra</title>
    <style>
        body { font-family: sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #e74c3c; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; }
        .footer { background: #f5f5f5; padding: 10px; text-align: center; font-size: 12px; color: #666; }
        .gumb { display: inline-block; background: #e74c3c; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; }
        .opozorilo { background: #fff3cd; border-left: 4px solid #f39c12; padding: 10px; margin: 20px 0; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>Ponastavitev gesla</h1>
    </div>
    <div class="content">
        <h2>Pozdravljeni, <?= htmlspecialchars($ime) ?>!</h2>
        <p>Prejeli smo zahtevo za ponastavitev gesla za vaš račun <strong><?= htmlspecialchars($email) ?></strong>.</p>
        
        <p>Za ponastavitev gesla kliknite na spodnjo povezavo:</p>
        <p style="text-align: center;">
            <a href="<?= htmlspecialchars($povezava) ?>" class="gumb">Ponastavi geslo</a>
        </p>
        
        <div class="opozorilo">
            <strong>⚠️ Opozorilo:</strong> Povezava velja 1 uro. Če niste zahtevali ponastavitve gesla, ignorirajte to sporočilo.
        </div>
        
        <p>Vaš varnostni token: <code><?= htmlspecialchars($token) ?></code></p>
    </div>
    <div class="footer">
        <p>Astra sistem &copy; <?= date('Y') ?></p>
        <p>To sporočilo je bilo generirano avtomatsko. Prosimo, ne odgovarjajte nanj.</p>
    </div>
</div>
</body>
</html>