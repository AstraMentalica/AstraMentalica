<?php
/**
 * MODUL: SfenShui
 * JSON BAZA: modul_fenhui_jsonbaza.php
 * VERZIJA: 1.0.0 (24.6.2026)
 *
 * NAMEN:
 *     JSON podatkovna baza za modul SfenShui
 *     Tip: orodje
 */

declare(strict_types=1);

class ModulFenhuiBaza {
    private string $potBaze;
    private array $podatki;
    
    public function __construct() {
        $this->potBaze = __DIR__ . '/podatki/baza.json';
        $this->podatki = $this->nalozi();
    }
    
    /**
     * Naloži podatke iz JSON baze
     */
    private function nalozi(): array {
        if (file_exists($this->potBaze)) {
            $vsebina = file_get_contents($this->potBaze);
            $json = json_decode($vsebina, true);
            if ($json) {
                return $json;
            }
        }
        return $this->privzeto();
    }
    
    /**
     * Privzeti podatki
     */
    private function privzeto(): array {
        return [
            'ime' => 'SfenShui',
            'id' => 'fenhui',
            'verzija' => '1.0.0',
            'tip' => 'orodje',
            'vnosov' => 0,
            'ustvarjeno' => date('Y-m-d H:i:s'),
            'nazadnje_posodobljeno' => date('Y-m-d H:i:s')
        ];
    }
    
    /**
     * Shrani podatke
     */
    public function shrani(): bool {
        $mapa = dirname($this->potBaze);
        if (!is_dir($mapa)) {
            mkdir($mapa, 0755, true);
        }
        $this->podatki['nazadnje_posodobljeno'] = date('Y-m-d H:i:s');
        return file_put_contents($this->potBaze, json_encode($this->podatki, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) !== false;
    }
    
    /**
     * Pridobi vse podatke
     */
    public function pridobiVse(): array {
        return $this->podatki;
    }
    
    /**
     * Pridobi specifičen podatek
     */
    public function pridobi(string $kljuc, $privzeto = null) {
        return $this->podatki[$kljuc] ?? $privzeto;
    }
}