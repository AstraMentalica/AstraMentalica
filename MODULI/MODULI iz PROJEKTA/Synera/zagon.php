<?php include 'glava.php'; ?>

declare(strict_types=1);

/**
 * Zagon aplikacije Synera
 */

require_once 'CoreApplication.php';
require_once 'UpravljalecPodatkovneBaze.php';
require_once 'UpravljalecUporabnikov.php';
require_once 'Model/Uporabnik.php';
require_once 'Model/StatusUporabnika.php';
require_once 'Model/Simbol.php';
require_once 'Model/Runa.php';
require_once 'KnjiznicaSimbolov.php';

// Zagon aplikacije
$syneraAplikacija = new Synera\Core\GlavnaAplikacija();
$syneraAplikacija->zaženi();

echo "Aplikacija Synera je uspesno zagnana!\n";
echo "Sistem simbolov, run in mantra je pripravljen za delo.\n";

<?php include 'noga.php'; ?>