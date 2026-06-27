<?php
/**
 * ---------------------------------------------------------
 * POT: SISTEM/kernel/baze/upravljalec_migracij.php
 * v111 (27.5.2026 14:30)
 * ---------------------------------------------------------
 * OPIS: Upravljalec migracij – upravljanje sprememb baze
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
 * - migracije_ustvari(), migracije_izvedi(), migracije_ponastavi()
 * - migracije_obnovi(), migracije_seznam(), migracije_statistika()
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

class UpravljalecMigracij
{
private static ?UpravljalecMigracij $primerek = null;
private string $potMigracij;
private string $tabelaMigracij = 'migracije';

private function __construct()
{
    $this->potMigracij = POT_PODATKI . '/migracije';
    if (!is_dir($this->potMigracij)) {
        mkdir($this->potMigracij, 0755, true);
    }
    $this->zagotoviTabeloMigracij();
}

public static function pridobiPrimerek(): UpravljalecMigracij
{
    if (self::$primerek === null) {
        self::$primerek = new UpravljalecMigracij();
    }
    return self::$primerek;
}

private function zagotoviTabeloMigracij(): void
{
    if (!baza_obstaja($this->tabelaMigracij)) {
        baza_zapisi($this->tabelaMigracij, [
            'id' => 'migracije_tabela',
            'ustvarjeno' => time(),
            'migracije' => []
        ]);
    }
}

public function ustvari(string $ime, string $opis = ''): string
{
    $timestamp = date('Ymd_His');
    $imeDatoteke = $timestamp . '_' . $ime . '.php';
    $pot = $this->potMigracij . '/' . $imeDatoteke;
    
    $vsebina = $this->generirajPredlogo($ime, $opis, $timestamp);
    file_put_contents($pot, $vsebina);
    
    return $imeDatoteke;
}

private function generirajPredlogo(string $ime, string $opis, string $timestamp): string
{
    return <<<PHP
<?php
/**
 * ---------------------------------------------------------
 * MIGRACIJA: $ime
 * ID: $timestamp
 * OPIS: $opis
 * ---------------------------------------------------------
 */

declare(strict_types=1);

function migracija_gon($pdo)
{
// Spremembe baze
// \$pdo->exec("CREATE TABLE ...");

return true;
}

function migracija_nazaj($pdo)
{
// Povrnitev sprememb
// \$pdo->exec("DROP TABLE ...");

return true;
}
PHP;
}

public function izvedi(?string $ime = null): array
{
    $rezultati = [];
    $izvedene = $this->pridobiIzvedene();
    $migracije = $this->pridobiSeznam();
    
    foreach ($migracije as $migracija) {
        if ($ime !== null && $migracija['ime'] !== $ime) {
            continue;
        }
        
        if (in_array($migracija['ime'], $izvedene)) {
            $rezultati[$migracija['ime']] = 'že izvedena';
            continue;
        }
        
        try {
            $this->izvediMigracijo($migracija);
            $this->zabeleziIzvedeno($migracija['ime']);
            $rezultati[$migracija['ime']] = 'uspešno';
        } catch (Throwable $e) {
            $rezultati[$migracija['ime']] = 'napaka: ' . $e->getMessage();
        }
        
        if ($ime !== null) {
            break;
        }
    }
    
    return $rezultati;
}

private function izvediMigracijo(array $migracija): void
{
    require_once $migracija['pot'];
    
    if (function_exists('migracija_gon')) {
        $pdo = $this->pridobiPovezavo();
        migracija_gon($pdo);
    }
}

public function ponastavi(?string $ime = null): array
{
    $rezultati = [];
    $izvedene = $this->pridobiIzvedene();
    $migracije = array_reverse($this->pridobiSeznam());
    
    foreach ($migracije as $migracija) {
        if ($ime !== null && $migracija['ime'] !== $ime) {
            continue;
        }
        
        if (!in_array($migracija['ime'], $izvedene)) {
            $rezultati[$migracija['ime']] = 'ni izvedena';
            continue;
        }
        
        try {
            $this->ponastaviMigracijo($migracija);
            $this->odstraniZapis($migracija['ime']);
            $rezultati[$migracija['ime']] = 'uspešno';
        } catch (Throwable $e) {
            $rezultati[$migracija['ime']] = 'napaka: ' . $e->getMessage();
        }
        
        if ($ime !== null) {
            break;
        }
    }
    
    return $rezultati;
}

private function ponastaviMigracijo(array $migracija): void
{
    require_once $migracija['pot'];
    
    if (function_exists('migracija_nazaj')) {
        $pdo = $this->pridobiPovezavo();
        migracija_nazaj($pdo);
    }
}

public function obnovi(): array
{
    $this->ponastavi();
    return $this->izvedi();
}

public function seznam(): array
{
    $migracije = $this->pridobiSeznam();
    $izvedene = $this->pridobiIzvedene();
    
    foreach ($migracije as &$migracija) {
        $migracija['izvedena'] = in_array($migracija['ime'], $izvedene);
    }
    
    return $migracije;
}

private function pridobiSeznam(): array
{
    $seznam = [];
    $datoteke = glob($this->potMigracij . '/*.php');
    sort($datoteke);
    
    foreach ($datoteke as $pot) {
        $ime = basename($pot, '.php');
        $seznam[] = [
            'ime' => $ime,
            'pot' => $pot,
            'timestamp' => substr($ime, 0, 15)
        ];
    }
    
    return $seznam;
}

private function pridobiIzvedene(): array
{
    $zapis = baza_beri_enega($this->tabelaMigracij, 'migracije_tabela');
    return $zapis['migracije'] ?? [];
}

private function zabeleziIzvedeno(string $ime): void
{
    $zapis = baza_beri_enega($this->tabelaMigracij, 'migracije_tabela');
    $migracije = $zapis['migracije'] ?? [];
    $migracije[] = $ime;
    
    baza_posodobi($this->tabelaMigracij, 'migracije_tabela', ['migracije' => $migracije]);
}

private function odstraniZapis(string $ime): void
{
    $zapis = baza_beri_enega($this->tabelaMigracij, 'migracije_tabela');
    $migracije = array_filter($zapis['migracije'] ?? [], fn($m) => $m !== $ime);
    
    baza_posodobi($this->tabelaMigracij, 'migracije_tabela', ['migracije' => array_values($migracije)]);
}

private function pridobiPovezavo()
{
    // Pridobi PDO povezavo (odvisno od adapterja)
    if (function_exists('baza_pridobi_pdo')) {
        return baza_pridobi_pdo();
    }
    return null;
}

public function statistika(): array
{
    $vse = $this->pridobiSeznam();
    $izvedene = $this->pridobiIzvedene();
    
    return [
        'skupaj' => count($vse),
        'izvedenih' => count($izvedene),
        'neizvedenih' => count($vse) - count($izvedene),
        'zadnja_migracija' => !empty($vse) ? end($vse)['ime'] : null
    ];
}
}

// Globalne funkcije
function migracije_ustvari(string $ime, string $opis = ''): string
{
return UpravljalecMigracij::pridobiPrimerek()->ustvari($ime, $opis);
}

function migracije_izvedi(?string $ime = null): array
{
return UpravljalecMigracij::pridobiPrimerek()->izvedi($ime);
}

function migracije_ponastavi(?string $ime = null): array
{
return UpravljalecMigracij::pridobiPrimerek()->ponastavi($ime);
}

function migracije_obnovi(): array
{
return UpravljalecMigracij::pridobiPrimerek()->obnovi();
}

function migracije_seznam(): array
{
return UpravljalecMigracij::pridobiPrimerek()->seznam();
}

function migracije_statistika(): array
{
return UpravljalecMigracij::pridobiPrimerek()->statistika();
}