<?php
/**
 * MODUL: Zverlandija
 * Otroški svet magičnih živali.
 */
declare(strict_types=1);

$bridgePoti = [__DIR__ . '/../Modul_Bridge/modul_bridge.php'];
$bridgeNajden = false;
foreach ($bridgePoti as $pot) {
    if (file_exists($pot)) { require_once $pot; $bridgeNajden = true; break; }
}
if (!$bridgeNajden) {
    header('Content-Type: application/json');
    echo json_encode(['napaka' => 'Modul_Bridge ni najden']);
    exit;
}

if (!function_exists('odziv_uspeh')) {
    function odziv_uspeh(array $vsebina, string $sporocilo = ''): array {
        return ['status' => 'uspeh', 'status_koda' => 200, 'sporocilo' => $sporocilo, 'vsebina' => $vsebina];
    }
    function odziv_napaka(string $sporocilo, int $koda = 400): array {
        return ['status' => 'napaka', 'status_koda' => $koda, 'sporocilo' => $sporocilo, 'vsebina' => []];
    }
}

function modul_zverlandija_akcija(string $akcija, array $podatki = []): array {
    if (!Modul_Bridge::vloga_preveri('S0')) {
        return odziv_napaka('Dostop zavrnjen', 403);
    }
    $funkcija = "_modul_zverlandija_$akcija";
    if (!function_exists($funkcija)) {
        return odziv_napaka("Neznana akcija: $akcija", 400);
    }
    return $funkcija($podatki);
}

function _modul_zverlandija_info(array $p): array {
    $svetovi = [
        ['id' => 'gozd', 'ime' => 'Gozd živali', 'ikona' => '🌲'],
        ['id' => 'morje', 'ime' => 'Morje čarov', 'ikona' => '🌊'],
        ['id' => 'nebo', 'ime' => 'Nebesna krila', 'ikona' => '☁️'],
        ['id' => 'gora', 'ime' => 'Gorska skrivnost', 'ikona' => '⛰️'],
        ['id' => 'mesto', 'ime' => 'Mesto meščanov', 'ikona' => '🏠'],
    ];
    return odziv_uspeh([
        'ime' => 'Zverlandija', 'id' => 'zverlandija', 'verzija' => '1.0.0',
        'opis' => 'Otroški svet poln magičnih živali.', 'svetovi' => $svetovi, 'otroski' => true,
    ], 'Informacije');
}

function _modul_zverlandija_domov(array $p): array {
    return odziv_uspeh(['sporocilo' => 'Dobrodošli v Zverlandiji!', 'cas' => time()], 'Domov');
}

if (basename($_SERVER['SCRIPT_FILENAME'] ?? '') === 'modul.php' && !defined('SISTEM_OBSTAJA')) {
    $akcija = $_REQUEST['akcija'] ?? 'domov';
    $odziv = modul_zverlandija_akcija($akcija, $_REQUEST);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($odziv, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}