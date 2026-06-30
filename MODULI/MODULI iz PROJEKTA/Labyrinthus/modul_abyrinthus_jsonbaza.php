<?php
/**
 * MODUL: Labyrinthus
 * JSON BAZA: modul_abyrinthus_jsonbaza.php
 * VERZIJA: 1.0.0 (24.6.2026)
 * TIP: orodje
 */

declare(strict_types=1);

class ModulAbyrinthusBaza {
    private string $potBaze;
    private array $podatki;
    
    public function __construct() {
        $this->potBaze = __DIR__ . '/podatki/baza.json';
        $this->podatki = $this->nalozi();
    }
    
    private function nalozi(): array {
        if (file_exists($this->potBaze)) {
            $json = json_decode(file_get_contents($this->potBaze), true);
            if ($json) return $json;
        }
        return $this->privzeto();
    }
    
    private function privzeto(): array {
        return [
            'ime' => 'Labyrinthus', 'id' => 'abyrinthus', 'verzija' => '1.0.0',
            'tip' => 'orodje', 'vnosov' => 0,
            'ustvarjeno' => date('Y-m-d H:i:s'),
            'nazadnje_posodobljeno' => date('Y-m-d H:i:s')
        ];
    }
    
    public function shrani(): bool {
        $mapa = dirname($this->potBaze);
        if (!is_dir($mapa)) mkdir($mapa, 0755, true);
        $this->podatki['nazadnje_posodobljeno'] = date('Y-m-d H:i:s');
        return file_put_contents($this->potBaze, json_encode($this->podatki, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) !== false;
    }
    
    public function pridobiVse(): array { return $this->podatki; }
    public function pridobi(string $kljuc, $privzeto = null) { return $this->podatki[$kljuc] ?? $privzeto; }
}