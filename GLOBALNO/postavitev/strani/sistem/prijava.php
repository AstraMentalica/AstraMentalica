<?php
/**
 * ============================================================
 * POT: GLOBALNO/postavitev/strani/sistem/prijava.php
 * 📅 VERZIJA: v114 (9.6.2026 18:00)
 * ============================================================
 *
 * 🏛️ NIVO: GLOBALNO (render/strani)
 * 📰 NAMEN: Prijavna stran – email/geslo + Google OAuth.
 *           Pasiven prikaz. Vse akcije gredo na SISTEM/api.php.
 * ✅ DOVOLJENO: echo, HTML
 * 🚫 PREPOVEDI: Brez business logike, brez SQL, brez session_start()
 * 📌 STATUS: Stabilno
 * 📅 ZGODOVINA: - v114: implementacija z Google OAuth gumbom
 * 👤 AVTOR: AstraMentalica Mojster
 * 🌐 JEZIK: sl
 * 🏷️ OZNAKE: globalno, render, prijava, oauth
 * ============================================================
 */

declare(strict_types=1);

defined('SISTEM_VARNOST') or die('Direkten dostop ni dovoljen.');

$napaka    = $vsebina['napaka']    ?? '';
$sporocilo = $vsebina['sporocilo'] ?? '';
$csrf      = $vsebina['csrf']      ?? '';

// Google OAuth URL gradi storitev — tukaj ga samo prikažemo
$googleUrl = $vsebina['google_url'] ?? '/api?akcija=oauth_google_zacni';
?>

<div class="prijava-ovoj">
    <div class="prijava-kartica">

        <!-- Logo -->
        <div class="prijava-logo">
            <span class="prijava-logo-ikona">✦</span>
            <span class="prijava-logo-ime">AstraMentalica</span>
        </div>

        <h1 class="prijava-naslov">Dobrodošel nazaj</h1>
        <p class="prijava-podnaslov">Nadaljuj svojo pot</p>

        <!-- Sporočila -->
        <?php if ($napaka): ?>
            <div class="sporocilo sporocilo-napaka"><?= htmlspecialchars($napaka) ?></div>
        <?php endif; ?>
        <?php if ($sporocilo): ?>
            <div class="sporocilo sporocilo-info"><?= htmlspecialchars($sporocilo) ?></div>
        <?php endif; ?>

        <!-- Google OAuth -->
        <a href="<?= htmlspecialchars($googleUrl) ?>" class="gumb-google">
            <svg class="gumb-google-ikona" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
            </svg>
            Nadaljuj z Google
        </a>

        <!-- Ločilo -->
        <div class="locilo">
            <span class="locilo-crta"></span>
            <span class="locilo-beseda">ali</span>
            <span class="locilo-crta"></span>
        </div>

        <!-- Email / Geslo forma -->
        <form class="obrazec" method="POST" action="/api?akcija=prijava" novalidate>
            <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf) ?>">
            <input type="hidden" name="preusmeritev" value="<?= htmlspecialchars($_GET['preusmeritev'] ?? '') ?>">

            <div class="obrazec-skupina">
                <label class="obrazec-oznaka" for="email">E-pošta</label>
                <input class="vnos"
                       type="email"
                       id="email"
                       name="email"
                       placeholder="janez@primer.si"
                       autocomplete="email"
                       required>
            </div>

            <div class="obrazec-skupina">
                <div class="geslo-glava">
                    <label class="obrazec-oznaka" for="geslo">Geslo</label>
                    <a href="?svet=pozabljeno_geslo" class="pozabljeno">Pozabljeno geslo?</a>
                </div>
                <div class="geslo-ovoj">
                    <input class="vnos"
                           type="password"
                           id="geslo"
                           name="geslo"
                           placeholder="••••••••"
                           autocomplete="current-password"
                           required>
                    <button type="button" class="geslo-prikaz" id="gesloPrikaz" aria-label="Pokaži/skrij geslo">
                        👁
                    </button>
                </div>
            </div>

            <button type="submit" class="gumb gumb-primarni gumb-prijava">
                Prijava
            </button>
        </form>

        <!-- Registracija -->
        <p class="prijava-registracija">
            Nimaš računa?
            <a href="?svet=registracija">Ustvari račun →</a>
        </p>

    </div>

    <!-- Ozadje s citati -->
    <div class="prijava-ozadje" aria-hidden="true">
        <div class="prijava-citat" id="prijavaCitat">
            "Vsaka pot se začne v tišini."
        </div>
    </div>
</div>

<style>
/* ============================================================
 * PRIJAVA STRANI
 * ============================================================ */

/* Ovoj – celoten zaslon */
.prijava-ovoj {
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: calc(100vh - var(--glava-visina, 60px));
    padding: var(--razmik-xl);
    position: relative;
}

/* Kartica z obrazcem */
.prijava-kartica {
    width: 100%;
    max-width: 400px;
    background: var(--kartica);
    border: 1px solid var(--rob);
    border-radius: var(--rob-xl);
    padding: 2.5rem 2rem;
    position: relative;
    z-index: 1;
    backdrop-filter: blur(10px);
}

/* Logo */
.prijava-logo {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    margin-bottom: 1.75rem;
}

.prijava-logo-ikona {
    font-size: 1.8rem;
    color: var(--zlata);
    text-shadow: 0 0 20px rgba(232, 200, 74, 0.5);
}

.prijava-logo-ime {
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--zlata);
    letter-spacing: 0.02em;
}

/* Naslovi */
.prijava-naslov {
    font-family: var(--pisava-naslov);
    font-size: 1.6rem;
    color: var(--besedilo-s);
    text-align: center;
    margin-bottom: 0.4rem;
    font-weight: 600;
}

.prijava-podnaslov {
    color: var(--besedilo-d);
    text-align: center;
    font-size: 0.9rem;
    margin-bottom: 1.75rem;
}

/* Google gumb */
.gumb-google {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.75rem;
    width: 100%;
    padding: 0.7rem 1rem;
    background: rgba(255, 255, 255, 0.06);
    border: 1px solid rgba(255, 255, 255, 0.12);
    border-radius: var(--rob-m);
    color: var(--besedilo-s);
    font-size: 0.9rem;
    font-weight: 500;
    text-decoration: none;
    cursor: pointer;
    transition: all 0.2s;
    margin-bottom: 1.25rem;
}

.gumb-google:hover {
    background: rgba(255, 255, 255, 0.1);
    border-color: rgba(255, 255, 255, 0.2);
    transform: translateY(-1px);
    color: var(--besedilo-s);
}

.gumb-google:active { transform: translateY(0); }

.gumb-google-ikona {
    width: 20px;
    height: 20px;
    flex-shrink: 0;
}

/* Ločilo */
.locilo {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 1.25rem;
}

.locilo-crta {
    flex: 1;
    height: 1px;
    background: var(--rob);
}

.locilo-beseda {
    font-size: 0.75rem;
    color: var(--besedilo-m);
    white-space: nowrap;
}

/* Geslo field */
.geslo-glava {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 0.4rem;
}

.geslo-glava .obrazec-oznaka { margin: 0; }

.pozabljeno {
    font-size: 0.75rem;
    color: var(--besedilo-d);
    text-decoration: none;
    transition: color 0.2s;
}

.pozabljeno:hover { color: var(--zlata); }

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
    font-size: 0.9rem;
    color: var(--besedilo-d);
    padding: 0.25rem;
    transition: color 0.2s;
    line-height: 1;
}

.geslo-prikaz:hover { color: var(--zlata); }

/* Prijava gumb */
.gumb-prijava {
    width: 100%;
    justify-content: center;
    padding: 0.7rem;
    font-size: 0.95rem;
    margin-top: 0.25rem;
}

/* Registracija */
.prijava-registracija {
    text-align: center;
    font-size: 0.82rem;
    color: var(--besedilo-d);
    margin: 1.25rem 0 0;
}

.prijava-registracija a {
    color: var(--zlata);
    font-weight: 500;
}

/* Ozadje z citati */
.prijava-ozadje {
    position: fixed;
    bottom: 2rem;
    left: 50%;
    transform: translateX(-50%);
    z-index: 0;
}

.prijava-citat {
    font-style: italic;
    color: var(--besedilo-m);
    font-size: 0.85rem;
    text-align: center;
    max-width: 400px;
    animation: pojavi 1s ease;
}

@keyframes pojavi {
    from { opacity: 0; transform: translateY(8px); }
    to   { opacity: 1; transform: translateY(0); }
}

/* Sporočila */
.sporocilo { margin-bottom: 1rem; }

@media (max-width: 500px) {
    .prijava-kartica { padding: 2rem 1.25rem; border-radius: var(--rob-l); }
    .prijava-ovoj    { padding: var(--razmik-m); align-items: flex-start; padding-top: 2rem; }
}
</style>

<script>
(function() {
    // Pokaži/skrij geslo
    var gumb  = document.getElementById('gesloPrikaz');
    var polje = document.getElementById('geslo');

    if (gumb && polje) {
        gumb.addEventListener('click', function() {
            var jeGeslo = polje.type === 'password';
            polje.type  = jejeGeslo ? 'text' : 'password';
            gumb.textContent = jejeGeslo ? '🙈' : '👁';
        });
    }

    // Rotirajoči citati
    var citati = [
        '"Vsaka pot se začne v tišini."',
        '"Zvezde so ogledalo tvoje duše."',
        '"Zavedanje je prvi korak k svobodi."',
        '"V temi blestijo najlepše zvezde."',
        '"Pot se razkrije tistemu, ki hodi."'
    ];
    var el = document.getElementById('prijavaCitat');
    if (el) {
        var i = Math.floor(Math.random() * citati.length);
        el.textContent = citati[i];
    }
})();
</script>
