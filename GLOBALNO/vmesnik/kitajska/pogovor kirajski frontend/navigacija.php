<?php
/**
 * ============================================================
 * POT: GLOBALNO/vmesnik/kitajska/pogovor kirajski frontend/navigacija.php
 * 📅 VERZIJA: v114 (9.6.2026 18:00)
 * ============================================================
 *
 * 🏛️ NIVO: GLOBALNO (render)
 *
 * 📰 NAMEN:
 *     Leva navigacija – moduli, sistem, uporabnik.
 *     Pasiven prikaz – brez poslovne logike.
 *
 * ✅ DOVOLJENO:
 *     - echo, HTML – render datoteka
 *
 * 🚫 PREPOVEDI:
 *     - Brez SQL klicev
 *     - Brez $_POST obdelave
 *     - Brez business logike
 *
 * 📌 STATUS:
 *     Stabilno
 *
 * 📅 ZGODOVINA:
 *     - v114: implementacija, kozmični dizajn
 *
 * 👤 AVTOR:
 *     AstraMentalica Mojster
 *
 * 🌐 JEZIK:
 *     sl
 *
 * 🏷️ OZNAKE:
 *     globalno, render, navigacija
 * ============================================================
 */

declare(strict_types=1);

defined('SISTEM_VARNOST') or die('Direkten dostop ni dovoljen.');

// Pričakuje spremenljivke iz render.php:
// $uporabnik   = ['ime', 'vloga', 'avatar']
// $aktivnaSvet = ime trenutnega sveta
// $moduli      = seznam navigacijskih modulov

$aktivniSvet = $aktivniSvet ?? '';
$uporabnik   = $uporabnik ?? ['ime' => 'Gost', 'vloga' => 0];
$navig       = $navigModuli ?? [];
?>
<nav class="navigacija" id="navigacija" role="navigation" aria-label="Glavna navigacija">

    <!-- Logo -->
    <div class="nav-logotip">
        <a href="?svet=GLOBALNO" class="nav-logotip-povezava">
            <span class="nav-logotip-ikona">✦</span>
            <span class="nav-logotip-ime">AstraMentalica</span>
        </a>
        <button class="nav-stisni-gumb" id="navStisni" aria-label="Stisni navigacijo" title="Stisni">
            ◀
        </button>
    </div>

    <!-- Seja / Avatar -->
    <div class="nav-seja">
        <?php if (!empty($uporabnik['avatar'])): ?>
            <img src="<?= htmlspecialchars($uporabnik['avatar']) ?>"
                 alt="Avatar"
                 class="nav-avatar">
        <?php else: ?>
            <div class="nav-avatar nav-avatar-privzet">
                <?= mb_strtoupper(mb_substr($uporabnik['ime'] ?? 'G', 0, 1)) ?>
            </div>
        <?php endif; ?>
        <div class="nav-seja-info">
            <span class="nav-seja-ime"><?= htmlspecialchars($uporabnik['ime'] ?? 'Gost') ?></span>
            <span class="nav-seja-vloga"><?= _nav_vloga_napis((int)($uporabnik['vloga'] ?? 0)) ?></span>
        </div>
    </div>

    <!-- Navigacijske povezave -->
    <div class="nav-vsebina">

        <!-- Glavne strani -->
        <div class="nav-skupina">
            <span class="nav-skupina-naslov">Svetovi</span>
            <?= _nav_gumb('?svet=GLOBALNO', '🏠', 'Domov',          $aktivniSvet === 'GLOBALNO') ?>
            <?= _nav_gumb('?svet=moduli',   '✦',  'Moduli',         $aktivniSvet === 'moduli') ?>
            <?= _nav_gumb('?svet=codex',    '📖', 'Codex',          $aktivniSvet === 'codex') ?>
            <?= _nav_gumb('?svet=aeternum', '📚', 'Aeternum',       $aktivniSvet === 'aeternum') ?>
        </div>

        <!-- Dinamični moduli -->
        <?php if (!empty($navig)): ?>
        <div class="nav-skupina">
            <span class="nav-skupina-naslov">Moduli</span>
            <?php foreach ($navig as $modul): ?>
                <?php
                $imeSveta = $modul['oznaka'] ?? $modul['ime'] ?? '';
                $ikona    = $modul['ikona'] ?? '◈';
                $ime      = $modul['ime'] ?? $imeSveta;
                $aktiven  = $aktivniSvet === $imeSveta;
                ?>
                <?= _nav_gumb('?svet=' . urlencode($imeSveta), $ikona, $ime, $aktiven) ?>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- Uporabniško področje -->
        <div class="nav-skupina">
            <span class="nav-skupina-naslov">Moja pot</span>
            <?= _nav_gumb('?svet=passport',    '🕊️', 'Moj Passport',  $aktivniSvet === 'passport') ?>
            <?= _nav_gumb('?svet=nastavitve',  '⚙️', 'Nastavitve',    $aktivniSvet === 'nastavitve') ?>
        </div>

        <!-- Admin (samo za vloge 100+) -->
        <?php if ((int)($uporabnik['vloga'] ?? 0) >= 100): ?>
        <div class="nav-skupina">
            <span class="nav-skupina-naslov">Admin</span>
            <?= _nav_gumb('?svet=astra',        '👁', 'Astra',          $aktivniSvet === 'astra') ?>
            <?= _nav_gumb('?svet=modul_bridge', '🔧', 'Modul Bridge',   $aktivniSvet === 'modul_bridge') ?>
        </div>
        <?php endif; ?>

    </div>

    <!-- Dno navigacije -->
    <div class="nav-dno">
        <button class="nav-gumb nav-tema-preklop tema-preklop" id="temaPreklop" title="Preklopi temo">
            🌙
        </button>
        <a href="?svet=odjava" class="nav-gumb nav-odjava" title="Odjava">
            ↪ Odjava
        </a>
    </div>

</nav>

<style>
/* ============================================================
 * NAVIGACIJA
 * ============================================================ */
.navigacija {
    grid-area: nav;
    width: var(--nav-sirina);
    background: var(--povrsina);
    border-right: 1px solid var(--rob);
    display: flex;
    flex-direction: column;
    position: sticky;
    top: 0;
    height: 100vh;
    overflow-y: auto;
    overflow-x: hidden;
    transition: width var(--prehod-pocasen);
    z-index: 200;
    scrollbar-width: none;
}

.navigacija::-webkit-scrollbar { display: none; }

/* Logo */
.nav-logotip {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1.1rem 1rem 0.8rem;
    border-bottom: 1px solid var(--rob);
    min-height: var(--glava-visina);
    flex-shrink: 0;
}

.nav-logotip-povezava {
    display: flex;
    align-items: center;
    gap: 0.6rem;
    color: var(--zlata);
    text-decoration: none;
    overflow: hidden;
    white-space: nowrap;
}

.nav-logotip-ikona {
    font-size: 1.3rem;
    flex-shrink: 0;
    text-shadow: 0 0 10px rgba(232,200,74,0.5);
}

.nav-logotip-ime {
    font-size: 0.95rem;
    font-weight: var(--teza-krepka);
    letter-spacing: 0.02em;
}

.nav-stisni-gumb {
    background: none;
    border: 1px solid var(--rob);
    border-radius: var(--rob-pill);
    color: var(--besedilo-d);
    cursor: pointer;
    padding: 0.2rem 0.5rem;
    font-size: 0.7rem;
    transition: all var(--prehod);
    flex-shrink: 0;
}

.nav-stisni-gumb:hover {
    border-color: var(--zlata);
    color: var(--zlata);
}

/* Seja */
.nav-seja {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.9rem 1rem;
    border-bottom: 1px solid var(--rob);
    flex-shrink: 0;
    overflow: hidden;
}

.nav-avatar {
    width: 36px;
    height: 36px;
    border-radius: var(--rob-krog);
    border: 2px solid var(--rob-aktiven);
    object-fit: cover;
    flex-shrink: 0;
}

.nav-avatar-privzet {
    background: var(--zlata-dim);
    color: var(--zlata);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: var(--teza-krepka);
    font-size: 0.9rem;
}

.nav-seja-info {
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

.nav-seja-ime {
    font-size: var(--velikost-s);
    font-weight: var(--teza-srednja);
    color: var(--besedilo-s);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.nav-seja-vloga {
    font-size: var(--velikost-xs);
    color: var(--besedilo-d);
}

/* Skupinice */
.nav-vsebina {
    flex: 1;
    padding: 0.75rem 0.6rem;
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
    overflow-y: auto;
}

.nav-skupina {
    margin-bottom: 0.5rem;
}

.nav-skupina-naslov {
    display: block;
    font-size: 0.68rem;
    font-weight: var(--teza-krepka);
    letter-spacing: 0.1em;
    text-transform: uppercase;
    color: var(--besedilo-m);
    padding: 0.6rem 0.7rem 0.3rem;
}

/* Navigacijski gumb */
.nav-gumb {
    display: flex;
    align-items: center;
    gap: 0.65rem;
    padding: 0.5rem 0.75rem;
    border-radius: var(--rob-m);
    color: var(--besedilo-d);
    text-decoration: none;
    font-size: var(--velikost-s);
    transition: background var(--prehod), color var(--prehod);
    cursor: pointer;
    border: none;
    background: none;
    width: 100%;
    white-space: nowrap;
    overflow: hidden;
}

.nav-gumb:hover {
    background: var(--kartica-hover);
    color: var(--besedilo-s);
}

.nav-gumb.aktiven {
    background: var(--zlata-dim);
    color: var(--zlata);
    font-weight: var(--teza-srednja);
}

.nav-gumb-ikona {
    font-size: 1rem;
    flex-shrink: 0;
    width: 1.4rem;
    text-align: center;
}

.nav-gumb-napis {
    flex: 1;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* Znacka */
.nav-znacka {
    background: var(--zlata-dim);
    color: var(--zlata);
    font-size: 0.65rem;
    padding: 0.1rem 0.45rem;
    border-radius: var(--rob-pill);
    font-weight: var(--teza-krepka);
    flex-shrink: 0;
}

/* Dno */
.nav-dno {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 0.6rem;
    border-top: 1px solid var(--rob);
    flex-shrink: 0;
}

.nav-odjava {
    flex: 1;
    font-size: var(--velikost-xs);
    color: var(--besedilo-m);
    justify-content: center;
}

.nav-odjava:hover { color: var(--rdeca); }

/* Stisnjena navigacija */
.navigacija.stisnjena {
    width: var(--nav-sirina-stisnjena);
}

.navigacija.stisnjena .nav-logotip-ime,
.navigacija.stisnjena .nav-seja-info,
.navigacija.stisnjena .nav-skupina-naslov,
.navigacija.stisnjena .nav-gumb-napis,
.navigacija.stisnjena .nav-znacka,
.navigacija.stisnjena .nav-odjava { display: none; }

.navigacija.stisnjena .nav-logotip { justify-content: center; }
.navigacija.stisnjena .nav-seja    { justify-content: center; }
.navigacija.stisnjena .nav-gumb    { justify-content: center; padding: 0.55rem; }
.navigacija.stisnjena .nav-dno     { justify-content: center; }
.navigacija.stisnjena .nav-stisni-gumb { transform: rotate(180deg); }
</style>

<?php
function _nav_gumb(string $url, string $ikona, string $ime, bool $aktiven = false, ?string $znacka = null): string
{
    $razred = 'nav-gumb' . ($aktiven ? ' aktiven' : '');
    $znackaHtml = $znacka
        ? '<span class="nav-znacka">' . htmlspecialchars($znacka) . '</span>'
        : '';

    return sprintf(
        '<a href="%s" class="%s">'
        . '<span class="nav-gumb-ikona">%s</span>'
        . '<span class="nav-gumb-napis">%s</span>'
        . '%s'
        . '</a>',
        htmlspecialchars($url),
        $razred,
        $ikona,
        htmlspecialchars($ime),
        $znackaHtml
    );
}

function _nav_vloga_napis(int $vloga): string
{
    return match (true) {
        $vloga >= 100 => '👑 Admin',
        $vloga >= 60  => 'S5',
        $vloga >= 50  => 'S4',
        $vloga >= 40  => 'S3',
        $vloga >= 30  => 'S2',
        $vloga >= 20  => 'S1',
        $vloga >= 10  => 'S0',
        default       => 'Gost',
    };
}
?>
