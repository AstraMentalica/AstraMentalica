<?php
/**
 * ============================================================
 * POT: UPORABNIKI/prikaz/sistem/uporabnik_prijava.php
 * 📅 VERZIJA: v116 (27.6.2026)
 * ============================================================
 *
 * 🏛️ NIVO: UPORABNIKI (prikaz)
 *
 * 📰 NAMEN:
 *     Prijavna stran z email/geslo in Google OAuth.
 *     Popolnoma predelan dizajn.
 *
 * 📡 PRIČAKUJE IZ $vsebina:
 *     - $vsebina['napaka']       = string
 *     - $vsebina['sporocilo']    = string
 *     - $vsebina['email']        = string
 *     - $vsebina['google_url']   = ?string (če je Google konfiguriran)
 *     - $vsebina['csp_nonce']    = string
 *     - $vsebina['redirect']     = string
 *
 * 🚫 PREPOVEDI:
 *     - Brez business logike
 *     - Brez direktnega branja PODATKI/
 *
 * 📌 STATUS:
 *     Popravljeno
 *
 * 👤 AVTOR:
 *     Mavis / AstraMentalica
 *
 * 🌐 JEZIK:
 *     sl
 * ============================================================
 */

declare(strict_types=1);

$napaka      = $vsebina['napaka'] ?? '';
$sporocilo   = $vsebina['sporocilo'] ?? '';
$email       = $vsebina['email'] ?? '';
$googleUrl   = $vsebina['google_url'] ?? null;
$redirect    = $vsebina['redirect'] ?? '?svet=GLOBALNO';
$cspNonce    = $vsebina['csp_nonce'] ?? '';
?>

<div class="prijava-stran">
    <div class="prijava-ozadje">
        <div class="zvezde-male"></div>
    </div>

    <div class="prijava-vsebina">
        <div class="prijava-okvir">
            <!-- Logo / Header -->
            <div class="prijava-glava">
                <a href="?svet=GLOBALNO" class="prijava-logo">
                    <span class="prijava-logo-ikona">✦</span>
                    <span class="prijava-logo-ime">AstraMentalica</span>
                </a>
            </div>

            <!-- Naslov -->
            <div class="prijava-naslov-sekcija">
                <h1 class="prijava-naslov">Dobrodošli nazaj</h1>
                <p class="prijava-podnaslov">Vpišite se v svoj čarobni račun</p>
            </div>

            <!-- Sporočila -->
            <?php if ($napaka): ?>
                <div class="sporocilo sporocilo-napaka">
                    <span>⚠️</span>
                    <span><?= htmlspecialchars($napaka) ?></span>
                </div>
            <?php endif; ?>

            <?php if ($sporocilo): ?>
                <div class="sporocilo sporocilo-uspeh">
                    <span>✨</span>
                    <span><?= htmlspecialchars($sporocilo) ?></span>
                </div>
            <?php endif; ?>

            <!-- Google gumb -->
            <?php if ($googleUrl): ?>
            <a href="<?= htmlspecialchars($googleUrl) ?>" class="gumb-google">
                <svg class="google-ikona" viewBox="0 0 24 24" width="20" height="20">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                </svg>
                <span>Nadaljuj z Google</span>
            </a>

            <div class="prijava-lociilo">
                <span>ali</span>
            </div>
            <?php endif; ?>

            <!-- Email forma -->
            <form method="post" action="?svet=SISTEM&amp;akcija=prijava" class="prijava-obrazec">
                <input type="hidden" name="redirect" value="<?= htmlspecialchars($redirect) ?>">

                <div class="obrazec-skupina">
                    <label for="email" class="obrazec-oznaka">Elektronski naslov</label>
                    <input type="email"
                           id="email"
                           name="email"
                           class="vnos"
                           value="<?= htmlspecialchars($email) ?>"
                           required
                           autofocus
                           placeholder="vas@primer.com"
                           autocomplete="email">
                </div>

                <div class="obrazec-skupina">
                    <label for="geslo" class="obrazec-oznaka">Geslo</label>
                    <input type="password"
                           id="geslo"
                           name="geslo"
                           class="vnos"
                           required
                           placeholder="••••••••"
                           autocomplete="current-password">
                </div>

                <div class="obrazec-vrstica">
                    <label class="obrazec-checkbox">
                        <input type="checkbox" name="zapomni_se" value="1">
                        <span>Zapomni si me</span>
                    </label>
                    <a href="?svet=pozabljeno_geslo" class="pozabljeno-geslo">
                        Pozabljeno geslo?
                    </a>
                </div>

                <button type="submit" class="gumb gumb-primarni prijava-gumb">
                    Prijava
                </button>
            </form>

            <!-- Registracija link -->
            <div class="prijava-noga">
                <p>
                    Še nimate računa?
                    <a href="?svet=registracija" class="prijava-povezava">
                        Ustvarite ga brezplačno
                    </a>
                </p>
            </div>
        </div>
    </div>
</div>

<style>
/* ============================================================
 * PRIJAVNA STRAN
 * ============================================================ */
.prijava-stran {
    min-height: calc(100vh - var(--glava-visina));
    display: flex;
    align-items: center;
    justify-content: center;
    padding: var(--razmik-xl);
    position: relative;
    overflow: hidden;
}

.prijava-ozadje {
    position: absolute;
    inset: 0;
    pointer-events: none;
}

.zvezde-male {
    position: absolute;
    inset: 0;
    background-image:
        radial-gradient(1px 1px at 10px 20px, var(--zlata), transparent),
        radial-gradient(1px 1px at 50px 60px, rgba(255,255,255,0.6), transparent),
        radial-gradient(1px 1px at 100px 30px, var(--besedilo), transparent),
        radial-gradient(1px 1px at 150px 80px, var(--modra), transparent);
    background-size: 180px 120px;
    opacity: 0.4;
}

.prijava-vsebina {
    position: relative;
    width: 100%;
    max-width: 420px;
}

.prijava-okvir {
    background: var(--povrsina);
    border: 1px solid var(--rob);
    border-radius: var(--rob-xl);
    padding: var(--razmik-2xl);
    box-shadow: var(--senca-l);
}

/* Header */
.prijava-glava {
    text-align: center;
    margin-bottom: var(--razmik-xl);
}

.prijava-logo {
    display: inline-flex;
    align-items: center;
    gap: var(--razmik-s);
    text-decoration: none;
    color: var(--zlata);
}

.prijava-logo-ikona {
    font-size: 1.5rem;
    text-shadow: 0 0 15px rgba(232, 200, 74, 0.5);
}

.prijava-logo-ime {
    font-family: var(--pisava-naslov);
    font-size: var(--velikost-l);
    font-weight: var(--teza-krepka);
}

/* Naslov */
.prijava-naslov-sekcija {
    text-align: center;
    margin-bottom: var(--razmik-xl);
}

.prijava-naslov {
    font-size: var(--velikost-2xl);
    margin-bottom: var(--razmik-xs);
}

.prijava-podnaslov {
    color: var(--besedilo-d);
    font-size: var(--velikost-m);
}

/* Google gumb */
.gumb-google {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: var(--razmik-m);
    width: 100%;
    padding: 0.75rem 1.5rem;
    background: white;
    border: none;
    border-radius: var(--rob-pill);
    color: #3c4043;
    font-size: var(--velikost-m);
    font-weight: var(--teza-srednja);
    text-decoration: none;
    cursor: pointer;
    transition: all var(--prehod);
    margin-bottom: var(--razmik-m);
}

.gumb-google:hover {
    background: #f8f9fa;
    box-shadow: 0 2px 8px rgba(0,0,0,0.2);
    transform: translateY(-1px);
}

.google-ikona {
    flex-shrink: 0;
}

/* Ločilnik */
.prijava-lociilo {
    display: flex;
    align-items: center;
    gap: var(--razmik-m);
    margin: var(--razmik-l) 0;
    color: var(--besedilo-m);
    font-size: var(--velikost-s);
}

.prijava-lociilo::before,
.prijava-lociilo::after {
    content: '';
    flex: 1;
    height: 1px;
    background: var(--rob);
}

/* Obrazec */
.prijava-obrazec {
    display: flex;
    flex-direction: column;
    gap: var(--razmik-m);
}

.obrazec-skupina {
    display: flex;
    flex-direction: column;
    gap: var(--razmik-xs);
}

.obrazec-oznaka {
    font-size: var(--velikost-s);
    color: var(--besedilo-d);
    font-weight: var(--teza-srednja);
}

.obrazec-vrstica {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: var(--razmik-s);
}

.obrazec-checkbox {
    display: flex;
    align-items: center;
    gap: var(--razmik-s);
    font-size: var(--velikost-s);
    color: var(--besedilo-d);
    cursor: pointer;
}

.obrazec-checkbox input {
    width: 16px;
    height: 16px;
    accent-color: var(--zlata);
}

.pozabljeno-geslo {
    font-size: var(--velikost-s);
    color: var(--besedilo-d);
}

.pozabljeno-geslo:hover {
    color: var(--zlata);
}

.prijava-gumb {
    width: 100%;
    justify-content: center;
    margin-top: var(--razmik-s);
}

/* Noga */
.prijava-noga {
    margin-top: var(--razmik-xl);
    padding-top: var(--razmik-l);
    border-top: 1px solid var(--rob);
    text-align: center;
    font-size: var(--velikost-s);
    color: var(--besedilo-d);
}

.prijava-povezava {
    color: var(--zlata);
    font-weight: var(--teza-srednja);
}

.prijava-povezava:hover {
    color: var(--zlata-svetla);
}

/* Responsive */
@media (max-width: 480px) {
    .prijava-okvir {
        padding: var(--razmik-l);
    }
    .obrazec-vrstica {
        flex-direction: column;
        align-items: flex-start;
    }
}
</style>