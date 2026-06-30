<?php
if (!defined('BRIDGE_VARNOST') && !defined('SISTEM_VARNOST')) {
    die('Direktni dostop je prepovedan');
}
require_once __DIR__ . '/modul_animaris_funkcije.php';
require_once __DIR__ . '/modul_animaris_pravila.php';
require_once __DIR__ . '/modul_animaris_jsonbaza.php';

class ModulAnimaris {
    private $db;
    private $config;

    public function __construct() {
        $this->db = new ModulAnimarisJsonBaza();
        $this->config = $this->db->pridobiVse();
    }

    public function pridobiOsnovnePodatke(): array {
        return [
            'ime'            => $this->config['ime'] ?? 'Animaris',
            'opis'           => $this->config['opis'] ?? '',
            'razlicica'      => $this->config['razlicica'] ?? '1.0.0',
            'avtor'          => $this->config['avtor'] ?? '',
            'minimalna_vloga'=> $this->config['minimalna_vloga'] ?? 'S0',
        ];
    }

    public function pridobiVsebino(array $param = []): array {
        $html = "<div class='modul-animaris'>" . htmlspecialchars($this->config['opis'] ?? '') . "</div>";
        return ['naslov' => $this->config['opis'] ?? 'Animaris', 'vsebina' => $html];
    }

    public function obdelajZahtevek(string $akcija, array $podatki = []): array {
        return ['uspeh' => false, 'napaka' => "Neznana akcija $akcija"];
    }
}
