<?php
/**
 * ---------------------------------------------------------
 * POT: SISTEM/kernel/baze/upravljalec_seedov.php
 * v111 (27.5.2026 14:30)
 * ---------------------------------------------------------
 * OPIS: Upravljalec seedov – polnjenje baze z začetnimi podatki
 * ---------------------------------------------------------
 *
 * ODVISNOSTI:
 * - pot.php
 * - SISTEM/kernel/baze/upravljalec_baz.php
 *
 * UPORABA:
 * - CLI orodja, namestitev sistema
 *
 * FUNKCIJE:
 * - seed_ustvari(), seed_izvedi(), seed_pocisti()
 * - seed_seznam(), seed_statistika()
 *
 * PREPOVEDI:
 * - Brez echo, print_r, var_dump
 *
 * STATUS: Stabilno
 *
 * ZGODOVINA:
 * - v111: FAZA 38 – implementacija
 *
 * ---------------------------------------------------------
 * AVTOR: AstraMentalica Mojster
 * ---------------------------------------------------------
 */

declare(strict_types=1);

class UpravljalecSeedov
{
private static ?UpravljalecSeedov $primerek = null;
private string $potSeedov;

private function __construct()
{
    $this->potSeedov = POT_PODATKI . '/seedi';
    if (!is_dir($this->potSeedov)) {
        mkdir($this->potSeedov, 0755, true);
    }
}

public static function pridobiPrimerek(): UpravljalecSeedov
{
    if (self::$primerek === null) {
        self::$primerek = new UpravljalecSeedov();
    }
    return self::$primerek;
}

public function ustvari(string $ime, string $tabela): string
{
    $imeDatoteke = $ime . '.php';
    $pot = $this->potSeedov . '/' . $imeDatoteke;
    
    $vsebina = $this->generirajPredlogo($ime, $tabela);
    file_put_contents($pot, $vsebina);
    
    return $imeDatoteke;
}

private function generirajPredlogo(string $ime, string $tabela): string
{
    return <<<PHP
<?php
/**
 * ---------------------------------------------------------
 * SEED: $ime
 * TABELA: $tabela
 * ---------------------------------------------------------
 */

declare(strict_types=1);

function seed_izvedi(\$baza)
{
\$podatki = [
    // Primer:
    // ['ime' => 'Admin', 'email' => 'admin@example.com']
];

foreach (\$podatki as \$zapis) {
    \$baza->zapisi('$tabela', \$zapis);
}

return true;
}

function seed_pocisti(\$baza)
{
// \$baza->zbrisi('$tabela', []);
return true;
}
PHP;
}

public function izvedi(?string $ime = null): array
{
    $rezultati = [];
    $seedi = $this->pridobiSeznam();
    
    foreach ($seedi as $seed) {
        if ($ime !== null && $seed['ime'] !== $ime) {
            continue;
        }
        
        try {
            $this->izvediSeed($seed);
            $rezultati[$seed['ime']] = 'uspešno';
        } catch (Throwable $e) {
            $rezultati[$seed['ime']] = 'napaka: ' . $e->getMessage();
        }
        
        if ($ime !== null) {
            break;
        }
    }
    
    return $rezultati;
}

private function izvediSeed(array $seed): void
{
    require_once $seed['pot'];
    
    if (function_exists('seed_izvedi')) {
        seed_izvedi(UpravljalecBaz::pridobiPrimerek());
    }
}

public function pocisti(?string $ime = null): array
{
    $rezultati = [];
    $seedi = array_reverse($this->pridobiSeznam());
    
    foreach ($seedi as $seed) {
        if ($ime !== null && $seed['ime'] !== $ime) {
            continue;
        }
        
        try {
            $this->pocistiSeed($seed);
            $rezultati[$seed['ime']] = 'uspešno';
        } catch (Throwable $e) {
            $rezultati[$seed['ime']] = 'napaka: ' . $e->getMessage();
        }
        
        if ($ime !== null) {
            break;
        }
    }
    
    return $rezultati;
}

private function pocistiSeed(array $seed): void
{
    require_once $seed['pot'];
    
    if (function_exists('seed_pocisti')) {
        seed_pocisti(UpravljalecBaz::pridobiPrimerek());
    }
}

public function obnovi(): array
{
    $this->pocisti();
    return $this->izvedi();
}

public function seznam(): array
{
    $seznam = [];
    $datoteke = glob($this->potSeedov . '/*.php');
    sort($datoteke);
    
    foreach ($datoteke as $pot) {
        $ime = basename($pot, '.php');
        $seznam[] = [
            'ime' => $ime,
            'pot' => $pot
        ];
    }
    
    return $seznam;
}

public function statistika(): array
{
    $seedi = $this->pridobiSeznam();
    
    return [
        'skupaj' => count($seedi),
        'seznam' => array_column($seedi, 'ime')
    ];
}
}

// Globalne funkcije
function seed_ustvari(string $ime, string $tabela): string
{
return UpravljalecSeedov::pridobiPrimerek()->ustvari($ime, $tabela);
}

function seed_izvedi(?string $ime = null): array
{
return UpravljalecSeedov::pridobiPrimerek()->izvedi($ime);
}

function seed_pocisti(?string $ime = null): array
{
return UpravljalecSeedov::pridobiPrimerek()->pocisti($ime);
}

function seed_obnovi(): array
{
return UpravljalecSeedov::pridobiPrimerek()->obnovi();
}

function seed_seznam(): array
{
return UpravljalecSeedov::pridobiPrimerek()->seznam();
}

function seed_statistika(): array
{
return UpravljalecSeedov::pridobiPrimerek()->statistika();
}