<?php
/**
 * POT: ASTRA/nadzorni_center.php | VERZIJA: v116
 * NIVO: ASTRA (ADMIN) | NAMEN: Admin nadzorni center – samo S5+ dostop.
 */
declare(strict_types=1);

if (!defined('SISTEM_VARNOST')) {
    http_response_code(403);
    header('Location: /');
    return;
}

// Preveri S5+ (vloga >= 60)
seja_zacni();
$vloga = $_SESSION['uporabnik_vloga'] ?? 0;

if ($vloga < 60) {
    header('Location: ?svet=GLOBALNO&error=' . urlencode('Nimate dostopa do admin centra'));
    return;
}
?>

<div class="kartica">
    <h1>Nadzorni center</h1>
    <p>Dobrodošli v admin panelu, <?= htmlspecialchars($_SESSION['uporabnik_ime'] ?? 'Admin') ?>.</p>
</div>

<div class="mreza-2">

    <a href="?svet=ASTRA&orodje=moduli" class="kartica kartica-klik">
        <div class="kartica-ikona">📦</div>
        <h3>Upravljanje modulov</h3>
        <p>Pregled, namestitev, aktivacija in deaktivacija modulov.</p>
    </a>

    <a href="?svet=ASTRA&orodje=uporabniki" class="kartica kartica-klik">
        <div class="kartica-ikona">👥</div>
        <h3>Upravljanje uporabnikov</h3>
        <p>Pregled, urejanje in spreminjanje vlog uporabnikov.</p>
    </a>

    <a href="?svet=ASTRA&orodje=diagnostika" class="kartica kartica-klik">
        <div class="kartica-ikona">📊</div>
        <h3>Diagnostika</h3>
        <p>Preverjanje zdravja sistema, logi in monitoring.</p>
    </a>

    <a href="?svet=ASTRA&orodje=nastavitve" class="kartica kartica-klik">
        <div class="kartica-ikona">⚙️</div>
        <h3>Sistemske nastavitve</h3>
        <p>Konfiguracija sistema in okoljske spremenljivke.</p>
    </a>

</div>

<div class="kartica" style="margin-top:var(--razmik-l)">
    <h2>Sistemske informacije</h2>
    <table class="tabela">
        <tr><td><strong>Verzija sistema</strong></td><td><?= SISTEM_VERZIJA ?></td></tr>
        <tr><td><strong>PHP</strong></td><td><?= PHP_VERSION ?></td></tr>
        <tr><td><strong>Čas strežnika</strong></td><td><?= date('Y-m-d H:i:s') ?></td></tr>
        <tr><td><strong>Vaša vloga</strong></td><td><?= $vloga ?></td></tr>
    </table>
</div>
