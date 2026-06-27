<?php
/**
 * ============================================================
 * GLOBALNO: postavitev/strani/uporabniki/registracija.php
 * POT: GLOBALNO/postavitev/strani/uporabniki/registracija.php
 * ============================================================
 *
 * Namen:
 *     Sestavljena registracijska stran uporabnika.
 *     Samo prikaz – podatke in odločanje pripravi SISTEM.
 * ============================================================
 */

declare(strict_types=1);

require_once __DIR__ . '/../layouti.php';
require_once __DIR__ . '/../gradniki/gumb.php';
require_once __DIR__ . '/../gradniki/kartica.php';
require_once __DIR__ . '/../gradniki/obrazec.php';
require_once __DIR__ . '/../gradniki/navigacija.php';

$podatki = $vsebina['registracija'] ?? $vsebina ?? [];
$napaka = (string)($podatki['napaka'] ?? '');
$sporocilo = (string)($podatki['sporocilo'] ?? '');
$ime = (string)($podatki['ime'] ?? '');
$email = (string)($podatki['email'] ?? '');

$polja = [
    ['ime' => 'ime', 'oznaka' => 'Ime', 'tip' => 'text', 'vrednost' => $ime, 'place' => 'Tvoje ime'],
    ['ime' => 'email', 'oznaka' => 'E-pošta', 'tip' => 'email', 'vrednost' => $email, 'place' => 'ime@domena.si'],
    ['ime' => 'geslo', 'oznaka' => 'Geslo', 'tip' => 'password', 'place' => '••••••••'],
    ['ime' => 'geslo_ponovno', 'oznaka' => 'Ponovi geslo', 'tip' => 'password', 'place' => '••••••••'],
];

$navigacija = [
    ['ime' => 'Domov', 'href' => '?svet=GLOBALNO'],
    ['ime' => 'Prijava', 'href' => '?svet=UPORABNIKI&pot=prijava'],
];

$vsebinaHtml = '';
$vsebinaHtml .= globalno_gradnik_navigacija($navigacija, ['razred' => 'registracija-navigacija']);

if ($napaka !== '') {
    $vsebinaHtml .= '<div class="sporocilo sporocilo-napaka">' . htmlspecialchars($napaka) . '</div>';
}

if ($sporocilo !== '') {
    $vsebinaHtml .= '<div class="sporocilo sporocilo-uspeh">' . htmlspecialchars($sporocilo) . '</div>';
}

$vsebinaHtml .= globalno_gradnik_obrazec('?svet=SISTEM&akcija=registracija', $polja, ['razred' => 'registracija-obrazec']);
$vsebinaHtml .= '<div class="registracija-opomba">S prijavo sprejmeš pogoje uporabe.</div>';

echo globalno_gradnik_kartica('Registracija', $vsebinaHtml, ['razred' => 'registracija-kartica']);

?>

<style>
.registracija-kartica {
    background: rgba(18, 22, 33, 0.88);
    border: 1px solid rgba(255, 220, 150, 0.18);
    color: #e6dcc6;
    box-shadow: 0 14px 40px rgba(0, 0, 0, 0.28);
}

.registracija-kartica h3,
.registracija-kartica label,
.registracija-kartica a,
.registracija-kartica .sporocilo,
.registracija-kartica .registracija-opomba {
    color: #e6dcc6;
}

.registracija-kartica .registracija-opomba {
    margin-top: 1rem;
    font-size: 0.85rem;
    color: #cdbb94;
}

.registracija-kartica .obrazec label,
.registracija-kartica .obrazec input {
    color: #f3ead7;
}

.registracija-kartica .obrazec input {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 220, 150, 0.18);
}

.registracija-kartica .obrazec input::placeholder {
    color: rgba(243, 234, 215, 0.6);
}
</style>
