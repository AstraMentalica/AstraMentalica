<?php
if (!defined('BRIDGE_VARNOST') && !defined('SISTEM_VARNOST')) {
    die('Direktni dostop je prepovedan');
}
require_once __DIR__ . '/modul_liberumbrae_funkcije.php';
require_once __DIR__ . '/modul_liberumbrae_pravila.php';
require_once __DIR__ . '/modul_liberumbrae_jsonbaza.php';

class ModulLiberUmbrae {
    private $db;
    private $config;

    public function __construct() {
        $this->db = new ModulLiberUmbraeJsonBaza();
        $this->config = $this->db->pridobiVse();
    }

    public function pridobiOsnovnePodatke() {
        return [
            'ime' => $this->config['ime'] ?? 'LiberUmbrae',
            'opis' => $this->config['opis'] ?? '',
            'razlicica' => $this->config['razlicica'] ?? '1.0.0',
            'avtor' => $this->config['avtor'] ?? '',
            'minimalna_vloga' => $this->config['minimalna_vloga'] ?? 'S0'
        ];
    }

    public function pridobiVsebino($param = []) {
        $html = \"<div class='modul-liberumbrae'>\" . htmlspecialchars($this->config['opis'] ?? '') . \"</div>\";
        return ['naslov' => $this->config['opis'] ?? 'LiberUmbrae', 'vsebina' => $html];
    }

    public function obdelajZahtevek($akcija, $podatki = []) {
        return ['uspeh' => false, 'napaka' => \"Neznana akcija $akcija\"];
    }
}
?>