<?php
require_once 'config.php';
require_once 'integracija_astramentor.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['napaka' => 'Metoda ni dovoljena']);
    exit();
}

// Preberi vhodne podatke
$vhod = json_decode(file_get_contents('php://input'), true);
$sporocilo = $vhod['sporocilo'] ?? '';
$uporabnik_id = $vhod['uporabnik_id'] ?? null;

if (empty($sporocilo)) {
    http_response_code(400);
    echo json_encode(['napaka' => 'Sporočilo je obvezno']);
    exit();
}

// Obdelaj sporočilo z AstraMentorjem
$astramentor = new AstraMentorIntegracija();
$odgovor = $astramentor->obdelaj_sporocilo($sporocilo, $uporabnik_id);

// Vrni odgovor
echo json_encode([
    'uspeh' => true,
    'odgovor' => $odgovor,
    'casovni_zig' => time()
]);
?>