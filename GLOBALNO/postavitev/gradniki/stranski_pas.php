<?php
/**
 * ---------------------------------------------------------
 * POT: GLOBALNO/postavitev/gradniki/stranski_pas.php
 * v111 (27.5.2026 15:00)
 * ---------------------------------------------------------
 * OPIS: Stranski pas (sidebar) element – pasivni PHP fragment
 * ---------------------------------------------------------
 *
 * ODVISNOSTI:
 * - nobenih
 *
 * UPORABA:
 * - GLOBALNO/render/postavitev/*.php
 *
 * PARAMETRI:
 * - $elementi (array) – seznam elementov v stranskem pasu
 * - $pozicija (string) – levo, desno
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
$elementi = $elementi ?? [];
$pozicija = $pozicija ?? 'levo';
$razred = $razred ?? '';
?>

<aside class="stranski-pas stranski-pas-<?= htmlspecialchars($pozicija) ?> <?= htmlspecialchars($razred) ?>">
    <?php foreach ($elementi as $element): 
        $tip = $element['tip'] ?? 'besedilo';
        $vsebina = $element['vsebina'] ?? '';
        $naslov = $element['naslov'] ?? '';
    ?>
        <div class="stranski-pas-element">
            <?php if ($naslov): ?>
            <h4 class="stranski-pas-naslov"><?= htmlspecialchars($naslov) ?></h4>
            <?php endif; ?>
            
            <?php if ($tip === 'meni' && is_array($vsebina)): ?>
                <ul class="stranski-pas-meni">
                    <?php foreach ($vsebina as $povezava): ?>
                        <li>
                            <a href="<?= htmlspecialchars($povezava['pot'] ?? '#') ?>">
                                <?php if (isset($povezava['ikona'])): ?>
                                    <span class="meni-ikona"><?= htmlspecialchars($povezava['ikona']) ?></span>
                                <?php endif; ?>
                                <?= htmlspecialchars($povezava['besedilo'] ?? $povezava) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <div class="stranski-pas-vsebina"><?= $vsebina ?></div>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</aside>

<style>
.stranski-pas {
    background: rgba(255, 255, 255, 0.03);
    border-radius: 15px;
    padding: 1.25rem;
}

.stranski-pas-levo {
    margin-right: 1.5rem;
}

.stranski-pas-desno {
    margin-left: 1.5rem;
}

.stranski-pas-element {
    margin-bottom: 1.5rem;
}

.stranski-pas-element:last-child {
    margin-bottom: 0;
}

.stranski-pas-naslov {
    color: #e8c84a;
    font-size: 1rem;
    margin-bottom: 0.75rem;
    padding-bottom: 0.5rem;
    border-bottom: 1px solid rgba(232, 200, 74, 0.2);
}

.stranski-pas-meni {
    list-style: none;
    padding: 0;
    margin: 0;
}

.stranski-pas-meni li {
    margin-bottom: 0.5rem;
}

.stranski-pas-meni a {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.4rem 0.75rem;
    border-radius: 8px;
    color: #d4c5a9;
    text-decoration: none;
    transition: background 0.3s;
}

.stranski-pas-meni a:hover {
    background: rgba(232, 200, 74, 0.1);
    color: #e8c84a;
}

.meni-ikona {
    font-size: 1rem;
}

.stranski-pas-vsebina {
    color: #aaa;
    font-size: 0.9rem;
    line-height: 1.5;
}

@media (max-width: 768px) {
    .stranski-pas-levo,
    .stranski-pas-desno {
        margin: 1rem 0;
    }
}
</style>
✅ GLOBALNO/render/elementi/ ZAKLJUČEN (6/6)
Datoteka	Status
gumb.php	✅ NAPISANO
kartica.php	✅ NAPISANO
tabela.php	✅ NAPISANO
obrazec.php	✅ NAPISANO
modal.php	✅ NAPISANO
stranski_pas.php	✅ NAPISANO