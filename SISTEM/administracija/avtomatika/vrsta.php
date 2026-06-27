<?php
declare(strict_types=1);
/**
 * ============================================================
 * POT: SISTEM/administracija/avtomatika/vrsta.php
 * ============================================================
 * 
 * @package AstraMentalica\Administracija\Avtomatika
 * 
 * 📦 NAMEN:
 *     Čakalna vrsta (queue) - za asinhrono obdelavo opravil
 *     Implementira CakalnaVrsta kontrakt iz FAZE 0
 * 
 * 🔧 JAVNE FUNKCIJE (iz kontrakta):
 *     - dodaj(array $paket, string $vrsta = 'obicajna'): bool
 *     - vzemi(string $vrsta = 'obicajna'): ?array
 * - stevilo(string $vrsta = 'obicajna'): int
 *     - ponovno_poskusi(array $paket, int $zakasnitev = 5): bool
 *     - mrtvo(string $vrsta, array $paket, string $razlog): void
 * 
 * @author ASTRAMENTALICA
 * @version 1.0.0
 * @since FAZA 7
 * ============================================================
 */

namespace AstraMentalica\administracija\Avtomatika;

use AstraMentalica\kernel\izjeme\NapakaQueue;

class Vrsta implements \AstraMentalica\kernel\CakalnaVrsta
{
    private string $korenskaPot;
    private array $dovoljeneVrste;
    
    public function __construct()
    {
        $this->korenskaPot = POT_PODATKI . '/sistem/vrste/';
        $this->dovoljeneVrste = [
            'sprotno', 'visoka_prednost', 'obicajna', 'nizka_prednost',
            'elektronska_posta', 'umetna_inteligenca', 'casovnik', 'obvestila', 'mrtvo'
        ];
        
        foreach ($this->dovoljeneVrste as $vrsta) {
            $pot = $this->korenskaPot . $vrsta;
            if (!is_dir($pot)) {
                mkdir($pot, 0755, true);
            }
        }
    }
    
    private function potVrste(string $vrsta): string
    {
        if (!in_array($vrsta, $this->dovoljeneVrste)) {
            $vrsta = 'obicajna';
        }
        return $this->korenskaPot . $vrsta;
    }
    
    private function zapisiPaket(string $vrsta, array $paket): string
    {
        $vrstaPot = $this->potVrste($vrsta);
        $id = uniqid('pkg_', true);
        $imeDatoteke = $id . '.json';
        $pot = $vrstaPot . '/' . $imeDatoteke;
        
        $paket['id'] = $id;
        $paket['ustvarjen'] = time();
        $paket['poskusi'] = $paket['poskusi'] ?? 0;
        
        file_put_contents($pot, json_encode($paket, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        return $id;
    }
    
    private function izbrisiPaket(string $vrsta, string $id): bool
    {
        $vrstaPot = $this->potVrste($vrsta);
        $pot = $vrstaPot . '/' . $id . '.json';
        
        if (file_exists($pot)) {
            return unlink($pot);
        }
        return true;
    }
    
    // ============================================================
    // JAVNE FUNKCIJE (iz kontrakta)
    // ============================================================
    
    public function dodaj(array $paket, string $vrsta = 'obicajna'): bool
    {
        $this->zapisiPaket($vrsta, $paket);
        return true;
    }
    
    public function vzemi(string $vrsta = 'obicajna'): ?array
    {
        $vrstaPot = $this->potVrste($vrsta);
        $datoteke = glob($vrstaPot . '/*.json');
        
        if (empty($datoteke)) {
            return null;
        }
        
        usort($datoteke, function($a, $b) {
            return filemtime($a) - filemtime($b);
        });
        
        $prva = $datoteke[0];
        $vsebina = file_get_contents($prva);
        $paket = json_decode($vsebina, true);
        
        if ($paket) {
            $paket['poskusi'] = ($paket['poskusi'] ?? 0) + 1;
            $paket['zadnji_poskus'] = time();
            file_put_contents($prva, json_encode($paket, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        }
        
        return $paket;
    }
    
    public function stevilo(string $vrsta = 'obicajna'): int
    {
        $vrstaPot = $this->potVrste($vrsta);
        $datoteke = glob($vrstaPot . '/*.json');
        return count($datoteke);
    }
    
    public function ponovno_poskusi(array $paket, int $zakasnitev = 5): bool
    {
        $vrsta = $paket['vrsta'] ?? 'obicajna';
        $id = $paket['id'] ?? null;
        
        if (!$id) {
            return false;
        }
        
        $poskusi = ($paket['poskusi'] ?? 0) + 1;
        $paket['poskusi'] = $poskusi;
        $paket['zadnji_poskus'] = time();
        $paket['naslednji_poskus'] = time() + $zakasnitev;
        
        $this->zapisiPaket($vrsta, $paket);
        $this->izbrisiPaket($vrsta, $id);
        
        return true;
    }
    
    public function mrtvo(string $vrsta, array $paket, string $razlog): void
    {
        $paket['razlog_napake'] = $razlog;
        $paket['cas_napake'] = time();
        
        $this->zapisiPaket('mrtvo', $paket);
        
        if (isset($paket['id'])) {
            $this->izbrisiPaket($vrsta, $paket['id']);
        }
    }
    
    public function pocisti(string $vrsta): void
    {
        $vrstaPot = $this->potVrste($vrsta);
        $datoteke = glob($vrstaPot . '/*.json');
        foreach ($datoteke as $datoteka) {
            unlink($datoteka);
        }
    }
    
    public function obdelajVse(string $vrsta, callable $obdelovalec): void
    {
        $vrstaPot = $this->potVrste($vrsta);
        $datoteke = glob($vrstaPot . '/*.json');
        
        foreach ($datoteke as $datoteka) {
            $vsebina = file_get_contents($datoteka);
            $paket = json_decode($vsebina, true);
            
            try {
                $obdelovalec($paket);
                unlink($datoteka);
            } catch (\Throwable $e) {
                $paket['napaka'] = $e->getMessage();
                $this->mrtvo($vrsta, $paket, $e->getMessage());
            }
        }
    }
}

// ============================================================
// PROCEDURALNI VHODI ZA LAŽJO UPORABO
// ============================================================

$GLOBALS['VRSTA_INSTANCA'] = null;

function vrsta_instanca(): Vrsta
{
    if ($GLOBALS['VRSTA_INSTANCA'] === null) {
        $GLOBALS['VRSTA_INSTANCA'] = new Vrsta();
    }
    return $GLOBALS['VRSTA_INSTANCA'];
}

function vrsta_dodaj(array $paket, string $vrsta = 'obicajna'): bool
{
    return vrsta_instanca()->dodaj($paket, $vrsta);
}

function vrsta_vzemi(string $vrsta = 'obicajna'): ?array
{
    return vrsta_instanca()->vzemi($vrsta);
}

function vrsta_stevilo(string $vrsta = 'obicajna'): int
{
    return vrsta_instanca()->stevilo($vrsta);
}

function vrsta_ponovno_poskusi(array $paket, int $zakasnitev = 5): bool
{
    return vrsta_instanca()->ponovno_poskusi($paket, $zakasnitev);
}

function vrsta_mrtvo(string $vrsta, array $paket, string $razlog): void
{
    vrsta_instanca()->mrtvo($vrsta, $paket, $razlog);
}

function vrsta_pocisti(string $vrsta): void
{
    vrsta_instanca()->pocisti($vrsta);
}

function vrsta_obdelajVse(string $vrsta, callable $obdelovalec): void
{
    vrsta_instanca()->obdelajVse($vrsta, $obdelovalec);
}