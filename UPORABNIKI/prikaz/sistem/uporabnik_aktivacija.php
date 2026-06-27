<?php
/**
 * ---------------------------------------------------------
 * POT: UPORABNIKI/prikaz/aktivacija.php
 * v111 (27.5.2026 22:00)
 * ---------------------------------------------------------
 * OPIS: Aktivacija računa
 * ---------------------------------------------------------
 *
 * ODVISNOSTI:
 * - pot.php
 *
 * UPORABA:
 * - Ko uporabnik klikne aktivacijsko povezavo
 *
 * PREPOVEDI:
 * - Brez business logike
 *
 * STATUS: Stabilno
 *
 * ZGODOVINA:
 * - v111: FAZA 55 – implementacija
 *
 * ---------------------------------------------------------
 * AVTOR: AstraMentalica Mojster
 * ---------------------------------------------------------
 */

declare(strict_types=1);

$zeton = $_GET['zeton'] ?? '';
$status = $vsebina['status'] ?? 'uspeh';
$sporocilo = $vsebina['sporocilo'] ?? '';
$email = $vsebina['email'] ?? '';
?>

<div class="aktivacija-stran">
<div class="aktivacija-vsebina">
    <div class="aktivacija-okvir">
        <?php if ($status === 'uspeh'): ?>
            <div class="aktivacija-ikona">✅</div>
            <h1 class="aktivacija-naslov">Račun aktiviran!</h1>
            <p class="aktivacija-sporocilo"><?= htmlspecialchars($sporocilo) ?: 'Vaš račun je uspešno aktiviran.' ?></p>
            <div class="aktivacija-gumbi">
                <a href="?svet=UPORABNIKI&pot=prijava" class="gumb gumb-primaren">Prijava</a>
                <a href="?svet=GLOBALNO" class="gumb gumb-sekundaren">Domov</a>
            </div>
        <?php elseif ($status === 'napaka'): ?>
            <div class="aktivacija-ikona">❌</div>
            <h1 class="aktivacija-naslov">Aktivacija ni uspela</h1>
            <p class="aktivacija-sporocilo"><?= htmlspecialchars($sporocilo) ?: 'Povezava je neveljavna ali je račun že aktiviran.' ?></p>
            <div class="aktivacija-gumbi">
                <a href="?svet=UPORABNIKI&pot=pozabljeno_geslo" class="gumb gumb-sekundaren">Ponovno pošlji aktivacijo</a>
                <a href="?svet=GLOBALNO" class="gumb gumb-sekundaren">Domov</a>
            </div>
        <?php else: ?>
            <div class="aktivacija-ikona">⏳</div>
            <h1 class="aktivacija-naslov">Aktiviramo račun...</h1>
            <p>Prosim počakajte.</p>
        <?php endif; ?>
    </div>
</div>
</div>

<script nonce="<?= $vsebina['csp_nonce'] ?? '' ?>">
if (<?= json_encode($status === 'nakladanje') ?>) {
// Samodejna aktivacija
window.location.href = window.location.href + '&potrdi=1';
}
</script>

<style>
.aktivacija-stran {
display: flex;
justify-content: center;
align-items: center;
min-height: calc(100vh - 200px);
padding: 2rem;
text-align: center;
}

.aktivacija-okvir {
background: rgba(255, 255, 255, 0.05);
border-radius: 20px;
padding: 2.5rem;
max-width: 450px;
}

.aktivacija-ikona {
font-size: 4rem;
margin-bottom: 1rem;
}

.aktivacija-naslov {
color: #e8c84a;
font-size: 1.8rem;
margin-bottom: 1rem;
}

.aktivacija-sporocilo {
color: #aaa;
margin-bottom: 2rem;
}

.aktivacija-gumbi {
display: flex;
gap: 1rem;
justify-content: center;
}
</style>