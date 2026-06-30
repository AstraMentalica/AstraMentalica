<?php
if (!defined('BRIDGE_VARNOST') && !defined('SISTEM_VARNOST')) {
    die('Direktni dostop je prepovedan');
}
require_once __DIR__ . '/modul_ogham_funkcije.php';
require_once __DIR__ . '/modul_ogham_pravila.php';
require_once __DIR__ . '/modul_ogham_jsonbaza.php';

class ModulOgham {
    private $db;
    private $config;

    public function __construct() {
        $this->db = new ModulOghamJsonBaza();
        $this->config = $this->db->pridobiVse();
    }

    public function pridobiOsnovnePodatke(): array {
        return [
            'ime'            => $this->config['ime'] ?? 'Ogham',
            'opis'           => $this->config['opis'] ?? '',
            'razlicica'      => $this->config['razlicica'] ?? '1.0.0',
            'avtor'          => $this->config['avtor'] ?? '',
            'minimalna_vloga'=> $this->config['minimalna_vloga'] ?? 'S0',
        ];
    }

    public function pridobiVsebino(array $param = []): array {
        $html = "<div class='modul-ogham'>" . htmlspecialchars($this->config['opis'] ?? '') . "</div>";
        return ['naslov' => $this->config['opis'] ?? 'Ogham', 'vsebina' => $html];
    }

    public function obdelajZahtevek(string $akcija, array $podatki = []): array {
        return ['uspeh' => false, 'napaka' => "Neznana akcija $akcija"];
    }
}
