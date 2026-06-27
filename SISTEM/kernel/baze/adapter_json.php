<?php
/**
 * ============================================================
 * POT: SISTEM/sistem_runtime/baze/adapter_json.php
 * ============================================================
 * 
 * @package AstraMentalica\Runtime\Baze
 * 
 * 📦 NAMEN:
 *     JSON adapter za shrambo
 * 
 * @author ASTRAMENTALICA
 * @version 1.0.0
 * @since FAZA 2
 * ============================================================
 */

namespace AstraMentalica\Runtime\Baze;

use AstraMentalica\Runtime\Izjeme\NapakaBaze;

class AdapterJson
{
    private string $baza_pot;
    private ?array $transakcija_podatki = null;
    
    public function __construct()
    {
        $this->baza_pot = POT_PODATKI . '/skladišče/podatkovne_zbirke/json/';
        
        if (!is_dir($this->baza_pot)) {
            mkdir($this->baza_pot, 0755, true);
        }
    }
    
    private function pot_zbirke(string $pot): string
    {
        return $this->baza_pot . str_replace('/', '_', $pot) . '.json';
    }
    
    private function preberi_zbirko(string $pot): array
    {
        $file = $this->pot_zbirke($pot);
        
        if (!file_exists($file)) {
            return [];
        }
        
        $vsebina = file_get_contents($file);
        if ($vsebina === false) {
            return [];
        }
        
        $data = json_decode($vsebina, true);
        return is_array($data) ? $data : [];
    }
    
    private function zapisi_zbirko(string $pot, array $data): bool
    {
        $file = $this->pot_zbirke($pot);
        $dir = dirname($file);
        
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        
        return file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) !== false;
    }
    
    public function beri(string $pot, array $pogoji = []): array
    {
        $data = $this->preberi_zbirko($pot);
        
        if (empty($pogoji)) {
            return $data;
        }
        
        return array_filter($data, function($item) use ($pogoji) {
            foreach ($pogoji as $kljuc => $vrednost) {
                if (!isset($item[$kljuc]) || $item[$kljuc] != $vrednost) {
                    return false;
                }
            }
            return true;
        });
    }
    
    public function beri_enega(string $pot, string $id): ?array
    {
        $data = $this->preberi_zbirko($pot);
        
        foreach ($data as $item) {
            if (isset($item['id']) && $item['id'] == $id) {
                return $item;
            }
        }
        
        return null;
    }
    
    public function zapisi(string $pot, array $podatki): bool
    {
        if ($this->transakcija_podatki !== null) {
            $this->transakcija_podatki['operacije'][] = ['tip' => 'zapisi', 'pot' => $pot, 'podatki' => $podatki];
            return true;
        }
        
        $data = $this->preberi_zbirko($pot);
        
        if (!isset($podatki['id'])) {
            $podatki['id'] = uniqid();
        }
        
        $data[] = $podatki;
        return $this->zapisi_zbirko($pot, $data);
    }
    
    public function posodobi(string $pot, string $id, array $podatki): bool
    {
        if ($this->transakcija_podatki !== null) {
            $this->transakcija_podatki['operacije'][] = ['tip' => 'posodobi', 'pot' => $pot, 'id' => $id, 'podatki' => $podatki];
            return true;
        }
        
        $data = $this->preberi_zbirko($pot);
        $najden = false;
        
        foreach ($data as $key => $item) {
            if (isset($item['id']) && $item['id'] == $id) {
                $data[$key] = array_merge($item, $podatki);
                $najden = true;
                break;
            }
        }
        
        if (!$najden) {
            return false;
        }
        
        return $this->zapisi_zbirko($pot, $data);
    }
    
    public function zbrisi(string $pot, string $id): bool
    {
        if ($this->transakcija_podatki !== null) {
            $this->transakcija_podatki['operacije'][] = ['tip' => 'zbrisi', 'pot' => $pot, 'id' => $id];
            return true;
        }
        
        $data = $this->preberi_zbirko($pot);
        $nova_data = [];
        $najden = false;
        
        foreach ($data as $item) {
            if (isset($item['id']) && $item['id'] == $id) {
                $najden = true;
                continue;
            }
            $nova_data[] = $item;
        }
        
        if (!$najden) {
            return false;
        }
        
        return $this->zapisi_zbirko($pot, $nova_data);
    }
    
    public function transakcija_zacni(): void
    {
        $this->transakcija_podatki = [
            'operacije' => [],
            'backup' => []
        ];
    }
    
    public function transakcija_potrdi(): void
    {
        if ($this->transakcija_podatki === null) {
            throw new NapakaBaze('Ni aktivne transakcije');
        }
        
        foreach ($this->transakcija_podatki['operacije'] as $op) {
            switch ($op['tip']) {
                case 'zapisi':
                    $this->zapisi($op['pot'], $op['podatki']);
                    break;
                case 'posodobi':
                    $this->posodobi($op['pot'], $op['id'], $op['podatki']);
                    break;
                case 'zbrisi':
                    $this->zbrisi($op['pot'], $op['id']);
                    break;
            }
        }
        
        $this->transakcija_podatki = null;
    }
    
    public function transakcija_preklici(): void
    {
        $this->transakcija_podatki = null;
    }
}