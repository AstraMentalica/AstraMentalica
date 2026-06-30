<?php
/**
 * DATOTEKA: shrani.php
 * POT:      ASTRA/nadzor/shrani.php
 * NAMEN:    Backend za shranjevanje datotek iz nadzornega centra
 * NIVO:     Admin
 * ODVISNO:  pot.php
 * VERZIJA:  1.0
 * DATUM:    2026-04-26
 */

require_once __DIR__ . '/../../pot.php';

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

// Varnost — zakomentirano za razvoj
/*
session_start();
if (empty($_SESSION['uporabnik_vloga']) || $_SESSION['uporabnik_vloga'] < 99) {
    echo json_encode(['uspeh' => false, 'napaka' => 'Ni dostopa.']);
    exit;
}
*/

$vhod = json_decode(file_get_contents('php://input'), true);
$pot = $vhod['pot'] ?? '';
$vsebina = $vhod['vsebina'] ?? '';

if (empty($pot)) {
    echo json_encode(['uspeh' => false, 'napaka' => 'Manjka pot.']);
    exit;
}

// Prepreči path traversal
$pot = str_replace(['../', '..\\', '..'], '', $pot);
$polnaPot = KOREN . '/' . $pot;

// Ustvari mape če ne obstajajo
$mapa = dirname($polnaPot);
if (!is_dir($mapa)) {
    if (!mkdir($mapa, 0755, true)) {
        echo json_encode(['uspeh' => false, 'napaka' => 'Ne morem ustvariti mape: ' . $mapa]);
        exit;
    }
}

// Shrani
$ok = file_put_contents($polnaPot, $vsebina);

if ($ok === false) {
    echo json_encode(['uspeh' => false, 'napaka' => 'Napaka pri pisanju: ' . $pot]);
} else {
    // Zabeleži v log
    $log = PODATKI_POT . 'log/nadzorni_center.log';
    @file_put_contents($log, date('Y-m-d H:i:s') . ' SHRANI: ' . $pot . ' (' . strlen($vsebina) . ' B)' . PHP_EOL, FILE_APPEND);

    echo json_encode(['uspeh' => true, 'pot' => $pot, 'velikost' => $ok]);
}
