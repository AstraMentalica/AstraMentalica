<?php
/**
 * ============================================================
 * POT: GLOBALNO/render/strani/javno/prijava.php
 * 📅 VERZIJA: v115 (14.6.2026 18:00)
 * ============================================================
 *
 * 🏛️ NIVO: GLOBALNO (RENDER)
 *
 * 📰 NAMEN:
 *     Prijavna stran – pasiven prikaz obrazca.
 *     Nima business logike, samo prikaz.
 *
 * ✅ DOVOLJENO:
 *     - echo, HTML
 *
 * 🚫 PREPOVEDI:
 *     - Brez session_start()
 *     - Brez direktnih DB klicev
 *     - Brez $_POST obdelave
 *     - Brez require_once SISTEM/ ali ADAPTER/
 *
 * 📌 STATUS:
 *     Stabilno
 *
 * 📅 ZGODOVINA:
 *     - v115: uskladitev s Header Standard v115
 *
 * 👤 AVTOR:
 *     AstraMentalica Mojster
 *
 * 🌐 JEZIK:
 *     sl
 *
 * 🏷️ OZNAKE:
 *     globalno, render, prijava
 * ============================================================
 */
declare(strict_types=1);

defined('SISTEM_VARNOST') or die('Direkten dostop ni dovoljen.');

$napaka    = $vsebina['napaka']    ?? '';
$sporocilo = $vsebina['sporocilo'] ?? '';
$email     = $vsebina['email']     ?? '';
$redirect  = $vsebina['redirect']  ?? '?svet=GLOBALNO';
?>

<div class="prijava-stran">
    <div class="prijava-vsebina">
        <div class="prijava-okvir">
            <div class="prijava-glava">
                <div class="prijava-ikona">🔑</div>
                <h1 class="prijava-naslov">Prijava</h1>
                <p class="prijava-podnaslov">Vpišite se v svoj račun</p>
            </div>

            <?php if ($napaka): ?>
                <div class="sporocilo sporocilo-napaka"><?= htmlspecialchars($napaka) ?></div>
            <?php endif; ?>

            <?php if ($sporocilo): ?>
                <div class="sporocilo sporocilo-uspeh"><?= htmlspecialchars($sporocilo) ?></div>
            <?php endif; ?>

            <form method="post" action="?svet=SISTEM&amp;akcija=prijava" class="prijava-obrazec">
                <input type="hidden" name="redirect" value="<?= htmlspecialchars($redirect) ?>">

                <div class="obrazec-skupina">
                    <label class="obrazec-oznaka" for="email">Elektronski naslov</label>
                    <input type="email"
                           id="email"
                           name="email"
                           class="vnos"
                           value="<?= htmlspecialchars($email) ?>"
                           required
                           autofocus>
                </div>

                <div class="obrazec-skupina">
                    <label class="obrazec-oznaka" for="geslo">Geslo</label>
                    <div class="geslo-ovoj">
                        <input type="password"
                               id="geslo"
                               name="geslo"
                               class="vnos"
                               required>
                        <button type="button"
                                class="geslo-prikaz"
                                id="gesloPrikaz"
                                aria-label="Pokaži/skrij geslo">👁</button>
                    </div>
                </div>

                <div class="obrazec-skupina obrazec-potrdilo">
                    <label class="obrazec-potrdilo-oznaka">
                        <input type="checkbox" name="zapomni_se" value="1">
                        <span>Zapomni si me</span>
                    </label>
                </div>

                <button type="submit" class="gumb gumb-primaren prijava-gumb">Prijava</button>

                <div class="prijava-povezave">
                    <a href="?svet=UPORABNIKI&amp;pot=registracija" class="prijava-povezava">
                        Še nimate računa? Registrirajte se
                    </a>
                    <a href="?svet=UPORABNIKI&amp;pot=pozabljeno_geslo" class="prijava-povezava">
                        Pozabljeno geslo?
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
(function() {
    const gumb = document.getElementById('gesloPrikaz');
    const polje = document.getElementById('geslo');
    if (gumb && polje) {
        gumb.addEventListener('click', function() {
            const tip = polje.type === 'password' ? 'text' : 'password';
            polje.type = tip;
            gumb.textContent = tip === 'password' ? '👁' : '🙈';
        });
    }
})();
</script>

<style>
.prijava-stran {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: calc(100vh - 200px);
    padding: 2rem;
}

.prijava-vsebina {
    width: 100%;
    max-width: 450px;
}

.prijava-okvir {
    background: var(--kartica, rgba(255,255,255,0.05));
    border-radius: 20px;
    padding: 2rem;
    border: 1px solid var(--rob, rgba(255,255,255,0.07));
}

.prijava-glava {
    text-align: center;
    margin-bottom: 2rem;
}

.prijava-ikona {
    font-size: 3rem;
    margin-bottom: 0.5rem;
}

.prijava-naslov {
    font-size: 1.8rem;
    color: var(--zlata, #e8c84a);
    margin-bottom: 0.25rem;
}

.prijava-podnaslov {
    color: var(--besedilo-d, #8a7f70);
    font-size: 0.9rem;
}

.geslo-ovoj {
    position: relative;
}

.geslo-ovoj .vnos {
    padding-right: 3rem;
}

.geslo-prikaz {
    position: absolute;
    right: 0.75rem;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    cursor: pointer;
    font-size: 1rem;
    color: var(--besedilo-d, #8a7f70);
    padding: 0.25rem;
}

.prijava-gumb {
    width: 100%;
    margin-top: 0.5rem;
}

.prijava-povezave {
    display: flex;
    justify-content: space-between;
    margin-top: 1.5rem;
    font-size: 0.85rem;
}

.prijava-povezava {
    color: var(--besedilo-d, #8a7f70);
    text-decoration: none;
}

.prijava-povezava:hover {
    color: var(--zlata, #e8c84a);
}

.sporocilo {
    padding: 0.75rem;
    border-radius: 8px;
    margin-bottom: 1rem;
    font-size: 0.9rem;
}

.sporocilo-napaka {
    background: var(--rdeca-dim, rgba(244,67,54,0.12));
    color: var(--rdeca, #f44336);
    border: 1px solid rgba(244,67,54,0.25);
}

.sporocilo-uspeh {
    background: var(--zelena-dim, rgba(76,175,80,0.12));
    color: var(--zelena, #4caf50);
    border: 1px solid rgba(76,175,80,0.25);
}

.obrazec-skupina {
    margin-bottom: 1rem;
}

.obrazec-oznaka {
    display: block;
    margin-bottom: 0.4rem;
    font-size: 0.85rem;
    color: var(--besedilo-d, #8a7f70);
}

.vnos {
    width: 100%;
    padding: 0.6rem 1rem;
    background: var(--kartica, rgba(255,255,255,0.05));
    border: 1px solid var(--rob, rgba(255,255,255,0.07));
    border-radius: 10px;
    color: var(--besedilo, #d4c5a9);
    font-size: 0.9rem;
}

.vnos:focus {
    outline: none;
    border-color: var(--zlata, #e8c84a);
}

.obrazec-potrdilo {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.obrazec-potrdilo-oznaka {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
    font-size: 0.85rem;
}

.gumb {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 0.6rem 1.2rem;
    border-radius: 30px;
    font-size: 0.9rem;
    cursor: pointer;
    border: none;
    transition: all 0.2s;
}

.gumb-primaren {
    background: var(--zlata, #e8c84a);
    color: #0a0a1a;
    font-weight: 600;
}

.gumb-primaren:hover {
    opacity: 0.9;
    transform: translateY(-1px);
}