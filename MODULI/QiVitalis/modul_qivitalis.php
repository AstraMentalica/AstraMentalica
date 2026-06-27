<?php
if (!defined('BRIDGE_VARNOST') && !defined('SISTEM_VARNOST')) {
    die('Direktni dostop je prepovedan');
}
require_once __DIR__ . '/modul_qivitalis_funkcije.php';
require_once __DIR__ . '/modul_qivitalis_pravila.php';
require_once __DIR__ . '/modul_qivitalis_jsonbaza.php';

class ModulQiVitalis {
    private $db;
    private $config;

    public function __construct() {
        $this->db = new ModulQiVitalisJsonBaza();
        $this->config = $this->db->pridobiVse();
    }

    public function pridobiOsnovnePodatke() {
        return [
            'ime' => $this->config['ime'] ?? 'QiVitalis',
            'opis' => $this->config['opis'] ?? '',
            'razlicica' => $this->config['razlicica'] ?? '1.0.0',
            'avtor' => $this->config['avtor'] ?? '',
            'minimalna_vloga' => $this->config['minimalna_vloga'] ?? 'S0'
        ];
    }

    public function pridobiVsebino($param = []) {
        $html = \"<div class='modul-qivitalis'>\" . htmlspecialchars($this->config['opis'] ?? '') . \"</div>\";
        return ['naslov' => $this->config['opis'] ?? 'QiVitalis', 'vsebina' => $html];
    }

    public function obdelajZahtevek($akcija, $podatki = []) {
        return ['uspeh' => false, 'napaka' => \"Neznana akcija $akcija\"];
    }
}
?>