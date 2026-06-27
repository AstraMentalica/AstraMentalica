<?php
/**
 * ============================================================
 * GLOBALNO: postavitev/strani/uporabniki/prijava.php
 * POT: GLOBALNO/postavitev/strani/uporabniki/prijava.php
 * ============================================================
 *
 * Namen:
 *     Sestavljena prijavna stran uporabnika.
 *     Samo prikaz – podatke in odločanje pripravi SISTEM.
 * ============================================================
 */

declare(strict_types=1);

require_once __DIR__ . '/../layouti.php';
require_once __DIR__ . '/../gradniki/gumb.php';
require_once __DIR__ . '/../gradniki/kartica.php';
require_once __DIR__ . '/../gradniki/obrazec.php';
require_once __DIR__ . '/../gradniki/navigacija.php';

$podatki = $vsebina['prijava'] ?? $vsebina ?? [];
$napaka = (string)($podatki['napaka'] ?? '');
$sporocilo = (string)($podatki['sporocilo'] ?? '');
$email = (string)($podatki['email'] ?? '');
$redirect = (string)($podatki['redirect'] ?? '?svet=UPORABNIKI');
$googleUrl = (string)($podatki['google_url'] ?? '/api?akcija=oauth_google_zacni');
$googleOmogocen = (bool)($podatki['google_omogocen'] ?? false);

$polja = [
    ['ime' => 'email', 'oznaka' => 'E-pošta', 'tip' => 'email', 'vrednost' => $email, 'place' => 'ime@domena.si'],
    ['ime' => 'geslo', 'oznaka' => 'Geslo', 'tip' => 'password', 'place' => '••••••••'],
];

$navigacija = [
    ['ime' => 'Domov', 'href' => '?svet=GLOBALNO'],
    ['ime' => 'Registracija', 'href' => '?svet=UPORABNIKI&pot=registracija'],
];

$vsebinaHtml = '';
$vsebinaHtml .= globalno_gradnik_navigacija($navigacija, ['razred' => 'prijava-navigacija']);

if ($napaka !== '') {
    $vsebinaHtml .= '<div class="sporocilo sporocilo-napaka">' . htmlspecialchars($napaka) . '</div>';
}

if ($sporocilo !== '') {
    $vsebinaHtml .= '<div class="sporocilo sporocilo-uspeh">' . htmlspecialchars($sporocilo) . '</div>';
}

if ($googleOmogocen) {
    $vsebinaHtml .= '<a href="' . htmlspecialchars($googleUrl) . '" class="gumb gumb-sekundarni prijava-google">Nadaljuj z Google</a>';
}

$vsebinaHtml .= globalno_gradnik_obrazec('?svet=SISTEM&akcija=prijava', $polja, ['razred' => 'prijava-obrazec']);
$vsebinaHtml .= '<div class="prijava-povezave">'
    . '<a href="?svet=UPORABNIKI&pot=registracija">Nimaš računa? Registriraj se</a>'
    . '<a href="?svet=UPORABNIKI&pot=pozabljeno_geslo">Pozabljeno geslo?</a>'
    . '</div>';

echo globalno_gradnik_kartica('Prijava', $vsebinaHtml, ['razred' => 'prijava-kartica']);

?>

<style>
.prijava-kartica {
    background: rgba(18, 22, 33, 0.88);
    border: 1px solid rgba(255, 220, 150, 0.18);
    color: #e6dcc6;
    box-shadow: 0 14px 40px rgba(0, 0, 0, 0.28);
}

.prijava-kartica h3,
.prijava-kartica label,
.prijava-kartica a,
.prijava-kartica .sporocilo,
.prijava-kartica .prijava-povezave {
    color: #e6dcc6;
}

.prijava-kartica .prijava-povezave {
    display: flex;
    justify-content: space-between;
    gap: 1rem;
    margin-top: 1rem;
    flex-wrap: wrap;
}

.prijava-kartica .prijava-povezave a {
    color: #d8b970;
    text-decoration: none;
}

.prijava-kartica .obrazec label,
.prijava-kartica .obrazec input {
    color: #f3ead7;
}

.prijava-kartica .obrazec input {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 220, 150, 0.18);
}

.prijava-kartica .obrazec input::placeholder {
    color: rgba(243, 234, 215, 0.6);
}
</style>
