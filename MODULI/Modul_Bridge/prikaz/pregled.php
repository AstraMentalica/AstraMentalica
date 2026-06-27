<?php
/**
 * ============================================================
 * POT: MODULI/Modul_Bridge/prikaz/pregled.php
 * 📅 VERZIJA: v118 (19.6.2026 02:00)
 * ============================================================
 *
 * 🏛️ NIVO: BRIDGE / PRIKAZ
 *
 * 📰 NAMEN:
 *     Pasivna predloga — prikaz vseh najdenih modulov.
 *     NOVA STRUKTURA: brez kategorij, direktno iz MODULI/*/podatki/manifest.json
 *
 * 📌 STATUS:
 *     Stabilno
 *
 * 👤 AVTOR:
 *     AstraMentalica Mojster
 * ============================================================
 */

declare(strict_types=1);

// Zagotovi, da $moduli obstaja
$moduli = $moduli ?? [];
$skupaj = count($moduli);
?>

<div class="kartica">
    <h2>📊 Statistika</h2>
    <p>Najdenih modulov: <strong style="color:var(--zlata)"><?= $skupaj ?></strong></p>
    <p style="opacity:0.5;font-size:0.8rem">Struktura: MODULI/*/podatki/manifest.json (brez kategorij)</p>
</div>

<?php if ($skupaj === 0): ?>
    <div class="kartica">
        <p style="opacity:0.6">Ni najdenih modulov. Preverite strukturo map v MODULI/.</p>
        <p style="opacity:0.4;font-size:0.8rem">Moduli morajo biti v: <code>MODULI/ImeModula/podatki/manifest.json</code></p>
        <br>
        <a href="?akcija=generiraj" class="gumb">🏭 Ustvari prvi modul</a>
    </div>

<?php else: ?>
    <div class="kartica">
        <h2>📦 Vsi moduli</h2>
        <table>
            <thead>
                <tr>
                    <th>Modul</th>
                    <th>ID</th>
                    <th>Verzija</th>
                    <th>Tip</th>
                    <th>Min. vloga</th>
                    <th>Akcije</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($moduli as $m):
                // Podatki iz NOVEGA manifesta
                $ime     = $m['modul']['ime'] ?? $m['_id'] ?? '—';
                $id      = $m['_id'] ?? $m['modul']['id'] ?? '—';
                $verzija = $m['modul']['verzija'] ?? $m['_verzija'] ?? '—';
                $tip     = $m['modul']['tip'] ?? '—';
                $vloga   = $m['dostop']['minimalna_vloga'] ?? 'S0';
                $pot     = $m['_pot'] ?? '';

                $modul_php = $pot . '/modul.php';
                $ima_vstop = file_exists($modul_php);
            ?>
                <tr>
                    <td><strong><?= htmlspecialchars($ime, ENT_QUOTES, 'UTF-8') ?></strong></td>
                    <td><code><?= htmlspecialchars($id, ENT_QUOTES, 'UTF-8') ?></code></td>
                    <td><?= htmlspecialchars($verzija, ENT_QUOTES, 'UTF-8') ?></td>
                    <td><span class="oznaka oznaka-info"><?= htmlspecialchars($tip, ENT_QUOTES, 'UTF-8') ?></span></td>
                    <td><?= htmlspecialchars($vloga, ENT_QUOTES, 'UTF-8') ?></td>
                    <td>
                        <?php if ($ima_vstop): ?>
                            <a href="<?= htmlspecialchars($modul_php, ENT_QUOTES, 'UTF-8') ?>?akcija=info"
                               target="_blank" class="gumb" style="font-size:0.75rem;padding:0.25rem 0.7rem">
                                🔗 Odpri
                            </a>
                        <?php else: ?>
                            <span style="opacity:0.4;font-size:0.8rem">ni modul.php</span>
                        <?php endif; ?>
                        <a href="?akcija=pakiraj&modul=<?= urlencode($ime) ?>"
                           class="gumb gumb-sekundarni" style="font-size:0.75rem;padding:0.25rem 0.7rem">
                            📦 Pakiranje
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<div style="margin-top:1rem">
    <a href="?akcija=generiraj" class="gumb">🏭 Nov modul</a>
    <a href="?akcija=testnik" class="gumb gumb-sekundarni">🧪 Testnik vlog</a>
</div>