<?php
/**
 * ============================================================
 * POT: UPORABNIKI/prikaz/sistem/uporabnik_registracija.php
 * 📅 VERZIJA: v116 (27.6.2026)
 * ============================================================
 *
 * 🏛️ NIVO: UPORABNIKI (prikaz)
 *
 * 📰 NAMEN:
 *     Registracijska stran z email/geslo in Google OAuth.
 *     Popolnoma predelan dizajn.
 *
 * 📡 PRIČAKUJE IZ $vsebina:
 *     - $vsebina['napaka']       = string
 *     - $vsebina['sporocilo']    = string
 *     - $vsebina['email']        = string
 *     - $vsebina['ime']          = string
 *     - $vsebina['google_url']   = ?string (če je Google konfiguriran)
 *     - $vsebina['csp_nonce']    = string
 *     - $vsebina['pogoji']       = bool (ali je potrebno sprejeti pogoje)
 *
 * 🚫 PREPOVEDI:
 *     - Brez business logike
 *     - Brez direktnega branja PODATKI/
 *
 * 📌 STATUS:
 *     Novo
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
$ime         = $vsebina['ime'] ?? '';
$googleUrl   = $vsebina['google_url'] ?? null;
$cspNonce    = $vsebina['csp_nonce'] ?? '';
$pogoji      = $vsebina['pogoji'] ?? true;
?>

<div class="registracija-stran">
    <div class="registracija-ozadje">
        <div class="zvezde-male"></div>
    </div>

    <div class="registracija-vsebina">
        <div class="registracija-okvir">
            <!-- Logo / Header -->
            <div class="registracija-glava">
                <a href="?svet=GLOBALNO" class="registracija-logo">
                    <span class="registracija-logo-ikona">✦</span>
                    <span class="registracija-logo-ime">AstraMentalica</span>
                </a>
            </div>

            <!-- Naslov -->
            <div class="registracija-naslov-sekcija">
                <h1 class="registracija-naslov">Pridružite se poti</h1>
                <p class="registracija-podnaslov">Ustvarite svoj čarobni račun</p>
            </div>

            <!-- Prednosti -->
            <div class="prednosti">
                <div class="prednost">
                    <span class="prednost-ikona">🌟</span>
                    <span class="prednost-tekst">Brezplačna registracija</span>
                </div>
                <div class="prednost">
                    <span class="prednost-ikona">🔮</span>
                    <span class="prednost-tekst">Takojšen dostop do modulov</span>
                </div>
                <div class="prednost">
                    <span class="prednost-ikona">✨</span>
                    <span class="prednost-tekst">Osebni peskovnik</span>
                </div>
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

            <div class="registracija-lociilo">
                <span>ali</span>
            </div>
            <?php endif; ?>

            <!-- Registracijska forma -->
            <form method="post" action="?svet=SISTEM&amp;akcija=registracija" class="registracija-obrazec">
                <div class="obrazec-skupina">
                    <label for="ime" class="obrazec-oznaka">Ime ali vzdevek</label>
                    <input type="text"
                           id="ime"
                           name="ime"
                           class="vnos"
                           value="<?= htmlspecialchars($ime) ?>"
                           required
                           autofocus
                           placeholder="Vaše ime"
                           autocomplete="name">
                </div>

                <div class="obrazec-skupina">
                    <label for="email" class="obrazec-oznaka">Elektronski naslov</label>
                    <input type="email"
                           id="email"
                           name="email"
                           class="vnos"
                           value="<?= htmlspecialchars($email) ?>"
                           required
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
                           placeholder="Najmanj 8 znakov"
                           autocomplete="new-password"
                           minlength="8">
                </div>

                <div class="obrazec-skupina">
                    <label for="geslo_ponovi" class="obrazec-oznaka">Ponovite geslo</label>
                    <input type="password"
                           id="geslo_ponovi"
                           name="geslo_ponovi"
                           class="vnos"
                           required
                           placeholder="••••••••"
                           autocomplete="new-password">
                </div>

                <?php if ($pogoji): ?>
                <div class="obrazec-pogoji">
                    <label class="obrazec-checkbox">
                        <input type="checkbox" name="sprejmi_pogoje" value="1" required>
                        <span>
                            Sprejemam
                            <a href="?svet=pogoji" class="povezava-pogoji">pogoje uporabe</a>
                            in
                            <a href="?svet=zasebnost" class="povezava-pogoji">politiko zasebnosti</a>
                        </span>
                    </label>
                </div>
                <?php endif; ?>

                <button type="submit" class="gumb gumb-primarni registracija-gumb">
                    Ustvari račun
                </button>
            </form>

            <!-- Prijava link -->
            <div class="registracija-noga">
                <p>
                    Že imate račun?
                    <a href="?svet=prijava" class="registracija-povezava">
                        Vpišite se
                    </a>
                </p>
            </div>
        </div>
    </div>
</div>

<style>
/* ============================================================
 * REGISTRACIJSKA STRAN
 * ============================================================ */
.registracija-stran {
    min-height: calc(100vh - var(--glava-visina));
    display: flex;
    align-items: center;
    justify-content: center;
    padding: var(--razmik-xl);
    position: relative;
    overflow: hidden;
}

.registracija-ozadje {
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

.registracija-vsebina {
    position: relative;
    width: 100%;
    max-width: 440px;
}

.registracija-okvir {
    background: var(--povrsina);
    border: 1px solid var(--rob);
    border-radius: var(--rob-xl);
    padding: var(--razmik-2xl);
    box-shadow: var(--senca-l);
}

/* Header */
.registracija-glava {
    text-align: center;
    margin-bottom: var(--razmik-m);
}

.registracija-logo {
    display: inline-flex;
    align-items: center;
    gap: var(--razmik-s);
    text-decoration: none;
    color: var(--zlata);
}

.registracija-logo-ikona {
    font-size: 1.5rem;
    text-shadow: 0 0 15px rgba(232, 200, 74, 0.5);
}

.registracija-logo-ime {
    font-family: var(--pisava-naslov);
    font-size: var(--velikost-l);
    font-weight: var(--teza-krepka);
}

/* Naslov */
.registracija-naslov-sekcija {
    text-align: center;
    margin-bottom: var(--razmik-m);
}

.registracija-naslov {
    font-size: var(--velikost-2xl);
    margin-bottom: var(--razmik-xs);
}

.registracija-podnaslov {
    color: var(--besedilo-d);
    font-size: var(--velikost-m);
}

/* Prednosti */
.prednosti {
    display: flex;
    justify-content: center;
    gap: var(--razmik-m);
    margin-bottom: var(--razmik-l);
    flex-wrap: wrap;
}

.prednost {
    display: flex;
    align-items: center;
    gap: var(--razmik-xs);
    font-size: var(--velikost-xs);
    color: var(--besedilo-d);
    background: var(--kartica);
    padding: 0.3rem 0.6rem;
    border-radius: var(--rob-pill);
}

.prednost-ikona {
    font-size: 0.9rem;
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
.registracija-lociilo {
    display: flex;
    align-items: center;
    gap: var(--razmik-m);
    margin: var(--razmik-l) 0;
    color: var(--besedilo-m);
    font-size: var(--velikost-s);
}

.registracija-lociilo::before,
.registracija-lociilo::after {
    content: '';
    flex: 1;
    height: 1px;
    background: var(--rob);
}

/* Obrazec */
.registracija-obrazec {
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

.obrazec-pogoji {
    font-size: var(--velikost-s);
    color: var(--besedilo-d);
}

.obrazec-checkbox {
    display: flex;
    align-items: flex-start;
    gap: var(--razmik-s);
    cursor: pointer;
}

.obrazec-checkbox input {
    width: 16px;
    height: 16px;
    accent-color: var(--zlata);
    margin-top: 2px;
    flex-shrink: 0;
}

.povezava-pogoji {
    color: var(--zlata);
}

.registracija-gumb {
    width: 100%;
    justify-content: center;
    margin-top: var(--razmik-s);
}

/* Noga */
.registracija-noga {
    margin-top: var(--razmik-xl);
    padding-top: var(--razmik-l);
    border-top: 1px solid var(--rob);
    text-align: center;
    font-size: var(--velikost-s);
    color: var(--besedilo-d);
}

.registracija-povezava {
    color: var(--zlata);
    font-weight: var(--teza-srednja);
}

.registracija-povezava:hover {
    color: var(--zlata-svetla);
}

/* Responsive */
@media (max-width: 480px) {
    .registracija-okvir {
        padding: var(--razmik-l);
    }
    .prednosti {
        flex-direction: column;
        align-items: center;
    }
}
</style>