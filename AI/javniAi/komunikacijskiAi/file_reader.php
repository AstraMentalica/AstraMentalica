<?php
/**
 * file_reader.php – prebere datoteke iz izbrane mape in vrne JSON
 * ============================================================
 * POST: { "mapa": "SISTEM" }  ali { "mapa": "" } za koren
 * ============================================================
 */
$ROOT = realpath(__DIR__ . '/..');
if ($ROOT === false) die(json_encode(['napaka' => 'ROOT ni določljiv.']));

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }

$telo = json_decode(file_get_contents('php://input'), true);
$mapa = trim($telo['mapa'] ?? '');
$pot = $ROOT . '/' . ltrim($mapa, '/');

if (!is_dir($pot)) {
    echo json_encode(['napaka' => "Mapa '$mapa' ne obstaja."]);
    exit;
}

$datoteke = [];
$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($pot, RecursiveDirectoryIterator::SKIP_DOTS)
);
foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $rel = str_replace($ROOT . '/', '', $file->getPathname());
        $vsebina = file_get_contents($file->getPathname());
        $datoteke[] = [
            'pot' => $rel,
            'vsebina' => substr($vsebina, 0, 1500) // omejimo, da ne preobremeni
        ];
    }
}

echo json_encode(['uspeh' => true, 'stevilo' => count($datoteke), 'datoteke' => $datoteke]);