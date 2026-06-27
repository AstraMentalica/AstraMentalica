<?php
/**
 * ============================================================
 * POT: MODULI/Modul_Bridge/prikaz/pakiranje.php
 * 📅 VERZIJA: v114 (18.6.2026)
 * ============================================================
 *
 * 🏛️ NIVO: BRIDGE / PRIKAZ
 *
 * 📰 NAMEN:
 *     Pasivna predloga — rezultat pakiranja modula v ZIP.
 *     Pričakuje $rezultat (array) in $ime (string).
 *
 * 📌 STATUS:
 *     Stabilno
 *
 * 👤 AVTOR:
 *     AstraMentalica Mojster
 * ============================================================
 */

declare(strict_types=1);

$rezultat = $rezultat ?? [];
$ime      = htmlspecialchars($_GET['modul'] ?? '', ENT_QUOTES, 'UTF-8');
?>

<div class="kartica">
    <h2>📦 Pakiranje: <?= $ime ?></h2>

    <?php if ($rezultat['uspeh'] ?? false): ?>
        <div class="sporocilo sporocilo-uspeh">
            ✅ Modul uspešno pakiran!
        </div>
        <p style="margin-top:1rem">
            ZIP datoteka:
            <a href="<?= htmlspecialchars($rezultat['pot'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
               class="gumb" download>
                ⬇️ Prenesi ZIP
            </a>
        </p>
        <?php if (!empty($rezultat['velikost'])): ?>
            <p style="margin-top:0.5rem;opacity:0.6">Velikost: <?= $rezultat['velikost'] ?></p>
        <?php endif; ?>
    <?php else: ?>
        <div class="sporocilo sporocilo-napaka">
            ❌ <?= htmlspecialchars($rezultat['napaka'] ?? 'Pakiranje ni uspelo.', ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>

    <br>
    <a href="?akcija=pregled" class="gumb gumb-sekundarni">← Nazaj na pregled</a>
</div>
