<?php
/**
 * ============================================================
 * POT: GLOBALNO/render/strani/javno/registracija.php
 * 📅 VERZIJA: v115 (14.6.2026 18:00)
 * ============================================================
 *
 * 🏛️ NIVO: GLOBALNO (RENDER)
 *
 * 📰 NAMEN:
 *     Registracijska stran – pasiven prikaz obrazca.
 *
 * ✅ DOVOLJENO:
 *     - echo, HTML
 *
 * 🚫 PREPOVEDI:
 *     - Brez session_start()
 *     - Brez direktnih DB klicev
 *     - Brez $_POST obdelave
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
 *     globalno, render, registracija
 * ============================================================
 */
declare(strict_types=1);

defined('SISTEM_VARNOST') or die('Direkten dostop ni dovoljen.');

$napaka = $vsebina['napaka'] ?? '';
$sporocilo = $vsebina['sporocilo'] ?? '';
$ime = $vsebina['ime'] ?? '';
$email = $vsebina['email'] ?? '';
?>

<div class="registracija-stran">
    <div class="registracija-vsebina">
        <div class="registracija-okvir">
            <div class="registracija-glava">
                <div class="registracija-ikona">📝</div>
                <h1 class="registracija-naslov">Registracija</h1>
                <p class="registracija-podnaslov">Ustvarite nov uporabniški račun</p>
            </div>

            <?php if ($napaka): ?>
                <div class="sporocilo sporocilo-napaka"><?= htmlspecialchars($napaka) ?></div>
            <?php endif; ?>

            <?php if ($sporocilo): ?>
                <div class="sporocilo sporocilo-uspeh"><?= htmlspecialchars($sporocilo) ?></div>
            <?php endif; ?>

            <form method="post" action="?svet=SISTEM&amp;akcija=registracija" class="registracija-obrazec">
                <div class="obrazec-skupina">
                    <label class="obrazec-oznaka" for="ime">Ime</label>
                    <input type="text"
                           id="ime"
                           name="ime"
                           class="vnos"
                           value="<?= htmlspecialchars($ime) ?>"
                           required
                           minlength="2">
                </div>

                <div class="obrazec-skupina">
                    <label class="obrazec-oznaka" for="email">Elektronski naslov</label>
                    <input type="email"
                           id="email"
                           name="email"
                           class="vnos"
                           value="<?= htmlspecialchars($email) ?>"
                           required>
                </div>

                <div class="obrazec-skupina">
                    <label class="obrazec-oznaka" for="geslo">Geslo</label>
                    <div class="geslo-ovoj">
                        <input type="password"
                               id="geslo"
                               name="geslo"
                               class="vnos"
                               required
                               minlength="8">
                        <button type="button"
                                class="geslo-prikaz"
                                id="gesloPrikaz"
                                aria-label="Pokaži/skrij geslo">👁</button>
                    </div>
                    <small class="obrazec-namig">Vsaj 8 znakov</small>
                </div>

                <div class="obrazec-skupina">
                    <label class="obrazec-oznaka" for="geslo_ponovno">Ponovite geslo</label>
                    <input type="password"
                           id="geslo_ponovno"
                           name="geslo_ponovno"
                           class="vnos"
                           required
                           minlength="8">
                </div>

                <div class="obrazec-skupina obrazec-potrdilo">
                    <label class="obrazec-potrdilo-oznaka">
                        <input type="checkbox" name="pogoji" value="1" required>
                        <span>Strinjam se s <a href="?svet=GLOBALNO&amp;pot=pogoji" target="_blank">pogoji uporabe</a></span>
                    </label>
                </div>

                <button type="submit" class="gumb gumb-primaren registracija-gumb">Ustvari račun</button>

                <div class="registracija-povezave">
                    <a href="?svet=UPORABNIKI&amp;pot=prijava" class="registracija-povezava">
                        Že imate račun? Prijavite se
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

    const geslo = document.getElementById('geslo');
    const ponovno = document.getElementById('geslo_ponovno');
    if (geslo && ponovno) {
        function preveriUjemanje() {
            if (geslo.value !== ponovno.value && ponovno.value.length > 0) {
                ponovno.style.borderColor = '#f44336';
            } else {
                ponovno.style.borderColor = '';
            }
        }
        geslo.addEventListener('input', preveriUjemanje);
        ponovno.addEventListener('input', preveriUjemanje);
    }
})();
</script>

<style>
.registracija-stran {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: calc(100vh - 200px);
    padding: 2rem;
}

.registracija-vsebina {
    width: 100%;
    max-width: 500px;
}

.registracija-okvir {
    background: var(--kartica, rgba(255,255,255,0.05));
    border-radius: 20px;
    padding: 2rem;
    border: 1px solid var(--rob, rgba(255,255,255,0.07));
}

.registracija-glava {
    text-align: center;
    margin-bottom: 2rem;
}

.registracija-ikona {
    font-size: 3rem;
    margin-bottom: 0.5rem;
}

.registracija-naslov {
    font-size: 1.8rem;
    color: var(--zlata, #e8c84a);
    margin-bottom: 0.25rem;
}

.registracija-podnaslov {
    color: var(--besedilo-d, #8a7f70);
    font-size: 0.9rem;
}

.obrazec-namig {
    display: block;
    margin-top: 0.25rem;
    font-size: 0.75rem;
    color: var(--besedilo-d, #8a7f70);
}

.registracija-gumb {
    width: 100%;
    margin-top: 0.5rem;
}

.registracija-povezave {
    text-align: center;
    margin-top: 1.5rem;
}

.registracija-povezava {
    color: var(--besedilo-d, #8a7f70);
    text-decoration: none;
    font-size: 0.85rem;
}

.registracija-povezava:hover {
    color: var(--zlata, #e8c84a);
}