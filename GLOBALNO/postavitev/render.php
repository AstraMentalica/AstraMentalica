<?php
/**
 * ============================================================
 * POT: GLOBALNO/postavitev/render.php
 * 📅 VERZIJA: v116 (27.6.2026)
 * ============================================================
 *
 * 🏛️ NIVO: GLOBALNO (render)
 *
 * 📰 NAMEN:
 *     Osrednji renderer – sestavlja HTML stran iz delov.
 *     KanalWeb ga pokliče z globalno_prikaz_strani().
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - globalno_prikaz_strani(string $tip, array $vsebina): void
 *     - globalno_html_glava(string $naslov): void
 *
 * ✅ DOVOLJENO:
 *     - echo, HTML, include
 *
 * 🚫 PREPOVEDI:
 *     - Brez business logike
 *     - Brez SQL
 *     - Brez $_POST obdelave
 *
 * 📌 STATUS:
 *     Stabilno
 *
 * 📅 ZGODOVINA:
 *     - v116: (27.6.2026) Popravek vseh poti – match dopolnjen
 *             z vsemi obstoječimi stranmi (passport/VIP, pogoji,
 *             zasebnost, 404); KanalWeb dvojni render odpravljen.
 *     - v114: implementacija, kozmični dizajn, tema sistem
 *
 * 👤 AVTOR:
 *     AstraMentalica Mojster
 *
 * 🌐 JEZIK:
 *     sl
 *
 * 🏷️ OZNAKE:
 *     globalno, render, orchestrator
 * ============================================================
 */

declare(strict_types=1);

defined('SISTEM_VARNOST') or die('Direkten dostop ni dovoljen.');

// Bližnjice za poti
const _STR = __DIR__ . '/strani';
const _POS = __DIR__ . '/../postavitve/strani';

// ============================================================
// JAVNI VMESNIK
// ============================================================

function globalno_prikaz_strani(string $tip, array $vsebina): void
{
    $naslov          = $vsebina['naslov']    ?? _render_ime_tipa($tip);
    $aktivniSvet     = $vsebina['svet']      ?? $tip;
    $uporabnik       = $vsebina['uporabnik'] ?? _render_privzet_uporabnik();
    $navigModuli     = $vsebina['nav_moduli'] ?? [];
    $steviloObvestil = $vsebina['obvestila'] ?? 0;

    globalno_html_glava($naslov);

    echo '<div class="zvezde-ozadje" aria-hidden="true"></div>';
    echo '<div class="postavitev">';

    include __DIR__ . '/navigacija.php';
    include __DIR__ . '/glava.php';

    echo '<main class="glavna" id="glavnaVsebina">';
    echo '<div class="notranjost">';
    _render_vsebina($tip, $vsebina);
    echo '</div>';
    echo '</main>';

    echo '</div>'; // .postavitev

    include __DIR__ . '/noga.php';
}

function globalno_html_glava(string $naslov): void
{
    $imeAplikacije = defined('IME_APLIKACIJE') ? IME_APLIKACIJE : 'AstraMentalica';
    $bazaUrl       = defined('KOREN_URL')      ? KOREN_URL      : '';
?>
<!DOCTYPE html>
<html lang="sl" data-tema="temna">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="AstraMentalica – Kozmična šola zavesti">
    <title><?= htmlspecialchars($naslov) ?> | <?= htmlspecialchars($imeAplikacije) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="stylesheet" href="<?= $bazaUrl ?>/GLOBALNO/vmesnik/stili/css/spremenljivke.css">
    <link rel="stylesheet" href="<?= $bazaUrl ?>/GLOBALNO/vmesnik/stili/css/osnova.css">
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>✦</text></svg>">
</head>
<body>
<?php
}

// ============================================================
// INTERNI POMOČNIKI
// ============================================================

function _render_vsebina(string $tip, array $vsebina): void
{
    $p = match ($tip) {
        // ── Osnovno ──────────────────────────────────────────────
        'domov',
        'GLOBALNO'         => _STR . '/domov.php',
        'napaka'           => _STR . '/napaka.php',

        // ── Auth (javne strani) ──────────────────────────────────
        'prijava'          => _POS . '/uporabniki/prijava.php',
        'registracija'     => _POS . '/uporabniki/registracija.php',
        'pozabljeno_geslo',
        'ponastavi_geslo'  => _POS . '/sistem/prijava.php',

        // ── Zasebne strani ───────────────────────────────────────
        'peskovnik'        => _POS . '/peskovnik/peskovnik.php',
        'profil'           => _POS . '/uporabniki/profil.php',
        'passport'         => _POS . '/uporabniki/VIP.php',
        'nastavitve'       => _POS . '/uporabniki/nastavitve_tema.php',

        // ── Moduli ───────────────────────────────────────────────
        'MODULI',
        'modul'            => _STR . '/../sistem/moduli_seznam.php',

        // ── Pravno / info ────────────────────────────────────────
        'pogoji'           => _POS . '/sistem/pravno/pogoji.php',
        'zasebnost'        => _POS . '/sistem/pravno/zasebnost.php',

        // ── Napake ───────────────────────────────────────────────
        '404'              => _POS . '/sistem/napake/404.php',

        default            => null,
    };

    if ($p !== null && file_exists($p)) {
        include $p;
        return;
    }

    _render_genericni($tip, $vsebina);
}

function _render_genericni(string $tip, array $vsebina): void
{
    echo '<div class="kartica">';
    echo '<h2 class="kartica-naslov">' . htmlspecialchars(_render_ime_tipa($tip)) . '</h2>';

    if (!empty($vsebina['sporocilo'])) {
        $razred = ($vsebina['status'] ?? 'info') === 'napaka' ? 'sporocilo-napaka' : 'sporocilo-info';
        echo '<div class="sporocilo ' . $razred . '">' . htmlspecialchars($vsebina['sporocilo']) . '</div>';
    }

    echo '</div>';
}

function _render_ime_tipa(string $tip): string
{
    return match ($tip) {
        'domov'            => 'Domov',
        'MODULI', 'moduli' => 'Moduli',
        'modul'            => 'Modul',
        'profil'           => 'Moj profil',
        'passport'         => 'Moj Passport',
        'nastavitve'       => 'Nastavitve',
        'napaka'           => 'Napaka',
        'prijava'          => 'Prijava',
        'registracija'     => 'Registracija',
        'peskovnik'        => 'Peskovnik',
        'pogoji'           => 'Pogoji uporabe',
        'zasebnost'        => 'Zasebnost',
        default            => ucfirst($tip),
    };
}

function _render_privzet_uporabnik(): array
{
    return ['id' => 0, 'ime' => 'Gost', 'vloga' => 0];
}