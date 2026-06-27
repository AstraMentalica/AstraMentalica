<?php
/**
 * ============================================================
 * POT: MODULI/Modul_Bridge/prikaz/generator.php
 * 📅 VERZIJA: v118 (19.6.2026 02:00)
 * ============================================================
 *
 * 🏛️ NIVO: BRIDGE / PRIKAZ
 *
 * 📰 NAMEN:
 *     Pasivna predloga — obrazec za generiranje novega modula.
 *     NOVA STRUKTURA: modul gre direktno v MODULI/ImeModula/ (brez kategorij).
 *
 * 📌 STATUS:
 *     Stabilno
 *
 * 👤 AVTOR:
 *     AstraMentalica Mojster
 * ============================================================
 */

declare(strict_types=1);

$sporocilo = $sporocilo ?? null;
?>

<?php if ($sporocilo !== null): ?>
    <div class="sporocilo <?= ($sporocilo['uspeh'] ?? false) ? 'sporocilo-uspeh' : 'sporocilo-napaka' ?>">
        <?php if ($sporocilo['uspeh'] ?? false): ?>
            ✅ Modul uspešno ustvarjen: <strong><?= htmlspecialchars($sporocilo['pot'] ?? '', ENT_QUOTES, 'UTF-8') ?></strong>
        <?php else: ?>
            ❌ Napaka: <?= htmlspecialchars($sporocilo['napaka'] ?? 'Neznana napaka', ENT_QUOTES, 'UTF-8') ?>
        <?php endif; ?>
    </div>
<?php endif; ?>

<div class="kartica">
    <h2>🏭 Nov modul</h2>
    <p style="margin-bottom:1.5rem;opacity:0.7">
        Ustvari nov modul po <strong>NOVI strukturi</strong> (brez kategorij):
        <code>MODULI/ImeModula/podatki/manifest.json</code>
    </p>

    <form method="POST" action="?akcija=generiraj">

        <div class="polje">
            <label for="ime">Ime modula (PascalCase)</label>
            <input type="text" name="ime" id="ime" required
                   placeholder="npr. Stelaris, OrakleumTarot"
                   pattern="[A-Za-z][A-Za-z0-9]*" maxlength="60">
            <small style="opacity:0.5">Primeri: Stelaris, Lunaris, Tarot, Codex</small>
        </div>

        <div class="polje">
            <label for="opis">Opis modula</label>
            <textarea name="opis" id="opis" required
                      placeholder="Kratko besedilo, ki opisuje namen modula..."></textarea>
        </div>

        <button type="submit" class="gumb">✨ Ustvari modul</button>
        <a href="?akcija=pregled" class="gumb gumb-sekundarni">← Prekliči</a>
    </form>
</div>

<div class="kartica">
    <h2>📋 Kaj bo generirano (NOVA STRUKTURA)</h2>
    <pre style="font-size:0.8rem;opacity:0.7;line-height:1.6">s
MODULI/{Ime}/
├── modul.php          ← vstopna točka (API logika)
├── .htaccess          ← varnostna zaščita
└── podatki/
    ├── manifest.json  ← sistemske nastavitve
    ├── api.json       ← HTTP poti
    └── izhod.json     ← shema in pisanje</pre>
</div>