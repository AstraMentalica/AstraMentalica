<?php
/**
 * ============================================================
 * POT: MODULI/Modul_Bridge/demo/vloge_demo.php
 * 📅 VERZIJA: v114 (18.6.2026)
 * ============================================================
 *
 * 🏛️ NIVO: BRIDGE / DEMO
 *
 * 📰 NAMEN:
 *     Standalone testnik vlog — deluje neodvisno od index.php.
 *     Direkten URL dostop za hitro testiranje dostopa modulov.
 *
 * 🔧 UPORABA:
 *     ?vloga=0  (gost)
 *     ?vloga=10 (uporabnik)
 *     ?vloga=60 (admin)
 *
 * 📌 STATUS:
 *     Stabilno
 *
 * 👤 AVTOR:
 *     AstraMentalica Mojster
 *
 * 🌐 JEZIK:
 *     sl
 *
 * 🏷️ OZNAKE:
 *     bridge, demo, vloge
 * ============================================================
 */

declare(strict_types=1);

define('BRIDGE_VARNOST', true);

require_once __DIR__ . '/../embed/mini_sistem.php';

// Inicializacija
mini_inicijalizacija();

// Preklop vloge
if (isset($_GET['vloga'])) {
    mini_dodeli_vlogo((int)$_GET['vloga']);
}

$uporabnik  = mini_pridobi_uporabnika();
$vloga_zdaj = (int)($uporabnik['vloga'] ?? 0);

// Pridobi vse module
$moduli = _mini_moduli_pridobi_vse();

mini_izhod_glava('Testnik vlog (Demo)');
?>

<div class="kartica" style="border-left:4px solid var(--zlata)">
    <h2>🔑 Trenutna vloga</h2>
    <p>
        <strong style="color:var(--zlata)"><?= htmlspecialchars(mini_vloga_v_ime($vloga_zdaj), ENT_QUOTES, 'UTF-8') ?></strong>
        &nbsp;(vloga <?= $vloga_zdaj ?>)
    </p>
    <div style="display:flex;gap:0.5rem;flex-wrap:wrap;margin-top:1rem">
        <?php
        $vloge = [MINI_VLOGA_GOST => '🚪 Gost', MINI_VLOGA_UPORABNIK => '👤 Uporabnik',
                  MINI_VLOGA_S1 => '⭐ S1', MINI_VLOGA_S2 => '⭐⭐ S2',
                  MINI_VLOGA_S3 => '⭐⭐⭐ S3', MINI_VLOGA_ADMIN => '👑 Admin'];
        foreach ($vloge as $st => $ime_v):
        ?>
            <a href="?vloga=<?= $st ?>"
               class="gumb <?= $st === $vloga_zdaj ? '' : 'gumb-sekundarni' ?>"
               style="font-size:0.8rem">
                <?= htmlspecialchars($ime_v, ENT_QUOTES, 'UTF-8') ?> (<?= $st ?>)
            </a>
        <?php endforeach; ?>
    </div>
</div>

<div class="kartica">
    <h2>📦 Dostop modulov</h2>
    <?php if (empty($moduli)): ?>
        <p style="opacity:0.6">Ni modulov.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr><th>Modul</th><th>Kategorija</th><th>Min. vloga</th><th>Dostop</th><th></th></tr>
            </thead>
            <tbody>
            <?php foreach ($moduli as $m):
                $manifest  = $m['manifest'];
                $ime       = $manifest['modul']['ime'] ?? $manifest['ime'] ?? basename($m['pot']);
                $min_vloga = (int)($manifest['dostop']['minimalna_vloga'] ?? $manifest['vloga'] ?? 0);
                $dostop    = $vloga_zdaj >= $min_vloga;
                $modul_php = $m['pot'] . '/modul.php';
            ?>
                <tr>
                    <td><strong><?= htmlspecialchars($ime, ENT_QUOTES, 'UTF-8') ?></strong></td>
                    <td><?= htmlspecialchars($m['kategorija'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= $min_vloga ?></td>
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

<a href="../index.php" class="gumb gumb-sekundarni">← Nazaj na Bridge</a>

<?php
mini_izhod_noga();
