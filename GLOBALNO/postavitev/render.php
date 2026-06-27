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

    $avtStrani = ['prijava', 'registracija', 'pozabljeno_geslo', 'ponastavi_geslo'];
    $jeAvt     = in_array($tip, $avtStrani, true);

    globalno_html_glava($naslov);

    echo '<div class="zvezde-ozadje" aria-hidden="true"></div>';

    if ($jeAvt) {
        // Avtentikacijske strani – brez navigacije in glave
        echo '<div class="avt-okvir">';
        _render_vsebina($tip, $vsebina);
        echo '</div>';
    } else {
        echo '<div class="postavitev">';

        // Navigacija
        if (file_exists(__DIR__ . '/navigacija.php')) {
            include __DIR__ . '/navigacija.php';
        }

        // Glava
        if (file_exists(__DIR__ . '/glava.php')) {
            include __DIR__ . '/glava.php';
        }

        // Glavna vsebina
        echo '<main class="glavna" id="glavnaVsebina">';
        echo '<div class="notranjost">';

        _render_vsebina($tip, $vsebina);

        echo '</div>';
        echo '</main>';

        echo '</div>'; // .postavitev
    }

    // Zemljevi JavaScript
    echo '<script src="' . $bazaUrl . '/GLOBALNO/vmesnik/js/zemljevi.js"></script>';

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
        'domov'          => __DIR__ . '/javno/domov.php',
        'peskovnik'      => __DIR__ . '/peskovnik/peskovnik_stran.php',
        'profil'         => __DIR__ . '/uporabniki/profil.php',
        'passport'       => __DIR__ . '/uporabniki/VIP.php',
        'nastavitve'     => __DIR__ . '/napredne/nastavitve_tema.php',
        'napaka'         => __DIR__ . '/napake/napaka.php',
        'prijava'        => __DIR__ . '/javno/prijava.php',
        'registracija'   => __DIR__ . '/javno/registracija.php',
        'moduli'         => __DIR__ . '/sistem/moduli_seznam.php',
        'modul'          => __DIR__ . '/sistem/modul.php',
        default          => null,
    };

    if ($prikazDatoteke !== null && file_exists($prikazDatoteke)) {
        // Izpostavi spremenljivke vključeni datoteki
        $podatki = $vsebina['podatki'] ?? $vsebina;
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
        'domov'        => 'Domov',
        'peskovnik'    => 'Moj Prostor',
        'moduli'       => 'Moduli',
        'modul'        => 'Modul',
        'profil'       => 'Moj profil',
        'passport'     => 'VIP Passport',
        'nastavitve'   => 'Nastavitve',
        'napaka'       => 'Napaka',
        'prijava'      => 'Prijava',
        'registracija' => 'Registracija',
        default        => ucfirst($tip),
    };
}

function _render_privzet_uporabnik(): array
{
    return ['id' => 0, 'ime' => 'Gost', 'vloga' => 0];
}
