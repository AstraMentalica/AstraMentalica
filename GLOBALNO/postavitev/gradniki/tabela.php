<?php
/**
 * ---------------------------------------------------------
 * POT: GLOBALNO/render/elementi/tabela.php
 * v111 (27.5.2026 15:00)
 * ---------------------------------------------------------
 * OPIS: Tabela element – pasivni PHP fragment
 * ---------------------------------------------------------
 *
 * ODVISNOSTI:
 * - nobenih
 *
 * UPORABA:
 * - GLOBALNO/render/strani/*.php
 *
 * PARAMETRI:
 * - $glava (array) – seznam naslovov stolpcev
 * - $vrstice (array) – podatki (array of arrays)
 * - $prazno (string) – sporocilo ko ni podatkov
 *
 * PREPOVEDI:
 * - Brez business logike
 *
 * STATUS: Stabilno
 *
 * ZGODOVINA:
 * - v111: FAZA 20+ – implementacija
 *
 * ---------------------------------------------------------
 * AVTOR: AstraMentalica Mojster
 * ---------------------------------------------------------
 */

// Parametri
$glava = $glava ?? [];
$vrstice = $vrstice ?? [];
$prazno = $prazno ?? 'Ni podatkov za prikaz.';
$razred = $razred ?? '';
?>

<div class="tabela-ovoj <?= htmlspecialchars($razred) ?>">
    <table class="tabela">
        <?php if (!empty($glava)): ?>
        <thead>
            </table>
                <?php foreach ($glava as $stolpec): ?>
                <th><?= htmlspecialchars($stolpec) ?></th>
                <?php endforeach; ?>
            </table>
        </thead>
        <?php endif; ?>
        
        <tbody>
            <?php if (empty($vrstice)): ?>
                <tr class="tabela-prazno">
                    <td colspan="<?= count($glava) ?: 1 ?>"><?= htmlspecialchars($prazno) ?></td>
                </tr>
            <?php else: ?>
                <?php foreach ($vrstice as $vrstica): ?>
                <tr>
                    <?php if (is_array($vrstica)): ?>
                        <?php foreach ($vrstica as $celica): ?>
                        <td><?= htmlspecialchars($celica) ?></td>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <td colspan="<?= count($glava) ?: 1 ?>"><?= htmlspecialchars($vrstica) ?></td>
                    <?php endif; ?>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<style>
.tabela-ovoj {
    overflow-x: auto;
    border-radius: 15px;
    background: rgba(255, 255, 255, 0.03);
}

.tabela {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.9rem;
}

.tabela th,
.tabela td {
    padding: 0.75rem 1rem;
    text-align: left;
    border-bottom: 1px solid rgba(255, 255, 255, 0.05);
}

.tabela th {
    background: rgba(232, 200, 74, 0.1);
    color: #e8c84a;
    font-weight: 600;
}

.tabela tr:hover {
    background: rgba(255, 255, 255, 0.02);
}

.tabela-prazno td {
    text-align: center;
    color: #888;
    padding: 2rem;
}
</style>