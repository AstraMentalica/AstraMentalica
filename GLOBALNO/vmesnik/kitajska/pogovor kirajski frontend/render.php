<?php
/**
 * ============================================================
 * POT: GLOBALNO/render/render.php
 * 📅 VERZIJA: v114 (9.6.2026 18:00)
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

// ============================================================
// JAVNI VMESNIK
// ============================================================

/**
 * Sestavi in izpiše celotno HTML stran.
 *
 * @param string $tip      Tip strani (domov, modul, profil, napaka...)
 * @param array  $vsebina  Podatki za prikaz
 */
function globalno_prikaz_strani(string $tip, array $vsebina): void
{
    $naslov      = $vsebina['naslov'] ?? _render_ime_tipa($tip);
    $aktivniSvet = $vsebina['svet']   ?? $tip;
    $uporabnik   = $vsebina['uporabnik'] ?? _render_privzet_uporabnik();
    $navigModuli = $vsebina['nav_moduli'] ?? [];
    $naslovStrani = $naslov;
    $steviloObvestil = $vsebina['obvestila'] ?? 0;

    globalno_html_glava($naslov);

    echo '<div class="zvezde-ozadje" aria-hidden="true"></div>';
    echo '<div class="postavitev">';

    // Navigacija
    include __DIR__ . '/navigacija.php';

    // Glava
    include __DIR__ . '/glava.php';

    // Glavna vsebina
    echo '<main class="glavna" id="glavnaVsebina">';
    echo '<div class="notranjost">';

    _render_vsebina($tip, $vsebina);

    echo '</div>';
    echo '</main>';

    echo '</div>'; // .postavitev

    include __DIR__ . '/noga.php';
}

/**
 * Izpiše HTML <head> z vsemi CSS in meta podatki.
 */
function globalno_html_glava(string $naslov): void
{
    $imeAplikacije = defined('IME_APLIKACIJE') ? IME_APLIKACIJE : 'AstraMentalica';
    $bazaUrl       = defined('POT_KOREN') ? '' : '';
?>
<!DOCTYPE html>
<html lang="sl" data-tema="temna">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="AstraMentalica – Kozmična šola zavesti">
    <title><?= htmlspecialchars($naslov) ?> | <?= htmlspecialchars($imeAplikacije) ?></title>

    <!-- Preconnect za Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">

    <!-- CSS – Design tokens -->
    <link rel="stylesheet" href="<?= $bazaUrl ?>/GLOBALNO/vmesnik/css/spremenljivke.css">
    <link rel="stylesheet" href="<?= $bazaUrl ?>/GLOBALNO/vmesnik/css/osnova.css">
    <link rel="stylesheet" href="<?= $bazaUrl ?>/GLOBALNO/vmesnik/css/zemljevi.css">

    <!-- Favicon -->
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
    $prikazDatoteke = match ($tip) {
        'domov'      => POT_GLOBALNO . '/strani/javno/domov.php',
        'modul'      => POT_GLOBALNO . '/strani/javno/domov.php', // TODO: modul.php
        'moduli'     => POT_GLOBALNO . '/strani/javno/domov.php', // TODO: moduli_seznam.php
        'profil'     => POT_GLOBALNO . '/strani/uporabniki/profil.php',
        'passport'   => __DIR__ . '/strani/passport.php',
        'nastavitve' => __DIR__ . '/strani/nastavitve.php',
        'napaka'     => POT_GLOBALNO . '/strani/napake/404.php',
        'prijava'    => POT_GLOBALNO . '/strani/javno/prijava.php',
        default      => null,
    };

    if ($prikazDatoteke !== null && file_exists($prikazDatoteke)) {
        include $prikazDatoteke;
        return;
    }

    // Fallback – generični prikaz
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

    if (!empty($vsebina['podatki'])) {
        echo '<pre>' . htmlspecialchars(json_encode($vsebina['podatki'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) . '</pre>';
    }

    echo '</div>';
}

function _render_ime_tipa(string $tip): string
{
    return match ($tip) {
        'domov'      => 'Domov',
        'moduli'     => 'Moduli',
        'modul'      => 'Modul',
        'profil'     => 'Moj profil',
        'passport'   => 'Moj Passport',
        'nastavitve' => 'Nastavitve',
        'napaka'     => 'Napaka',
        'prijava'    => 'Prijava',
        default      => ucfirst($tip),
    };
}

function _render_privzet_uporabnik(): array
{
    return ['id' => 0, 'ime' => 'Gost', 'vloga' => 0];
}
