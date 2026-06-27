<?php
/**
 * ---------------------------------------------------------
 * POT: GLOBALNO/render/elementi/obrazec.php
 * v111 (27.5.2026 15:00)
 * ---------------------------------------------------------
 * OPIS: Obrazec element – pasivni PHP fragment
 * ---------------------------------------------------------
 *
 * ODVISNOSTI:
 * - nobenih
 *
 * UPORABA:
 * - GLOBALNO/render/strani/*.php
 *
 * PARAMETRI:
 * - $akcija (string) – kam se obrazec poslje
 * - $metoda (string) – DOBI, OBJAVA
 * - $polja (array) – seznam polj v obrazcu
 * - $gumb (string) – besedilo na gumbu
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
$akcija = $akcija ?? '';
$metoda = $metoda ?? 'OBJAVA';
$polja = $polja ?? [];
$gumb = $gumb ?? 'Pošlji';
$razred = $razred ?? '';
?>

<form class="obrazec <?= htmlspecialchars($razred) ?>" action="<?= htmlspecialchars($akcija) ?>" method="<?= htmlspecialchars($metoda) ?>">
    <?php foreach ($polja as $polje): 
        $tip = $polje['tip'] ?? 'besedilo';
        $ime = $polje['ime'] ?? '';
        $oznaka = $polje['oznaka'] ?? '';
        $privzeto = $polje['privzeto'] ?? '';
        $zahtevano = $polje['zahtevano'] ?? false;
        $moznosti = $polje['moznosti'] ?? [];
    ?>
        <div class="obrazec-skupina">
            <?php if ($oznaka): ?>
            <label class="obrazec-oznaka" for="<?= htmlspecialchars($ime) ?>">
                <?= htmlspecialchars($oznaka) ?>
                <?php if ($zahtevano): ?>
                <span class="obrazec-zahtevano">*</span>
                <?php endif; ?>
            </label>
            <?php endif; ?>
            
            <?php if ($tip === 'besedilo'): ?>
                <input type="text" class="obrazec-vnos" id="<?= htmlspecialchars($ime) ?>" name="<?= htmlspecialchars($ime) ?>" value="<?= htmlspecialchars($privzeto) ?>" <?= $zahtevano ? 'required' : '' ?>>
                
            <?php elseif ($tip === 'geslo'): ?>
                <input type="password" class="obrazec-vnos" id="<?= htmlspecialchars($ime) ?>" name="<?= htmlspecialchars($ime) ?>" <?= $zahtevano ? 'required' : '' ?>>
                
            <?php elseif ($tip === 'email'): ?>
                <input type="email" class="obrazec-vnos" id="<?= htmlspecialchars($ime) ?>" name="<?= htmlspecialchars($ime) ?>" value="<?= htmlspecialchars($privzeto) ?>" <?= $zahtevano ? 'required' : '' ?>>
                
            <?php elseif ($tip === 'stevilka'): ?>
                <input type="number" class="obrazec-vnos" id="<?= htmlspecialchars($ime) ?>" name="<?= htmlspecialchars($ime) ?>" value="<?= htmlspecialchars($privzeto) ?>" <?= $zahtevano ? 'required' : '' ?>>
                
            <?php elseif ($tip === 'besedilo_daljse'): ?>
                <textarea class="obrazec-vnos" id="<?= htmlspecialchars($ime) ?>" name="<?= htmlspecialchars($ime) ?>" rows="4" <?= $zahtevano ? 'required' : '' ?>><?= htmlspecialchars($privzeto) ?></textarea>
                
            <?php elseif ($tip === 'izbira'): ?>
                <select class="obrazec-vnos" id="<?= htmlspecialchars($ime) ?>" name="<?= htmlspecialchars($ime) ?>" <?= $zahtevano ? 'required' : '' ?>>
                    <?php foreach ($moznosti as $moznost): ?>
                        <option value="<?= htmlspecialchars($moznost['vrednost']) ?>" <?= $moznost['vrednost'] == $privzeto ? 'selected' : '' ?>>
                            <?= htmlspecialchars($moznost['oznaka'] ?? $moznost['vrednost']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                
            <?php elseif ($tip === 'potrdilo'): ?>
                <label class="obrazec-potrdilo">
                    <input type="checkbox" name="<?= htmlspecialchars($ime) ?>" value="1" <?= $privzeto ? 'checked' : '' ?> <?= $zahtevano ? 'required' : '' ?>>
                    <span><?= htmlspecialchars($oznaka) ?></span>
                </label>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
    
    <div class="obrazec-gumbi">
        <button type="submit" class="gumb gumb-primaren"><?= htmlspecialchars($gumb) ?></button>
    </div>
</form>

<style>
.obrazec-skupina {
    margin-bottom: 1.25rem;
}

.obrazec-oznaka {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
    color: #d4c5a9;
}

.obrazec-zahtevano {
    color: #e8c84a;
}

.obrazec-vnos {
    width: 100%;
    padding: 0.75rem;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid #2a2a4a;
    border-radius: 8px;
    color: #d4c5a9;
    font-size: 1rem;
    transition: border-color 0.3s;
}

.obrazec-vnos:focus {
    outline: none;
    border-color: #e8c84a;
}

.obrazec-vnos::placeholder {
    color: #555;
}

.obrazec-potrdilo {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
    color: #d4c5a9;
}

.obrazec-potrdilo input {
    width: 1.2rem;
    height: 1.2rem;
    cursor: pointer;
}

.obrazec-gumbi {
    margin-top: 1.5rem;
}
</style>