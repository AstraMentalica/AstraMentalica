<?php
/**
 * ============================================================
 * POT: MODULI/Modul_Bridge/prikaz/testnik.php
 * 📅 VERZIJA: v118 (19.6.2026 02:00)
 * ============================================================
 *
 * 🏛️ NIVO: BRIDGE / PRIKAZ
 *
 * 📰 NAMEN:
 *     Pasivna predloga — testiranje dostopa modulov z različnimi vlogami.
 *     NOVA STRUKTURA: brez kategorij.
 *
 * 📌 STATUS:
 *     Stabilno
 *
 * 👤 AVTOR:
 *     AstraMentalica Mojster
 * ============================================================
 */

declare(strict_types=1);

$moduli      = $moduli ?? [];
$uporabnik   = mini_pridobi_uporabnika();
$vloga_zdaj  = (int)($uporabnik['vloga'] ?? 0);

$vloge = [
    MINI_VLOGA_GOST      => '🚪 Gost',
    MINI_VLOGA_UPORABNIK => '👤 Uporabnik',
    MINI_VLOGA_S1        => '⭐ S1',
    MINI_VLOGA_S2        => '⭐⭐ S2',
    MINI_VLOGA_S3        => '⭐⭐⭐ S3',
    MINI_VLOGA_ADMIN     => '👑 Admin',
];
?>

<!-- Trenutna vloga -->
<div class="kartica" style="border-left: 4px solid var(--zlata)">
    <h2>🔑 Trenutna vloga</h2>
    <p>
        <strong style="color:var(--zlata)"><?= htmlspecialchars(mini_vloga_v_ime($vloga_zdaj), ENT_QUOTES, 'UTF-8') ?></strong>
        &nbsp;(številka vloge: <code><?= $vloga_zdaj ?></code>)
    </p>

    <div style="display:flex;gap:0.5rem;flex-wrap:wrap;margin-top:1rem">
        <?php foreach ($vloge as $st => $ime_vloge): ?>
            <a href="?akcija=testnik&vloga=<?= $st ?>"
               class="gumb <?= $st === $vloga_zdaj ? '' : 'gumb-sekundarni' ?>"
               style="font-size:0.8rem">
                <?= htmlspecialchars($ime_vloge, ENT_QUOTES, 'UTF-8') ?> (<?= $st ?>)
            </a>
        <?php endforeach; ?>
    </div>
</div>

<!-- Dostop do modulov -->
<div class="kartica">
    <h2>📦 Dostop do modulov</h2>

    <?php if (empty($moduli)): ?>
        <p style="opacity:0.6">Ni modulov za prikaz.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Modul</th>
                    <th>ID</th>
                    <th>Min. vloga</th>
                    <th>Dostop</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($moduli as $m):
                $ime       = $m['modul']['ime'] ?? $m['_id'] ?? '—';
                $id        = $m['_id'] ?? $m['modul']['id'] ?? '—';
                $min_vloga = $m['dostop']['minimalna_vloga'] ?? 'S0';
                $min_int   = _vloga_string_v_int($min_vloga);
                $dostop    = $vloga_zdaj >= $min_int;
                $pot       = $m['_pot'] ?? '';
                $modul_php = $pot . '/modul.php';
            ?>
                <tr>
                    <td><strong><?= htmlspecialchars($ime, ENT_QUOTES, 'UTF-8') ?></strong></td>
                    <td><code><?= htmlspecialchars($id, ENT_QUOTES, 'UTF-8') ?></code></td>
                    <td><?= htmlspecialchars($min_vloga, ENT_QUOTES, 'UTF-8') ?></td>
                    <td>
                        <?php if ($dostop): ?>
                            <span class="oznaka oznaka-uspeh">✅ Dovoljen</span>
                        <?php else: ?>
                            <span class="oznaka oznaka-napaka">❌ Zavrnjen</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($dostop && file_exists($modul_php)): ?>
                            <a href="<?= htmlspecialchars($modul_php, ENT_QUOTES, 'UTF-8') ?>?akcija=info"
                               target="_blank" class="gumb" style="font-size:0.75rem;padding:0.25rem 0.7rem">
                                🔗 Odpri
                            </a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<div style="margin-top:1rem">
    <a href="?akcija=pregled" class="gumb gumb-sekundarni">← Nazaj na pregled</a>
</div>

<?php
// ── POMOŽNA FUNKCIJA ──────────────────────────────────────

function _vloga_string_v_int(string $vloga): int {
    return match(strtoupper($vloga)) {
        'GOST'  => 0,
        'S0'    => 10,
        'S1'    => 20,
        'S2'    => 30,
        'S3'    => 40,
        'S4'    => 50,
        'S5'    => 60,
        'ADMIN' => 100,
        default => 0,
    };
}