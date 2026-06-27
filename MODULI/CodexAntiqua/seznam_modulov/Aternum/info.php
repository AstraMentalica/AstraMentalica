<?php
/**
 * Aeternum modul – Večna modrost in struktura zavedanja
 * 
 * Akcije:
 * - struktura    -> vrne strukturo zavedanja (struktura.txt)
 * - manifest     -> vrne manifest modula
 * - pravila      -> vrne AI pravila (če obstajajo)
 * - info         -> vrne osnovne podatke o modulu
 */

// Vključimo loader (3 nivoje navzgor: Aeternum/ -> osnovni/ -> MODULI/ -> koren)
require_once __DIR__ . '/../../../loader.php';

$akcija = $_GET['akcija'] ?? $_POST['akcija'] ?? 'info';

switch ($akcija) {
    // ------------------------------------------------------------
    case 'struktura':
        $struktura = file_get_contents(__DIR__ . '/podatki/struktura.txt');
        if ($struktra === false) {
            odziv(false, null, 'Struktura ni najdena.', 404);
        }
        odziv(true, [
            'modul' => 'Aeternum',
            'struktura' => $struktura,
            'velikost' => strlen($struktura)
        ]);
        break;
    
    // ------------------------------------------------------------
    case 'manifest':
        $manifest = file_get_contents(__DIR__ . '/manifest.json');
        if ($manifest === false) {
            odziv(false, null, 'Manifest ni najden.', 404);
        }
        odziv(true, json_decode($manifest, true));
        break;
    
    // ------------------------------------------------------------
    case 'pravila':
        $pravila = file_get_contents(__DIR__ . '/podatki/pravila.md');
        if ($pravila === false) {
            odziv(false, null, 'Pravila niso najdena.', 404);
        }
        odziv(true, [
            'modul' => 'Aeternum',
            'pravila' => $pravila,
            'format' => 'markdown'
        ]);
        break;
    
    // ------------------------------------------------------------
    case 'info':
        odziv(true, [
            'ime' => 'Aeternum',
            'različica' => '1.0.0',
            'avtor' => 'Damir',
            'opis' => 'Večna modrost in struktura zavedanja',
            'zahteva' => [
                'php' => '7.4',
                'loader' => '1.0.0'
            ],
            'akcije' => ['struktura', 'manifest', 'pravila', 'info']
        ]);
        break;
    
    // ------------------------------------------------------------
    default:
        odziv(false, null, "Neznana akcija: '$akcija'. Dovoljene: struktura, manifest, pravila, info", 400);
}