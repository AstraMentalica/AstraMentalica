<?php
/**
 * ---------------------------------------------------------
 * POT: SISTEM/storitve_svetov/globalno/globalno_prikaz.php
 * v111 (27.5.2026 05:45)
 * ---------------------------------------------------------
 * OPIS: Pomožne funkcije za prikaz (render helperji)
 * ---------------------------------------------------------
 *
 * ODVISNOSTI:
 * - pot.php
 *
 * UPORABA:
 * - GLOBALNO/render/
 *
 * FUNKCIJE:
 * - globalno_prikaz_strani() – prikaz strani
 * - globalno_prikaz_gradnika() – prikaz gradnika
 * - globalno_prikaz_navigacije() – prikaz navigacije
 * - globalno_prikaz_glava() – prikaz glave
 * - globalno_prikaz_noga() – prikaz noge
 *
 * PREPOVEDI:
 * - Brez echo, print_r, var_dump (razen prikaza)
 *
 * STATUS: Stabilno
 *
 * ZGODOVINA:
 * - v111: FAZA 2b – implementacija
 *
 * ---------------------------------------------------------
 * AVTOR: AstraMentalica Mojster
 * ---------------------------------------------------------
 */

declare(strict_types=1);

function globalno_prikaz_strani(string $imeStrani, array $podatki = []): void
{
$potStrani = GLOBALNO . '/render/strani/' . $imeStrani . '.php';

if (!file_exists($potStrani)) {
    $potStrani = GLOBALNO . '/render/strani/napake/404.php';
}

// Pripravi podatke za prikaz
$vsebina = [
    'naslov' => IME_APLIKACIJE,
    'vsebina' => '',
    'podatki' => $podatki,
    'jezik' => globalno_jezik_aktivni(),
    'tema' => globalno_tema_aktivna(),
    'uporabnik' => seja_pridobi_uporabnika(),
    'csp_nonce' => bin2hex(random_bytes(16))
];

// Uporabi postavitev
globalno_postavitev_prikazi($vsebina);
}

function globalno_prikaz_gradnika(string $imeGradnika, array $parametri = []): string
{
return globalno_gradnik_prikazi($imeGradnika, $parametri);
}

function globalno_prikaz_navigacije(string $pozicija = 'glavna'): string
{
$vloga = seja_pridobi_vlogo();
$meni = globalno_navigacija_menu($pozicija, $vloga);
$aktivnaPot = $_GET['svet'] ?? 'GLOBALNO';

ob_start();
?>
<nav class="navigacija navigacija-<?= htmlspecialchars($pozicija) ?>">
    <div class="navigacija-vsebina">
        <div class="nav-logotip">
            <a href="?svet=GLOBALNO">
                <span class="nav-logotip-ikona">🌌</span>
                <span class="nav-logotip-besedilo"><?= IME_APLIKACIJE ?></span>
            </a>
        </div>
        <button class="nav-meni-gumb" id="navMeniGumb" aria-label="Meni">☰</button>
        <ul class="nav-seznam" id="navSeznam">
            <?php foreach ($meni as $element): ?>
                <li class="nav-element <?= $element['aktivno'] ? 'aktivno' : '' ?>">
                    <a href="<?= htmlspecialchars($element['pot']) ?>" class="nav-povezava">
                        <?php if (!empty($element['ikona'])): ?>
                            <span class="nav-ikona"><?= htmlspecialchars($element['ikona']) ?></span>
                        <?php endif; ?>
                        <span class="nav-besedilo"><?= htmlspecialchars($element['naslov']) ?></span>
                    </a>
                    <?php if (!empty($element['podmeni'])): ?>
                        <ul class="nav-podmeni">
                            <?php foreach ($element['podmeni'] as $podmeni): ?>
                                <li><a href="<?= htmlspecialchars($podmeni['pot']) ?>"><?= htmlspecialchars($podmeni['naslov']) ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</nav>
<?php
return ob_get_clean();
}

function globalno_prikaz_glava(): string
{
ob_start();
?>
<!DOCTYPE html>
<html lang="<?= globalno_jezik_aktivni() ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= IME_APLIKACIJE ?> – platforma za duhovni razvoj">
    <title><?= IME_APLIKACIJE ?></title>
    <link rel="stylesheet" href="<?= globalno_tema_css() ?>">
    <link rel="stylesheet" href="<?= GLOBALNO ?>/vmesnik/css/jedro/osnova.css">
</head>
<body class="tema-<?= globalno_tema_aktivna() ?>">
<?php
return ob_get_clean();
}

function globalno_prikaz_noga(): string
{
ob_start();
?>
<footer class="noga">
    <div class="noga-vsebina">
        <div class="noga-levo">
            <p>&copy; <?= date('Y') ?> <?= IME_APLIKACIJE ?> – vse pravice pridržane</p>
            <p class="noga-verzija">Verzija: <?= SISTEM_VERZIJA ?></p>
        </div>
        <div class="noga-sredina">
            <nav class="noga-navigacija">
                <a href="?svet=GLOBALNO">Domov</a>
                <a href="?svet=GLOBALNO&pot=pogoji">Pogoji uporabe</a>
                <a href="?svet=GLOBALNO&pot=zasebnost">Zasebnost</a>
            </nav>
        </div>
        <div class="noga-desno">
            <div class="noga-social">
                <a href="#" target="_blank" rel="noopener">📘</a>
                <a href="#" target="_blank" rel="noopener">📷</a>
                <a href="#" target="_blank" rel="noopener">🐦</a>
            </div>
        </div>
    </div>
</footer>
<?php
return ob_get_clean();
}