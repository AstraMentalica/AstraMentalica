<?php
/**
 * Numya modul – Numerološki izračuni in analize
 * (uporablja tvoje obstoječe funkcije iz numyra1-4.php)
 */

require_once __DIR__ . '/../../../loader.php';
require_once __DIR__ . '/funkcije.php';  // tvoje združene funkcije

$akcija = $_GET['akcija'] ?? 'info';

switch ($akcija) {
    case 'izracunaj':
        $besedilo = $_GET['besedilo'] ?? $_POST['besedilo'] ?? '';
        if (empty($besedilo)) {
            odziv(false, null, 'Parameter "besedilo" je obvezen.', 400);
        }
        // kličem TVOJO funkcijo iz numyra1.php
        $rezultat = numyra_izracunaj($besedilo);
        odziv(true, ['vnos' => $besedilo, 'rezultat' => $rezultat]);
        break;
    
    case 'osebno_leto':
        $datum = $_GET['datum'] ?? $_POST['datum'] ?? '';
        $rezultat = numyra_osebno_leto($datum);
        odziv(true, ['datum' => $datum, 'rezultat' => $rezultat]);
        break;
    
    case 'podatki':
        $podatki = file_get_contents(__DIR__ . '/numero_vse.txt');
        odziv(true, ['baza' => $podatki]);
        break;
    
    case 'porocilo':
        $porocilo = file_get_contents(__DIR__ . '/porocilo.txt');
        odziv(true, ['porocilo' => $porocilo]);
        break;
    
    default:
        odziv(true, [
            'ime' => 'Numya',
            'različica' => '2.0.0',
            'akcije' => ['izracunaj', 'osebno_leto', 'podatki', 'porocilo', 'info']
        ]);
}